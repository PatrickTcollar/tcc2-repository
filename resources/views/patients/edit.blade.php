@extends('layouts.app') {{-- Garante que esta view use seu layout principal --}}

@section('content')
    <div class="container mx-auto p-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6 text-center">Editar Paciente</h2>
        <p class="text-center text-lg text-gray-600 mb-10">
            Atualize as informações do paciente.
        </p>

        <div class="bg-white shadow-lg rounded-lg p-8 max-w-xl mx-auto">
            <form action="{{ route('pacientes.update', $paciente->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Método HTTP para atualização --}}

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nome do paciente:</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $paciente->name) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="birth_date" class="block text-gray-700 text-sm font-bold mb-2">Data de nascimento:</label>
                    <input type="datetime-local" id="birth_date" name="birth_date" value="{{ old('birth_date', $paciente->birth_date ? $paciente->birth_date->format('Y-m-d\TH:i') : '') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('birth_date')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">Sexo:</label>
                    <select name="gender" id="gender"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Selecione</option>
                        <option value="M" {{ old('gender', $paciente->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('gender', $paciente->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fumante:</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="smoker" value="1"
                                   {{ old('smoker', $paciente->smoker ? '1' : '0') == '1' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600">
                            <span class="text-gray-700">Sim</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="smoker" value="0"
                                   {{ old('smoker', $paciente->smoker ? '1' : '0') == '0' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600">
                            <span class="text-gray-700">Não</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-2"></i> Atualizar Paciente
                    </button>
                    <a href="{{ route('pacientes.index') }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-indigo-600 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
