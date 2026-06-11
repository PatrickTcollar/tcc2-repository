<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['report', 'patient'])->orderBy('upload_date', 'desc')->get();
        return view('exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        abort(404);
    }

    public function destroy(Exam $exam)
    {
        abort_if($exam->patient->user_id !== Auth::id(), 403);

        Storage::disk('public')->delete($exam->file_path);
        $exam->delete();

        return redirect()->route('exames.index')->with('success', 'Exame excluído com sucesso.');
    }
}
