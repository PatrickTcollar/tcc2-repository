@extends('layouts.app') {{-- Garante que esta view use seu layout principal --}}

@section('content')
    <div class="container mx-auto p-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6 text-center">Lista de Pacientes</h2>
        <p class="text-center text-lg text-gray-600 mb-10">
            Gerencie todos os pacientes cadastrados no sistema.
        </p>

        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('pacientes.create') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Novo Paciente
            </a>
            {{-- Espaço para futuros filtros ou pesquisas --}}
        </div>

        @if($pacientes->isEmpty())
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative text-center" role="alert">
                <p>Nenhum paciente cadastrado ainda.</p>
            </div>
        @else
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Nome
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Data de Nascimento
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Sexo
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Fumante
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pacientes as $paciente)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $paciente->id }}</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $paciente->name }}</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $paciente->birth_date ? \Carbon\Carbon::parse($paciente->birth_date)->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $paciente->gender == 'M' ? 'Masculino' : 'Feminino' }}</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <span class="{{ $paciente->smoker === 'sim' ? 'text-red-600 font-semibold' : ($paciente->smoker === 'ex_fumante' ? 'text-orange-600 font-semibold' : 'text-gray-600') }}">
                                        {{ $paciente->smoker === 'sim' ? 'Sim' : ($paciente->smoker === 'ex_fumante' ? 'Ex-fumante' : 'Não') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('pacientes.show', $paciente) }}"
                                           class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out">Ver</a>
                                        <span>|</span>
                                        <a href="{{ route('pacientes.edit', $paciente) }}"
                                           class="text-purple-600 hover:text-purple-900 transition duration-150 ease-in-out">Editar</a>
                                        <span>|</span>
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out"
                                                    onclick="return confirm('Tem certeza que deseja excluir este paciente? Esta ação é irreversível.');">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i> Voltar ao Painel
            </a>
        </div>
    </div>
@endsection
