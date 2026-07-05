<x-app-layout>
    <x-slot name="header">
        <span>Ventas</span>
    </x-slot>

    <style>
        .btn-icon:focus-visible,
        .btn-icon:focus,
        .btn-icon-sm:focus-visible,
        .btn-icon-sm:focus {
            outline: 0 none !important;
        }
        .btn-icon:active,
        .btn-icon-sm:active {
            filter: brightness(0.85);
        }
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            flex-shrink: 0;
            color: #fff;
        }
        .btn-icon.is-active {
            filter: brightness(0.8);
        }
        .btn-icon-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            flex-shrink: 0;
            color: #fff;
        }
    </style>

    <div x-data="{ modalVenta: false, ventaVista: null, modalComprobante: false, urlComprobante: '', filtrosAbiertos: false }" class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <form id="search-form" method="GET" action="{{ route('ventas.index') }}" class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="Buscar por codigo, cliente o tipo" />
                </form>
                <button type="submit" form="search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <button
                    type="button"
                    @click="filtrosAbiertos = !filtrosAbiertos"
                    class="btn-icon bg-sky-500 hover:bg-sky-600"
                    title="Filtrar"
                    :class="{ 'is-active': filtrosAbiertos || '{{ $tipo ?? '' }}' !== '' }"
                >
                    <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if($tipo || $busqueda)
                    <a href="{{ route('ventas.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
                @if(auth()->user()->tienePermiso('ventas.gestionar'))
                    <a href="{{ route('ventas.create') }}" class="btn-icon" style="background-color:#09090f;color:white" title="Nueva venta">
                        <img src="{{ asset('icons/nuevo.ico') }}" alt="Nuevo" class="h-5 w-5 object-contain pointer-events-none" />
                    </a>
                @endif
            </div>

            <form x-show="filtrosAbiertos" x-transition method="GET" action="{{ route('ventas.index') }}" class="mt-4 flex flex-wrap items-end gap-4 border-t border-[#efe7d2] pt-4">
                <input type="hidden" name="q" value="{{ $busqueda }}" />
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Tipo</label>
                    <select name="tipo" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                        <option value="">Todos</option>
                        <option value="stock" @selected(($tipo ?? '') === 'stock')>Venta stock</option>
                        <option value="pedido" @selected(($tipo ?? '') === 'pedido')>Cierre pedido</option>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">Filtrar</button>
            </form>
        </div>

        @if (session('ok'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('ok') }}</div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Codigo</th>
                            <th class="px-4 py-3 font-semibold">Fecha</th>
                            <th class="px-4 py-3 font-semibold">Tipo</th>
                            <th class="px-4 py-3 font-semibold">Comprobante</th>
                            <th class="px-4 py-3 font-semibold">Cliente</th>
                            <th class="px-4 py-3 font-semibold">Monto</th>
                            <th class="px-4 py-3 font-semibold">Cobrado</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($ventas as $venta)
                            @php
                                $ventaVistaData = [
                                    'codigo' => $venta->codigo,
                                    'fecha' => optional($venta->fecha_venta)->format('d/m/Y') ?: '-',
                                    'tipo' => $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock',
                                    'comprobante_codigo' => $venta->comprobante?->codigo ?: '-',
                                    'comprobante_tipo' => $venta->comprobante ? ucfirst($venta->comprobante->tipo_comprobante) : '-',
                                    'comprobante_url' => $venta->comprobante ? route('ventas.comprobante', $venta, false) : null,
                                    'cliente' => $venta->cliente_nombre ?: '-',
                                    'monto_total' => 'S/ ' . number_format((float) $venta->monto_total, 2),
                                    'monto_cobrado' => 'S/ ' . number_format((float) $venta->monto_cobrado, 2),
                                    'estado_pago' => str_replace('_', ' ', $venta->estado_pago ?? '-'),
                                    'pedido_codigo' => $venta->pedido?->codigo,
                                    'observaciones' => $venta->observaciones ?: '-',
                                    'detalles' => $venta->detalles->map(fn ($d) => [
                                        'producto' => $d->producto_nombre,
                                        'cantidad' => (int) $d->cantidad,
                                        'precio' => 'S/ ' . number_format((float) $d->precio_unitario, 2),
                                        'subtotal' => 'S/ ' . number_format((float) $d->subtotal, 2),
                                    ])->values()->all(),
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $venta->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ optional($venta->fecha_venta)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock' }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">
                                    @if($venta->comprobante)
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ strtoupper($venta->comprobante->tipo_comprobante) }}</span>
                                        <p class="mt-1 text-xs text-[#6e6e6e]">{{ $venta->comprobante->codigo }}</p>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $venta->cliente_nombre ?: '-' }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">S/ {{ number_format((float) $venta->monto_total, 2) }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">S/ {{ number_format((float) $venta->monto_cobrado, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->tienePermiso('ventas.ver'))
                                            <button
                                                type="button"
                                                @click="ventaVista = @js($ventaVistaData); modalVenta = true"
                                                class="btn-icon-sm" style="background-color:#0891B2"
                                                title="Ver detalle"
                                            >
                                                <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @endif
                                        @if($venta->comprobante)
                                            <button
                                                type="button"
                                                @click="urlComprobante = '{{ route('ventas.comprobante', $venta, false) }}'; modalComprobante = true"
                                                class="btn-icon-sm" style="background-color:#64748B"
                                                title="Imprimir comprobante"
                                            >
                                                <img src="{{ asset('icons/imprimir.ico') }}" alt="Imprimir" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('ventas.emitir_comprobante', $venta) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="btn-icon-sm" style="background-color:#64748B"
                                                    title="Emitir comprobante"
                                                >
                                                    <img src="{{ asset('icons/imprimir.ico') }}" alt="Emitir" class="h-4 w-4 object-contain pointer-events-none" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-[#777]">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#efe7d2] px-4 py-3">{{ $ventas->links('pagination.gold') }}</div>
        </div>

        <template x-teleport="body">
            <div x-show="modalVenta" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="modalVenta = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="max-h-[92vh] w-full max-w-4xl overflow-y-auto rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                    <h3 class="text-base font-semibold text-[#2a2419]">Detalle rapido de venta</h3>
                    <button type="button" @click="modalVenta = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>

                <div class="grid gap-4 p-5 md:grid-cols-2" x-show="ventaVista">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Codigo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.codigo"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Fecha</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.fecha"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Tipo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.tipo"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Comprobante</p>
                        <p class="mt-1 text-[#1f1f1f]"><span x-text="ventaVista?.comprobante_tipo"></span> - <span x-text="ventaVista?.comprobante_codigo"></span></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cliente</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.cliente"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Monto total</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.monto_total"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cobrado</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.monto_cobrado"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Estado pago</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.estado_pago"></p>
                    </div>
                    <div x-show="ventaVista?.pedido_codigo">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Pedido relacionado</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.pedido_codigo"></p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Detalles</p>
                        <div class="mt-2 overflow-hidden rounded-xl border border-[#e9dec2]">
                            <table class="min-w-full text-sm">
                                <thead class="bg-[#faf6ea] text-left text-[#5a4a2a]">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold">Producto</th>
                                        <th class="px-3 py-2 font-semibold">Cantidad</th>
                                        <th class="px-3 py-2 font-semibold">Precio</th>
                                        <th class="px-3 py-2 font-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#efeee9]">
                                    <template x-for="(d, idx) in (ventaVista?.detalles || [])" :key="idx">
                                        <tr>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="d.producto"></td>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="d.cantidad"></td>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="d.precio"></td>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="d.subtotal"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="!ventaVista?.detalles || ventaVista.detalles.length === 0">
                                        <td colspan="4" class="px-3 py-4 text-center text-[#777]">Sin detalles registrados.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Observaciones</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="ventaVista?.observaciones"></p>
                    </div>
                    <div class="md:col-span-2" x-show="ventaVista?.comprobante_url">
                        <button
                            type="button"
                            @click="urlComprobante = ventaVista.comprobante_url; modalComprobante = true"
                            class="rounded-lg border border-[#d3c49f] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]"
                        >
                            Ver comprobante
                        </button>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="modalComprobante" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-[60] bg-black/60" @click="modalComprobante = false"></div>
                <div x-transition class="fixed inset-0 z-[70] flex items-center justify-center p-4">
                    <div class="h-[92vh] w-full max-w-6xl overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                        <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                            <h3 class="text-base font-semibold text-[#2a2419]">Comprobante</h3>
                            <button type="button" @click="modalComprobante = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                            </button>
                        </div>
                        <iframe :src="urlComprobante" class="h-[calc(92vh-56px)] w-full" title="Vista previa comprobante"></iframe>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

