<x-app-layout>
    <x-slot name="header">
        <span>Ventas</span>
    </x-slot>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-xl">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Alerta" class="h-8 w-8 object-contain pointer-events-none" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No hay ninguna caja abierta</h3>
            <p class="mt-2 text-sm text-gray-500">Ve al módulo de caja, abre una caja y vuelve para empezar a registrar ventas.</p>
            <a href="{{ route('cajas.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] px-6 py-3 text-sm font-semibold text-white hover:bg-[#262626]"><img src="{{ asset('icons/Ventas-Blanco.png') }}" alt="" class="h-5 w-5 object-contain pointer-events-none" /> Ir a abrir caja</a>
        </div>
    </div>
</x-app-layout>
