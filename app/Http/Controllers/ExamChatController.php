<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use Exception;

class ExamChatController extends Controller
{
    /**
     * Exibe a interface de chat para um exame espec\u00edfico.
     * Mapeado para GET /exames/{exam}/chat
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\View\View
     */
    public function showChatInterface(Exam $exam)
    {
        // Carrega o relacionamento patient para ter o nome do paciente
        $exam->load('patient');
        return view('exams.chat', compact('exam'));
    }

    /**
     * Processa a mensagem do chat, envia para a IA e retorna a resposta.
     * Mapeado para POST /api/exames/{exam}/chat
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleChatMessage(Request $request, Exam $exam)
    {
        $userMessage = $request->input('message');

        // 1. Localizar o PDF do exame
        $pdfPath = Storage::disk('public')->path($exam->file_path);
        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'Arquivo PDF do exame n\u00e3o encontrado.'], 404);
        }

        // 2. Construir o Prompt para a Intelig\u00eancia Artificial (Gemini)
        $systemContext =
            "Voc\u00ea \u00e9 um especialista em pneumologia e fisioterapia respirat\u00f3ria com amplo conhecimento em interpreta\u00e7\u00e3o de espirometria. " .
            "Seu papel \u00e9 auxiliar profissionais de sa\u00fade a entender os resultados do exame de forma clara, precisa e clinicamente fundamentada.\n\n" .
            "**Diretrizes para suas respostas:**\n" .
            "- Sempre responda de forma completa, sem truncar frases ou racioc\u00ednios\n" .
            "- Use terminologia cl\u00ednica adequada, mas explique os termos quando necess\u00e1rio\n" .
            "- Baseie suas respostas estritamente nos dados do exame fornecido\n" .
            "- Quando relevante, cite os valores num\u00e9ricos do exame para embasar sua an\u00e1lise\n" .
            "- Se a pergunta envolver conduta cl\u00ednica, lembre que a decis\u00e3o final \u00e9 do profissional de sa\u00fade\n" .
            "- Responda sempre em portugu\u00eas do Brasil\n\n" .
            "**Dados do Paciente:**\n" .
            "Nome: " . ($exam->patient->name ?? 'N\u00e3o informado') . "\n" .
            "ID do Exame: " . $exam->id . "\n\n";

        $question = "**Pergunta do profissional:** " . $userMessage . "\n\nResponda de forma completa e detalhada:";

        // Tenta extra\u00e7\u00e3o de texto; se PDF for escaneado ou encoding corrompido, usa vis\u00e3o
        $examText = $this->tryExtractPdfText($pdfPath);

        if ($examText !== null) {
            $parts = [['text' => $systemContext . "**Resultados do Exame de Espirometria:**\n" . $examText . "\n\n" . $question]];
        } else {
            $pdfBase64 = base64_encode(file_get_contents($pdfPath));
            $parts = [
                ['inlineData' => ['mimeType' => 'application/pdf', 'data' => $pdfBase64]],
                ['text' => $systemContext . "Analise o exame de espirometria no PDF anexo e responda:\n\n" . $question],
            ];
        }

        // 3. Fazer a Requisi\u00e7\u00e3o para a API do Gemini
        $apiKey = config('services.gemini.key');
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => $parts,
                ]
            ],
            'generationConfig' => [
                'temperature' => auth()->user()->ia_temperature,
                'maxOutputTokens' => 8192,
            ]
        ];

        try {
            $aiResponse = Http::timeout(60)
                            ->withoutVerifying() // APENAS PARA TESTE LOCAL - REMOVER EM PRODU\u00c7\u00c3O
                            ->post($apiUrl, $payload)->json();

            $replyContent = $aiResponse['candidates'][0]['content']['parts'][0]['text']
                            ?? 'N\u00e3o foi poss\u00edvel obter uma resposta da IA. Tente novamente.';

            return response()->json(['reply' => $replyContent]);

        } catch (Exception $e) {
            \Log::error('Erro na API da IA (Chat): ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao comunicar com a IA: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tenta extrair texto legível do PDF via smalot/pdfparser.
     * Retorna null se o PDF for escaneado (sem texto) ou se o encoding
     * estiver corrompido (ex.: PDFs do MIRSpiro sem espaços entre palavras).
     */
    private function tryExtractPdfText(string $pdfPath): ?string
    {
        try {
            $parser = new Parser();
            $cleaned = preg_replace('/\s+/', ' ', trim($parser->parseFile($pdfPath)->getText()));

            if (empty($cleaned)) {
                return null;
            }

            $totalChars = strlen($cleaned);
            $spaceRatio = substr_count($cleaned, ' ') / $totalChars;

            // Texto com menos de 5% de espaços ou com "palavras" de 20+ letras indica
            // encoding proprietário corrompido (concatenação de palavras sem separadores).
            if ($spaceRatio < 0.05 || preg_match('/[a-zA-Z]{20,}/', $cleaned)) {
                return null;
            }

            return $cleaned;
        } catch (\Exception $e) {
            return null;
        }
    }
}
