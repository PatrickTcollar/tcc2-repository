<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Laudos - Fisioterapia Respiratória</title>

    {{-- Link para Font Awesome para ícones --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" xintegrity="sha512-1ycn6IcaQQ40jHzl+tWTfLyhE6ZkP2D2zG1tbbqUmyD/F8rS9zN5T5h5e5L5S5x5S5p5O5e5L5S5x5S5S5p5O5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Scripts do Vite para Tailwind CSS e JavaScript da aplicação --}}
    @vite(['resources/css/app.css'])
</head>
<body>
    {{-- A navegação superior ou lateral, se você tiver uma --}}
    {{-- CORREÇÃO AQUI: navigation.blade.php está dentro da pasta 'layouts', não 'partials' --}}
    @include('layouts.navigation')

    <main class="py-4"> {{-- Container principal para o conteúdo da página --}}
        @if (session('success'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Sucesso!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 my-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Erro!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content') {{-- Aqui o conteúdo das views será injetado --}}
    </main>
</body>
</html>
