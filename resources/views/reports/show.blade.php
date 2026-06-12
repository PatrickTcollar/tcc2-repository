@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 sm:px-6 lg:px-8">
    <h2 class="text-4xl font-extrabold text-gray-800 mb-6 text-center">Detalhes do Laudo</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if ($report)
        <div id="laudo-content" class="bg-white shadow-lg rounded-lg p-8 mb-8">
            <div class="text-lg text-gray-800 prose max-w-none mb-6">
                {!! nl2br(e($report->report_content)) !!}
            </div>

            <div class="mt-8 text-sm text-gray-600 border-t pt-4">
                <p><strong>Gerado em:</strong> {{ $report->generation_date->format('d/m/Y H:i') }}</p>
                @if ($report->exam)
                    <p><strong>Exame Associado:</strong>
                        <a href="{{ route('exames.show', $report->exam->id) }}" class="text-blue-600 hover:text-blue-800">
                            #{{ $report->exam->id }} ({{ $report->exam->original_filename }})
                        </a>
                    </p>
                    @if ($report->exam->patient)
                        <p><strong>Paciente:</strong> {{ $report->exam->patient->name }} (ID: {{ $report->exam->patient->id }})</p>
                    @endif
                @elseif ($report->patient)
                    <p><strong>Paciente:</strong> {{ $report->patient->name }} (ID: {{ $report->patient->id }})</p>
                @endif
            </div>

            {{-- Bloco de assinatura (exibido após assinar) --}}
            @if($report->signed_at)
            <div id="signature-block" class="mt-8 border-2 border-green-600 rounded-lg p-6 bg-green-50">
                <div class="flex items-start justify-between flex-wrap gap-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Assinado digitalmente por</p>
                        <p class="text-lg font-bold text-gray-800">{{ $report->signed_by }}</p>
                        <p class="text-sm text-gray-600">CRF: {{ $report->signer_crf }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                            Assinado em {{ $report->signed_at->format('d/m/Y \à\s H:i') }}
                        </p>
                    </div>
                    <div class="border border-gray-300 rounded bg-white p-2">
                        <img src="{{ $report->signature_image }}" alt="Assinatura" class="h-20 object-contain">
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-6 flex flex-wrap justify-center gap-4">
            @if(!$report->signed_at)
            <button onclick="document.getElementById('modal-assinatura').classList.remove('hidden')"
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-base font-medium rounded-md shadow transition">
                <i class="fas fa-signature mr-2"></i> Validar e Assinar
            </button>
            @endif

            <button id="downloadLaudoBtn"
                    class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-base font-medium rounded-md shadow transition">
                <i class="fas fa-download mr-2"></i> Baixar Laudo
            </button>

            <a href="{{ route('exames.index') }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-base font-medium rounded-md shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Voltar para Lista de Exames
            </a>
        </div>
    @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-center">
            <p>Laudo não encontrado.</p>
        </div>
    @endif
</div>

{{-- Modal de assinatura --}}
@if($report && !$report->signed_at)
<div id="modal-assinatura" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-800">Validar e Assinar Laudo</h3>
            <p class="text-sm text-gray-500 mt-1">Preencha seus dados e assine no campo abaixo.</p>
        </div>

        <form id="form-assinatura" action="{{ route('laudos.sign', $report->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="signature_image" id="signature_image_input">

            <div>
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Nome completo do profissional <span style="color:#ef4444;">*</span></label>
                <input type="text" name="signed_by" required placeholder="Ex: Dr. João da Silva"
                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;outline:none;box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">CRF / Registro profissional <span style="color:#ef4444;">*</span></label>
                <input type="text" name="signer_crf" required placeholder="Ex: CREFITO-3 123456"
                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#111827;outline:none;box-sizing:border-box;">
            </div>

            <div>
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Assinatura <span style="color:#ef4444;">*</span></label>
                <canvas id="signature-canvas"
                        style="display:block;width:100%;height:150px;border:2px dashed #d1d5db;border-radius:8px;background:#f9fafb;cursor:crosshair;touch-action:none;"></canvas>
                <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
                    <button type="button" onclick="clearSignature()"
                            style="font-size:12px;color:#dc2626;border:1px solid #fca5a5;border-radius:6px;padding:4px 10px;background:white;cursor:pointer;">
                        <i class="fas fa-eraser" style="margin-right:4px;"></i> Limpar
                    </button>
                    <span style="font-size:12px;color:#9ca3af;">Assine com o mouse ou toque na tela</span>
                </div>
            </div>

            <div style="display:flex;gap:12px;padding-top:8px;">
                <button type="button" onclick="document.getElementById('modal-assinatura').classList.add('hidden')"
                        style="flex:1;padding:10px;border:1px solid #d1d5db;color:#374151;background:white;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancelar
                </button>
                <button type="button" onclick="submitSignature()"
                        style="flex:1;padding:10px;background:#059669;color:white;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;">
                    <i class="fas fa-check" style="margin-right:4px;"></i> Confirmar Assinatura
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad;

window.onload = function () {
    const canvas = document.getElementById('signature-canvas');
    if (canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = 150 * ratio;
        canvas.style.width = rect.width + 'px';
        canvas.style.height = '150px';
        canvas.getContext('2d').scale(ratio, ratio);

        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(249,250,251)',
            penColor: 'rgb(17,24,39)',
            minWidth: 1,
            maxWidth: 2.5,
        });
    }

    // Download PDF
    const { jsPDF } = window.jspdf;
    document.getElementById('downloadLaudoBtn').addEventListener('click', function () {
        const reportContent = document.querySelector('.prose').innerText;
        const reportId = {{ $report->id }};
        const generationDate = "{{ $report->generation_date->format('d/m/Y H:i') }}";
        const patientName = "{{ addslashes($report->patient->name ?? $report->exam->patient->name ?? 'N/A') }}";

        const doc = new jsPDF();
        let yPos = 15;
        const margin = 15;
        const maxWidth = doc.internal.pageSize.width - 2 * margin;

        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.text('Laudo — Fisioterapia Respiratória', margin, yPos); yPos += 10;

        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        doc.text(`ID do Laudo: ${reportId}  |  Paciente: ${patientName}  |  Emitido em: ${generationDate}`, margin, yPos); yPos += 12;

        doc.setLineWidth(0.3);
        doc.line(margin, yPos, doc.internal.pageSize.width - margin, yPos); yPos += 8;

        doc.setFontSize(11);
        const lines = doc.splitTextToSize(reportContent, maxWidth);
        lines.forEach(line => {
            if (yPos > doc.internal.pageSize.height - 30) { doc.addPage(); yPos = 15; }
            doc.text(line, margin, yPos); yPos += 6;
        });

        @if($report->signed_at)
        yPos += 10;
        if (yPos > doc.internal.pageSize.height - 60) { doc.addPage(); yPos = 15; }
        doc.setLineWidth(0.3);
        doc.line(margin, yPos, doc.internal.pageSize.width - margin, yPos); yPos += 8;
        doc.setFontSize(10);
        doc.setFont(undefined, 'bold');
        doc.text('Assinatura Digital', margin, yPos); yPos += 6;
        doc.setFont(undefined, 'normal');
        doc.text('Assinado por: {{ addslashes($report->signed_by) }}', margin, yPos); yPos += 6;
        doc.text('CRF: {{ addslashes($report->signer_crf) }}', margin, yPos); yPos += 6;
        doc.text('Data/Hora: {{ $report->signed_at->format("d/m/Y H:i") }}', margin, yPos); yPos += 8;
        const sigImg = '{{ $report->signature_image }}';
        if (sigImg) {
            doc.addImage(sigImg, 'PNG', margin, yPos, 60, 20);
        }
        @endif

        doc.save(`laudo_${reportId}_${patientName.replace(/\s+/g, '_')}.pdf`);
    });
};

function clearSignature() {
    if (signaturePad) signaturePad.clear();
}

function submitSignature() {
    if (!signaturePad || signaturePad.isEmpty()) {
        alert('Por favor, realize a assinatura antes de confirmar.');
        return;
    }
    document.getElementById('signature_image_input').value = signaturePad.toDataURL('image/png');
    document.getElementById('form-assinatura').submit();
}
</script>
@endsection
