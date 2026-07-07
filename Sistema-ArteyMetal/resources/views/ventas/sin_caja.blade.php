<x-app-layout>
    <x-slot name="header">
        <span>Ventas</span>
    </x-slot>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
    @endif

    @if (session('ok'))
        <div class="mb-5 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('ok') }}</div>
    @endif

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-xl">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-base font-semibold text-gray-800">Abrir caja</h3>
            </div>
            <form method="POST" action="{{ route('ventas.abrir_caja') }}" class="space-y-4 p-5">
                @csrf
                <p class="text-sm text-gray-600">Necesitas abrir una caja para continuar.</p>
                <div>
                    <label for="monto_inicial_ventas" class="mb-2 block text-sm font-medium text-gray-700">Monto inicial</label>
                    <input id="monto_inicial_ventas" name="monto_inicial" type="number" step="0.01" min="0" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="0.00" />
                </div>
                <div>
                    <label for="obs_ventas" class="mb-2 block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea id="obs_ventas" name="observaciones" rows="2" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Opcional"></textarea>
                </div>
                <button type="submit" class="w-full rounded-xl bg-[#111] px-4 py-3 text-sm font-semibold text-white hover:bg-[#262626]">Abrir caja</button>
            </form>
        </div>
    </div>
</x-app-layout>
