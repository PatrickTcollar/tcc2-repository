<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nome -->
        <div>
            <x-input-label for="name" value="Nome completo" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Senha -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar senha" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Clínica -->
        <div class="mt-6 border-t pt-4">
            <p class="text-sm font-semibold text-gray-700 mb-3">Clínica</p>

            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="clinic_option" value="existing" id="opt_existing"
                           {{ old('clinic_option', $clinics->isEmpty() ? 'new' : 'existing') === 'existing' ? 'checked' : '' }}
                           onchange="toggleClinicOption(this.value)"
                           {{ $clinics->isEmpty() ? 'disabled' : '' }}>
                    <span class="text-sm text-gray-700">Entrar em clínica existente</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="clinic_option" value="new" id="opt_new"
                           {{ old('clinic_option', $clinics->isEmpty() ? 'new' : 'existing') === 'new' ? 'checked' : '' }}
                           onchange="toggleClinicOption(this.value)">
                    <span class="text-sm text-gray-700">Criar nova clínica</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('clinic_option')" class="mb-2" />

            <!-- Selecionar clínica existente -->
            <div id="section_existing" class="{{ old('clinic_option', $clinics->isEmpty() ? 'new' : 'existing') === 'existing' ? '' : 'hidden' }}">
                <x-input-label for="clinic_id" value="Selecione a clínica" />
                <select id="clinic_id" name="clinic_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Selecione --</option>
                    @foreach($clinics as $clinic)
                        <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>
                            {{ $clinic->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('clinic_id')" class="mt-2" />
            </div>

            <!-- Criar nova clínica -->
            <div id="section_new" class="{{ old('clinic_option', $clinics->isEmpty() ? 'new' : 'existing') === 'new' ? '' : 'hidden' }}">
                <div>
                    <x-input-label for="clinic_name" value="Nome da clínica *" />
                    <x-text-input id="clinic_name" class="block mt-1 w-full" type="text" name="clinic_name" :value="old('clinic_name')" />
                    <x-input-error :messages="$errors->get('clinic_name')" class="mt-2" />
                </div>
                <div class="mt-3">
                    <x-input-label for="clinic_cnpj" value="CNPJ" />
                    <x-text-input id="clinic_cnpj" class="block mt-1 w-full" type="text" name="clinic_cnpj" :value="old('clinic_cnpj')" placeholder="00.000.000/0000-00" />
                    <x-input-error :messages="$errors->get('clinic_cnpj')" class="mt-2" />
                </div>
                <div class="mt-3">
                    <x-input-label for="clinic_email" value="E-mail da clínica" />
                    <x-text-input id="clinic_email" class="block mt-1 w-full" type="email" name="clinic_email" :value="old('clinic_email')" />
                    <x-input-error :messages="$errors->get('clinic_email')" class="mt-2" />
                </div>
                <div class="mt-3">
                    <x-input-label for="clinic_phone" value="Telefone" />
                    <x-text-input id="clinic_phone" class="block mt-1 w-full" type="text" name="clinic_phone" :value="old('clinic_phone')" placeholder="(00) 00000-0000" />
                    <x-input-error :messages="$errors->get('clinic_phone')" class="mt-2" />
                </div>
                <div class="mt-3">
                    <x-input-label for="clinic_address" value="Endereço" />
                    <x-text-input id="clinic_address" class="block mt-1 w-full" type="text" name="clinic_address" :value="old('clinic_address')" />
                    <x-input-error :messages="$errors->get('clinic_address')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                Já tem conta?
            </a>
            <x-primary-button class="ms-4">Cadastrar</x-primary-button>
        </div>
    </form>

    <script>
        function toggleClinicOption(value) {
            document.getElementById('section_existing').classList.toggle('hidden', value !== 'existing');
            document.getElementById('section_new').classList.toggle('hidden', value !== 'new');
        }
    </script>
</x-guest-layout>
