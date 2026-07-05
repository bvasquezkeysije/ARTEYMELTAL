<section>
    <header>
        <h3 class="text-base font-semibold text-[#2a2419]">Seguridad</h3>
        <p class="mt-1 text-sm text-[#6e6758]">Cambia la contrasena de acceso.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="mb-2 block text-sm font-medium text-[#4d4026]">Contrasena actual</label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full rounded-xl border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12] focus:border-[#b9943d] focus:ring-[#b9943d]" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-sm" />
        </div>

        <div>
            <label for="update_password_password" class="mb-2 block text-sm font-medium text-[#4d4026]">Nueva contrasena</label>
            <x-text-input id="update_password_password" name="password" type="password" class="block w-full rounded-xl border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12] focus:border-[#b9943d] focus:ring-[#b9943d]" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-sm" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="mb-2 block text-sm font-medium text-[#4d4026]">Confirmar contrasena</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full rounded-xl border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12] focus:border-[#b9943d] focus:ring-[#b9943d]" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-sm" />
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Actualizar contrasena</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-700"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>
