<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExameLaudoController;
use App\Http\Controllers\ExamUploadController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\EvolutionController;
use App\Http\Controllers\ExamChatController;
use App\Http\Controllers\ChatModuleController;
use App\Http\Controllers\ClinicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rotas de perfil de usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas para Pacientes
    Route::resource('pacientes', PatientController::class);

    // Rotas para Upload de Exames
    Route::get('/exames/upload', [ExamUploadController::class, 'showUploadForm'])->name('exams.upload.form');
    Route::post('/exames/upload', [ExamUploadController::class, 'handleUpload'])->name('exams.upload.handle');

    // Rotas para Exames (listagem e visualização)
    Route::get('/exames', [ExamController::class, 'index'])->name('exames.index');
    Route::get('/exames/{exam}', [ExamController::class, 'show'])->name('exames.show');
    Route::delete('/exames/{exam}', [ExamController::class, 'destroy'])->name('exames.destroy');

    // Rota para Gerar Laudo Automatizado
    Route::post('/exames/{exam}/gerar-laudo', [ExameLaudoController::class, 'gerarLaudoAutomotizado'])->name('exams.generate_report');

    // Rotas para Laudos
    Route::get('/laudos', [ExameLaudoController::class, 'index'])->name('laudos.index');
    Route::get('/laudos/{report}', [ExameLaudoController::class, 'showReport'])->name('reports.show');
    Route::delete('/laudos/{report}', [ExameLaudoController::class, 'destroy'])->name('laudos.destroy');
    Route::post('/laudos/{report}/assinar', [ExameLaudoController::class, 'sign'])->name('laudos.sign');

    // Rotas para Evolução do Paciente
    // CORRIGIDO: Rota GET para exibir o formulário de seleção (index)
    Route::get('/evolucao', [EvolutionController::class, 'index'])->name('evolucao.index');
    // Rota POST para processar a análise
    Route::post('/evolucao/analisar', [EvolutionController::class, 'analyzeEvolution'])->name('evolucao.analyze');

    // Rota para a interface de chat do exame (destino do redirecionamento)
    Route::get('/exames/{exam}/chat', [ExamChatController::class, 'showChatInterface'])->name('exames.chat');

    // Rotas para Clínica
    Route::resource('clinics', ClinicController::class);

    // Rotas para o Módulo de Chat
    Route::get('/chat/upload', [ChatModuleController::class, 'showUploadFormForChat'])->name('chat.upload.form');
    Route::post('/chat/upload', [ChatModuleController::class, 'handleUploadAndRedirectToChat'])->name('chat.upload.handle');

    // Rota de API para chat (AJAX do React) — protegida por auth
    Route::post('/api/exames/{exam}/chat', [ExamChatController::class, 'handleChatMessage'])->name('api.exames.chat');
});

require __DIR__.'/auth.php';
