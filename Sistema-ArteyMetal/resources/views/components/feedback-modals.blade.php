@php
    $hasSuccess = session()->has('ok') || session()->has('success');
    $successMsg = session('ok') ?? session('success') ?? 'Operacion exitosa.';
    $hasErrors = $errors->any();
    $errorMessages = $errors->all();
@endphp

<div x-data="{ showSuccess: {{ $hasSuccess ? 'true' : 'false' }}, showErrors: {{ $hasErrors ? 'true' : 'false' }} }" x-cloak>
    {{-- Modal exito --}}
    <div x-show="showSuccess" style="display: none;">
        <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showSuccess = false"></div>
        <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                <button type="button" @click="showSuccess = false" class="btn-icon-sm absolute top-3 right-3 bg-red-600 hover:bg-red-700" title="Cerrar">
                    <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                </button>
                <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                    <img src="{{ asset('icons/Valido-Verde.png') }}" alt="Valido" class="h-8 w-8 object-contain pointer-events-none" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $successMsg }}</h3>
                <button type="button" @click="showSuccess = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
            </div>
        </div>
    </div>

    {{-- Modal errores --}}
    <div x-show="showErrors" style="display: none;">
        <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showErrors = false"></div>
        <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white px-8 pt-10 pb-10 text-center shadow-xl">
                <button type="button" @click="showErrors = false" class="btn-icon-sm absolute top-3 right-3 bg-red-600 hover:bg-red-700" title="Cerrar">
                    <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                </button>
                <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                    <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Alerta" class="h-8 w-8 object-contain pointer-events-none" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Ha ocurrido un error</h3>
                <ul class="mt-3 space-y-1 text-sm text-gray-600">
                    @foreach($errorMessages as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
                <button type="button" @click="showErrors = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
            </div>
        </div>
    </div>
</div>
