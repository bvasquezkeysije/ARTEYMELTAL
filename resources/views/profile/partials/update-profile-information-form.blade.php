<section>
    <header>
        <h3 class="text-base font-semibold text-[#2a2419]">Datos de perfil</h3>
        <p class="mt-1 text-sm text-[#6e6758]">Actualiza nombre y correo de la cuenta.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-[#4d4026]">Nombre</label>
            <x-text-input id="name" name="name" type="text" class="block w-full rounded-xl border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12] focus:border-[#b9943d] focus:ring-[#b9943d]" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1 text-sm" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-[#4d4026]">Correo</label>
            <x-text-input id="email" name="email" type="email" class="block w-full rounded-xl border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12] focus:border-[#b9943d] focus:ring-[#b9943d]" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1 text-sm" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                    <p>Tu correo aun no esta verificado.</p>
                    <button form="send-verification" class="mt-1 font-medium underline underline-offset-2 hover:text-amber-900">
                        Reenviar correo de verificacion
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-emerald-700">Se envio un nuevo enlace de verificacion.</p>
                @endif
            @endif
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Guardar cambios</button>

            @if (session('status') === 'profile-updated')
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
