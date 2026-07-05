<section class="space-y-5">
    <header>
        <h3 class="text-base font-semibold text-rose-800">Zona sensible</h3>
        <p class="mt-1 text-sm text-rose-700">Eliminar la cuenta borra el acceso de forma permanente.</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-xl border border-rose-300 bg-rose-600 px-4 py-2.5 text-sm font-medium normal-case tracking-normal text-white hover:bg-rose-500"
    >Eliminar cuenta</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-[#2a2419]">Confirmar eliminacion de cuenta</h2>

            <p class="mt-1 text-sm text-[#6e6758]">
                Esta accion es irreversible. Ingresa tu contrasena para confirmar.
            </p>

            <div class="mt-5">
                <x-input-label for="password" value="Contrasena" class="mb-2 block text-sm font-medium text-[#4d4026]" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full rounded-xl border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12] focus:border-[#b9943d] focus:ring-[#b9943d]"
                    placeholder="Contrasena"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1 text-sm" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl border border-[#d3c49f] px-4 py-2.5 text-sm font-medium normal-case tracking-normal text-[#5a4314] hover:bg-[#fff5dd]">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="rounded-xl border border-rose-300 bg-rose-600 px-4 py-2.5 text-sm font-medium normal-case tracking-normal text-white hover:bg-rose-500">
                    Eliminar cuenta
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
