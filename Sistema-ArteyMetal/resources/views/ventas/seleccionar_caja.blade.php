<x-app-layout>
    <x-slot name="header">
        <span>Seleccionar caja</span>
    </x-slot>

    <div class="flex items-center justify-center py-12">
        <div class="w-full max-w-lg space-y-5">
            <div class="text-center">
                <h2 class="text-lg font-semibold text-gray-900">Selecciona tu caja de trabajo</h2>
                <p class="mt-1 text-sm text-gray-500">Tienes varias cajas abiertas. Elige una para empezar a operar.</p>
            </div>

            <div class="space-y-3">
                @foreach ($cajasAbiertas as $caja)
                    <a
                        href="{{ route('ventas.seleccionar_caja', $caja) }}"
                        class="flex items-center justify-between rounded-2xl border border-[#d1be8a] bg-[#fffdf7] p-4 shadow-sm transition hover:border-[#b8953a] hover:shadow-md"
                    >
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-[#2a2419]">Caja #{{ $caja->id }}</p>
                            <p class="text-xs text-gray-500">Abierta: {{ $caja->fecha_apertura->format('d/m/Y H:i') }}</p>
                            @if ($caja->monto_inicial > 0)
                                <p class="text-xs text-gray-500">Monto inicial: S/ {{ number_format($caja->monto_inicial, 2) }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Abierta</span>
                            <svg class="h-5 w-5 text-[#b8953a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
