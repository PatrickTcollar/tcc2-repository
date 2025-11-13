@extends('layouts.app') {{-- Garante que esta view usa seu layout principal --}}

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-5xl font-extrabold text-center text-gray-900 mb-6 leading-tight animate-fade-in-down">
                Seja bem-vindo!
            </h1>
            <p class="text-xl text-center text-gray-600 mb-12 animate-fade-in">
                Seu Assistente Fisioterapeuta Inteligente.
            </p>

            <!--
            MUDANÇA CRUCIAL:
            - Usamos 'flex justify-center' para centralizar horizontalmente o bloco de cards.
            - O 'gap-6' já define o espaçamento entre os cards.
            - A grade agora é fixada em 'md:grid-cols-3' para ter no máximo 3 cards por linha.
            - Adicionei 'mb-10' para dar o espaçamento entre as duas linhas de cards.
            -->

            <!-- PRIMEIRA LINHA (Cards 1, 2, 3) - Centralizado e com espaçamento inferior (mb-10) -->
            <div class="flex justify-center mb-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl">
                    <!-- Cartão/Botão: Gerenciar Pacientes -->
                    <a href="{{ route('pacientes.index') }}"
                       class="dashboard-card group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 p-6 flex flex-col items-center text-center border border-gray-200 w-full">
                        <div class="icon-circle bg-blue-100 text-blue-600 mb-4 p-4 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-user-injured text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2 group-hover:text-blue-700 transition-colors duration-300">Gerenciar Pacientes</h3>
                        <p class="text-gray-600 text-sm group-hover:text-gray-700">Adicione, edite e visualize os registros dos seus pacientes.</p>
                    </a>

                    <!-- Cartão/Botão: Upload de Exames -->
                    <a href="{{ route('exams.upload.form') }}"
                       class="dashboard-card group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 p-6 flex flex-col items-center text-center border border-gray-200 w-full">
                        <div class="icon-circle bg-green-100 text-green-600 mb-4 p-4 rounded-full group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-cloud-upload-alt text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2 group-hover:text-green-700 transition-colors duration-300">Upload de Exames</h3>
                        <p class="text-gray-600 text-sm group-hover:text-gray-700">Envie novos exames de espirometria para análise.</p>
                    </a>

                    <!-- Cartão/Botão: Lista de Exames -->
                    <a href="{{ route('exames.index') }}"
                       class="dashboard-card group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 p-6 flex flex-col items-center text-center border border-gray-200 w-full">
                        <div class="icon-circle bg-yellow-100 text-yellow-600 mb-4 p-4 rounded-full group-hover:bg-yellow-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-list-alt text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2 group-hover:text-yellow-700 transition-colors duration-300">Lista de Exames</h3>
                        <p class="text-gray-600 text-sm group-hover:text-gray-700">Navegue por todos os exames já carregados no sistema.</p>
                    </a>
                </div>
            </div> {{-- Fim da PRIMEIRA LINHA --}}

            <!-- SEGUNDA LINHA (Cards 4, 5, 6) - Centralizado -->
            <div class="flex justify-center">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl">
                    <!-- Cartão/Botão: Acessar Laudos -->
                    <a href="{{ route('laudos.index') }}"
                       class="dashboard-card group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 p-6 flex flex-col items-center text-center border border-gray-200 w-full">
                        <div class="icon-circle bg-purple-100 text-purple-600 mb-4 p-4 rounded-full group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-file-medical-alt text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2 group-hover:text-purple-700 transition-colors duration-300">Acessar Laudos</h3>
                        <p class="text-gray-600 text-sm group-hover:text-gray-700">Visualize e revise os laudos gerados pela IA.</p>
                    </a>

                    {{-- Cartão/Botão: Evolução do Paciente --}}
                    <a href="{{ route('evolucao.index') }}"
                       class="dashboard-card group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 p-6 flex flex-col items-center text-center border border-gray-200 w-full">
                        <div class="icon-circle bg-teal-100 text-teal-600 mb-4 p-4 rounded-full group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-chart-line text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2 group-hover:text-teal-700 transition-colors duration-300">Evolução do Paciente</h3>
                        <p class="text-gray-600 text-sm group-hover:text-gray-700">Analise o histórico de exames e a evolução ao longo do tempo.</p>
                    </a>

                    {{-- Cartão/Botão: Iniciar Chat sobre Exame --}}
                    <a href="{{ route('chat.upload.form') }}"
                       class="dashboard-card group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 p-6 flex flex-col items-center text-center border border-gray-200 w-full">
                        <div class="icon-circle bg-pink-100 text-pink-600 mb-4 p-4 rounded-full group-hover:bg-pink-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-comments text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2 group-hover:text-pink-700 transition-colors duration-300">Chat IA de Exames</h3>
                        <p class="text-gray-600 text-sm group-hover:text-gray-700">Faça upload de um exame e converse com a IA em tempo real.</p>
                    </a>
                </div>
            </div> {{-- Fim da SEGUNDA LINHA --}}

        </div>
    </div>

    <style>
        /* Animações (mantidas do seu código original) */
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.8s ease-out forwards;
        }

        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in {
            animation: fade-in 1s ease-out forwards;
            animation-delay: 0.3s;
        }

        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-scale-in {
            animation: scale-in 0.7s ease-out forwards;
            animation-delay: 0.6s;
        }
    </style>
@endsection
