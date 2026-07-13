<x-app-layout>
    <x-slot name="header">
        <span>Repartidor</span>
    </x-slot>

    <style>
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
        .btn-icon:active { filter: brightness(0.85); }
        .btn-icon:focus,
        .btn-icon:focus-visible { outline: 0 none !important; }
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
        .btn-icon-sm:active { filter: brightness(0.85); }
        .btn-icon-sm:focus,
        .btn-icon-sm:focus-visible { outline: 0 none !important; }
    </style>

    <div x-data="{
        showSuccess: {{ session()->has('ok') ? 'true' : 'false' }},
        successMessage: @js(session('ok', '')),
        showError: false,
        errorMessage: ''
    }" class="space-y-5">

        {{-- Barra de busqueda + filtros --}}
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <form id="repartidor-search-form" method="GET" action="{{ route('repartidor.index') }}" class="flex min-w-0 flex-1">
                    @if($filtroEstado)
                        <input type="hidden" name="estado" value="{{ $filtroEstado }}" />
                    @endif
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="Buscar por codigo o cliente" />
                </form>
                <button type="submit" form="repartidor-search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <div x-data="{ filtrosAbiertos: false }" class="relative">
                    <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                        class="btn-icon bg-sky-500 hover:bg-sky-600 {{ $filtroEstado ? 'ring-2 ring-sky-400' : '' }}" title="Filtrar">
                        <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                    </button>
                    <div x-show="filtrosAbiertos" x-cloak @click.outside="filtrosAbiertos = false"
                         class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl border border-[#e5dec8] bg-white p-3 shadow-lg">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Estado</p>
                        <div class="space-y-1">
                            <a href="{{ route('repartidor.index', array_filter(['q' => $busqueda])) }}"
                               class="block rounded-lg px-3 py-1.5 text-sm {{ !$filtroEstado ? 'bg-amber-100 text-amber-800 font-medium' : 'text-[#555] hover:bg-[#f5f3ed]' }}">Todos</a>
                            <a href="{{ route('repartidor.index', array_filter(['q' => $busqueda, 'estado' => 'listo_entrega'])) }}"
                               class="block rounded-lg px-3 py-1.5 text-sm {{ $filtroEstado === 'listo_entrega' ? 'bg-amber-100 text-amber-800 font-medium' : 'text-[#555] hover:bg-[#f5f3ed]' }}">Listo para recoger</a>
                            <a href="{{ route('repartidor.index', array_filter(['q' => $busqueda, 'estado' => 'en_transporte'])) }}"
                               class="block rounded-lg px-3 py-1.5 text-sm {{ $filtroEstado === 'en_transporte' ? 'bg-sky-100 text-sky-800 font-medium' : 'text-[#555] hover:bg-[#f5f3ed]' }}">En transporte</a>
                        </div>
                    </div>
                </div>
                @if($busqueda || $filtroEstado)
                    <a href="{{ route('repartidor.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
            </div>
        </div>

        {{-- Tabla --}}
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
                            @php
                                $productosText = $pedido->productos->isNotEmpty()
                                    ? $pedido->productos->pluck('nombre')->implode(', ')
                                    : '-';
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->nombre_cliente }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">
                                    @if($pedido->productos->count() > 1)
                                        <div>
                                            @foreach($pedido->productos as $i => $p)
                                                <div>{{ $i + 1 }}. {{ $p->nombre }}</div>
                                            @endforeach
                                        </div>
                                    @else
                                        {{ $productosText }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($pedido->estado === 'listo_entrega')
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Listo para recoger</span>
                                    @elseif($pedido->estado === 'en_transporte')
                                        <span class="rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En transporte</span>
                                    @else
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ $pedido->estado }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="$dispatch('open-detalle-{{ $pedido->id }}')" class="btn-icon-sm" style="background-color:#0891B2" title="Ver detalle">
                                            <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none">
                                        </button>
                                        @if($pedido->estado === 'listo_entrega')
                                            <button type="button"
                                                @click="$dispatch('open-recoger-{{ $pedido->id }}')"
                                                class="btn-icon-sm bg-sky-600 hover:bg-sky-700" title="Recoger pedido">
                                                <svg class="h-4 w-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        @endif
                                        @if($pedido->estado === 'en_transporte')
                                            <button type="button"
                                                @click="$dispatch('open-entregar-{{ $pedido->id }}')"
                                                class="btn-icon-sm bg-amber-600 hover:bg-amber-700" title="Entregar en almacen">
                                                <svg class="h-4 w-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m8-4v10l-8 4m0-10v10l8 4m0-10v10l8 4"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[#777]">No hay pedidos pendientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="px-1">
            {{ $pedidos->links() }}
        </div>
    </div>

    {{-- Modales por cada pedido --}}
    @foreach($pedidos as $pedido)
        @php
            $refs = $pedido->productos->flatMap(function ($p) {
                return $p->archivos->map(function ($a) use ($p) {
                    return [
                        'tipo' => 'referencia',
                        'url' => asset('storage/' . $a->archivo_path),
                        'nombre' => $a->nombre_original,
                        'mime' => $a->mime_type,
                        'producto' => $p->nombre,
                    ];
                });
            });
            $disenos = $pedido->archivosDiseno->map(function ($a) {
                return [
                    'tipo' => 'diseno',
                    'url' => asset('storage/' . $a->archivo_path),
                    'nombre' => $a->nombre_original,
                    'mime' => $a->mime_type,
                    'producto' => 'Diseno',
                ];
            });
            $viewerFiles = $refs->concat($disenos);
        @endphp

        {{-- Modal Ver Detalle --}}
        <div x-data="{
            open: false,
            viewerOpen: false,
            viewerIndex: 0,
            viewerFiles: {{ Js::from($viewerFiles) }},
            get viewerTotal() { return this.viewerFiles.length },
            get currentFile() { return this.viewerFiles[this.viewerIndex] },
            get esImagen() {
                if (!this.currentFile) return false;
                return ['image/png','image/jpeg','image/jpg','image/gif','image/svg+xml','image/webp'].includes(this.currentFile.mime);
            },
            prevFile() { if (this.viewerIndex > 0) this.viewerIndex-- },
            nextFile() { if (this.viewerIndex < this.viewerTotal - 1) this.viewerIndex++ }
        }"
             x-show="open"
             x-on:open-detalle-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="open = false" class="mx-4 flex max-h-[85vh] w-full max-w-3xl flex-col rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-[#e5dec8] px-6 py-4">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Repartidor — {{ $pedido->codigo }}</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="viewerOpen = true; viewerIndex = 0" class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700" title="Ver modelos">
                            <img src="{{ asset('icons/VerModelo-Blanco.png') }}" alt="Ver modelos" class="h-4 w-4 object-contain pointer-events-none">
                        </button>
                        <button type="button" @click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                            <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Cliente</p>
                            <p class="mt-1 text-[#2d2b24]">{{ $pedido->nombre_cliente }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Estado</p>
                            @if($pedido->estado === 'listo_entrega')
                                <span class="mt-1 inline-block rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Listo para recoger</span>
                            @elseif($pedido->estado === 'en_transporte')
                                <span class="mt-1 inline-block rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En transporte</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Tipo producto</p>
                            <p class="mt-1 text-[#2d2b24]">{{ $pedido->tipo_producto }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Tipo entrega</p>
                            <p class="mt-1 text-[#2d2b24]">{{ $pedido->tipo_entrega ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Detalle del trabajo</p>
                        <p class="mt-1 text-[#2d2b24]">{{ $pedido->detalle_trabajo ?: '-' }}</p>
                    </div>

                    @if($pedido->direccion_entrega)
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Direccion de entrega</p>
                        <p class="mt-1 text-[#2d2b24]">{{ $pedido->direccion_entrega }}@if($pedido->distrito_entrega), {{ $pedido->distrito_entrega }}@endif</p>
                    </div>
                    @endif

                    @if($pedido->productos->isNotEmpty())
                        <div class="mt-4 overflow-x-auto rounded-xl border border-[#e5dec8]">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-[#faf8f2] text-left text-xs font-semibold uppercase tracking-wider text-[#6a5122]">
                                        <th class="px-3 py-2">#</th>
                                        <th class="px-3 py-2">Nombre</th>
                                        <th class="px-3 py-2">Descripcion</th>
                                        <th class="px-3 py-2">Cant.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->productos as $idx => $pp)
                                        <tr class="border-t border-[#efeee9]">
                                            <td class="px-3 py-2 text-center text-[#999]">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-[#2d2b24]">{{ $pp->nombre }}</td>
                                            <td class="px-3 py-2 text-[#4a4026]">{{ $pp->descripcion ?? '-' }}</td>
                                            <td class="px-3 py-2 text-[#4a4026]">{{ $pp->cantidad }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Viewer --}}
            <div x-show="viewerOpen"
                 x-on:keydown.escape.window="viewerOpen = false"
                 x-on:keydown.left.window="viewerOpen && prevFile()"
                 x-on:keydown.right.window="viewerOpen && nextFile()"
                 x-cloak
                 class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60">
                <div @click.outside="viewerOpen = false" class="relative mx-4 w-full max-w-3xl rounded-2xl bg-[#1a1a1a] p-4 shadow-xl">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white/80">Modelos — {{ $pedido->codigo }}</h3>
                        <button type="button" @click="viewerOpen = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                            <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                        </button>
                    </div>
                    <template x-if="viewerTotal === 0">
                        <div class="flex h-64 items-center justify-center text-white/50">No hay archivos.</div>
                    </template>
                    <template x-if="viewerTotal > 0">
                        <div>
                            <div class="relative flex items-center">
                                <button x-show="viewerIndex > 0" @click="prevFile()"
                                        class="absolute left-0 z-10 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <div class="mx-auto flex h-80 w-full items-center justify-center overflow-hidden rounded-xl bg-black/40">
                                    <template x-if="esImagen">
                                        <img :src="currentFile.url" :alt="currentFile.nombre" class="max-h-full max-w-full object-contain">
                                    </template>
                                    <template x-if="!esImagen">
                                        <div class="flex flex-col items-center gap-3 text-center text-white/70">
                                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            <p class="text-sm" x-text="currentFile.nombre"></p>
                                            <a :href="currentFile.url" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white hover:bg-white/30">Descargar archivo</a>
                                        </div>
                                    </template>
                                </div>
                                <button x-show="viewerIndex < viewerTotal - 1" @click="nextFile()"
                                        class="absolute right-0 z-10 flex h-10 w-10 translate-x-1/2 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                            <div class="mt-3 flex items-center justify-center gap-3 text-xs text-white/60">
                                <span x-text="`${viewerIndex + 1} de ${viewerTotal}`"></span>
                                <span class="text-white/30">|</span>
                                <span x-text="currentFile?.tipo === 'referencia' ? 'Modelo cliente' : 'Diseno disenador'"></span>
                                <span class="text-white/30">|</span>
                                <span x-text="currentFile?.nombre || ''" class="max-w-[200px] truncate"></span>
                            </div>
                            <div class="mt-2 flex justify-center gap-1">
                                <template x-for="(_, i) in viewerFiles" :key="i">
                                    <button @click="viewerIndex = i"
                                            :class="i === viewerIndex ? 'bg-amber-500' : 'bg-white/20 hover:bg-white/40'"
                                            class="h-1.5 w-6 rounded-full transition-colors"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Modal Recoger Pedido --}}
        <div x-data="{ open: false }"
             x-show="open"
             x-on:open-recoger-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="open = false" class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Recoger pedido — {{ $pedido->codigo }}</h3>
                    <button type="button" @click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <form action="{{ route('repartidor.recoger', $pedido) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="overflow-x-auto rounded-xl border border-[#e5dec8]">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#faf8f2] text-left text-xs font-semibold uppercase tracking-wider text-[#6a5122]">
                                    <th class="px-3 py-2">Producto</th>
                                    <th class="px-3 py-2">Cant. pedido</th>
                                    <th class="px-3 py-2">Cant. a recoger</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->productos as $idx => $pp)
                                    <tr class="border-t border-[#efeee9]">
                                        <td class="px-3 py-2 font-medium text-[#2d2b24]">{{ $pp->nombre }}</td>
                                        <td class="px-3 py-2 text-[#4a4026]">{{ $pp->cantidad }}</td>
                                        <td class="px-3 py-2">
                                            <input type="hidden" name="productos[{{ $idx }}][id]" value="{{ $pp->id }}">
                                            <input type="number" name="productos[{{ $idx }}][cantidad_recoge]" value="{{ $pp->cantidad }}" min="1" max="{{ $pp->cantidad }}"
                                                   class="w-24 rounded-lg border border-[#e5dec8] px-3 py-1.5 text-sm text-[#2d2b24] focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open = false"
                                class="rounded-xl border border-[#d5d0c0] px-4 py-2 text-sm font-medium text-[#555] hover:bg-[#f5f3ed]">Cancelar</button>
                        <button type="submit"
                                class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Marcar recogido</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Entregar en Almacen --}}
        <div x-data="{ open: false }"
             x-show="open"
             x-on:open-entregar-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @click.outside="open = false" class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Entregar en almacen</h3>
                    <button type="button" @click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <div class="text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100">
                        <svg class="h-7 w-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m8-4v10l-8 4m0-10v10l8 4"/></svg>
                    </div>
                    <p class="text-sm text-gray-600">Entregar el pedido <strong>{{ $pedido->codigo }}</strong> en almacen y notificar al almacenero?</p>

                    <div class="mt-5 flex justify-center gap-3">
                        <button type="button" @click="open = false"
                                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Cancelar</button>
                        <button type="button"
                                @click="
                                    fetch('{{ route('repartidor.entregar_almacen', $pedido) }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        body: JSON.stringify({})
                                    })
                                    .then(r => r.json().then(d => ({ ok: r.ok, body: d })))
                                    .then(({ ok, body }) => {
                                        open = false;
                                        if (ok && body.ok) { successMessage = body.message; showSuccess = true; setTimeout(() => location.reload(), 1500); }
                                        else { errorMessage = body.message || 'Error al entregar.'; showError = true; }
                                    })
                                    .catch(() => { open = false; errorMessage = 'Error de conexion.'; showError = true; })
                                "
                                class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">Confirmar entrega</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Exito --}}
    <template x-teleport="body">
        <div x-show="showSuccess" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showSuccess = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                    <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                        <img src="{{ asset('icons/Valido-Verde.png') }}" alt="Valido" class="h-8 w-8 object-contain pointer-events-none" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" x-text="successMessage"></h3>
                    <button type="button" @click="showSuccess = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Modal Error --}}
    <template x-teleport="body">
        <div x-show="showError" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showError = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                    <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                        <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Alerta" class="h-8 w-8 object-contain pointer-events-none" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" x-text="errorMessage"></h3>
                    <button type="button" @click="showError = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                </div>
            </div>
        </div>
    </template>
</x-app-layout>
