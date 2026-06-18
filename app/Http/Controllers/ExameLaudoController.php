<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use Exception;
use Illuminate\Support\Facades\Log; // Importação ainda é necessária para Log::error

class ExameLaudoController extends Controller
{
    /**
     * Exibe uma listagem de todos os laudos.
     */
    public function index()
    {
        $laudos = Report::whereHas('exam.patient', function ($query) {
            $query->where('user_id', auth()->id());
        })->orWhereHas('patient', function ($query) {
            $query->where('user_id', auth()->id());
        })->orderBy('id', 'asc')->get();
        return view('reports.index', compact('laudos'));
    }

    /**
     * Método placeholder (se não for usado, pode ser removido no futuro).
     */
    public function gerarLaudo($id)
    {
        abort(404, 'Método de geração de laudo antigo. Use a nova funcionalidade de geração de laudo automatizado.');
    }

    /**
     * Gera um laudo automatizado para um exame usando a API do Gemini (IA).
     */
    public function gerarLaudoAutomotizado(Request $request, Exam $exam)
    {
        $report = null;

        try {
            // 1. Acessar e Extrair Texto do Arquivo PDF do Exame
            $pdfPath = Storage::disk('public')->path($exam->file_path);

            if (!file_exists($pdfPath)) {
                throw new Exception('Arquivo PDF do exame não encontrado no diretório de armazenamento.');
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            $cleanedText = preg_replace('/\s+/', ' ', trim($text));

            if (empty($cleanedText)) {
                // Se o PDF não tiver texto, lançamos esta exceção com uma mensagem mais clara.
                throw new Exception('Não foi possível extrair texto relevante do arquivo PDF. O arquivo pode estar vazio ou ser apenas imagem.');
            }

            // 2. Construir o Prompt com dados do paciente + exame
            $patient = $exam->patient;
            $idade = $patient->birth_date ? $patient->birth_date->age . ' anos' : 'não informada';
            $sexo = match($patient->gender) { 'M' => 'Masculino', 'F' => 'Feminino', default => 'Não informado' };
            $tabagismo = match($patient->smoker) { 'sim' => 'Fumante', 'ex_fumante' => 'Ex-fumante', default => 'Não fumante' };
            $peso = $patient->weight ? $patient->weight . ' kg' : 'não informado';
            $altura = $patient->height ? $patient->height . ' cm' : 'não informada';

            $prompt = "Aja estritamente como um **Fisioterapeuta Respiratório Sênior**. Analise os dados de espirometria abaixo considerando os dados clínicos do paciente para calcular corretamente os valores previstos e gerar um LAUDO PROFISSIONAL em Português.

**DADOS DO PACIENTE:**
- Nome: {$patient->name}
- Idade: {$idade}
- Sexo: {$sexo}
- Peso: {$peso}
- Altura: {$altura}
- Tabagismo: {$tabagismo}

Use esses dados para calcular os valores previstos (predicted) conforme critérios ATS/ERS e comparar com os valores obtidos no exame.

O laudo deve seguir este formato obrigatório, usando títulos em Markdown:

## Achados Principais
[Lista concisa das métricas chave, valores obtidos vs. previstos e desvios relevantes.]

## Interpretação Clínica
[Análise profissional da função pulmonar (restritiva, obstrutiva, mista ou normal), classificação da gravidade e correlação com o perfil clínico do paciente. Máximo 2 parágrafos.]

## Recomendações
[Sugestões de acompanhamento ou tratamento considerando o histórico clínico. Máximo 1 parágrafo.]

Se os dados do exame estiverem ilegíveis ou faltantes, substitua todo o laudo por: 'ERRO: Não foi possível realizar a análise devido à falta de dados legíveis no exame.'

**DADOS BRUTOS DO EXAME:**\n\n" . $cleanedText;

            // 3. Fazer a Requisição para a API do Gemini
            $apiKey = config('services.gemini.key');
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => auth()->user()->ia_temperature,
                    'maxOutputTokens' => 8192,
                ],
            ];

            // ** ADICIONE O LOG AQUI (Entre as linhas 93 e 95) **
            \Log::info('--- Debug Chamada API Gemini ---', [
                'api_key_presente' => !empty($apiKey),
                 'api_url_gerada'   => $apiUrl,
                'payload_enviado'  => $payload
            ]);

            // Realiza a requisição HTTP POST com até 3 tentativas em caso de sobrecarga da API.
            $aiResponse = null;
            $reportContent = null;
            $maxTentativas = 3;
            for ($tentativa = 1; $tentativa <= $maxTentativas; $tentativa++) {
                $aiResponse = Http::timeout(120)->withoutVerifying()->post($apiUrl, $payload)->json();
                $reportContent = $aiResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;
                $apiErrorMsg = $aiResponse['error']['message'] ?? '';

                if (!empty($reportContent)) {
                    break;
                }

                $isOverload = str_contains(strtolower($apiErrorMsg), 'demand') ||
                              str_contains(strtolower($apiErrorMsg), 'overload') ||
                              str_contains(strtolower($apiErrorMsg), 'quota') ||
                              str_contains(strtolower($apiErrorMsg), 'resource_exhausted');

                if ($tentativa < $maxTentativas && $isOverload) {
                    sleep(5 * $tentativa);
                    continue;
                }

                break;
            }

            // 4. Processar a Resposta da IA
            if (empty($reportContent) || isset($aiResponse['error'])) {
                $apiError = $aiResponse['error']['message'] ?? 'Resposta vazia ou incompleta.';
                throw new Exception("A API da IA não retornou um laudo válido. Motivo: {$apiError}. Verifique o log para detalhes se o erro persistir.");
            }

            // 5. Salvar ou Atualizar o Laudo no Banco de Dados
            $report = Report::updateOrCreate(
                ['exam_id' => $exam->id],
                [
                    'patient_id' => $exam->patient_id,
                    'report_content' => $reportContent,
                    'generation_date' => now(),
                    'signed_by' => null,
                    'signer_crf' => null,
                    'signed_at' => null,
                    'signature_image' => null,
                ]
            );

            return redirect()->route('reports.show', $report->id)->with('success', 'Laudo gerado com sucesso para o exame ' . $exam->original_filename . '!');

        } catch (\Illuminate\Http\Client\RequestException $e) {
            $errorMessage = 'Erro na comunicação com a API da IA: ' . ($e->response ? $e->response->body() : $e->getMessage());
            Log::error($errorMessage);
            return back()->with('error', 'Erro na API da IA. Verifique sua chave, conexão ou limites de uso. Detalhes no log.');
        } catch (Exception $e) {
            Log::error('Erro ao gerar laudo para exame ' . $exam->id . ': ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao gerar o laudo: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o conteúdo de um laudo específico.
     */
    public function showReport(Report $report)
    {
        return view('reports.show', compact('report'));
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('laudos.index')->with('success', 'Laudo excluído com sucesso.');
    }

    public function sign(Request $request, Report $report)
    {
        $request->validate([
            'signed_by'       => 'required|string|max:255',
            'signer_crf'      => 'required|string|max:50',
            'signature_image' => 'required|string',
        ]);

        if ($report->signed_at) {
            return back()->with('error', 'Este laudo já foi assinado.');
        }

        $report->update([
            'signed_by'       => $request->signed_by,
            'signer_crf'      => $request->signer_crf,
            'signed_at'       => now(),
            'signature_image' => $request->signature_image,
        ]);

        return back()->with('success', 'Laudo assinado com sucesso por ' . $request->signed_by . '.');
    }
}
