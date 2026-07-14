<x-app-layout>
    <x-slot name="header">
        <span>Nuevo pedido</span>
    </x-slot>

    <div x-data="{
        showSuccess: {{ session()->has('ok') ? 'true' : 'false' }},
        showErrors: {{ $errors->any() ? 'true' : 'false' }},
        errorMessages: @js($errors->any() ? $errors->all() : [])
    }" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('pedidos.store') }}" enctype="multipart/form-data">
            @csrf
            @include('pedidos._form')

            <div class="mt-6 flex gap-2">
                <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Registrar pedido</button>
                <a href="{{ route('pedidos.index') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Cancelar</a>
            </div>
        </form>

        {{-- Modal exito --}}
        <template x-teleport="body">
            <div x-show="showSuccess" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showSuccess = false"></div>
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                        <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                            <img src="{{ asset('icons/Valido-Verde.png') }}" alt="Valido" class="h-8 w-8 object-contain pointer-events-none" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ session('ok') }}</h3>
                        <button type="button" @click="showSuccess = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal errores --}}
        <template x-teleport="body">
            <div x-show="showErrors" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showErrors = false"></div>
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-8 pt-10 pb-10 shadow-xl">
                        <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                            <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Alerta" class="h-8 w-8 object-contain pointer-events-none" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 text-center">Se encontraron errores</h3>
                        <ul class="mt-4 space-y-2 text-sm text-red-700">
                            <template x-for="(msg, idx) in errorMessages" :key="idx">
                                <li x-text="msg"></li>
                            </template>
                        </ul>
                        <div class="text-center">
                            <button type="button" @click="showErrors = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
