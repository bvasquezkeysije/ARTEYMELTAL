<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Ingresa el codigo de 6 digitos que enviamos a tu correo y establece tu nueva contrasena.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.code.store') }}">
        @csrf

        <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">

        <div class="mt-4">
            <x-input-label for="code" value="Codigo de 6 digitos" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required maxlength="6" inputmode="numeric" autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Restablecer contrasena
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 text-center text-sm text-gray-600">
        <a href="{{ route('password.request') }}" class="underline hover:text-gray-900">Solicitar un nuevo codigo</a>
    </div>
</x-guest-layout>
