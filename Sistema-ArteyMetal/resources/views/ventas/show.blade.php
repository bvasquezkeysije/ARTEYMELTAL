<x-app-layout>
    <x-slot name="header">
        <span>Detalle venta</span>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Codigo</p>
                    <p class="mt-1 text-gray-900">{{ $venta->codigo }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Fecha</p>
                    <p class="mt-1 text-gray-900">{{ optional($venta->fecha_venta)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Tipo</p>
                    <p class="mt-1 text-gray-900">{{ $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Cliente</p>
                    <p class="mt-1 text-gray-900">{{ $venta->cliente_nombre ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Monto total</p>
                    <p class="mt-1 text-gray-900">S/ {{ number_format((float) $venta->monto_total, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Cobrado</p>
                    <p class="mt-1 text-gray-900">S/ {{ number_format((float) $venta->monto_cobrado, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Forma de pago</p>
                    <p class="mt-1 font-medium">
                        @php
                            $labelPago = ['efectivo' => 'Efectivo', 'yape' => 'Yape', 'plin' => 'Plin', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto'];
                        @endphp
                        <span class="rounded-lg px-2.5 py-1 text-xs font-semibold {{ $venta->metodo_pago === 'efectivo' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                            {{ $labelPago[$venta->metodo_pago] ?? ucfirst($venta->metodo_pago) }}
                        </span>
                    </p>
                </div>
                @if ((float) $venta->monto_efectivo > 0)
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Efectivo</p>
                    <p class="mt-1 text-emerald-700 font-medium">S/ {{ number_format((float) $venta->monto_efectivo, 2) }}</p>
                </div>
                @endif
                @if ((float) $venta->monto_digital > 0)
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Digital</p>
                    <p class="mt-1 text-sky-700 font-medium">S/ {{ number_format((float) $venta->monto_digital, 2) }}</p>
                </div>
                @endif
                @if ((float) $venta->vuelto > 0)
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Vuelto</p>
                    <p class="mt-1 text-red-600 font-medium">-S/ {{ number_format((float) $venta->vuelto, 2) }}</p>
                </div>
                @endif
                @if($venta->comprobante)
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Comprobante</p>
                    <p class="mt-1">
                        <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200">
                            {{ $venta->comprobante->codigo ?? $venta->comprobante->tipo_comprobante }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </p>
                </div>
                @endif
            </div>

            @if($venta->pedido)
                <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                    Relacionado al pedido {{ $venta->pedido->codigo }}.
                </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-200 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Item</th>
                            <th class="px-4 py-3 font-semibold">Cantidad</th>
                            <th class="px-4 py-3 font-semibold">Precio unitario</th>
                            <th class="px-4 py-3 font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($venta->detalles as $detalle)
                            <tr>
                                <td class="px-4 py-3">{{ $detalle->producto_nombre }}</td>
                                <td class="px-4 py-3">{{ $detalle->cantidad }}</td>
                                <td class="px-4 py-3">S/ {{ number_format((float) $detalle->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3">S/ {{ number_format((float) $detalle->subtotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Sin detalles registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <a href="{{ route('ventas.index') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700">Volver</a>
        </div>
    </div>
</x-app-layout>
