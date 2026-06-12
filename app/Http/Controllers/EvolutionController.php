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

        // 1. Construção do Prompt Aprimorado
        $prompt = "Você é um assistente especializado em fisioterapia respiratória. Sua tarefa é analisar a sequência de exames de espirometria e gerar um Laudo de Evolução. O foco deve ser em COMPARAR os resultados ao longo do tempo. Use a seguinte estrutura em Markdown:\n\n" .
                  "## Laudo de Evolução - Paciente {$patient->name}\n\n" .
                  "**ID do Paciente:** {$patient->id}\n\n" .
                  "### 1. Resumo da Evolução\n" .
                  "Gere um parágrafo conciso que resuma a tendência geral das métricas respiratórias ao longo dos exames.\n\n" .
                  "### 2. Análise Detalhada (Comparativa)\n" .
                  "Utilize uma lista com os pontos mais importantes, comparando os exames mais antigos com os mais recentes. Destaque:\n" .
                  "* Mudanças no VEF1 e CVF (Em % ou valores absolutos).\n" .
                  "* Sinais de progressão ou regressão da patologia.\n" .
                  "* Recomendações de acompanhamento.\n\n" .
                  "### 3. Histórico de Exames (Dados de Entrada)\n" .
                  "Não altere ou interprete estes dados, apenas liste-os como foram fornecidos pela fonte:\n\n" .
                  "**Atenção:** Se houver menos de dois exames válidos, gere um alerta informando que a evolução é inconclusiva.\n\n" .
                  implode("\n\n---\n\n", $allExamsText); // Adiciona os textos de exame ao final do prompt

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
                'temperature' => 0.7,
                'maxOutputTokens' => 2000,
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
