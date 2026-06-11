@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    @if(!$clinic)
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 p-4 rounded mb-6">
            Você ainda não está vinculado a nenhuma clínica.
            <a href="{{ route('clinics.create') }}" class="underline font-semibold">Criar uma clínica</a>.
        </div>
    @else
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $clinic->name }}</h1>
            <a href="{{ route('clinics.edit', $clinic) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Editar</a>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">CNPJ</p>
                <p class="text-gray-800">{{ $clinic->cnpj ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">E-mail</p>
                <p class="text-gray-800">{{ $clinic->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Telefone</p>
                <p class="text-gray-800">{{ $clinic->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Endereço</p>
                <p class="text-gray-800">{{ $clinic->address ?? '—' }}</p>
            </div>
        </div>

        <h2 class="text-lg font-semibold text-gray-700 mb-3">Profissionais vinculados</h2>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">E-mail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($clinic->users as $professional)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $professional->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $professional->email }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhum profissional vinculado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
