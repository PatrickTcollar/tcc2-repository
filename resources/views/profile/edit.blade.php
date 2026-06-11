@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Meu Perfil</h1>

    {{-- Atualizar Nome e E-mail --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informações Pessoais</h2>

        @if(session('status') === 'profile-updated')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                Dados atualizados com sucesso.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition duration-150">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    {{-- Alterar Senha --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Alterar Senha</h2>

        @if(session('status') === 'password-updated')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                Senha atualizada com sucesso.
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Senha Atual</label>
                <input type="password" id="current_password" name="current_password"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                @error('current_password', 'updatePassword')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                <input type="password" id="password" name="password"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                @error('password', 'updatePassword')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition duration-150">
                    Atualizar Senha
                </button>
            </div>
        </form>
    </div>

    <div class="text-center">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-5 py-2 text-sm font-medium text-indigo-600 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Voltar ao Painel
        </a>
    </div>
</div>
@endsection
