<x-app-layout>
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

    <x-slot name="header">
        <span>Diseños</span>
    </x-slot>

    <div x-data="{ showSuccess: {{ session()->has('ok') ? 'true' : 'false' }} }" class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <form id="diseno-search-form" method="GET" action="{{ route('diseno.index') }}" class="flex min-w-0 flex-1">
                        <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="Buscar por codigo, cliente o producto" />
                    </form>
                </div>
                <button type="submit" form="diseno-search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <div x-data="{ filtrosAbiertos: false }" class="relative">
                    <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                        class="btn-icon bg-sky-500 hover:bg-sky-600 {{ $filtroEstado ? 'ring-2 ring-sky-400' : '' }}" title="Filtrar">
                        <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                    </button>
                    <div x-show="filtrosAbiertos" x-cloak @click.outside="filtrosAbiertos = false"
                         class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl border border-[#e5dec8] bg-white p-3 shadow-lg">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Estado de personalizacion</p>
                        <div class="space-y-1">
                            <a href="{{ route('diseno.index', array_filter(['q' => $busqueda])) }}"
                               class="block rounded-lg px-3 py-1.5 text-sm {{ !$filtroEstado ? 'bg-amber-100 text-amber-800 font-medium' : 'text-[#555] hover:bg-[#f5f3ed]' }}">Todos</a>
                            <a href="{{ route('diseno.index', array_filter(['q' => $busqueda, 'estado_personalizacion' => 'en_diseno'])) }}"
                               class="block rounded-lg px-3 py-1.5 text-sm {{ $filtroEstado === 'en_diseno' ? 'bg-amber-100 text-amber-800 font-medium' : 'text-[#555] hover:bg-[#f5f3ed]' }}">En diseño</a>
                            <a href="{{ route('diseno.index', array_filter(['q' => $busqueda, 'estado_personalizacion' => 'en_revision'])) }}"
                               class="block rounded-lg px-3 py-1.5 text-sm {{ $filtroEstado === 'en_revision' ? 'bg-sky-100 text-sky-800 font-medium' : 'text-[#555] hover:bg-[#f5f3ed]' }}">En revisión</a>
                        </div>
                    </div>
                </div>
                @if($busqueda || $filtroEstado)
                    <a href="{{ route('diseno.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
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
                            <th class="px-4 py-3 font-semibold">Archivos</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse($pedidos as $pedido)
                            @php
                                $totalArchivosRef = $pedido->productos->flatMap->archivos->count();
                                $totalArchivosDiseno = $pedido->archivosDiseno->count();
                                $totalArchivos = $totalArchivosRef + $totalArchivosDiseno;
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
                                        {{ $pedido->productos->first()->nombre ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($pedido->estado_personalizacion === 'en_diseno')
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">En diseno</span>
                                    @elseif($pedido->estado_personalizacion === 'en_revision')
                                        <span class="rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En revision</span>
                                    @else
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ str_replace('_', ' ', $pedido->estado_personalizacion) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($totalArchivos > 0)
                                        <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            {{ $totalArchivos }} archivo{{ $totalArchivos > 1 ? 's' : '' }}
                                            @if($totalArchivosRef > 0 && $totalArchivosDiseno > 0)
                                                <span class="ml-1 text-[10px] text-amber-500">({{ $totalArchivosRef }} ref + {{ $totalArchivosDiseno }} dis)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-xs text-[#999]">Sin archivos</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @@click="$dispatch('open-detalle-{{ $pedido->id }}')" class="btn-icon-sm" style="background-color:#0891B2" title="Ver detalle">
                                            <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none">
                                        </button>
                                        <button type="button" @@click="$dispatch('open-modelos-{{ $pedido->id }}')" class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700" title="Ver modelo">
                                            <img src="{{ asset('icons/VerModelo-Blanco.png') }}" alt="Ver modelo" class="h-4 w-4 object-contain pointer-events-none">
                                        </button>
                                        <button type="button" @@click="$dispatch('open-subir-{{ $pedido->id }}')" class="btn-icon-sm bg-amber-600 hover:bg-amber-700" title="Subir diseno">
                                            <img src="{{ asset('icons/Subir-Blanco.png') }}" alt="Subir diseno" class="h-4 w-4 object-contain pointer-events-none">
                                        </button>
                                        <button type="button" @@click="$dispatch('open-editar-{{ $pedido->id }}')" class="btn-icon-sm bg-purple-600 hover:bg-purple-700" title="Editar archivos" {{ $totalArchivos === 0 ? 'disabled style="opacity:0.35;cursor:not-allowed"' : '' }}>
                                            <img src="{{ asset('icons/editar.ico') }}" alt="Editar archivos" class="h-4 w-4 object-contain pointer-events-none">
                                        </button>
                                        <button type="button" @@click="$dispatch('open-notificar-{{ $pedido->id }}')" class="btn-icon-sm bg-amber-500 hover:bg-amber-600" title="Notificar al vendedor">
                                            <svg class="h-4 w-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-[#777]">No hay pedidos en diseno.</td>
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

    {{-- Modal Ver Modelos (viewer de archivos referencia + diseno) --}}
    @foreach($pedidos as $pedido)
        @php
            $modelosRef = collect();
            foreach ($pedido->productos as $pp) {
                foreach ($pp->archivos as $a) {
                    $modelosRef->push([
                        'url' => asset('storage/' . $a->archivo_path),
                        'nombre' => $a->nombre_original,
                        'mime' => $a->mime_type,
                        'producto' => $pp->nombre,
                    ]);
                }
            }
            $modelosDis = collect();
            foreach ($pedido->productos as $pp) {
                foreach ($pp->archivosDiseno as $a) {
                    $modelosDis->push([
                        'url' => asset('storage/' . $a->archivo_path),
                        'nombre' => $a->nombre_original,
                        'mime' => $a->mime_type,
                        'producto' => $pp->nombre,
                    ]);
                }
            }
        @endphp

        <div x-data="{
            open: false,
            tab: 'ref',
            ref: {{ Js::from($modelosRef->values()) }},
            dis: {{ Js::from($modelosDis->values()) }},
            index: 0,
            get archivos() { return this.tab === 'ref' ? this.ref : this.dis },
            get current() { return this.archivos[this.index] },
            get total() { return this.archivos.length },
            get esImagen() {
                if (!this.current) return false;
                return ['image/png','image/jpeg','image/jpg','image/gif','image/svg+xml','image/webp'].includes(this.current.mime);
            },
            switchTab(t) { this.tab = t; this.index = 0; },
            prev() { if (this.index > 0) this.index-- },
            next() { if (this.index < this.total - 1) this.index++ }
        }"
             x-show="open"
             x-on:open-modelos-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-on:keydown.left.window="open && prev()"
             x-on:keydown.right.window="open && next()"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div @@click.outside="open = false" class="relative mx-4 w-full max-w-3xl rounded-2xl bg-[#1a1a1a] p-4 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white/80">{{ $pedido->codigo }}</h3>
                    <button type="button" @@click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="mb-3 flex justify-center gap-2">
                    <button type="button" @@click="switchTab('ref')"
                        :class="tab === 'ref' ? 'bg-amber-500 text-white' : 'bg-white/10 text-white/60 hover:bg-white/20'"
                        class="rounded-lg px-4 py-1.5 text-xs font-semibold transition-colors">
                        Referencia <span x-show="ref.length > 0" x-text="'(' + ref.length + ')'" class="ml-1 opacity-70"></span>
                    </button>
                    <button type="button" @@click="switchTab('dis')"
                        :class="tab === 'dis' ? 'bg-sky-500 text-white' : 'bg-white/10 text-white/60 hover:bg-white/20'"
                        class="rounded-lg px-4 py-1.5 text-xs font-semibold transition-colors">
                        Diseno <span x-show="dis.length > 0" x-text="'(' + dis.length + ')'" class="ml-1 opacity-70"></span>
                    </button>
                </div>

                <template x-if="total === 0">
                    <div class="flex h-64 items-center justify-center text-white/50">
                        No hay archivos en esta seccion.
                    </div>
                </template>

                <template x-if="total > 0">
                    <div>
                        <div class="relative flex items-center">
                            <button x-show="index > 0" @@click="prev()"
                                    class="absolute left-0 z-10 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="mx-auto flex h-80 w-full items-center justify-center overflow-hidden rounded-xl bg-black/40">
                                <template x-if="esImagen">
                                    <img :src="current.url" :alt="current.nombre" class="max-h-full max-w-full object-contain">
                                </template>
                                <template x-if="!esImagen">
                                    <div class="flex flex-col items-center gap-3 text-center text-white/70">
                                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <p class="text-sm" x-text="current.nombre"></p>
                                        <a :href="current.url" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white hover:bg-white/30">Descargar archivo</a>
                                    </div>
                                </template>
                            </div>
                            <button x-show="index < total - 1" @@click="next()"
                                    class="absolute right-0 z-10 flex h-10 w-10 translate-x-1/2 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="mt-3 flex items-center justify-center gap-3 text-xs text-white/60">
                            <span x-text="`${index + 1} de ${total}`"></span>
                            <span class="text-white/30">|</span>
                            <span x-text="current?.producto || ''"></span>
                            <span class="text-white/30">|</span>
                            <span x-text="current?.nombre || ''" class="max-w-[200px] truncate"></span>
                        </div>
                        <div class="mt-2 flex justify-center gap-1">
                            <template x-for="(_, i) in archivos" :key="i">
                                <button @@click="index = i"
                                        :class="i === index ? 'bg-amber-500' : 'bg-white/20 hover:bg-white/40'"
                                        class="h-1.5 w-6 rounded-full transition-colors"></button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    @endforeach

    {{-- Modal Ver Detalle --}}
    @foreach($pedidos as $pedido)
        <div x-data="{
            open: false,
            viewerOpen: false,
            viewerIndex: 0,
            get viewerFiles() {
                return {{Js::from(
                    $pedido->productos->flatMap(fn($p) => $p->archivos->map(fn($a) => [
                        'url' => asset('storage/' . $a->archivo_path),
                        'nombre' => $a->nombre_original,
                        'mime' => $a->mime_type,
                        'producto' => $p->nombre,
                    ]))
                )}}
            },
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
                    <h3 class="text-lg font-bold text-[#2d2b24]">Detalle del pedido — {{ $pedido->codigo }}</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @@click="viewerOpen = true; viewerIndex = 0" class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700" title="Ver modelo">
                            <img src="{{ asset('icons/VerModelo-Blanco.png') }}" alt="Ver modelo" class="h-4 w-4 object-contain pointer-events-none">
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
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Estado</p>
                            @if($pedido->estado_personalizacion === 'en_diseno')
                                <span class="mt-1 inline-block rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">En diseno</span>
                            @elseif($pedido->estado_personalizacion === 'en_revision')
                                <span class="mt-1 inline-block rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En revision</span>
                            @else
                                <span class="mt-1 inline-block rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ str_replace('_', ' ', $pedido->estado_personalizacion ?? 'sin_iniciar') }}</span>
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
                                        <th class="px-3 py-2">Archivos referencia</th>
                                        <th class="px-3 py-2">Archivos de diseno</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->productos as $idx => $pp)
                                        <tr class="border-t border-[#efeee9]">
                                            <td class="px-3 py-2 text-center text-[#999]">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-[#2d2b24]">{{ $pp->nombre }}</td>
                                            <td class="px-3 py-2 text-[#4a4026]">{{ $pp->descripcion ?? '-' }}</td>
                                            <td class="px-3 py-2">
                                                @if($pp->archivos->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($pp->archivos as $a)
                                                            <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                                               class="inline-flex items-center gap-0.5 rounded bg-amber-100 px-1.5 py-0.5 text-xs text-amber-800 hover:bg-amber-200"
                                                               title="{{ $a->nombre_original }}">
                                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                                {{ Str::limit($a->nombre_original, 25) }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-[#bbb]">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                @php $disenos = $pp->archivosDiseno->count() > 0 ? $pp->archivosDiseno : $pedido->archivosDiseno->where('pedido_producto_id', $pp->id); @endphp
                                                @if($disenos->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($disenos as $a)
                                                            <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                                               class="inline-flex items-center gap-0.5 rounded bg-sky-100 px-1.5 py-0.5 text-xs text-sky-800 hover:bg-sky-200"
                                                               title="{{ $a->nombre_original }}">
                                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                                {{ Str::limit($a->nombre_original, 25) }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-[#bbb]">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Viewer dentro del detalle --}}
            <div x-show="viewerOpen"
                 x-on:keydown.escape.window="viewerOpen = false"
                 x-cloak
                 @click.self="viewerOpen = false"
                 class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60">
                <div class="relative mx-4 w-full max-w-3xl rounded-2xl bg-[#1a1a1a] p-4 shadow-xl">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white/80">Modelos de referencia</h3>
                        <button type="button" @click="viewerOpen = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                            <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                        </button>
                    </div>
                    <template x-if="viewerTotal === 0">
                        <div class="flex h-64 items-center justify-center text-white/50">No hay archivos de referencia.</div>
                    </template>
                    <template x-if="viewerTotal > 0">
                        <div>
                            <div class="flex items-center gap-2">
                                <button type="button" x-show="viewerIndex > 0" @click="prevFile()"
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
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
                                <button type="button" x-show="viewerIndex < viewerTotal - 1" @click="nextFile()"
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                            <div class="mt-3 flex items-center justify-center gap-3 text-xs text-white/60">
                                <span x-text="`${viewerIndex + 1} de ${viewerTotal}`"></span>
                                <span class="text-white/30">|</span>
                                <span x-text="currentFile?.producto || ''"></span>
                                <span class="text-white/30">|</span>
                                <span x-text="currentFile?.nombre || ''" class="max-w-[200px] truncate"></span>
                            </div>
                            <div class="mt-2 flex justify-center gap-1">
                                <template x-for="(_, i) in viewerFiles" :key="i">
                                    <button type="button" @click="viewerIndex = i"
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

    {{-- Modal Subir Diseno (por producto) --}}
    @foreach($pedidos as $pedido)
        <div x-data="{ open: false }"
             x-show="open"
             x-on:open-subir-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @@click.outside="open = false" class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Subir diseno — {{ $pedido->codigo }}</h3>
                    <button type="button" @@click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <p class="mb-3 text-sm text-[#555]">{{ $pedido->nombre_cliente }}</p>

                <form action="{{ route('diseno.update', $pedido) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#2d2b24]">Producto personalizado</label>
                        <select name="pedido_producto_id" required
                                class="block w-full rounded-lg border border-[#d5d0c0] bg-white px-3 py-2.5 text-sm text-[#2d2b24] focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">Seleccionar producto...</option>
                            @foreach($pedido->productos as $pp)
                                @php
                                    $countDiseno = $pp->archivosDiseno->count();
                                    $countRef = $pp->archivos->count();
                                @endphp
                                <option value="{{ $pp->id }}">{{ $pp->nombre }} @if($countDiseno > 0)(archivos diseno: {{ $countDiseno }})@endif @if($countRef > 0)(ref: {{ $countRef }})@endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#2d2b24]">Archivos de diseno</label>
                        <input type="file" name="archivos_diseno[]" multiple required
                               accept=".cdr,.pdf,.png,.jpg,.jpeg,.svg,.ai,.eps,.psd,.webp"
                               class="block w-full rounded-lg border border-[#d5d0c0] bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-amber-100 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-amber-800 hover:file:bg-amber-200">
                        <p class="mt-1 text-xs text-[#999]">Max 10MB por archivo. Formatos: cdr, pdf, png, jpg, svg, ai, eps, psd, webp</p>
                    </div>

                    <input type="hidden" name="estado_personalizacion" value="en_revision">

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @@click="open = false"
                                class="rounded-xl border border-[#d5d0c0] px-4 py-2 text-sm font-medium text-[#555] hover:bg-[#f5f3ed]">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                            Subir archivos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Modal Editar Archivos de Diseno (por producto) --}}
    @foreach($pedidos as $pedido)
        <div x-data="{ open: false }"
             x-show="open"
             x-on:open-editar-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @@click.outside="open = false" class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Archivos de diseno — {{ $pedido->codigo }}</h3>
                    <button type="button" @@click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <div class="max-h-[60vh] space-y-4 overflow-y-auto">
                    @foreach($pedido->productos as $pp)
                        @php $archivosDiseno = $pp->archivosDiseno; @endphp
                        <div class="rounded-lg border border-[#e5dec8] bg-[#faf8f2] p-3">
                            <p class="mb-2 text-sm font-semibold text-[#2d2b24]">{{ $pp->nombre }}</p>
                            @if($archivosDiseno->isNotEmpty())
                                <ul class="space-y-2">
                                    @foreach($archivosDiseno as $archivo)
                                        <li class="flex items-center justify-between gap-2 rounded-md bg-white px-3 py-2 text-xs border border-[#efe7d2]">
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ asset('storage/' . $archivo->archivo_path) }}" target="_blank"
                                                   class="text-amber-700 underline hover:text-amber-900 truncate block" title="{{ $archivo->nombre_original }}">
                                                    {{ $archivo->nombre_original }}
                                                </a>
                                                <span class="text-[#999]">{{ round($archivo->tamano_bytes / 1024) }} KB</span>
                                            </div>
                                            <form action="{{ route('diseno.destroy_archivo', $archivo) }}" method="POST"
                                                  onsubmit="return confirm('Eliminar este archivo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex-shrink-0 rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 hover:text-red-700" title="Eliminar">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-[#999]">Sin archivos de diseno para este producto.</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" @@click="open = false"
                            class="rounded-xl border border-[#d5d0c0] px-4 py-2 text-sm font-medium text-[#555] hover:bg-[#f5f3ed]">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Notificar al vendedor --}}
    @foreach($pedidos as $pedido)
        <div x-data="{ open: false, notifExito: false, notifError: false, notifMsg: '' }"
             x-show="open"
             x-on:open-notificar-{{ $pedido->id }}.window="open = true"
             x-on:keydown.escape.window="open = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @@click.outside="open = false" class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Notificar al vendedor</h3>
                    <button type="button" @@click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <p class="mb-2 text-sm text-[#555]">
                    ¿Quieres notificar al vendedor que el pedido <strong>{{ $pedido->codigo }}</strong> tiene archivos listos para revisar?
                </p>
                <p class="mb-4 text-xs text-[#999]">Se enviara una notificacion al vendedor {{ $pedido->usuario->name ?? 'asignado' }}.</p>

                <div class="flex justify-end gap-2">
                    <button type="button" @@click="open = false"
                            class="rounded-xl border border-[#d5d0c0] px-4 py-2 text-sm font-medium text-[#555] hover:bg-[#f5f3ed]">
                        Cancelar
                    </button>
                    <button type="button"
                            @@click="
                                fetch('{{ route('diseno.notificar', $pedido) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                }).then(r => r.json().then(d => ({ ok: r.ok, body: d })))
                                  .then(({ ok, body }) => {
                                      open = false;
                                      if (ok && body.ok) { notifMsg = body.message; notifExito = true; window.dispatchEvent(new Event('notificacion-enviada')); }
                                      else { notifMsg = body.message || 'Error al enviar notificacion.'; notifError = true; }
                                  })
                                  .catch(() => { open = false; notifMsg = 'Error de conexion.'; notifError = true; })
                            "
                            class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                        Notificar
                    </button>
                </div>
            </div>

            {{-- Modal Exito --}}
            <template x-if="notifExito">
                <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div x-transition.opacity class="fixed inset-0 bg-black/50" @click="notifExito = false"></div>
                    <div x-transition class="relative w-full max-w-md rounded-2xl bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                        <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                            <img src="{{ asset('icons/Valido-Verde.png') }}" alt="Valido" class="h-8 w-8 object-contain pointer-events-none" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900" x-text="notifMsg"></h3>
                        <button type="button" @click="notifExito = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                    </div>
                </div>
            </template>

            {{-- Modal Error --}}
            <template x-if="notifError">
                <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div x-transition.opacity class="fixed inset-0 bg-black/50" @click="notifError = false"></div>
                    <div x-transition class="relative w-full max-w-md rounded-2xl bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                        <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                            <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Error" class="h-8 w-8 object-contain pointer-events-none" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900" x-text="notifMsg"></h3>
                        <button type="button" @click="notifError = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                    </div>
                </div>
            </template>
        </div>
    @endforeach

        {{-- Modal exito --}}
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

    </div>
</x-app-layout>
