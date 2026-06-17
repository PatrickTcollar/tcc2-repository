@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 sm:px-6 lg:px-8 max-w-2xl">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-800">Preferências de IA</h2>
        <p class="text-gray-600 mt-2">Configure como a inteligência artificial deve responder e gerar seus laudos.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Erro ao salvar:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-8">
        <form action="{{ route('ia-preferences.update') }}" method="POST">
            @csrf

            <div class="mb-8">
                <label class="block text-lg font-semibold text-gray-800 mb-4">Nível de detalhamento</label>
                <p class="text-sm text-gray-600 mb-6">Escolha como você prefere que a IA estruture suas respostas e laudos:</p>

                <div class="space-y-4">
                    {{-- Objetivo --}}
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition" onchange="updateStyles()"
                           style="border-color: {{ (float)$user->ia_temperature === 0.3 ? '#3b82f6' : '#e5e7eb' }}; background-color: {{ (float)$user->ia_temperature === 0.3 ? '#eff6ff' : 'white' }};">
                        <input type="radio" name="ia_temperature" value="0.3" {{ (float)$user->ia_temperature === 0.3 ? 'checked' : '' }}
                               style="margin-top: 2px; margin-right: 12px; width: 20px; height: 20px; cursor: pointer;">
                        <div>
                            <p style="font-weight: 600; color: #111827; font-size: 15px;">Objetivo</p>
                            <p style="color: #6b7280; font-size: 13px; margin-top: 4px;">
                                Respostas diretas e concisas. Foco em fatos e resultados essenciais. Ideal para laudos técnicos e rápidos.
                            </p>
                        </div>
                    </label>

                    {{-- Balanceado --}}
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition"
                           style="border-color: {{ (float)$user->ia_temperature === 0.5 ? '#3b82f6' : '#e5e7eb' }}; background-color: {{ (float)$user->ia_temperature === 0.5 ? '#eff6ff' : 'white' }};">
                        <input type="radio" name="ia_temperature" value="0.5" {{ (float)$user->ia_temperature === 0.5 ? 'checked' : '' }}
                               style="margin-top: 2px; margin-right: 12px; width: 20px; height: 20px; cursor: pointer;">
                        <div>
                            <p style="font-weight: 600; color: #111827; font-size: 15px;">Balanceado (Recomendado)</p>
                            <p style="color: #6b7280; font-size: 13px; margin-top: 4px;">
                                Equilíbrio entre clareza e contexto. Explicações moderadas com informações relevantes. Melhor para maioria dos casos.
                            </p>
                        </div>
                    </label>

                    {{-- Detalhado --}}
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition"
                           style="border-color: {{ (float)$user->ia_temperature === 0.8 ? '#3b82f6' : '#e5e7eb' }}; background-color: {{ (float)$user->ia_temperature === 0.8 ? '#eff6ff' : 'white' }};">
                        <input type="radio" name="ia_temperature" value="0.8" {{ (float)$user->ia_temperature === 0.8 ? 'checked' : '' }}
                               style="margin-top: 2px; margin-right: 12px; width: 20px; height: 20px; cursor: pointer;">
                        <div>
                            <p style="font-weight: 600; color: #111827; font-size: 15px;">Detalhado</p>
                            <p style="color: #6b7280; font-size: 13px; margin-top: 4px;">
                                Respostas mais completas e contextualizadas. Explicações profundas com exemplos. Ideal para aprendizado e análise profunda.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 24px;">
                <button type="submit" style="display: inline-flex; align-items: center; padding: 10px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Salvar Preferências
                </button>
                <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; padding: 10px 24px; margin-left: 12px; background: #6b7280; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Voltar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateStyles() {
    const value = document.querySelector('input[name="ia_temperature"]:checked').value;
    const labels = document.querySelectorAll('label[style*="border-color"]');
    labels.forEach(label => {
        if (label.querySelector('input').value === value) {
            label.style.borderColor = '#3b82f6';
            label.style.backgroundColor = '#eff6ff';
        } else {
            label.style.borderColor = '#e5e7eb';
            label.style.backgroundColor = 'white';
        }
    });
}

document.querySelectorAll('input[name="ia_temperature"]').forEach(radio => {
    radio.addEventListener('change', updateStyles);
});
</script>
@endsection
