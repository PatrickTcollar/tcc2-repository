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

        // 1. Extrair Texto do PDF do Exame (se ainda n\u00e3o tiver sido extra\u00eddo e armazenado)
        // Para um chat em tempo real, idealmente o texto j\u00e1 estaria pr\u00e9-processado/armazenado.
        // Mas para garantir o contexto, vamos extrair novamente o texto do PDF do exame atual.
        $pdfPath = Storage::disk('public')->path($exam->file_path);
        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'Arquivo PDF do exame n\u00e3o encontrado.'], 404);
        }
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $examText = preg_replace('/\s+/', ' ', trim($pdf->getText()));

        if (empty($examText)) {
            return response()->json(['error' => 'N\u00e3o foi poss\u00edvel extrair texto relevante do arquivo PDF.'], 400);
        }

        // 2. Construir o Prompt para a Intelig\u00eancia Artificial (Gemini)
        // O prompt agora inclui o texto do exame e a pergunta do usu\u00e1rio.
        $prompt = "Voc\u00ea \u00e9 um assistente especializado em fisioterapia respirat\u00f3ria. O exame de espirometria a seguir foi realizado pelo paciente " . ($exam->patient->name ?? 'desconhecido') . " (ID: " . $exam->patient->id . ").\n\nResultados do Exame:\n" . $examText . "\n\nCom base nestes resultados, por favor, responda \u00e0 seguinte pergunta: " . $userMessage;

        // 3. Fazer a Requisi\u00e7\u00e3o para a API do Gemini
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
                'temperature' => 0.7,
                'maxOutputTokens' => 1000,
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
}
