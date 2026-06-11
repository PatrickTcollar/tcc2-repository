@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Clínica</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('clinics.update', $clinic) }}">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nome da clínica *</label>
                <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input type="text" name="cnpj" value="{{ old('cnpj', $clinic->cnpj) }}" placeholder="00.000.000/0000-00"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('cnpj')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $clinic->email) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone', $clinic->phone) }}" placeholder="(00) 00000-0000"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Endereço</label>
                <input type="text" name="address" value="{{ old('address', $clinic->address) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('clinics.show', $clinic) }}" class="text-gray-600 hover:text-gray-800 mr-4 text-sm self-center">Cancelar</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded text-sm font-medium">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
