@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 sm:px-6 lg:px-8 max-w-2xl">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-extrabold text-gray-800">Análise de Evolução</h2>
            <p class="text-gray-600 mt-2">Selecione um paciente para gerar um laudo comparativo baseado nos laudos individuais já gerados.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow-lg rounded-lg p-8">
            @if($patients->isEmpty())
                <div style="text-align:center; padding: 32px 0;">
                    <i class="fas fa-file-medical" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                    <p style="color: #6b7280; font-size: 15px;">Nenhum paciente possui laudos individuais gerados ainda.</p>
                    <p style="color: #9ca3af; font-size: 13px; margin-top: 8px;">Faça o upload de um exame e gere o laudo individual primeiro.</p>
                    <a href="{{ route('exams.upload.form') }}" style="display:inline-block; margin-top: 20px; padding: 10px 24px; background:#4f46e5; color:white; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
                        <i class="fas fa-upload" style="margin-right:6px;"></i> Fazer Upload de Exame
                    </a>
                </div>
            @else
                <form action="{{ route('evolucao.analyze') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="patient_id" style="display:block; font-weight:600; color:#374151; margin-bottom:8px; font-size:14px;">
                            Selecione o Paciente:
                        </label>
                        <select name="patient_id" id="patient_id" required
                                style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; color:#374151; background:white;">
                            <option value="">-- Selecione um Paciente --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }} — {{ $patient->reports->count() }} laudo(s)
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p style="color:#ef4444; font-size:12px; margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:12px 16px; margin-bottom:24px; font-size:13px; color:#1e40af;">
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i>
                        A IA irá comparar os laudos individuais do paciente selecionado e identificar a progressão clínica ao longo do tempo.
                    </div>

                    <div style="display:flex; gap:12px;">
                        <button type="submit"
                                style="display:inline-flex; align-items:center; padding:10px 24px; background:#4f46e5; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                            <i class="fas fa-magic" style="margin-right:8px;"></i> Gerar Laudo de Evolução
                        </button>
                        <a href="{{ route('dashboard') }}"
                           style="display:inline-flex; align-items:center; padding:10px 24px; background:#f3f4f6; color:#374151; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none;">
                            <i class="fas fa-arrow-left" style="margin-right:8px;"></i> Voltar
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
