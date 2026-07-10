@php
    $categoriasMap = $categoriasMap ?? [];
@endphp

<x-app-layout>
    <x-slot name="header">
        <span>Productos</span>
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

    <div
        x-data="{
            modalProducto: false,
            filtrosAbiertos: false,
            productoVista: null,
            indiceImagen: 0,
            modalCategorias: false,
            categoriasAdmin: @js($categorias->map(fn($c) => ['id' => $c->id, 'slug' => $c->slug, 'nombre' => $c->nombre, 'activo' => (bool) $c->activo])->values()),
            nuevaCategoria: '',
            guardandoCategoria: false,
            mensajeCategorias: '',
            errorCategorias: false,
            editandoId: null,
            editandoNombre: '',
            get csrf() {
                return document.querySelector('meta[name=csrf-token]')?.content || '';
            },
            abrirProducto(data) {
                this.productoVista = data;
                this.indiceImagen = 0;
                this.modalProducto = true;
            },
            cerrarProducto() {
                this.modalProducto = false;
            },
            tieneImagenes() {
                return this.productoVista && this.productoVista.imagenes && this.productoVista.imagenes.length > 0;
            },
            imagenActual() {
                if (!this.tieneImagenes()) {
                    return null;
                }
                return this.productoVista.imagenes[this.indiceImagen] ?? null;
            },
            siguienteImagen() {
                if (!this.tieneImagenes()) {
                    return;
                }
                this.indiceImagen = (this.indiceImagen + 1) % this.productoVista.imagenes.length;
            },
            anteriorImagen() {
                if (!this.tieneImagenes()) {
                    return;
                }
                this.indiceImagen = (this.indiceImagen - 1 + this.productoVista.imagenes.length) % this.productoVista.imagenes.length;
            },
            abrirModalCategorias() {
                this.modalCategorias = true;
                this.cargarCategorias();
                this.mensajeCategorias = '';
                this.errorCategorias = false;
            },
            async cargarCategorias() {
                try {
                    const response = await fetch('{{ route('productos.categorias.json', [], false) }}', {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const contentType = response.headers.get('content-type') || '';
                    const data = contentType.includes('application/json')
                        ? await response.json()
                        : null;

                    if (response.ok && data?.ok && Array.isArray(data.categorias)) {
                        this.categoriasAdmin = data.categorias;
                        this.mensajeCategorias = '';
                        this.errorCategorias = false;
                        return;
                    }

                    if ((this.categoriasAdmin || []).length === 0) {
                        this.mensajeCategorias = data?.message || `No se pudo cargar categorias (HTTP ${response.status}).`;
                        this.errorCategorias = true;
                    } else {
                        this.mensajeCategorias = '';
                        this.errorCategorias = false;
                    }
                } catch (e) {
                    if ((this.categoriasAdmin || []).length === 0) {
                        this.mensajeCategorias = 'No se pudo cargar categorias.';
                        this.errorCategorias = true;
                    } else {
                        this.mensajeCategorias = '';
                        this.errorCategorias = false;
                    }
                }
            },
            async crearCategoria() {
                const nombre = (this.nuevaCategoria || '').trim();
                if (!nombre) {
                    this.mensajeCategorias = 'Ingresa nombre de categoria.';
                    this.errorCategorias = true;
                    return;
                }

                this.guardandoCategoria = true;
                try {
                    const response = await fetch('{{ route('productos.categorias.store', [], false) }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({ nombre }),
                    });
                    const contentType = response.headers.get('content-type') || '';
                    const data = contentType.includes('application/json')
                        ? await response.json()
                        : { ok: false, message: `Respuesta no JSON (HTTP ${response.status}).` };
                    if (!response.ok || !data?.ok) {
                        this.mensajeCategorias = data?.message || `No se pudo crear categoria (HTTP ${response.status}).`;
                        this.errorCategorias = true;
                        return;
                    }

                    this.nuevaCategoria = '';
                    this.mensajeCategorias = data.message || 'Categoria creada.';
                    this.errorCategorias = false;
                    await this.cargarCategorias();
                } catch (e) {
                    this.mensajeCategorias = 'Error al crear categoria (conexion o sesion).';
                    this.errorCategorias = true;
                } finally {
                    this.guardandoCategoria = false;
                }
            },
            iniciarEdicion(categoria) {
                this.editandoId = categoria.id;
                this.editandoNombre = categoria.nombre;
            },
            cancelarEdicion() {
                this.editandoId = null;
                this.editandoNombre = '';
            },
            async guardarEdicion(categoria) {
                const nombre = (this.editandoNombre || '').trim();
                if (!nombre) {
                    this.mensajeCategorias = 'Ingresa nombre valido.';
                    this.errorCategorias = true;
                    return;
                }

                this.guardandoCategoria = true;
                try {
                    const response = await fetch(`/productos/categorias/${categoria.id}`, {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({ nombre }),
                    });
                    const contentType = response.headers.get('content-type') || '';
                    const data = contentType.includes('application/json')
                        ? await response.json()
                        : { ok: false, message: `Respuesta no JSON (HTTP ${response.status}).` };
                    if (!response.ok || !data?.ok) {
                        this.mensajeCategorias = data?.message || 'No se pudo actualizar categoria.';
                        this.errorCategorias = true;
                        return;
                    }

                    this.mensajeCategorias = data.message || 'Categoria actualizada.';
                    this.errorCategorias = false;
                    this.cancelarEdicion();
                    await this.cargarCategorias();
                    window.location.reload();
                } catch (e) {
                    this.mensajeCategorias = 'Error al actualizar categoria.';
                    this.errorCategorias = true;
                } finally {
                    this.guardandoCategoria = false;
                }
            },
            async toggleCategoria(categoria) {
                this.guardandoCategoria = true;
                try {
                    const response = await fetch(`/productos/categorias/${categoria.id}/toggle`, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                    });
                    const contentType = response.headers.get('content-type') || '';
                    const data = contentType.includes('application/json')
                        ? await response.json()
                        : { ok: false, message: `Respuesta no JSON (HTTP ${response.status}).` };
                    if (!response.ok || !data?.ok) {
                        this.mensajeCategorias = data?.message || 'No se pudo actualizar estado.';
                        this.errorCategorias = true;
                        return;
                    }

                    this.mensajeCategorias = data.message || 'Estado actualizado.';
                    this.errorCategorias = false;
                    await this.cargarCategorias();
                    window.location.reload();
                } catch (e) {
                    this.mensajeCategorias = 'Error al cambiar estado.';
                    this.errorCategorias = true;
                } finally {
                    this.guardandoCategoria = false;
                }
            }
        }"
        class="space-y-5"
    >
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm" x-data="{ filtrosAbiertos: false }">
            <div class="flex items-center gap-2">
                <form id="search-form" method="GET" action="{{ route('productos.index') }}" class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" placeholder="Buscar por codigo, nombre o descripcion" />
                </form>
                <button type="submit" form="search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <button
                    type="button"
                    @click="filtrosAbiertos = !filtrosAbiertos"
                    class="btn-icon bg-sky-500 hover:bg-sky-600"
                    title="Filtrar"
                    :class="{ 'is-active': filtrosAbiertos || '{{ $categoria || ($filtroActivo !== null && $filtroActivo !== '') }}' === '1' }"
                >
                    <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if($categoria || ($filtroActivo !== null && $filtroActivo !== '') || $busqueda)
                    <a href="{{ route('productos.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
                <button
                    type="button"
                    @click="abrirModalCategorias()"
                    class="btn-icon" style="background-color:#9333EA"
                    title="Gestionar categorias"
                >
                    <img src="{{ asset('icons/gestionar-categorias.png') }}" alt="Categorias" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if(in_array(auth()->user()->rol?->nombre, ['administrador', 'almacenero'], true))
                <a href="{{ route('productos.create') }}" class="btn-icon" style="background-color:#09090f;color:white" title="Nuevo producto">
                    <img src="{{ asset('icons/nuevo.ico') }}" alt="Nuevo" class="h-5 w-5 object-contain pointer-events-none" />
                </a>
                @endif
            </div>

            <form x-show="filtrosAbiertos" x-transition method="GET" action="{{ route('productos.index') }}" class="mt-4 flex flex-wrap items-end gap-4 border-t border-[#efe7d2] pt-4">
                <input type="hidden" name="q" value="{{ $busqueda }}" />
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Categoria</label>
                    <select name="categoria" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoriaItem)
                            <option value="{{ $categoriaItem->slug }}" @selected($categoria === $categoriaItem->slug)>{{ $categoriaItem->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</label>
                    <select name="activo" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Todos</option>
                        <option value="1" @selected($filtroActivo === '1')>Activo</option>
                        <option value="0" @selected($filtroActivo === '0')>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus:outline-none focus-visible:outline-[none] focus:ring-2 focus:ring-sky-500">Filtrar</button>
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
                            <th class="px-4 py-3 font-semibold">Nombre</th>
                            <th class="px-4 py-3 font-semibold">Categoria</th>
                            <th class="px-4 py-3 font-semibold">Precio ref.</th>
                            <th class="px-4 py-3 font-semibold">Stock Tienda</th>
                            <th class="px-4 py-3 font-semibold">Stock Almacen</th>
                            <th class="px-4 py-3 font-semibold">Total</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($productos as $producto)
                            @php
                                $productoVistaData = [
                                    'codigo' => $producto->codigo,
                                    'nombre' => $producto->nombre,
                                    'categoria' => $categoriasMap[$producto->categoria] ?? $producto->categoria,
                                    'precio_referencia' => $producto->precio_referencia !== null ? 'S/ ' . number_format((float) $producto->precio_referencia, 2) : '-',
                                    'stock_actual' => (int) $producto->stock_actual,
                                    'stock_tienda' => (int) ($producto->stock_tienda ?? 0),
                                    'stock_almacen' => (int) ($producto->stock_almacen ?? 0),
                                    'estado' => $producto->activo ? 'Activo' : 'Inactivo',
                                    'descripcion' => $producto->descripcion ?: '-',
                                    'imagenes' => $producto->imagenes->map(function ($img) {
                                        return [
                                            'url' => route('productos.imagen.ver', $img, false),
                                            'nombre' => $img->nombre_original ?: 'Imagen del producto',
                                        ];
                                    })->values()->all(),
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $producto->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $producto->nombre }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $categoriasMap[$producto->categoria] ?? $producto->categoria }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $producto->precio_referencia !== null ? 'S/ ' . number_format((float) $producto->precio_referencia, 2) : '-' }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ (int) ($producto->stock_tienda ?? 0) }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ (int) ($producto->stock_almacen ?? 0) }}</td>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $producto->stock_actual }}</td>
                                <td class="px-4 py-3">
                                    @if($producto->activo)
                                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Activo</span>
                                    @else
                                        <span class="rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->tienePermiso('productos.ver'))
                                            <button
                                                type="button"
                                                @click="abrirProducto(@js($productoVistaData))"
                                                class="btn-icon-sm" style="background-color:#0891B2"
                                                title="Ver detalle"
                                            >
                                                <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @endif
                                        @if(auth()->user()->tienePermiso('productos.gestionar'))
                                            <a href="{{ route('productos.edit', $producto) }}" class="btn-icon-sm bg-amber-400 hover:bg-amber-500" title="Editar">
                                                <img src="{{ asset('icons/editar.ico') }}" alt="Editar" class="h-4 w-4 object-contain pointer-events-none" />
                                            </a>
                                            <form method="POST" action="{{ route('productos.destroy', $producto) }}" onsubmit="return confirm('Deseas eliminar este producto?')">
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
                                <td colspan="7" class="px-4 py-8 text-center text-[#777]">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#efe7d2] px-4 py-3">{{ $productos->links('pagination.gold') }}</div>
        </div>

        <template x-teleport="body">
            <div x-show="modalProducto" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="cerrarProducto()"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                    <h3 class="text-base font-semibold text-[#2a2419]">Detalle rapido de producto</h3>
                    <button type="button" @click="cerrarProducto()" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>

                <div class="grid gap-5 p-5 md:grid-cols-2" x-show="productoVista">
                    <div class="space-y-3">
                        <div class="relative flex h-[320px] items-center justify-center overflow-hidden rounded-xl border border-[#e5dec8] bg-[#fcf9f3]">
                            <template x-if="imagenActual()">
                                <img :src="imagenActual().url" :alt="imagenActual().nombre" class="h-full w-full object-contain" />
                            </template>
                            <template x-if="!imagenActual()">
                                <div class="px-4 text-center text-sm text-[#777]">Sin imagenes para este producto.</div>
                            </template>

                            <button
                                type="button"
                                @click="anteriorImagen()"
                                x-show="tieneImagenes() && productoVista.imagenes.length > 1"
                                class="absolute left-2 inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#d1be8a] bg-white/95 text-[#5a4314] shadow hover:bg-[#fff5dd]"
                                title="Imagen anterior"
                            >
                                <span class="text-lg leading-none">&#8249;</span>
                            </button>

                            <button
                                type="button"
                                @click="siguienteImagen()"
                                x-show="tieneImagenes() && productoVista.imagenes.length > 1"
                                class="absolute right-2 inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#d1be8a] bg-white/95 text-[#5a4314] shadow hover:bg-[#fff5dd]"
                                title="Imagen siguiente"
                            >
                                <span class="text-lg leading-none">&#8250;</span>
                            </button>
                        </div>

                        <p class="text-xs text-[#777]" x-show="tieneImagenes()" x-text="'Imagen ' + (indiceImagen + 1) + ' de ' + productoVista.imagenes.length"></p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Codigo</p>
                            <p class="mt-1 text-[#1f1f1f]" x-text="productoVista?.codigo"></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Nombre</p>
                            <p class="mt-1 text-[#1f1f1f]" x-text="productoVista?.nombre"></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Categoria</p>
                            <p class="mt-1 text-[#1f1f1f]" x-text="productoVista?.categoria"></p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Precio ref.</p>
                                <p class="mt-1 text-[#1f1f1f]" x-text="productoVista?.precio_referencia"></p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Stock</p>
                                <div class="mt-1 flex gap-4 text-[#1f1f1f]">
                                    <span>Tienda: <strong x-text="productoVista?.stock_tienda"></strong></span>
                                    <span>Almacen: <strong x-text="productoVista?.stock_almacen"></strong></span>
                                    <span class="text-[#8a6a2e]">| Total: <strong x-text="productoVista?.stock_actual"></strong></span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Estado</p>
                                <p class="mt-1 text-[#1f1f1f]" x-text="productoVista?.estado"></p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Descripcion</p>
                            <p class="mt-1 text-[#1f1f1f]" x-text="productoVista?.descripcion"></p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="modalCategorias" style="display: none;">
                <div x-transition.opacity class="fixed inset-0 z-[60] bg-black/50" @click="modalCategorias = false"></div>
                <div x-transition class="fixed inset-0 z-[70] flex items-center justify-center p-4">
                    <div class="w-full max-w-2xl rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                        <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                            <h3 class="text-base font-semibold text-[#2a2419]">Gestionar categorias</h3>
                            <button type="button" @click="modalCategorias = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                            </button>
                        </div>

                        <div class="space-y-4 p-5">
                            <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                <input x-model="nuevaCategoria" type="text" class="rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" placeholder="Nueva categoria" />
                                <button type="button" @click="crearCategoria()" :disabled="guardandoCategoria" class="btn-icon bg-[#111] hover:bg-[#262626] disabled:opacity-60" title="Crear categoria">
                                    <img src="{{ asset('icons/nuevo.ico') }}" alt="Crear" class="h-5 w-5 object-contain pointer-events-none" />
                                </button>
                            </div>

                            <p x-show="mensajeCategorias" class="text-xs" :class="errorCategorias ? 'text-rose-700' : 'text-emerald-700'" x-text="mensajeCategorias"></p>

                            <div class="max-h-80 overflow-auto rounded-xl border border-[#e5dec8]">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                                        <tr>
                                            <th class="px-3 py-2 font-semibold">Nombre</th>
                                            <th class="px-3 py-2 font-semibold">Slug</th>
                                            <th class="px-3 py-2 font-semibold">Estado</th>
                                            <th class="px-3 py-2 font-semibold text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#efeee9]">
                                        <template x-for="cat in categoriasAdmin" :key="cat.id">
                                            <tr>
                                                <td class="px-3 py-2 text-[#4a4026]">
                                                    <template x-if="editandoId !== cat.id">
                                                        <span x-text="cat.nombre"></span>
                                                    </template>
                                                    <template x-if="editandoId === cat.id">
                                                        <input x-model="editandoNombre" type="text" class="w-full rounded-lg border border-[#d1be8a] px-2 py-1 text-sm" />
                                                    </template>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-[#777]" x-text="cat.slug"></td>
                                                <td class="px-3 py-2">
                                                    <span class="rounded-lg px-2 py-1 text-xs font-medium" :class="cat.activo ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'" x-text="cat.activo ? 'Activo' : 'Inactivo'"></span>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <div class="flex justify-end gap-2">
                                                        <template x-if="editandoId !== cat.id">
                                                            <button type="button" @click="iniciarEdicion(cat)" class="btn-icon-sm bg-amber-400 hover:bg-amber-500" title="Editar">
                                                                <img src="{{ asset('icons/editar.ico') }}" alt="Editar" class="h-4 w-4 object-contain pointer-events-none" />
                                                            </button>
                                                        </template>
                                                        <template x-if="editandoId === cat.id">
                                                            <button type="button" @click="guardarEdicion(cat)" class="rounded-lg border border-emerald-300 px-2.5 py-1 text-xs text-emerald-700 hover:bg-emerald-50">Guardar</button>
                                                        </template>
                                                        <template x-if="editandoId === cat.id">
                                                            <button type="button" @click="cancelarEdicion()" class="rounded-lg border border-[#d1be8a] px-2.5 py-1 text-xs text-[#5a4314] hover:bg-[#fff5dd]">Cancelar</button>
                                                        </template>
                                                        <button type="button" @click="toggleCategoria(cat)" :class="cat.activo ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-500 hover:bg-emerald-600'" class="btn-icon-sm" :title="cat.activo ? 'Inactivar' : 'Activar'">
                                                            <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="Toggle" class="h-4 w-4 object-contain pointer-events-none" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="categoriasAdmin.length === 0">
                                            <td colspan="4" class="px-3 py-4 text-center text-[#777]">Sin categorias registradas.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

