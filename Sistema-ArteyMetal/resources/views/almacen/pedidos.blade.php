<x-app-layout>
    <x-slot name="header">
        <span>Almacen — Pedidos</span>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .btn-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: 0.75rem;
            flex-shrink: 0; color: #fff; transition: filter 0.15s;
        }
        .btn-icon:active { filter: brightness(0.85); }
        .btn-icon:focus, .btn-icon:focus-visible { outline: 0 none !important; }
        .btn-icon-sm {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem; border-radius: 0.5rem;
            flex-shrink: 0; color: #fff; transition: filter 0.15s;
        }
        .btn-icon-sm:active { filter: brightness(0.85); }
        .btn-icon-sm:focus, .btn-icon-sm:focus-visible { outline: 0 none !important; }
    </style>

    <div x-data="almacenPedidos()" x-init="init()" class="space-y-5">

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 flex gap-2">
                <div class="flex-1 relative">
                    <input x-model="q" type="text" placeholder="Buscar por codigo o cliente..."
                        class="w-full rounded-xl border border-[#d4cfc0] bg-white px-4 py-2.5 pr-10 text-sm text-[#2d2b24] placeholder:text-[#9a8e78] focus:border-[#b9943d] focus:ring-1 focus:ring-[#b9943d]/40"
                        x-on:keyup.enter="buscar()">
                </div>
                <button type="button" x-on:click="buscar()" title="Buscar"
                    class="btn-icon bg-[#b9943d] hover:bg-[#a68535]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
            <div class="relative" x-data="{ open: false }">
                <button x-on:click="open = !open" title="Filtrar por estado"
                    class="btn-icon bg-[#5a4a2a] hover:bg-[#4a3a1a] relative"
                    :class="{ 'ring-2 ring-[#b9943d]': filtroEstado !== '' }">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                </button>
                <div x-show="open" x-on:click.away="open = false" x-cloak
                    class="absolute right-0 z-30 mt-2 w-56 rounded-xl border border-[#e5dec8] bg-white py-2 shadow-xl">
                    <button x-on:click="filtroEstado = ''; open = false; buscar()"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-[#f4ebd4] transition-colors"
                        :class="filtroEstado === '' ? 'font-semibold text-[#b9943d]' : 'text-[#4a4026]'">
                        Todos
                    </button>
                    <button x-on:click="filtroEstado = 'en_almacen'; open = false; buscar()"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-[#f4ebd4] transition-colors"
                        :class="filtroEstado === 'en_almacen' ? 'font-semibold text-[#b9943d]' : 'text-[#4a4026]'">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Pendiente de recibir
                    </button>
                    <button x-on:click="filtroEstado = 'listo_recoger'; open = false; buscar()"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-[#f4ebd4] transition-colors"
                        :class="filtroEstado === 'listo_recoger' ? 'font-semibold text-[#b9943d]' : 'text-[#4a4026]'">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Recibido en almacen
                    </button>
                    <button x-on:click="filtroEstado = 'entregado'; open = false; buscar()"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-[#f4ebd4] transition-colors"
                        :class="filtroEstado === 'entregado' ? 'font-semibold text-[#b9943d]' : 'text-[#4a4026]'">
                        <span class="inline-block w-2 h-2 rounded-full bg-purple-500 mr-2"></span> Entregado al cliente
                    </button>
                </div>
            </div>
            @if($busqueda !== '' || $filtroEstado !== '')
                <button x-on:click="q = ''; filtroEstado = ''; buscar()" title="Limpiar filtros"
                    class="btn-icon bg-[#d4534a] hover:bg-[#c0392b]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Codigo</th>
                            <th class="px-4 py-3 font-semibold">Cliente</th>
                            <th class="px-4 py-3 font-semibold">Productos</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse($pedidos as $pedido)
                            @php $cantRecoge = $pedido->productos->sum('cantidad_recoge') ?: $pedido->productos->sum('cantidad'); @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->nombre_cliente }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">
                                    @if($pedido->productos->count() > 1)
                                        <div>
                                            @foreach($pedido->productos as $i => $p)
                                                <div>{{ $i + 1 }}. {{ $p->nombre }} ({{ $p->cantidad_recoge ?? $p->cantidad }})</div>
                                            @endforeach
                                        </div>
                                    @else
                                        @php $first = $pedido->productos->first(); @endphp
                                        {{ $first?->nombre ?? '-' }} ({{ $first?->cantidad_recoge ?? $first?->cantidad ?? $pedido->cantidad }})
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($pedido->estado === 'en_almacen')
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Pendiente de recibir</span>
                                    @elseif($pedido->estado === 'listo_recoger')
                                        <span class="rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">Recibido en almacen</span>
                                    @elseif($pedido->estado === 'entregado')
                                        <span class="rounded-lg bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-700">Entregado</span>
                                    @else
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ $pedido->estado }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button x-on:click="verDetalle({{ $pedido->id }})" title="Ver detalle"
                                            class="btn-icon-sm bg-blue-600 hover:bg-blue-700">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        @if($pedido->estado === 'en_almacen')
                                            <button x-on:click="abrirRecibir({{ $pedido->id }}, '{{ $pedido->codigo }}', {{ $cantRecoge }})" title="Recibir en almacen"
                                                class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        @endif
                                        @if($pedido->estado === 'listo_recoger')
                                            <button x-on:click="abrirEntregar({{ $pedido->id }}, '{{ $pedido->codigo }}', '{{ $pedido->estado_pago }}', {{ $pedido->monto_total }}, {{ $pedido->monto_saldo }})" title="Entregar al cliente"
                                                @if(($pedido->estado_pago ?? '') !== 'pagado_completo') class="btn-icon-sm bg-gray-400 cursor-not-allowed" @else class="btn-icon-sm bg-amber-600 hover:bg-amber-700" @endif>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m-8-4v10l8 4m0-10v10"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[#777]">No hay pedidos en almacen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-1" x-html="paginationHtml">
            {{ $pedidos->links() }}
        </div>

        <div x-show="modalDetalle" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-on:keydown.escape.window="modalDetalle = false"
            x-on:click.self="modalDetalle = false">
            <div x-show="modalDetalle" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Detalle del Pedido</h3>
                    <button x-on:click="modalDetalle = false" class="btn-icon-sm bg-red-600 hover:bg-red-700">
                        <img src="{{ asset('iconos/cerrar.ico') }}" alt="Cerrar" class="h-5 w-5">
                    </button>
                </div>
                <template x-if="detallePedido">
                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="font-semibold text-[#5a4a2a]">Codigo:</span> <span x-text="detallePedido.codigo" class="text-[#2d2b24]"></span></div>
                            <div><span class="font-semibold text-[#5a4a2a]">Estado:</span> <span x-text="detallePedido.estado" class="text-[#2d2b24]"></span></div>
                            <div><span class="font-semibold text-[#5a4a2a]">Cliente:</span> <span x-text="detallePedido.nombre_cliente" class="text-[#2d2b24]"></span></div>
                            <div><span class="font-semibold text-[#5a4a2a]">Tipo:</span> <span x-text="detallePedido.tipo_producto" class="text-[#2d2b24]"></span></div>
                            <div class="col-span-2"><span class="font-semibold text-[#5a4a2a]">Direccion:</span> <span x-text="detallePedido.direccion_entrega || 'No especificada'" class="text-[#2d2b24]"></span></div>
                        </div>
                        <div>
                            <h4 class="mb-2 font-semibold text-[#5a4a2a]">Productos</h4>
                            <table class="min-w-full text-xs">
                                <thead class="bg-[#faf8f2]"><tr><th class="px-3 py-2 text-left">Producto</th><th class="px-3 py-2 text-right">Cant.</th><th class="px-3 py-2 text-right">Recoge</th></tr></thead>
                                <tbody class="divide-y divide-[#efeee9]">
                                    <template x-for="prod in detallePedido.productos" :key="prod.id">
                                        <tr>
                                            <td class="px-3 py-2" x-text="prod.nombre"></td>
                                            <td class="px-3 py-2 text-right" x-text="prod.cantidad"></td>
                                            <td class="px-3 py-2 text-right" x-text="prod.cantidad_recoge || prod.cantidad"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="modalRecibir" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-on:keydown.escape.window="modalRecibir = false"
            x-on:click.self="modalRecibir = false">
            <div x-show="modalRecibir" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Recibir en Almacen</h3>
                    <button x-on:click="modalRecibir = false" class="btn-icon-sm bg-red-600 hover:bg-red-700">
                        <img src="{{ asset('iconos/cerrar.ico') }}" alt="Cerrar" class="h-5 w-5">
                    </button>
                </div>
                <p class="mb-4 text-sm text-[#4a4026]">
                    Recibir el pedido <strong x-text="recibirCodigo"></strong> en el almacen? Se registrara la entrada de <strong x-text="recibirCantidad"></strong> unidades al stock.
                </p>
                <div class="flex justify-end gap-2">
                    <button x-on:click="modalRecibir = false"
                        class="rounded-xl border border-[#d4cfc0] bg-white px-4 py-2 text-sm font-medium text-[#5a4a2a] hover:bg-[#f4ebd4]">
                        Cancelar
                    </button>
                    <button x-on:click="confirmarRecibir()" :disabled="procesando"
                        class="rounded-xl bg-[#059669] px-4 py-2 text-sm font-medium text-white hover:bg-[#047857] disabled:opacity-50">
                        <span x-show="!procesando">Si, recibir</span>
                        <span x-show="procesando" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="modalEntregar" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-on:keydown.escape.window="modalEntregar = false"
            x-on:click.self="modalEntregar = false">
            <div x-show="modalEntregar" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Entregar al Cliente</h3>
                    <button x-on:click="modalEntregar = false" class="btn-icon-sm bg-red-600 hover:bg-red-700">
                        <img src="{{ asset('iconos/cerrar.ico') }}" alt="Cerrar" class="h-5 w-5">
                    </button>
                </div>
                <p class="mb-4 text-sm text-[#4a4026]">
                    Entregar el pedido <strong x-text="entregarCodigo"></strong> al cliente? Se dara salida del almacen.
                </p>

                <div class="mb-4 rounded-xl border p-3"
                    :class="entregarEstadoPago === 'pagado_completo' ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                    <p class="text-xs font-semibold uppercase tracking-wider"
                        :class="entregarEstadoPago === 'pagado_completo' ? 'text-emerald-700' : 'text-red-700'">Estado del pago</p>
                    <p class="mt-1 text-sm font-semibold"
                        :class="entregarEstadoPago === 'pagado_completo' ? 'text-emerald-800' : 'text-red-800'"
                        x-text="entregarEstadoPago === 'pagado_completo' ? 'Pagado completo' : (entregarEstadoPago === 'adelanto_pagado' ? 'Solo adelanto pagado' : 'Pendiente de pago')"></p>
                    <template x-if="entregarEstadoPago !== 'pagado_completo'">
                        <div>
                            <p class="mt-1 text-xs text-red-600">Saldo pendiente: <span class="font-semibold" x-text="'S/ ' + Number(entregarSaldo).toFixed(2)"></span></p>
                            <p class="mt-2 text-xs text-red-700 font-medium">No se puede entregar el pedido hasta que el pago este completo.</p>
                        </div>
                    </template>
                    <template x-if="entregarEstadoPago === 'pagado_completo'">
                        <p class="mt-1 text-xs text-emerald-600">Total: <span class="font-semibold" x-text="'S/ ' + Number(entregarMontoTotal).toFixed(2)"></span></p>
                    </template>
                </div>

                <div class="flex justify-end gap-2">
                    <button x-on:click="modalEntregar = false"
                        class="rounded-xl border border-[#d4cfc0] bg-white px-4 py-2 text-sm font-medium text-[#5a4a2a] hover:bg-[#f4ebd4]">
                        Cancelar
                    </button>
                    <button x-on:click="confirmarEntregar()" :disabled="procesando || entregarEstadoPago !== 'pagado_completo'"
                        class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 disabled:opacity-50">
                        <span x-show="!procesando">Si, entregar</span>
                        <span x-show="procesando" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mostrarExito" x-cloak x-transition
            class="fixed bottom-6 right-6 z-[60] max-w-sm rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-xl">
            <div class="flex items-start gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-emerald-800">Operacion exitosa</p>
                    <p class="mt-1 text-sm text-emerald-700" x-text="mensajeExito"></p>
                </div>
                <button x-on:click="mostrarExito = false" class="ml-2 flex-shrink-0 text-emerald-400 hover:text-emerald-600">
                    <img src="{{ asset('iconos/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4">
                </button>
            </div>
        </div>

        <div x-show="mostrarError" x-cloak x-transition
            class="fixed bottom-6 right-6 z-[60] max-w-sm rounded-2xl border border-red-200 bg-red-50 p-4 shadow-xl">
            <div class="flex items-start gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-800">Error</p>
                    <p class="mt-1 text-sm text-red-700" x-text="mensajeError"></p>
                </div>
                <button x-on:click="mostrarError = false" class="ml-2 flex-shrink-0 text-red-400 hover:text-red-600">
                    <img src="{{ asset('iconos/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4">
                </button>
            </div>
        </div>
    </div>

    <script>
        function almacenPedidos() {
            return {
                q: '{{ $busqueda ?? "" }}',
                filtroEstado: '{{ $filtroEstado ?? "" }}',
                paginationHtml: '',
                modalDetalle: false,
                modalRecibir: false,
                modalEntregar: false,
                detallePedido: null,
                recibirId: null,
                recibirCodigo: '',
                recibirCantidad: 0,
                entregarId: null,
                entregarCodigo: '',
                entregarEstadoPago: '',
                entregarMontoTotal: 0,
                entregarSaldo: 0,
                procesando: false,
                mostrarExito: false,
                mostrarError: false,
                mensajeExito: '',
                mensajeError: '',

                init() {
                    this.paginationHtml = '{{ $pedidos->links("pagination.gold")->toHtml() }}';
                },

                buscar() {
                    const params = new URLSearchParams();
                    if (this.q) params.set('q', this.q);
                    if (this.filtroEstado) params.set('estado', this.filtroEstado);
                    const url = '{{ route("almacen.pedidos") }}?' + params.toString();
                    window.location.href = url;
                },

                async verDetalle(pedidoId) {
                    try {
                        const resp = await fetch('/pedidos/' + pedidoId, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await resp.json();
                        this.detallePedido = data;
                        this.modalDetalle = true;
                    } catch (e) {
                        this.error('No se pudo cargar el detalle del pedido.');
                    }
                },

                abrirRecibir(id, codigo, cantidad) {
                    this.recibirId = id;
                    this.recibirCodigo = codigo;
                    this.recibirCantidad = cantidad;
                    this.modalRecibir = true;
                },

                async confirmarRecibir() {
                    this.procesando = true;
                    try {
                        const resp = await fetch('/almacen/pedidos/' + this.recibirId + '/recibir', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await resp.json();
                        this.modalRecibir = false;
                        if (data.ok) {
                            this.exito(data.message || 'Pedido recibido correctamente.');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            this.error(data.message || 'No se pudo recibir el pedido.');
                        }
                    } catch (e) {
                        this.modalRecibir = false;
                        this.error('Error de conexion al recibir el pedido.');
                    }
                    this.procesando = false;
                },

                abrirEntregar(id, codigo, estadoPago, montoTotal, saldo) {
                    this.entregarId = id;
                    this.entregarCodigo = codigo;
                    this.entregarEstadoPago = estadoPago;
                    this.entregarMontoTotal = montoTotal;
                    this.entregarSaldo = saldo;
                    this.modalEntregar = true;
                },

                async confirmarEntregar() {
                    this.procesando = true;
                    try {
                        const resp = await fetch('/almacen/pedidos/' + this.entregarId + '/entregar-cliente', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await resp.json();
                        this.modalEntregar = false;
                        if (data.ok) {
                            this.exito(data.message || 'Pedido entregado al cliente.');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            this.error(data.message || 'No se pudo entregar el pedido.');
                        }
                    } catch (e) {
                        this.modalEntregar = false;
                        this.error('Error de conexion al entregar el pedido.');
                    }
                    this.procesando = false;
                },

                exito(msg) {
                    this.mensajeExito = msg;
                    this.mostrarExito = true;
                    this.mostrarError = false;
                    setTimeout(() => { this.mostrarExito = false; }, 4000);
                },

                error(msg) {
                    this.mensajeError = msg;
                    this.mostrarError = true;
                    this.mostrarExito = false;
                    setTimeout(() => { this.mostrarError = false; }, 4000);
                }
            }
        }
    </script>
</x-app-layout>
