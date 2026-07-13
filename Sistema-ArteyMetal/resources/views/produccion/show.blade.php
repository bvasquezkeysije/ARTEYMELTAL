<x-app-layout>
    <x-slot name="header">
        <span>Produccion — {{ $pedido->codigo }}</span>
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
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .fade-in { animation: fadeIn 0.2s ease-out; }
    </style>

    @php
        $refFiles = $pedido->productos->flatMap(fn($p) => $p->archivos->map(fn($a) => [
            'tipo' => 'referencia',
            'url' => asset('storage/' . $a->archivo_path),
            'nombre' => $a->nombre_original,
            'mime' => $a->mime_type,
            'producto' => $p->nombre,
        ]));
        $disFiles = $pedido->archivosDiseno->map(fn($a) => [
            'tipo' => 'diseno',
            'url' => asset('storage/' . $a->archivo_path),
            'nombre' => $a->nombre_original,
            'mime' => $a->mime_type,
            'producto' => 'Diseno',
        ]);
        $allFiles = collect($refFiles)->concat($disFiles);
    @endphp

    <div x-data="{
        viewerOpen: false,
        viewerIndex: 0,
        files: {{Js::from($allFiles)}},
        get total() { return this.files.length },
        get current() { return this.files[this.viewerIndex] },
        get esImagen() {
            if (!this.current) return false;
            return ['image/png','image/jpeg','image/jpg','image/gif','image/svg+xml','image/webp'].includes(this.current.mime);
        },
        prev() { if (this.viewerIndex > 0) this.viewerIndex-- },
        next() { if (this.viewerIndex < this.total - 1) this.viewerIndex++ }
    }">
        <div class="space-y-4">
            <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
                <div class="px-6 py-5">
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
                </div>
            </div>

            @if($pedido->productos->isNotEmpty())
                <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-[#faf8f2] text-left text-xs font-semibold uppercase tracking-wider text-[#6a5122]">
                                <tr>
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Descripcion</th>
                                    <th class="px-6 py-3">Cant.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#efeee9]">
                                @foreach($pedido->productos as $idx => $pp)
                                    <tr>
                                        <td class="px-6 py-3 text-center text-[#999]">{{ $idx + 1 }}</td>
                                        <td class="px-6 py-3 font-medium text-[#2d2b24]">{{ $pp->nombre }}</td>
                                        <td class="px-6 py-3 text-[#4a4026]">{{ $pp->descripcion ?? '-' }}</td>
                                        <td class="px-6 py-3 text-[#4a4026]">{{ $pp->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
                <div class="px-6 py-5">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-[#2d2b24]">Archivos</h3>
                        <button type="button" @@click="viewerOpen = true; viewerIndex = 0" class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700" title="Ver todos los modelos">
                            <img src="{{ asset('icons/VerModelo-Blanco.png') }}" alt="Ver modelos" class="h-4 w-4 object-contain">
                        </button>
                    </div>

                    <div class="rounded-xl border border-[#e5dec8] p-4">
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
                </div>
            </div>
        </div>

        <div x-show="viewerOpen"
             x-on:keydown.escape.window="viewerOpen = false"
             x-on:keydown.left.window="viewerOpen && prev()"
             x-on:keydown.right.window="viewerOpen && next()"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 fade-in">
            <div @@click.outside="viewerOpen = false" class="relative mx-4 w-full max-w-3xl rounded-2xl bg-[#1a1a1a] p-4 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white/80">Modelos — {{ $pedido->codigo }}</h3>
                    <button type="button" @@click="viewerOpen = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>
                <template x-if="total === 0">
                    <div class="flex h-64 items-center justify-center text-white/50">No hay archivos.</div>
                </template>
                <template x-if="total > 0">
                    <div>
                        <div class="relative flex items-center">
                            <button x-show="viewerIndex > 0" @@click="prev()"
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
                            <button x-show="viewerIndex < total - 1" @@click="next()"
                                    class="absolute right-0 z-10 flex h-10 w-10 translate-x-1/2 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="mt-3 flex items-center justify-center gap-3 text-xs text-white/60">
                            <span x-text="`${viewerIndex + 1} de ${total}`"></span>
                            <span class="text-white/30">|</span>
                            <span x-text="current?.tipo === 'referencia' ? 'Modelo cliente' : 'Diseno disenador'"></span>
                            <span class="text-white/30">|</span>
                            <span x-text="current?.nombre || ''" class="max-w-[200px] truncate"></span>
                        </div>
                        <div class="mt-2 flex justify-center gap-1">
                            <template x-for="(_, i) in files" :key="i">
                                <button @@click="viewerIndex = i"
                                        :class="i === viewerIndex ? 'bg-amber-500' : 'bg-white/20 hover:bg-white/40'"
                                        class="h-1.5 w-6 rounded-full transition-colors"></button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-3">
                @if($pedido->estado === 'en_produccion')
                    <form action="{{ route('produccion.iniciar', $pedido) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-sky-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Iniciar produccion
                        </button>
                    </form>
                @endif
                @if($pedido->estado === 'produciendo')
                    <form action="{{ route('produccion.notificar', $pedido) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-amber-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Notificar repartidor
                        </button>
                    </form>
                @endif
                <a href="{{ route('produccion.index') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
