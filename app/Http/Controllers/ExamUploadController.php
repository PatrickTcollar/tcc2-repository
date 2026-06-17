<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Patient; // Importe o modelo Patient
use Illuminate\Support\Facades\Storage; // Para manipulação de arquivos
use Exception; // Para capturar exceções

class ExamUploadController extends Controller
{
    /**
     * Exibe o formulário de upload de exames.
     *
     * @return \Illuminate\View\View
     */
    public function showUploadForm()
    {
        $patients = Patient::where('user_id', auth()->id())->get();
        return view('exams.upload_form', compact('patients'));
    }

    /**
     * Lida com o upload do arquivo de exame.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleUpload(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'exam_file'  => 'required|file|mimes:pdf|max:10240', // PDF, max 10MB
        ]);

        try {
            $patient = Patient::where('user_id', auth()->id())->findOrFail($request->input('patient_id'));
            $file = $request->file('exam_file');

            // Gera um nome único para o arquivo
            $filename = time() . '_' . $file->getClientOriginalName();
            // Armazena o arquivo no disco 'public' dentro da pasta 'exams'
            $filePath = $file->storeAs('exams', $filename, 'public');

            // Cria um novo registro de exame no banco de dados
            $exam = Exam::create([
                'patient_id'        => $patient->id,
                'file_path'         => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'upload_date'       => now(),
            ]);

            // REDIRECIONAMENTO CORRIGIDO: Usando 'exames.index' em português
            return redirect()->route('exames.index')->with('success', 'Exame enviado com sucesso e associado ao paciente ' . $patient->name . '!');

        } catch (Exception $e) {
            \Log::error('Erro ao processar upload de exame: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao processar o upload: ' . $e->getMessage());
        }
    }
}
