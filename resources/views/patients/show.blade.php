@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <!-- Verifica se o objeto $paciente existe e não é nulo -->
    @if ($paciente)
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">
            Detalhes do Paciente: {{ $paciente->name }}
        </h2>

        <div class="bg-white shadow-md rounded-lg p-6 mb-4">
            <div class="mb-4">
                <strong class="block text-gray-600 text-sm font-bold mb-2">ID:</strong>
                <p class="text-gray-900">{{ $paciente->id }}</p>
            </div>
            <div class="mb-4">
                <strong class="block text-gray-600 text-sm font-bold mb-2">Data de Nascimento:</strong>
                <p class="text-gray-900">{{ \Carbon\Carbon::parse($paciente->birth_date)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-4">
                <strong class="block text-gray-600 text-sm font-bold mb-2">Gênero:</strong>
                <p class="text-gray-900">{{ $paciente->gender == 'M' ? 'Masculino' : 'Feminino' }}</p>
            </div>
            <div class="mb-4">
                <strong class="block text-gray-600 text-sm font-bold mb-2">Fumante:</strong>
                <p class="text-gray-900">{{ $paciente->smoker === 'sim' ? 'Sim' : ($paciente->smoker === 'ex_fumante' ? 'Ex-fumante' : 'Não') }}</p>
            </div>
            <div class="mb-4">
                <strong class="block text-gray-600 text-sm font-bold mb-2">Criado por (User ID):</strong>
                <p class="text-gray-900">{{ $paciente->user_id }}</p>
            </div>
            <!-- Adicione mais detalhes conforme necessário -->
        </div>

        <div class="mt-6">
            <!-- Rota corrigida de 'patients.edit' para 'pacientes.edit' -->
            <a href="{{ route('pacientes.edit', $paciente) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                Editar Paciente
            </a>
            <a href="{{ route('pacientes.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 transition duration-300">
                Voltar à Lista
            </a>
        </div>

    @else
        <h2 class="text-3xl font-semibold text-red-500">Paciente Não Encontrado</h2>
    @endif
</div>
@endsection
