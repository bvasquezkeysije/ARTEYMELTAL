<x-app-layout>
    <x-slot name="header">
        <span>Detalle de caja</span>
    </x-slot>

    <div class="space-y-5">
        <a href="{{ route('cajas.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">&larr; Volver</a>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Usuario</p>
                    <p class="mt-1 text-gray-900">{{ $caja->usuario?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Apertura</p>
                    <p class="mt-1 text-gray-900">{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Cierre</p>
                    <p class="mt-1 text-gray-900">{{ $caja->fecha_cierre?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Monto inicial</p>
                    <p class="mt-1 text-gray-900">S/ {{ number_format($caja->monto_inicial, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Total ventas</p>
                    <p class="mt-1 text-gray-900">S/ {{ number_format($caja->total_ventas, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Monto final</p>
                    <p class="mt-1 text-gray-900">{{ $caja->monto_final ? 'S/ '.number_format($caja->monto_final, 2) : '—' }}</p>
                </div>
                <div class="md:col-span-3">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Estado</p>
                    <p class="mt-1">
                        @if ($caja->estado === 'abierta')
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Abierta</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Cerrada</span>
                        @endif
                    </p>
                </div>
                @if ($caja->observaciones)
                    <div class="md:col-span-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Observaciones</p>
                        <p class="mt-1 text-gray-700">{{ $caja->observaciones }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if ($ventas->count())
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <h3 class="border-b border-gray-200 px-5 py-3 text-sm font-semibold text-gray-800">Ventas registradas</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs uppercase tracking-[0.15em] text-gray-500">
                                <th class="px-4 py-3 font-medium">Codigo</th>
                                <th class="px-4 py-3 font-medium">Cliente</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ventas as $venta)
                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-900">{{ $venta->codigo }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $venta->cliente_nombre ?? 'Consumidor Final' }}</td>
                                    <td class="px-4 py-3 text-gray-900">S/ {{ number_format($venta->monto_total, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $venta->created_at->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">No se registraron ventas en este turno.</div>
        @endif
    </div>
</x-app-layout>
