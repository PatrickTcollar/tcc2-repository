<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Exam;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use Exception;

class EvolutionController extends Controller
{
    /**
     * Exibe o formulário para selecionar o paciente e gerar o laudo de evolução.
     * Mapeado para GET /evolucao
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Busca todos os pacientes para o select
        // 'with('exams')' garante que a coleção de exames está carregada para a view
        $patients = Patient::with('exams')->get();
        return view('evolucao.index', compact('patients'));
    }

    /**
     * Analisa a evolução de um paciente com base em seus exames ao longo do tempo.
     * Mapeado para POST /evolucao/analisar
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function analyzeEvolution(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $patientId = $request->input('patient_id');
        // Carrega o paciente com seus exames.
        $patient = Patient::with(['exams'])->find($patientId);

        if (!$patient) {
            return back()->with('error', 'Paciente não encontrado.');
        }

        // Verifica se há exames para o paciente
        if ($patient->exams->isEmpty()) {
            return back()->with('error', 'O paciente selecionado não possui exames cadastrados.');
        }

        $allExamsText = [];
        // Ordena por data de upload para garantir a sequência da evolução
        foreach ($patient->exams->sortBy('upload_date') as $exam) {
            $pdfPath = Storage::disk('public')->path($exam->file_path);

            if (file_exists($pdfPath)) {
                try {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($pdfPath);
                    $cleanedText = preg_replace('/\s+/', ' ', trim($pdf->getText()));

                    if (!empty($cleanedText)) {
                        $allExamsText[] = "Exame ID: {$exam->id}, Data: " . ($exam->upload_date->format('d/m/Y')) . "\nResultados:\n" . $cleanedText;
                    }
                } catch (Exception $e) {
                    \Log::error("Erro ao extrair PDF para evolução (Exame ID: {$exam->id}): " . $e->getMessage());
                }
            }
        }

        if (empty($allExamsText)) {
            return back()->with('error', 'Nenhum exame com texto extraível encontrado para este paciente no período. Certifique-se de que os PDFs contêm texto.');
        }

        // 1. Construção do Prompt Enriquecido
        $numExames = count($allExamsText);
        $prompt = "Você é um especialista em pneumologia e fisioterapia respiratória com vasta experiência em interpretação de espirometria seriada. " .
                  "Sua tarefa é elaborar um Laudo de Evolução Clínica detalhado, comparando os exames de espirometria do paciente ao longo do tempo.\n\n" .
                  "**Diretrizes:**\n" .
                  "- Analise cada parâmetro espirométrico individualmente e de forma comparativa\n" .
                  "- Use os critérios da ATS/ERS para classificação dos distúrbios ventilatórios\n" .
                  "- Quantifique as variações percentuais entre os exames quando possível\n" .
                  "- Classifique a gravidade conforme: leve (VEF1 ≥70%), moderado (50-69%), grave (35-49%), muito grave (<35%)\n" .
                  "- Responda sempre em português do Brasil com linguagem clínica formal\n" .
                  "- O laudo deve ser completo — não omita seções nem trunce raciocínios\n\n" .
                  "**Dados do Paciente:**\n" .
                  "Nome: {$patient->name} | ID: {$patient->id} | Total de exames analisados: {$numExames}\n\n" .
                  "---\n\n" .
                  "Gere o laudo usando EXATAMENTE esta estrutura em Markdown:\n\n" .
                  "## Laudo de Evolução Clínica — {$patient->name}\n\n" .
                  "**Paciente:** {$patient->name} | **ID:** {$patient->id} | **Exames analisados:** {$numExames}\n\n" .
                  "---\n\n" .
                  "### 1. Síntese Clínica\n" .
                  "Escreva 2 a 3 parágrafos descrevendo a tendência geral da função pulmonar ao longo dos exames, incluindo o tipo de distúrbio ventilátório identificado (obstrutivo, restritivo, misto ou ausente) e sua evolução.\n\n" .
                  "### 2. Análise Comparativa dos Parâmetros Espirométricos\n" .
                  "Compare sistematicamente os seguintes parâmetros entre os exames (quando disponíveis):\n" .
                  "- **CVF** (Capacidade Vital Forçada): valores absolutos, % do previsto e variação entre exames\n" .
                  "- **VEF1** (Volume Expiratório Forçado no 1º segundo): valores, % do previsto e variação\n" .
                  "- **Relação VEF1/CVF**: classificação do distúrbio obstrutivo\n" .
                  "- **FEF 25-75%**: fluxo expiratório forçado médio (vias aéreas pequenas)\n" .
                  "- **PFE** (Pico de Fluxo Expiratório): quando disponível\n" .
                  "- **Resposta ao broncodilatador**: se realizado, descreva a reversibilidade\n\n" .
                  "### 3. Classificação e Gravidade\n" .
                  "- Tipo de distúrbio ventilátório predominante\n" .
                  "- Grau de gravidade atual (leve/moderado/grave/muito grave)\n" .
                  "- Comparação com exame(s) anterior(es): melhora, estabilidade ou piora\n\n" .
                  "### 4. Interpretação Clínica e Correlações\n" .
                  "Discuta as possíveis implicações clínicas dos achados, possíveis diagnósticos diferenciais compatíveis com o padrão encontrado e fatores que podem influenciar os resultados (esforço do paciente, calibração do equipamento, etc.).\n\n" .
                  "### 5. Recomendações\n" .
                  "Liste recomendações objetivas para conduta clínica, como:\n" .
                  "- Necessidade de repetição do exame\n" .
                  "- Ajuste de tratamento farmacológico ou fisioterapêutico\n" .
                  "- Encaminhamentos sugeridos\n" .
                  "- Frequência de reavaliação\n\n" .
                  "### 6. Dados Brutos dos Exames\n" .
                  "Transcreva abaixo os dados originais de cada exame sem alterações:\n\n" .
                  implode("\n\n---\n\n", $allExamsText);

        $apiKey = config('services.gemini.key');
        // Usando gemini-2.5-flash, o modelo recomendado e mais robusto.
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
            ]
        ];

        try {
            // CORREÇÃO ESSENCIAL: Adiciona withoutVerifying() para contornar o erro cURL 60 (problema de SSL) no ambiente local.
            $aiResponse = Http::timeout(120)
                            ->withoutVerifying()
                            ->post($apiUrl, $payload)->json();

            $evolutionReportContent = $aiResponse['candidates'][0]['content']['parts'][0]['text']
                                      ?? 'Não foi possível gerar o laudo de evolução. Resposta da IA vazia ou estrutura inesperada.';

            if (empty($evolutionReportContent) || str_contains($evolutionReportContent, 'Não foi possível gerar o laudo.')) {
                 throw new Exception('A API da IA não retornou um laudo de evolução válido. Verifique o prompt ou a resposta da API.');
            }

            // AQUI: Cria o laudo de evolução com a referência ao paciente.
            $evolutionReport = Report::create([
                'patient_id' => $patient->id,
                'exam_id' => null, // Assinala que é um laudo de evolução, não ligado a um único exame.
                'report_content' => $evolutionReportContent,
                'generation_date' => now(),
            ]);

            return redirect()->route('reports.show', $evolutionReport->id)->with('success', 'Laudo de evolução gerado com sucesso para ' . $patient->name . '!');

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Captura erros específicos de requisições HTTP (ex: 4xx, 5xx).
            \Log::error('Erro HTTP/API ao gerar laudo de evolução: ' . ($e->response ? $e->response->body() : $e->getMessage()));
            return back()->with('error', 'Erro na comunicação com a API da IA. Verifique sua chave ou limites de uso. Detalhes no log.');
        } catch (Exception $e) {
            \Log::error('Erro geral ao gerar laudo de evolução para paciente ' . $patient->name . ': ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao gerar o laudo de evolução: ' . $e->getMessage());
        }
    }
}
