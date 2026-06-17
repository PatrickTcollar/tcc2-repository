<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient; // Para selecionar o paciente no formulário
use App\Models\Exam;    // Para salvar o novo exame
use Illuminate\Support\Facades\Storage; // Para manipula\u00e7\u00e3o de arquivos
use Exception; // Para capturar exce\u00e7\u00f5es

class ChatModuleController extends Controller
{
    /**
     * Exibe o formul\u00e1rio para upload de um exame e in\u00edcio de chat.
     * Mapeado para GET /chat/upload
     *
     * @return \Illuminate\View\View
     */
    public function showUploadFormForChat()
    {
        $patients = Patient::where('user_id', auth()->id())->get();
        return view('chat.upload', compact('patients'));
    }

    /**
     * Lida com o upload do arquivo de exame e redireciona para a interface de chat.
     * Mapeado para POST /chat/upload
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleUploadAndRedirectToChat(Request $request)
    {
        // 1. Valida\u00e7\u00e3o dos dados do formul\u00e1rio
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'exam_file'  => 'required|file|mimes:pdf|max:10240', // PDF, m\u00e1x 10MB
        ]);

        try {
            $patient = Patient::where('user_id', auth()->id())->findOrFail($request->input('patient_id'));
            $file = $request->file('exam_file');

            // 2. Armazenar o arquivo PDF
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('exams', $filename, 'public');

            // 3. Criar o registro do exame no banco de dados
            $exam = Exam::create([
                'patient_id'        => $patient->id,
                'file_path'         => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'upload_date'       => now(),
            ]);

            // 4. Redirecionar para a interface de chat do exame rec\u00e9m-carregado
            return redirect()->route('exames.chat', $exam->id)->with('success', 'Exame enviado e chat pronto para usar!');

        } catch (Exception $e) {
            \Log::error('Erro no m\u00f3dulo de chat (upload): ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao processar seu exame para o chat: ' . $e->getMessage());
        }
    }
}
