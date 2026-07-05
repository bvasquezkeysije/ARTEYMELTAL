<x-app-layout>
    <x-slot name="header">
        <span>Pedidos</span>
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

    <div x-data="{ modalPedido: false, pedidoVista: null, filtrosAbiertos: false }" class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <form id="search-form" method="GET" action="{{ route('pedidos.index') }}" class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="Buscar por codigo, cliente, producto o estado" />
                </form>
                <button type="submit" form="search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <button
                    type="button"
                    @click="filtrosAbiertos = !filtrosAbiertos"
                    class="btn-icon bg-sky-500 hover:bg-sky-600"
                    title="Filtrar"
                    :class="{ 'is-active': filtrosAbiertos || '{{ $filtroEstado ?? '' }}' !== '' }"
                >
                    <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if($filtroEstado || $busqueda)
                    <a href="{{ route('pedidos.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
                @if(auth()->user()->tienePermiso('pedidos.gestionar'))
                    <a href="{{ route('pedidos.create') }}" class="btn-icon" style="background-color:#09090f;color:white" title="Nuevo pedido">
                        <img src="{{ asset('icons/nuevo.ico') }}" alt="Nuevo" class="h-5 w-5 object-contain pointer-events-none" />
                    </a>
                @endif
            </div>

            <form x-show="filtrosAbiertos" x-transition method="GET" action="{{ route('pedidos.index') }}" class="mt-4 flex flex-wrap items-end gap-4 border-t border-[#efe7d2] pt-4">
                <input type="hidden" name="q" value="{{ $busqueda }}" />
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</label>
                    <select name="estado" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Todos</option>
                        <option value="registrado" @selected($filtroEstado === 'registrado')>Registrado</option>
                        <option value="en_produccion" @selected($filtroEstado === 'en_produccion')>En produccion</option>
                        <option value="listo_entrega" @selected($filtroEstado === 'listo_entrega')>Listo para entrega</option>
                        <option value="entregado" @selected($filtroEstado === 'entregado')>Entregado</option>
                        <option value="cancelado" @selected($filtroEstado === 'cancelado')>Cancelado</option>
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
                            <th class="px-4 py-3 font-semibold">Cliente</th>
                            <th class="px-4 py-3 font-semibold">Producto</th>
                            <th class="px-4 py-3 font-semibold">Cantidad</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 font-semibold">Pago</th>
                            <th class="px-4 py-3 font-semibold">Entrega</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($pedidos as $pedido)
                            @php
                                $montoTotal = (float) ($pedido->monto_total ?? 0);
                                $saldoPendiente = (float) ($pedido->monto_saldo ?? 0);
                                $montoCancelado = max(0, $montoTotal - $saldoPendiente);
                                $porcentajeCancelado = $montoTotal > 0 ? min(100, round(($montoCancelado / $montoTotal) * 100, 2)) : 0;
                                $pedidoVistaData = [
                                    'codigo' => $pedido->codigo,
                                    'nombre_cliente' => $pedido->nombre_cliente,
                                    'cliente_catalogo' => $pedido->cliente?->id ? ('Cliente catalogo #' . $pedido->cliente->id) : null,
                                    'telefono_cliente' => $pedido->telefono_cliente ?: '-',
                                    'documento_cliente' => $pedido->documento_cliente ?: '-',
                                    'correo_cliente' => $pedido->correo_cliente ?: '-',
                                    'tipo_producto' => $pedido->tipo_producto,
                                    'cantidad' => $pedido->cantidad,
                                    'estado' => str_replace('_', ' ', $pedido->estado),
                                    'estado_pago' => str_replace('_', ' ', $pedido->estado_pago ?? 'pendiente_adelanto'),
                                    'estado_personalizacion' => str_replace('_', ' ', $pedido->estado_personalizacion ?? 'sin_iniciar'),
                                    'tipo_entrega' => $pedido->tipo_entrega === 'agencia' ? 'Agencia' : ($pedido->tipo_entrega === 'delivery' ? 'Delivery' : 'Local'),
                                    'direccion_entrega' => $pedido->direccion_entrega ?: '-',
                                    'distrito_entrega' => $pedido->distrito_entrega ?: '-',
                                    'codigo_postal_entrega' => $pedido->codigo_postal_entrega ?: '-',
                                    'referencia_entrega' => $pedido->referencia_entrega ?: '-',
                                    'nombre_recibe' => $pedido->nombre_recibe ?: '-',
                                    'telefono_recibe' => $pedido->telefono_recibe ?: '-',
                                    'fecha_entrega_compromiso' => optional($pedido->fecha_entrega_compromiso)->format('d/m/Y') ?? '-',
                                    'monto_total' => $pedido->monto_total !== null ? 'S/ ' . number_format((float) $pedido->monto_total, 2) : '-',
                                    'monto_cancelado' => 'S/ ' . number_format($montoCancelado, 2),
                                    'monto_saldo' => 'S/ ' . number_format($saldoPendiente, 2),
                                    'porcentaje_cancelado' => number_format($porcentajeCancelado, 2) . '%',
                                    'detalle_trabajo' => $pedido->detalle_trabajo ?: '-',
                                    'observaciones' => $pedido->observaciones ?: '-',
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">
                                    {{ $pedido->nombre_cliente }}
                                    @if($pedido->cliente)
                                        <p class="text-xs text-[#6e6e6e]">Cliente catalogo #{{ $pedido->cliente->id }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->tipo_producto }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->cantidad }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ str_replace('_', ' ', $pedido->estado) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="min-w-[210px]">
                                        <p class="text-xs font-semibold text-[#5a4a2a]">{{ number_format($porcentajeCancelado, 2) }}% cancelado</p>
                                        <div class="mt-1 h-2 overflow-hidden rounded-full bg-[#efe7d2]">
                                            <div class="h-full rounded-full bg-[#8a6a2e]" style="width: {{ $porcentajeCancelado }}%"></div>
                                        </div>
                                        <p class="mt-1 text-[11px] text-[#6e6e6e]">
                                            Cancelado: S/ {{ number_format($montoCancelado, 2) }} | Falta: S/ {{ number_format($saldoPendiente, 2) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ optional($pedido->fecha_entrega_compromiso)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->tienePermiso('pedidos.gestionar') && in_array($pedido->estado, ['listo_entrega', 'entregado'], true) && $pedido->estado_pago === 'adelanto_pagado' && (float) ($pedido->monto_saldo ?? 0) > 0)
                                            <form method="POST" action="{{ route('pedidos.confirmar_pago_final', $pedido) }}" onsubmit="return confirm('Confirmar pago final y cerrar este pedido? Se registrara automaticamente en ventas.')">
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-emerald-300 px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50">Cobrar 50% y cerrar</button>
                                            </form>
                                        @endif
                                        @if(auth()->user()->tienePermiso('pedidos.ver'))
                                            <button
                                                type="button"
                                                @click="pedidoVista = @js($pedidoVistaData); modalPedido = true"
                                                class="btn-icon-sm" style="background-color:#0891B2"
                                                title="Ver detalle"
                                            >
                                                <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @endif
                                        @if(auth()->user()->tienePermiso('pedidos.gestionar'))
                                            <a href="{{ route('pedidos.edit', $pedido) }}" class="btn-icon-sm bg-amber-400 hover:bg-amber-500" title="Editar">
                                                <img src="{{ asset('icons/editar.ico') }}" alt="Editar" class="h-4 w-4 object-contain pointer-events-none" />
                                            </a>
                                            <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" onsubmit="return confirm('Deseas eliminar este pedido?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Eliminar">
                                                    <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="Eliminar" class="h-4 w-4 object-contain pointer-events-none" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-[#777]">No hay pedidos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#efe7d2] px-4 py-3">{{ $pedidos->links('pagination.gold') }}</div>
        </div>

        <template x-teleport="body">
            <div x-show="modalPedido" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="modalPedido = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                    <h3 class="text-base font-semibold text-[#2a2419]">Detalle rapido del pedido</h3>
                    <button type="button" @click="modalPedido = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>

                <div class="grid gap-4 p-5 md:grid-cols-2" x-show="pedidoVista">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Codigo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.codigo"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Estado</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.estado"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cliente</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.nombre_cliente"></p>
                        <p class="text-xs text-[#6e6e6e]" x-show="pedidoVista?.cliente_catalogo" x-text="pedidoVista?.cliente_catalogo"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Documento</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.documento_cliente"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Telefono</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.telefono_cliente"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Correo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.correo_cliente"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Producto</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.tipo_producto"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cantidad</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.cantidad"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Pago</p>
                        <p class="mt-1 text-[#1f1f1f]"><span x-text="pedidoVista?.porcentaje_cancelado"></span> cancelado</p>
                        <p class="text-xs text-[#6e6e6e]">Cancelado: <span x-text="pedidoVista?.monto_cancelado"></span> | Falta: <span x-text="pedidoVista?.monto_saldo"></span></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Monto total</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.monto_total"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Estado pago</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.estado_pago"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Entrega</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.fecha_entrega_compromiso"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Tipo entrega</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.tipo_entrega"></p>
                    </div>
                    <div class="md:col-span-2" x-show="pedidoVista?.tipo_entrega !== 'Local'">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Direccion entrega</p>
                        <p class="mt-1 text-[#1f1f1f]"><span x-text="pedidoVista?.direccion_entrega"></span> | Distrito: <span x-text="pedidoVista?.distrito_entrega"></span> | CP: <span x-text="pedidoVista?.codigo_postal_entrega"></span></p>
                        <p class="text-xs text-[#6e6e6e]">Referencia: <span x-text="pedidoVista?.referencia_entrega"></span> | Recibe: <span x-text="pedidoVista?.nombre_recibe"></span> (<span x-text="pedidoVista?.telefono_recibe"></span>)</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Detalle trabajo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.detalle_trabajo"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Observaciones</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.observaciones"></p>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </template>
    </div>
</x-app-layout>
