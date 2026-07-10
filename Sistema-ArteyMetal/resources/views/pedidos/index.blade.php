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

    <div x-data="{ modalPedido: false, pedidoVista: null, modalCobro: false, cobroData: null, modalDerivar: false, derivarData: null, metodoPago: 'efectivo', montoRecibido: 0, filtrosAbiertos: false, selectorCajaAbierto: {{ ($cajasAbiertas ?? collect())->isNotEmpty() ? 'true' : 'false' }}, sinCajaAbierto: {{ ($sinCaja ?? false) ? 'true' : 'false' }}, showSuccess: {{ session()->has('ok') ? 'true' : 'false' }}, showErrors: {{ $errors->any() ? 'true' : 'false' }}, errorMessages: @js($errors->any() ? $errors->all() : []) }" class="space-y-3">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    @if(isset($caja) && $caja)
                    <div class="shrink-0 flex items-center rounded-lg bg-[#f4ebd4] px-3 text-xs text-[#6a5122] h-10">
                        {{ $caja->nombre ?? 'Caja #'.$caja->id }}
                        <span class="ml-1 text-emerald-600">Abierta</span>
                    </div>
                    @endif
                    <form id="search-form" method="GET" action="{{ route('pedidos.index') }}" class="flex min-w-0 flex-1">
                        <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 text-sm text-gray-900 h-10" placeholder="Buscar por codigo, cliente, producto o estado" />
                    </form>
                </div>
                <button type="submit" form="search-form" class="h-10 w-10 rounded-xl bg-blue-600 hover:bg-blue-700 flex items-center justify-center shrink-0" title="Buscar">
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
                @if($filtroEstado || $filtroPersonalizacion || $busqueda)
                    <a href="{{ route('pedidos.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
                <form method="POST" action="{{ route('pedidos.cambiar_caja') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-icon bg-amber-600 hover:bg-amber-700" title="Cambiar caja">
                        <svg class="h-5 w-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </button>
                </form>
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
                        <option value="en_transporte" @selected($filtroEstado === 'en_transporte')>En transporte</option>
                        <option value="en_almacen" @selected($filtroEstado === 'en_almacen')>En almacen</option>
                        <option value="listo_recoger" @selected($filtroEstado === 'listo_recoger')>Listo recoger</option>
                        <option value="entregado" @selected($filtroEstado === 'entregado')>Entregado</option>
                        <option value="cancelado" @selected($filtroEstado === 'cancelado')>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">Filtrar</button>
            </form>
        </div>

        @php $rol = auth()->user()->rol->nombre; @endphp

        <div class="flex flex-wrap gap-2">
            @if($rol === 'disenador')
                <a href="{{ route('pedidos.index', array_filter(['estado_personalizacion' => 'en_diseno'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado_personalizacion') === 'en_diseno' ? 'ring-2 ring-amber-400' : '' }}">En diseno</a>
                <a href="{{ route('pedidos.index', array_filter(['estado_personalizacion' => 'en_revision'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado_personalizacion') === 'en_revision' ? 'ring-2 ring-amber-400' : '' }}">En revision</a>
            @endif
            @if($rol === 'orfebre')
                <a href="{{ route('pedidos.index', array_filter(['estado_personalizacion' => 'aprobado'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado_personalizacion') === 'aprobado' ? 'ring-2 ring-amber-400' : '' }}">Aprobados</a>
                <a href="{{ route('pedidos.index', array_filter(['estado' => 'en_produccion'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado') === 'en_produccion' ? 'ring-2 ring-amber-400' : '' }}">En produccion</a>
            @endif
            @if($rol === 'repartidor')
                <a href="{{ route('pedidos.index', array_filter(['estado' => 'listo_entrega'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado') === 'listo_entrega' ? 'ring-2 ring-amber-400' : '' }}">Listos para recoger</a>
                <a href="{{ route('pedidos.index', array_filter(['estado' => 'en_transporte'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado') === 'en_transporte' ? 'ring-2 ring-amber-400' : '' }}">En transporte</a>
            @endif
            @if($rol === 'almacenero')
                <a href="{{ route('pedidos.index', array_filter(['estado' => 'en_transporte'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado') === 'en_transporte' ? 'ring-2 ring-amber-400' : '' }}">Por recibir</a>
                <a href="{{ route('pedidos.index', array_filter(['estado' => 'listo_recoger'])) }}" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd] {{ request('estado') === 'listo_recoger' ? 'ring-2 ring-amber-400' : '' }}">Listos recoger</a>
            @endif
        </div>

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
                                    'nombre_producto' => $pedido->nombre_producto ?: '-',
                                    'descripcion' => $pedido->detalle_trabajo ?: '-',
                                    'nombre_cliente' => $pedido->nombre_cliente,
                                    'cliente_catalogo' => $pedido->cliente?->id ? ('Cliente catalogo #' . $pedido->cliente->id) : null,
                                    'telefono_cliente' => $pedido->telefono_cliente ?: '-',
                                    'documento_cliente' => $pedido->documento_cliente ?: '-',
                                    'correo_cliente' => $pedido->correo_cliente ?: '-',
                                    'cantidad' => $pedido->productos->isNotEmpty() ? $pedido->productos->sum('cantidad') : $pedido->cantidad,
                                    'productos' => $pedido->productos->map(fn($pp) => [
                                        'nombre' => $pp->nombre,
                                        'descripcion' => $pp->descripcion ?? '-',
                                        'precio_unitario' => (float) $pp->precio_unitario,
                                        'cantidad' => $pp->cantidad,
                                        'total' => (float) $pp->total,
                                        'archivos' => $pp->archivos->map(fn($a) => [
                                            'nombre_original' => $a->nombre_original,
                                            'url' => asset('storage/' . $a->archivo_path),
                                        ]),
                                    ])->toArray(),
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
                                    'cobro_url' => route('pedidos.confirmar_pago_final', $pedido),
                                    'monto_total_raw' => (float) ($pedido->monto_total ?? 0),
                                    'monto_adelanto_raw' => (float) ($pedido->monto_adelanto ?? 0),
                                    'monto_saldo_raw' => (float) ($pedido->monto_saldo ?? 0),
                                    'tipo_pago' => $pedido->tipo_pago ?? 'dos_partes',
                                    'derivar_url' => route('pedidos.derivar', $pedido),
                                    'estado_personalizacion_raw' => $pedido->estado_personalizacion ?? 'sin_iniciar',
                                    'estado_raw' => $pedido->estado,
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
                                <td class="px-4 py-3 text-[#4a4026]">
                                    @php
                                        $prodText = $pedido->nombre_producto ?: $pedido->tipo_producto;
                                        if ($pedido->productos->isNotEmpty()) {
                                            $prodText = $pedido->productos->pluck('nombre')->implode(', ');
                                        }
                                    @endphp
                                    {{ $prodText }}
                                </td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->productos->isNotEmpty() ? $pedido->productos->sum('cantidad') : $pedido->cantidad }}</td>
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
                                        @if(in_array($rol, ['administrador', 'vendedor'], true) && $pedido->estado !== 'en_almacen' && $pedido->estado_pago === 'adelanto_pagado' && (float) ($pedido->monto_saldo ?? 0) > 0)
                                            <button type="button"
                                                @click="cobroData = @js($pedidoVistaData); modalCobro = true"
                                                class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700"
                                                title="Cobrar 50%">
                                                <img src="{{ asset('icons/cobro-blanco.png') }}" class="h-4 w-4 object-contain pointer-events-none" alt="Cobrar">
                                            </button>
                                        @endif
                                        @if(in_array($rol, ['administrador', 'vendedor'], true))
                                            <button type="button"
                                                @click="derivarData = @js($pedidoVistaData); modalDerivar = true"
                                                class="btn-icon-sm" style="background-color:#7c3aed"
                                                title="Derivar">
                                                <img src="{{ asset('icons/Derivar-Blanco.png') }}" class="h-4 w-4 object-contain pointer-events-none" alt="Derivar">
                                            </button>
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
            @if(method_exists($pedidos, 'links'))
            <div class="border-t border-[#efe7d2] px-4 py-3">{{ $pedidos->links('pagination.gold') }}</div>
            @endif
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
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Nombre del pedido</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.nombre_producto"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Descripcion</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.descripcion"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cantidad</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="pedidoVista?.cantidad"></p>
                    </div>
                    <div class="md:col-span-2" x-show="pedidoVista?.productos?.length">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Productos personalizados</p>
                        <div class="mt-2 overflow-x-auto rounded-xl border border-[#e9dec2]">
                            <table class="min-w-full text-sm">
                                <thead class="bg-[#faf6ea] text-left text-[#5a4a2a]">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold">#</th>
                                        <th class="px-3 py-2 font-semibold">Nombre</th>
                                        <th class="px-3 py-2 font-semibold">Descripcion</th>
                                        <th class="px-3 py-2 font-semibold">P.Unit</th>
                                        <th class="px-3 py-2 font-semibold">Cant</th>
                                        <th class="px-3 py-2 font-semibold">Total</th>
                                        <th class="px-3 py-2 font-semibold">Diseno</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#efeee9]">
                                    <template x-for="(pp, idx) in (pedidoVista?.productos || [])" :key="idx">
                                        <tr>
                                            <td class="px-3 py-2 text-center text-[#8a7a5a]" x-text="idx + 1"></td>
                                            <td class="px-3 py-2 font-medium text-[#4a4026]" x-text="pp.nombre"></td>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="pp.descripcion || '-'"></td>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="'S/ ' + (Number(pp.precio_unitario) || 0).toFixed(2)"></td>
                                            <td class="px-3 py-2 text-[#4a4026]" x-text="pp.cantidad"></td>
                                            <td class="px-3 py-2 font-semibold text-[#4a4026]" x-text="'S/ ' + (Number(pp.total) || 0).toFixed(2)"></td>
                                            <td class="px-3 py-2 text-[#4a4026]">
                                                <template x-if="pp.archivos?.length">
                                                    <div class="flex flex-wrap gap-1">
                                                        <template x-for="a in pp.archivos" :key="a.url">
                                                            <a :href="a.url" target="_blank" class="inline-flex items-center rounded bg-amber-50 px-2 py-0.5 text-xs text-amber-700 hover:bg-amber-100" x-text="a.nombre_original.length > 12 ? a.nombre_original.substring(0, 12) + '...' : a.nombre_original"></a>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!pp.archivos?.length">
                                                    <span class="text-gray-400">-</span>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
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

        {{-- Modal cobrar 50% y cerrar --}}
        <template x-teleport="body">
            <div x-show="modalCobro" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="modalCobro = false"></div>
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="w-full max-w-lg rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                        <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('icons/cobro-blanco.png') }}" class="h-5 w-5" alt="">
                                <h3 class="text-base font-semibold text-[#2a2419]">Cobrar 50%</h3>
                            </div>
                            <button type="button" @click="modalCobro = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                            </button>
                        </div>

                        <div class="p-5 space-y-4" x-show="cobroData">
                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cliente</p>
                                    <p class="mt-1 text-sm text-[#1f1f1f]" x-text="cobroData?.nombre_cliente"></p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Codigo</p>
                                    <p class="mt-1 text-sm text-[#1f1f1f]" x-text="cobroData?.codigo"></p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Monto total</p>
                                    <p class="mt-1 text-sm text-[#1f1f1f]" x-text="cobroData?.monto_total"></p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Adelanto pagado</p>
                                    <p class="mt-1 text-sm text-[#1f1f1f]" x-text="cobroData?.monto_cancelado"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Forma de pago</p>
                                    <p class="mt-1 text-sm font-semibold text-[#1f1f1f]" x-text="cobroData?.tipo_pago === 'contado' ? 'Contado (100%)' : 'En 2 Partes (50% + 50%)'"></p>
                                </div>
                                <div class="md:col-span-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-700">A cobrar ahora</p>
                                    <p class="mt-1 text-xl font-bold text-emerald-800" x-text="'S/ ' + (cobroData?.monto_saldo_raw || 0).toFixed(2)"></p>
                                </div>
                            </div>

                            <div>
                                <p class="mb-2 text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Metodo de pago</p>
                                <div class="flex flex-wrap gap-3">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="radio" name="metodo_pago_modal" value="efectivo" x-model="metodoPago" class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm text-gray-700">Efectivo</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="radio" name="metodo_pago_modal" value="yape" x-model="metodoPago" class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm text-gray-700">Yape</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="radio" name="metodo_pago_modal" value="plin" x-model="metodoPago" class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm text-gray-700">Plin</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="radio" name="metodo_pago_modal" value="tarjeta" x-model="metodoPago" class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm text-gray-700">Tarjeta</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="radio" name="metodo_pago_modal" value="transferencia" x-model="metodoPago" class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm text-gray-700">Transferencia</span>
                                    </label>
                                </div>
                                <div x-show="metodoPago === 'efectivo'" style="display: none;" class="grid grid-cols-2 gap-3 mt-2">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">Monto recibido</label>
                                        <input type="number" step="0.01" min="0" x-model="montoRecibido"
                                               class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900"
                                               placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-600">Vuelto</label>
                                        <p class="mt-2 text-lg font-bold text-emerald-700" x-text="'S/ ' + Math.max(0, Number(montoRecibido) - Number(cobroData?.monto_saldo_raw || 0)).toFixed(2)"></p>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" x-bind:action="cobroData?.cobro_url">
                                @csrf
                                <input type="hidden" name="metodo_pago" x-model="metodoPago">
                                <input type="hidden" name="monto_recibido" x-model="montoRecibido">
                                <input type="hidden" name="vuelto" x-model="Math.max(0, Number(montoRecibido) - Number(cobroData?.monto_saldo_raw || 0)).toFixed(2)">
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="modalCobro = false" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                        <img src="{{ asset('icons/cobro-blanco.png') }}" class="h-4 w-4" alt="">
                                        Confirmar pago
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Derivar --}}
        <template x-teleport="body">
            <div x-show="modalDerivar" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="modalDerivar = false"></div>
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                            <h3 class="text-base font-semibold text-[#2a2419]">Derivar pedido</h3>
                            <button type="button" @click="modalDerivar = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                            </button>
                        </div>
                        <div class="space-y-3 p-5">
                            <p class="text-sm text-gray-600" x-text="'Selecciona el destino para el pedido ' + (derivarData?.codigo || '')"></p>
                            <div class="grid grid-cols-2 gap-3">
                                <form method="POST" x-bind:action="derivarData?.derivar_url">
                                    @csrf
                                    <input type="hidden" name="destino" value="diseno">
                                    <button type="submit"
                                        class="flex w-full flex-col items-center gap-2 rounded-xl px-4 py-5 text-sm font-medium text-white shadow-sm bg-amber-600 hover:bg-amber-700 border-0"
                                        :class="derivarData?.estado_personalizacion_raw !== 'sin_iniciar' ? 'opacity-40 cursor-not-allowed' : ''"
                                        :disabled="derivarData?.estado_personalizacion_raw !== 'sin_iniciar'">
                                        <img src="{{ asset('icons/Disenos-Blanco.png') }}" class="h-8 w-8 object-contain" alt="">
                                        <span>A Diseño</span>
                                        <span class="text-xs text-amber-200" x-show="derivarData?.estado_personalizacion_raw !== 'sin_iniciar'">Ya derivado</span>
                                    </button>
                                </form>
                                <form method="POST" x-bind:action="derivarData?.derivar_url">
                                    @csrf
                                    <input type="hidden" name="destino" value="produccion">
                                    <button type="submit"
                                        class="flex w-full flex-col items-center gap-2 rounded-xl px-4 py-5 text-sm font-medium text-white shadow-sm bg-emerald-600 hover:bg-emerald-700 border-0"
                                        :class="derivarData?.estado_raw !== 'registrado' ? 'opacity-40 cursor-not-allowed' : ''"
                                        :disabled="derivarData?.estado_raw !== 'registrado'">
                                        <img src="{{ asset('icons/Produccion-Blanco.png') }}" class="h-8 w-8 object-contain" alt="">
                                        <span>A Producción</span>
                                        <span class="text-xs text-gray-500" x-show="derivarData?.estado_raw !== 'registrado'">Ya derivado</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal seleccionar caja --}}
        <template x-teleport="body">
            <div x-show="selectorCajaAbierto" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="selectorCajaAbierto = false"></div>
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                            <h3 class="text-base font-semibold text-gray-800">Selecciona tu caja de trabajo</h3>
                            <a href="{{ route('cajas.index') }}" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Ir a abrir caja">
                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                            </a>
                        </div>
                        <div class="p-5 space-y-3">
                            <p class="text-sm text-gray-500">Elige la caja en la que deseas operar.</p>
                            @if(isset($cajasAbiertas) && $cajasAbiertas->isNotEmpty())
                            @foreach ($cajasAbiertas as $cajaItem)
                                <a
                                    href="{{ route('pedidos.seleccionar_caja', $cajaItem) }}"
                                    class="flex items-center justify-between rounded-2xl border border-[#d1be8a] bg-[#fffdf7] p-4 shadow-sm transition hover:border-[#b8953a] hover:shadow-md"
                                >
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-[#2a2419]">{{ $cajaItem->nombre ?? 'Caja #'.$cajaItem->id }}</p>
                                        <p class="text-xs text-gray-500">Abierta: {{ $cajaItem->fecha_apertura->format('d/m/Y H:i') }}</p>
                                        @if ($cajaItem->monto_inicial > 0)
                                            <p class="text-xs text-gray-500">Monto inicial: S/ {{ number_format($cajaItem->monto_inicial, 2) }}</p>
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal sin caja abierta --}}
        <template x-teleport="body">
            <div x-show="sinCajaAbierto" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50"></div>
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                        <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                            <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Alerta" class="h-8 w-8 object-contain pointer-events-none" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">No hay ninguna caja abierta</h3>
                        <p class="mt-2 text-sm text-gray-500">Ve al modulo de caja, abre una caja y vuelve para empezar a registrar pedidos.</p>
                        <a href="{{ route('cajas.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px"><img src="{{ asset('icons/Ventas-Blanco.png') }}" alt="" class="h-5 w-5 object-contain pointer-events-none" /> Ir a abrir caja</a>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal pedido registrado correctamente --}}
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

        {{-- Modal errores validacion --}}
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
