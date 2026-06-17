@extends('layouts.app') {{-- Garante que esta view use seu layout principal --}}

@section('content')
    <div class="container mx-auto p-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6 text-center">Cadastrar Novo Paciente</h2>
        <p class="text-center text-lg text-gray-600 mb-10">
            Preencha os dados abaixo para registrar um novo paciente no sistema.
        </p>

        <div class="bg-white shadow-lg rounded-lg p-8 max-w-xl mx-auto">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sucesso!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
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

            <form action="{{ route('pacientes.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nome do paciente:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="birth_date" class="block text-gray-700 text-sm font-bold mb-2">Data de nascimento:</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    @error('birth_date')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">Sexo:</label>
                    <select name="gender" id="gender" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        <option value="">-- Selecione --</option>
                        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="flex-1">
                        <label for="weight" class="block text-gray-700 text-sm font-bold mb-2">Peso (kg):</label>
                        <input type="number" id="weight" name="weight" value="{{ old('weight') }}" step="0.1" min="1" max="300"
                               placeholder="Ex: 70.5"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        @error('weight')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1">
                        <label for="height" class="block text-gray-700 text-sm font-bold mb-2">Altura (cm):</label>
                        <input type="number" id="height" name="height" value="{{ old('height') }}" step="0.1" min="50" max="250"
                               placeholder="Ex: 170"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        @error('height')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tabagismo:</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="smoker" value="sim" {{ old('smoker') == 'sim' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600">
                            <span class="text-gray-700">Fumante</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="smoker" value="nao" {{ old('smoker', 'nao') == 'nao' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600">
                            <span class="text-gray-700">Não fumante</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="smoker" value="ex_fumante" {{ old('smoker') == 'ex_fumante' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600">
                            <span class="text-gray-700">Ex-fumante</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-2"></i> Salvar Paciente
                    </button>
                    <a href="{{ route('pacientes.index') }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-indigo-600 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
