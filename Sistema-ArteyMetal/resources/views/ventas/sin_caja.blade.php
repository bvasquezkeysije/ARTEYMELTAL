<x-app-layout>
    <x-slot name="header">
        <span>Ventas</span>
    </x-slot>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-xl">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-7.364A9 9 0 1112 3a9 9 0 017.364 4.636z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No hay ninguna caja abierta</h3>
            <p class="mt-2 text-sm text-gray-500">Ve al módulo de caja, abre una caja y vuelve para empezar a registrar ventas.</p>
            <a href="{{ route('cajas.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] px-6 py-3 text-sm font-semibold text-white hover:bg-[#262626]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                Ir a abrir caja
            </a>
        </div>
    </div>
</x-app-layout>
