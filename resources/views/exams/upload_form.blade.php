@extends('layouts.app') {{-- Assume que você tem um layout base 'layouts.app' --}}

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Upload de Exame de Espirometria</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sucesso!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Validação falhou:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- APAGUE E DIGITE ESTA LINHA DO ZERO OU COPIE EXATAMENTE ESTE CÓDIGO --}}
    <form action="{{ route('exams.upload.handle') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6 mb-6">
        @csrf

        <div class="mb-4">
            <label for="patient_id" class="block text-gray-700 text-sm font-bold mb-2">Selecione o Paciente:</label>
            <select name="patient_id" id="patient_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">-- Selecione um Paciente --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }} (ID: {{ $patient->id }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="exam_file" class="block text-gray-700 text-sm font-bold mb-2">Arquivo do Exame (PDF):</label>
            <input type="file" name="exam_file" id="exam_file" accept="application/pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
            <p class="mt-1 text-sm text-gray-500">Apenas arquivos PDF são permitidos (máx. 10MB).</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-md">
                Realizar Upload
            </button>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out shadow-md">
                <i class="fas fa-arrow-left mr-1"></i> Voltar ao Painel
            </a>
        </div>
    </form>
</div>
@endsection
