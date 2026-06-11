@extends('layouts.app') {{-- Garante que esta view use seu layout principal --}}

@section('content')
    <div class="container mx-auto p-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6 text-center">Detalhes do Laudo</h2>

        @if ($report)
            <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
                <div class="text-lg text-gray-800 prose max-w-none mb-6">
                    {{-- Exibe o conteúdo do laudo, formatando quebras de linha --}}
                    {!! nl2br(e($report->report_content)) !!}
                </div>

                <div class="mt-8 text-sm text-gray-600 border-t pt-4">
                    <p><strong>Gerado em:</strong> {{ $report->generation_date->format('d/m/Y H:i') }}</p>
                    @if ($report->exam)
                        <p><strong>Exame Associado:</strong> <a href="{{ route('exames.show', $report->exam->id) }}" class="text-blue-600 hover:text-blue-800">#{{ $report->exam->id }} ({{ $report->exam->original_filename }})</a></p>
                        @if ($report->exam->patient)
                            <p><strong>Paciente:</strong> {{ $report->exam->patient->name }} (ID: {{ $report->exam->patient->id }})</p>
                        @endif
                    @elseif ($report->patient) {{-- Para laudos de evolução, que podem ter patient_id direto --}}
                        <p><strong>Paciente:</strong> {{ $report->patient->name }} (ID: {{ $report->patient->id }})</p>
                    @else
                        <p><strong>Exame/Paciente:</strong> Não associado diretamente</p>
                    @endif
                </div>
            </div>

            <div class="mt-10 text-center flex justify-center space-x-4">
                <button id="downloadLaudoBtn"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <i class="fas fa-download mr-2"></i> Baixar Laudo
                </button>

                <a href="{{ route('exames.index') }}"
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar para Lista de Exames
                </a>
            </div>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center" role="alert">
                <p>Laudo não encontrado.</p>
            </div>
        @endif
    </div>

    {{-- Script para o jsPDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        window.onload = function() {
            // Garante que o jsPDF esteja dispon\u00edvel no escopo global
            const { jsPDF } = window.jspdf;

            document.getElementById('downloadLaudoBtn').addEventListener('click', function() {
                const reportContent = document.querySelector('.prose').innerText; // Pega o texto da div do laudo
                const reportId = {{ $report->id }};
                const generationDate = "{{ $report->generation_date->format('d/m/Y H:i') }}";
                const patientName = "{{ $report->patient->name ?? 'N/A' }}";
                const examId = "{{ $report->exam->id ?? 'N/A' }}";

                const doc = new jsPDF();
                let yPos = 10;
                const margin = 10;
                const maxWidth = doc.internal.pageSize.width - 2 * margin;

                doc.setFontSize(18);
                doc.text('Laudo Automatizado - Fisioterapia Respiratória', margin, yPos);
                yPos += 10;

                doc.setFontSize(12);
                doc.text(`ID do Laudo: ${reportId}`, margin, yPos); yPos += 7;
                doc.text(`Paciente: ${patientName}`, margin, yPos); yPos += 7;
                doc.text(`Exame ID: ${examId}`, margin, yPos); yPos += 7;
                doc.text(`Data de Geração: ${generationDate}`, margin, yPos); yPos += 15;

                doc.setFontSize(11);
                const textLines = doc.splitTextToSize(reportContent, maxWidth);
                doc.text(textLines, margin, yPos);

                doc.save(`laudo_${reportId}_${patientName.replace(/\s+/g, '_')}.pdf`);
            });
        };
    </script>
@endsection
