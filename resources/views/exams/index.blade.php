@extends('layouts.app') {{-- Garante que esta vista usa seu layout principal --}}

@section('content')
    <div class="container mx-auto p-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6 text-center">Lista de Exames</h2>
        <p class="text-center text-lg text-gray-600 mb-10">
            Gerencie e visualize todos os exames carregados no sistema.
        </p>

        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('exams.upload.form') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Novo Exame
            </a>
            {{-- Espaço para futuros filtros ou pesquisas --}}
        </div>

        @if($exams->isEmpty())
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative text-center" role="alert">
                <p>Nenhum exame cadastrado ainda.</p>
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
                                Paciente
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Arquivo Original
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Data de Upload
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exams as $exam)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $exam->id }}</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $exam->patient->name ?? 'Paciente n\u00e3o encontrado' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $exam->original_filename }}</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $exam->upload_date->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex items-center space-x-3">
                                        @if ($exam->report)
                                            <a href="{{ route('reports.show', $exam->report->id) }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                                Ver Laudo
                                            </a>
                                        @else
                                            <form action="{{ route('exams.generate_report', $exam->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                                    Gerar Laudo IA
                                                </button>
                                            </form>
                                        @endif
                                        {{-- Bot\u00e3o: Conversar com IA --}}
                                        <a href="{{ route('exames.chat', $exam->id) }}"
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 shadow-md ml-2">
                                            <i class="fas fa-comments mr-2"></i> Chat IA
                                        </a>
                                        <form action="{{ route('exames.destroy', $exam->id) }}" method="POST" class="inline-block ml-2"
                                              onsubmit="return confirm('Excluir este exame? O arquivo PDF e o laudo associado serão removidos permanentemente.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                                <i class="fas fa-trash mr-1"></i> Excluir
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
