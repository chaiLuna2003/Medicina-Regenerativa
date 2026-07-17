<x-guest-layout>
<form method="POST" action="{{ route('register') }}" x-data="{
    email: '{{ old('email') }}',
    hadUppercase: false,
    password: '',
    get hasMinLength() { return this.password.length >= 8 },
    get hasUpper() { return /[A-Z]/.test(this.password) },
    get hasLower() { return /[a-z]/.test(this.password) },
    get hasNumber() { return /[0-9]/.test(this.password) },
    get hasSymbol() { return /[^A-Za-z0-9]/.test(this.password) },
    get strength() {
        return [this.hasMinLength, this.hasUpper, this.hasLower, this.hasNumber, this.hasSymbol]
            .filter(Boolean).length
    }
}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                pattern="[\p{L}\s]+" title="Solo se permiten letras y espacios" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">Solo letras y espacios, sin números ni símbolos.</p>
        </div>

        <!-- Email -->
        <!-- Email -->
<div class="mt-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username"
        x-model="email"
        @input="
            hadUppercase = email !== email.toLowerCase();
            email = email.toLowerCase();
        " />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />

    <p class="text-xs text-amber-600 mt-1" x-show="hadUppercase" x-transition>
        Solo se permiten minúsculas — tu correo se convirtió automáticamente.
    </p>
</div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password"
                x-model="password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <!-- Barra de fortaleza -->
            <div class="mt-2" x-show="password.length > 0">
                <div class="flex gap-1 h-1.5">
                    <template x-for="i in 5">
                        <div class="flex-1 rounded"
                             :class="strength >= i
                                ? (strength <= 2 ? 'bg-red-500' : strength <= 4 ? 'bg-amber-500' : 'bg-emerald-500')
                                : 'bg-gray-200'">
                        </div>
                    </template>
                </div>
                <ul class="text-xs mt-2 space-y-1">
                    <li :class="hasMinLength ? 'text-emerald-600' : 'text-gray-400'">
                        <span x-text="hasMinLength ? '✓' : '○'"></span> Mínimo 8 caracteres
                    </li>
                    <li :class="hasUpper && hasLower ? 'text-emerald-600' : 'text-gray-400'">
                        <span x-text="hasUpper && hasLower ? '✓' : '○'"></span> Mayúsculas y minúsculas
                    </li>
                    <li :class="hasNumber ? 'text-emerald-600' : 'text-gray-400'">
                        <span x-text="hasNumber ? '✓' : '○'"></span> Al menos un número
                    </li>
                    <li :class="hasSymbol ? 'text-emerald-600' : 'text-gray-400'">
                        <span x-text="hasSymbol ? '✓' : '○'"></span> Al menos un símbolo (!@#$...)
                    </li>
                </ul>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>