<x-app-layout>
    <x-slot name="header">
        <span>Produccion</span>
    </x-slot>

    <style>
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

    <div class="space-y-3">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <form id="produccion-search-form" method="GET" action="{{ route('produccion.index') }}" class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 text-sm text-gray-900 h-10" placeholder="Buscar por codigo, cliente o producto" />
                </form>
                <button type="submit" form="produccion-search-form" class="h-10 w-10 rounded-xl bg-blue-600 hover:bg-blue-700 flex items-center justify-center shrink-0" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if($busqueda)
                    <a href="{{ route('produccion.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
            </div>
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
                            <th class="px-4 py-3 font-semibold">Estado diseno</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse($pedidos as $pedido)
                            @php
                                $productosText = $pedido->productos->isNotEmpty()
                                    ? $pedido->productos->pluck('nombre')->implode(', ')
                                    : ($pedido->nombre_producto ?: $pedido->tipo_producto ?: '-');
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
                                    @if($pedido->estado === 'produciendo')
                                        <span class="rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">Produciendo</span>
                                    @else
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">En produccion</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php $estDis = $pedido->estado_personalizacion; @endphp
                                    @if($estDis === 'aprobado')
                                        <span class="rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">Aprobado</span>
                                    @elseif($estDis === 'en_revision')
                                        <span class="rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En revision</span>
                                    @elseif($estDis === 'en_diseno')
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">En diseno</span>
                                    @else
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ str_replace('_', ' ', $estDis ?? 'sin_iniciar') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @@click="$dispatch('open-detalle-{{ $pedido->id }}')" class="btn-icon-sm" style="background-color:#0891B2" title="Ver detalle">
                                            <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none">
                                        </button>
                                        @if($pedido->estado === 'en_produccion')
                                            <form method="POST" action="{{ route('produccion.iniciar', $pedido) }}" onsubmit="return confirm('Iniciar produccion de este pedido?')">
                                                @csrf
                                                <button type="submit" class="btn-icon-sm bg-sky-600 hover:bg-sky-700" title="Iniciar produccion">
                                                    <svg class="h-4 w-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($pedido->estado === 'produciendo')
                                            <form method="POST" action="{{ route('produccion.notificar', $pedido) }}" onsubmit="return confirm('Marcar como listo y notificar al repartidor?')">
                                                @csrf
                                                <button type="submit" class="btn-icon-sm bg-amber-600 hover:bg-amber-700" title="Notificar repartidor">
                                                    <svg class="h-4 w-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-[#777]">No hay pedidos en produccion.</td>
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
            <div @@click.outside="open = false" class="mx-4 flex max-h-[85vh] w-full max-w-3xl flex-col rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-[#e5dec8] px-6 py-4">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Produccion — {{ $pedido->codigo }}</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @@click="viewerOpen = true; viewerIndex = 0" class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700" title="Ver modelos">
                            <img src="{{ asset('icons/VerModelo-Blanco.png') }}" alt="Ver modelos" class="h-4 w-4 object-contain pointer-events-none">
                        </button>
                        <button type="button" @@click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
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
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Tipo producto</p>
                            <p class="mt-1 text-[#2d2b24]">{{ $pedido->tipo_producto }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Cantidad</p>
                            <p class="mt-1 text-[#2d2b24]">{{ $pedido->productos->sum('cantidad') ?: $pedido->cantidad }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Estado diseno</p>
                            @php $estDis = $pedido->estado_personalizacion; @endphp
                            @if($estDis === 'aprobado')
                                <span class="mt-1 inline-block rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">Aprobado</span>
                            @elseif($estDis === 'en_revision')
                                <span class="mt-1 inline-block rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En revision</span>
                            @else
                                <span class="mt-1 inline-block rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ str_replace('_', ' ', $estDis ?? 'sin_iniciar') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Detalle del trabajo</p>
                        <p class="mt-1 text-[#2d2b24]">{{ $pedido->detalle_trabajo ?: '-' }}</p>
                    </div>

                    @if($pedido->observaciones_personalizacion)
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Observaciones</p>
                            <p class="mt-1 text-[#2d2b24]">{{ $pedido->observaciones_personalizacion }}</p>
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

                    <div class="mt-4 rounded-xl border border-[#e5dec8] p-4">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-emerald-700">Modelo del cliente / referencia</p>
                        @php $refs = $pedido->productos->flatMap->archivos; @endphp
                        @if($refs->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($refs as $a)
                                    <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg border border-[#e5dec8] px-3 py-1.5 text-sm text-[#6a5122] hover:bg-[#faf8f2]">
                                        <img src="{{ asset('icons/Imagen-Test.png') }}" alt="" class="h-4 w-4 object-contain">
                                        {{ $a->nombre_original }}
                                        <span class="text-[#bbb]">({{ round($a->tamano_bytes / 1024) }} KB)</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[#bbb]">Sin archivos de referencia</p>
                        @endif
                    </div>

                    <div class="mt-4 rounded-xl border border-[#e5dec8] p-4">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-amber-700">Diseno del disenador</p>
                        @if($pedido->archivosDiseno->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($pedido->archivosDiseno as $a)
                                    <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg border border-[#e5dec8] px-3 py-1.5 text-sm text-[#6a5122] hover:bg-[#faf8f2]">
                                        <img src="{{ asset('icons/Imagen-Test.png') }}" alt="" class="h-4 w-4 object-contain">
                                        {{ $a->nombre_original }}
                                        <span class="text-[#bbb]">({{ round($a->tamano_bytes / 1024) }} KB)</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[#bbb]">Sin diseno del disenador</p>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t border-[#e5dec8] pt-4">
                        @if($pedido->estado === 'en_produccion')
                        <form action="{{ route('produccion.iniciar', $pedido) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Iniciar produccion
                            </button>
                        </form>
                        @endif
                        @if($pedido->estado === 'produciendo')
                        <form action="{{ route('produccion.notificar', $pedido) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notificar repartidor
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div x-show="viewerOpen"
                 x-on:keydown.escape.window="viewerOpen = false"
                 x-on:keydown.left.window="viewerOpen && prevFile()"
                 x-on:keydown.right.window="viewerOpen && nextFile()"
                 x-cloak
                 class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60">
                <div @@click.outside="viewerOpen = false" class="relative mx-4 w-full max-w-3xl rounded-2xl bg-[#1a1a1a] p-4 shadow-xl">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white/80">Modelos — {{ $pedido->codigo }}</h3>
                        <button type="button" @@click="viewerOpen = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                            <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                        </button>
                    </div>
                    <template x-if="viewerTotal === 0">
                        <div class="flex h-64 items-center justify-center text-white/50">No hay archivos.</div>
                    </template>
                    <template x-if="viewerTotal > 0">
                        <div>
                            <div class="relative flex items-center">
                                <button x-show="viewerIndex > 0" @@click="prevFile()"
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
                                <button x-show="viewerIndex < viewerTotal - 1" @@click="nextFile()"
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
                                    <button @@click="viewerIndex = i"
                                            :class="i === viewerIndex ? 'bg-amber-500' : 'bg-white/20 hover:bg-white/40'"
                                            class="h-1.5 w-6 rounded-full transition-colors"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
