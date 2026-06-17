<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Report;
use Illuminate\Support\Facades\Http;
use Exception;

class EvolutionController extends Controller
{
    public function index()
    {
        $patients = Patient::with(['reports' => function ($q) {
                $q->whereNotNull('exam_id')->orderBy('generation_date');
            }])
            ->where('user_id', auth()->id())
            ->get()
            ->filter(fn($p) => $p->reports->count() >= 1);

        return view('evolucao.index', compact('patients'));
    }

    public function analyzeEvolution(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $patient = Patient::where('user_id', auth()->id())
            ->findOrFail($request->patient_id);

        $reports = Report::whereNotNull('exam_id')
            ->whereHas('exam.patient', fn($q) => $q->where('id', $patient->id))
            ->orderBy('generation_date')
            ->get();

        if ($reports->count() < 1) {
            return back()->with('error', 'Este paciente não possui laudos individuais gerados. Gere ao menos um laudo antes de analisar a evolução.');
        }

        $numLaudos = $reports->count();

        $laudosTexto = $reports->map(function ($report, $index) {
            $data = $report->generation_date?->format('d/m/Y') ?? 'Data não registrada';
            return "**Laudo " . ($index + 1) . " — gerado em {$data}**\n\n" . $report->report_content;
        })->implode("\n\n---\n\n");

        $idade = $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age . ' anos' : 'não informada';
        $sexo = match($patient->gender ?? '') { 'M' => 'Masculino', 'F' => 'Feminino', default => 'Não informado' };
        $tabagismo = match($patient->smoker ?? '') { 'sim' => 'Fumante', 'ex_fumante' => 'Ex-fumante', default => 'Não fumante' };
        $peso = $patient->weight ? $patient->weight . ' kg' : 'não informado';
        $altura = $patient->height ? $patient->height . ' cm' : 'não informada';

        $prompt = "Você é um especialista em pneumologia e fisioterapia respiratória com vasta experiência em análise de evolução clínica seriada. " .
                  "Sua tarefa é elaborar um Laudo de Evolução Clínica detalhado, comparando os laudos individuais de espirometria do paciente ao longo do tempo.\n\n" .
                  "**Diretrizes:**\n" .
                  "- Analise a progressão clínica com base nos laudos já interpretados\n" .
                  "- Use os critérios da ATS/ERS para classificação dos distúrbios ventilatórios\n" .
                  "- Identifique tendências de melhora, estabilidade ou piora entre os laudos\n" .
                  "- Considere o perfil clínico do paciente na interpretação (idade, sexo, peso, altura, tabagismo)\n" .
                  "- Classifique a gravidade conforme: leve (VEF1 ≥70%), moderado (50-69%), grave (35-49%), muito grave (<35%)\n" .
                  "- Responda sempre em português do Brasil com linguagem clínica formal\n" .
                  "- O laudo deve ser completo — não omita seções nem truncue raciocínios\n\n" .
                  "**Dados do Paciente:**\n" .
                  "Nome: {$patient->name} | Idade: {$idade} | Sexo: {$sexo} | Peso: {$peso} | Altura: {$altura} | Tabagismo: {$tabagismo} | Laudos analisados: {$numLaudos}\n\n" .
                  "---\n\n" .
                  "Gere o laudo usando EXATAMENTE esta estrutura em Markdown:\n\n" .
                  "## Laudo de Evolução Clínica — {$patient->name}\n\n" .
                  "**Paciente:** {$patient->name} | **ID:** {$patient->id} | **Laudos analisados:** {$numLaudos}\n\n" .
                  "---\n\n" .
                  "### 1. Síntese Clínica\n" .
                  "Escreva 2 a 3 parágrafos descrevendo a tendência geral da função pulmonar ao longo dos laudos, incluindo o tipo de distúrbio ventilatório identificado e sua evolução.\n\n" .
                  "### 2. Análise Comparativa dos Laudos\n" .
                  "Compare sistematicamente os achados entre os laudos, identificando:\n" .
                  "- Variações nos parâmetros espirométricos (CVF, VEF1, relação VEF1/CVF, FEF 25-75%)\n" .
                  "- Mudanças na classificação e gravidade do distúrbio ventilatório\n" .
                  "- Respostas ao broncodilatador (quando presentes)\n\n" .
                  "### 3. Classificação e Gravidade Atual\n" .
                  "- Tipo de distúrbio ventilatório predominante no laudo mais recente\n" .
                  "- Grau de gravidade atual\n" .
                  "- Comparação com laudos anteriores: melhora, estabilidade ou piora\n\n" .
                  "### 4. Interpretação Clínica\n" .
                  "Discuta as implicações clínicas da evolução observada, possíveis diagnósticos diferenciais e fatores que podem ter influenciado a progressão.\n\n" .
                  "### 5. Recomendações\n" .
                  "Liste recomendações objetivas para conduta clínica:\n" .
                  "- Ajuste de tratamento farmacológico ou fisioterapêutico\n" .
                  "- Necessidade de novos exames\n" .
                  "- Frequência de reavaliação\n" .
                  "- Encaminhamentos sugeridos\n\n" .
                  "---\n\n" .
                  "### Laudos Individuais Utilizados como Base\n\n" .
                  $laudosTexto;

        $apiKey = config('services.gemini.key');
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]
            ],
            'generationConfig' => [
                'temperature' => auth()->user()->ia_temperature ?? 0.5,
                'maxOutputTokens' => 8192,
            ],
        ];

        try {
            $aiResponse = Http::timeout(120)
                ->withoutVerifying()
                ->post($apiUrl, $payload)
                ->json();

            $content = $aiResponse['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($content)) {
                $apiError = $aiResponse['error']['message'] ?? 'Resposta vazia.';
                throw new Exception("A IA não retornou um laudo válido. Motivo: {$apiError}");
            }

            $evolutionReport = Report::create([
                'patient_id'      => $patient->id,
                'exam_id'         => null,
                'report_content'  => $content,
                'generation_date' => now(),
            ]);

            return redirect()
                ->route('reports.show', $evolutionReport->id)
                ->with('success', "Laudo de evolução gerado com sucesso para {$patient->name}!");

        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('Erro HTTP ao gerar laudo de evolução: ' . ($e->response ? $e->response->body() : $e->getMessage()));
            return back()->with('error', 'Erro na comunicação com a API da IA. Verifique sua chave ou limites de uso.');
        } catch (Exception $e) {
            \Log::error('Erro ao gerar laudo de evolução para ' . $patient->name . ': ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }
}
