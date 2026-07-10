<x-app-layout>
    <x-slot name="header">
        <span>Diseno — {{ $pedido->codigo }}</span>
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

    @if(session('ok'))
        <div class="mb-3 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif

    <div x-data="{
        uploadModal: false,
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
    }">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-sm">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Cliente</p>
                    <p class="mt-1 text-[#2d2b24]">{{ $pedido->nombre_cliente }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Estado diseno</p>
                    @if($pedido->estado_personalizacion === 'en_diseno')
                        <span class="mt-1 inline-block rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">En diseno</span>
                    @elseif($pedido->estado_personalizacion === 'en_revision')
                        <span class="mt-1 inline-block rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En revision</span>
                    @else
                        <span class="mt-1 inline-block rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ str_replace('_', ' ', $pedido->estado_personalizacion ?? 'sin_iniciar') }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Tipo producto</p>
                    <p class="mt-1 text-[#2d2b24]">{{ $pedido->tipo_producto }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Cantidad</p>
                    <p class="mt-1 text-[#2d2b24]">{{ $pedido->productos->sum('cantidad') ?: $pedido->cantidad }}</p>
                </div>
            </div>

            <div class="mt-5">
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
                <div class="mt-5 overflow-x-auto rounded-xl border border-[#e5dec8]">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#faf8f2] text-left text-xs font-semibold uppercase tracking-wider text-[#6a5122]">
                                <th class="px-3 py-2">#</th>
                                <th class="px-3 py-2">Nombre</th>
                                <th class="px-3 py-2">Descripcion</th>
                                <th class="px-3 py-2">Archivos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->productos as $idx => $pp)
                                <tr class="border-t border-[#efeee9]">
                                    <td class="px-3 py-2 text-center text-[#999]">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-[#2d2b24]">
                                        {{ $pp->nombre }}
                                    </td>
                                    <td class="px-3 py-2 text-[#4a4026]">{{ $pp->descripcion ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if($pp->archivosCliente->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mb-1">
                                                @foreach($pp->archivosCliente as $a)
                                                    <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                                       class="inline-flex items-center gap-0.5 rounded bg-amber-100 px-1.5 py-0.5 text-xs text-amber-800 hover:bg-amber-200"
                                                       title="Cliente: {{ $a->nombre_original }}">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        {{ Str::limit($a->nombre_original, 25) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($pp->archivosDisenador->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($pp->archivosDisenador as $a)
                                                    <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                                       class="inline-flex items-center gap-0.5 rounded bg-sky-100 px-1.5 py-0.5 text-xs text-sky-800 hover:bg-sky-200"
                                                       title="Disenador: {{ $a->nombre_original }}">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        {{ Str::limit($a->nombre_original, 25) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($pp->archivosCliente->isEmpty() && $pp->archivosDisenador->isEmpty())
                                            <span class="text-[#bbb]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#6a5122]">Archivos de diseno subidos</p>
                <div class="mt-1 space-y-2">
                    @php
                        $disenosPorProducto = $pedido->productos->filter(fn($p) => $p->archivosDisenador->isNotEmpty());
                    @endphp
                    @if($disenosPorProducto->isNotEmpty())
                        @foreach($disenosPorProducto as $pp)
                            <div>
                                <p class="text-xs text-[#6a5122] font-medium mb-1">{{ $pp->nombre }}:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($pp->archivosDisenador as $a)
                                        <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1 rounded-lg border border-[#e5dec8] px-2.5 py-1 text-xs text-sky-700 hover:bg-sky-50">
                                            {{ $a->nombre_original }}
                                            <span class="text-[#bbb]">({{ round($a->tamano_bytes / 1024) }} KB)</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-[#bbb]">-</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <button type="button" @@click="viewerOpen = true; viewerIndex = 0" class="btn-icon-sm bg-emerald-600 hover:bg-emerald-700" title="Ver modelo">
                    <img src="{{ asset('icons/VerModelo-Blanco.png') }}" alt="Ver modelo" class="h-4 w-4 object-contain pointer-events-none">
                </button>
                <button type="button" @@click="uploadModal = true" class="btn-icon-sm bg-amber-600 hover:bg-amber-700" title="Subir diseno">
                    <img src="{{ asset('icons/Subir-Blanco.png') }}" alt="Subir diseno" class="h-4 w-4 object-contain pointer-events-none">
                </button>
                <a href="{{ route('diseno.index') }}" class="btn-icon-sm bg-[#555] hover:bg-[#333]" title="Volver">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
            </div>
        </div>

        <div x-show="viewerOpen"
             x-on:keydown.escape.window="viewerOpen = false"
             x-on:keydown.left.window="viewerOpen && prevFile()"
             x-on:keydown.right.window="viewerOpen && nextFile()"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div @@click.outside="viewerOpen = false" class="relative mx-4 w-full max-w-3xl rounded-2xl bg-[#1a1a1a] p-4 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white/80">Modelos de referencia</h3>
                    <button type="button" @@click="viewerOpen = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <template x-if="viewerTotal === 0">
                    <div class="flex h-64 items-center justify-center text-white/50">No hay archivos de referencia.</div>
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
                                        <a :href="currentFile.url" target="_blank"
                                           class="inline-flex items-center gap-1 rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white hover:bg-white/30">Descargar archivo</a>
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
                            <span x-text="currentFile?.producto || ''"></span>
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

        <div x-show="uploadModal"
             x-on:keydown.escape.window="uploadModal = false"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div @@click.outside="uploadModal = false" class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-[#2d2b24]">Subir diseno</h3>
                    <button type="button" @@click="uploadModal = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
                    </button>
                </div>

                <form action="{{ route('diseno.update', $pedido) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="max-h-60 space-y-3 overflow-y-auto rounded-lg border border-[#e5dec8] bg-[#faf8f2] p-3">
                        @if($pedido->productos->isNotEmpty())
                            @foreach($pedido->productos as $pp)
                                <div class="rounded-lg border border-[#e5dec8] bg-white p-3">
                                    <p class="text-sm font-medium text-[#2d2b24] mb-1">{{ $pp->nombre }}</p>
                                    @if($pp->descripcion)
                                        <p class="text-xs text-[#555] mb-2">{{ $pp->descripcion }}</p>
                                    @endif
                                    @if($pp->archivosCliente->isNotEmpty())
                                        <div class="mb-2 flex flex-wrap gap-1">
                                            @foreach($pp->archivosCliente as $a)
                                                <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                                   class="inline-flex items-center gap-0.5 rounded bg-amber-100 px-1.5 py-0.5 text-xs text-amber-800 hover:bg-amber-200"
                                                   title="Cliente: {{ $a->nombre_original }}">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    {{ Str::limit($a->nombre_original, 18) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($pp->archivosDisenador->isNotEmpty())
                                        <div class="mb-2 flex flex-wrap gap-1">
                                            @foreach($pp->archivosDisenador as $a)
                                                <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank"
                                                   class="inline-flex items-center gap-0.5 rounded bg-sky-100 px-1.5 py-0.5 text-xs text-sky-800 hover:bg-sky-200"
                                                   title="Disenador: {{ $a->nombre_original }}">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    {{ Str::limit($a->nombre_original, 18) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <input type="file" name="archivos_producto[{{ $pp->id }}][]" multiple
                                           accept=".cdr,.pdf,.png,.jpg,.jpeg,.svg,.ai,.eps,.psd,.webp"
                                           class="block w-full rounded-lg border border-[#d5d0c0] bg-white px-3 py-1.5 text-xs file:mr-2 file:rounded-lg file:border-0 file:bg-amber-100 file:px-2 file:py-0.5 file:text-xs file:font-semibold file:text-amber-800 hover:file:bg-amber-200">
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <p class="text-xs text-[#999]">Max 10MB c/u. cdr, pdf, png, jpg, svg, ai, eps, psd, webp</p>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#2d2b24]">Accion</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 rounded-lg border border-[#d5d0c0] px-4 py-2.5 cursor-pointer has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                <input type="radio" name="estado_personalizacion" value="en_diseno" @checked($pedido->estado_personalizacion !== 'en_revision')
                                       class="text-amber-600 accent-amber-600">
                                <span class="text-sm font-medium text-[#2d2b24]">Solo subir archivos</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-[#d5d0c0] px-4 py-2.5 cursor-pointer has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50">
                                <input type="radio" name="estado_personalizacion" value="en_revision" @checked($pedido->estado_personalizacion === 'en_revision')
                                       class="text-sky-600 accent-sky-600">
                                <span class="text-sm font-medium text-[#2d2b24]">Subir y notificar</span>
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-[#999]">"Subir y notificar" envia a revision para que el vendedor lo vea.</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @@click="uploadModal = false"
                                class="rounded-xl border border-[#d5d0c0] px-4 py-2 text-sm font-medium text-[#555] hover:bg-[#f5f3ed]">Cancelar</button>
                        <button type="submit"
                                class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Subir archivos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
