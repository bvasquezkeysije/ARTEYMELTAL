<x-app-layout>
    <x-slot name="header">
        <span>Inventario - Productos en almacen</span>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex flex-1 items-center gap-2 min-w-0">
                    <input type="text" id="busqueda" placeholder="Buscar por codigo o nombre..."
                        class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-[#251e12] placeholder-[#9f8c62] focus:border-[#cba34d] focus:ring-[#cba34d]"
                        value="{{ request('q') }}" />
                    <button type="button" id="btnBuscar" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700">
                        <img src="/icons/buscar.ico" alt="Buscar" width="18" height="18" class="pointer-events-none brightness-0 invert" />
                    </button>
                    <button type="button" id="btnFiltrar" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500 hover:bg-sky-600">
                        <img src="/icons/filtros.ico" alt="Filtrar" width="18" height="18" class="pointer-events-none brightness-0 invert" />
                    </button>
                </div>
                @if(auth()->user()->tienePermiso('almacen.gestionar'))
                    <button type="button" id="btnEntrada" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-600 px-4 text-sm font-semibold text-white hover:bg-green-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Entrada
                    </button>
                    <button type="button" id="btnSalida" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        Salida
                    </button>
                @endif
            </div>

            <div id="panelFiltros" class="mt-4 hidden border-t border-[#efe7d2] pt-4">
                <form id="formFiltros" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Stock</label>
                        <select name="stock" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                            <option value="">Todos</option>
                            <option value="con" {{ request('stock') === 'con' ? 'selected' : '' }}>Con stock</option>
                            <option value="bajo" {{ request('stock') === 'bajo' ? 'selected' : '' }}>Stock bajo (<=5)</option>
                            <option value="sin" {{ request('stock') === 'sin' ? 'selected' : '' }}>Sin stock</option>
                        </select>
                    </div>
                    <button type="submit" class="rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">Filtrar</button>
                    <a href="{{ route('almacen.productos') }}" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                </form>
            </div>
        </div>

        <div id="tablaContainer" class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            @include('almacen._tabla_productos')
        </div>

        <div id="paginationContainer" class="border-t border-[#efe7d2] px-4 py-3">
            {{ $productos->links('pagination.gold') }}
        </div>
    </div>

    @if(auth()->user()->tienePermiso('almacen.gestionar'))
        <div x-data="{ modalEntrada: false, modalSalida: false, productoId: '', productoCodigo: '' }">
            <template x-teleport="body">
                <div x-show="modalEntrada" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="modalEntrada = false">
                    <div class="w-full max-w-lg rounded-2xl border border-[#e5dec8] bg-white shadow-2xl">
                        <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-4">
                            <h3 class="text-base font-semibold text-[#2a2419]">Registrar entrada</h3>
                            <button type="button" @click="modalEntrada = false" class="btn-icon-sm bg-red-600 hover:bg-red-700">
                                <img src="/icons/cerrar.ico" alt="Cerrar" width="16" height="16" class="pointer-events-none brightness-0 invert" />
                            </button>
                        </div>
                        <form method="POST" action="{{ route('almacen.entrada.store') }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Producto</label>
                                <select name="producto_id" required class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                                    <option value="">Seleccionar producto</option>
                                    @foreach ($todosProductos as $p)
                                        <option value="{{ $p->id }}">{{ $p->codigo }} - {{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Ubicacion</label>
                                <select name="ubicacion" required class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                                    <option value="">Seleccionar</option>
                                    <option value="almacen">Almacen</option>
                                    <option value="tienda">Tienda</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cantidad</label>
                                <input type="number" name="cantidad" required min="1" class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Concepto</label>
                                <input type="text" name="concepto" placeholder="Ej: Ingreso de fabricacion" class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" />
                            </div>
                            <div class="border-t border-[#efe7d2] pt-4 flex justify-end gap-2">
                                <button type="button" @click="modalEntrada = false" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Cancelar</button>
                                <button type="submit" class="rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Registrar entrada</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div x-show="modalSalida" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="modalSalida = false">
                    <div class="w-full max-w-lg rounded-2xl border border-[#e5dec8] bg-white shadow-2xl">
                        <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-4">
                            <h3 class="text-base font-semibold text-[#2a2419]">Registrar salida</h3>
                            <button type="button" @click="modalSalida = false" class="btn-icon-sm bg-red-600 hover:bg-red-700">
                                <img src="/icons/cerrar.ico" alt="Cerrar" width="16" height="16" class="pointer-events-none brightness-0 invert" />
                            </button>
                        </div>
                        <form method="POST" action="{{ route('almacen.salida.store') }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Producto</label>
                                <select name="producto_id" required class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                                    <option value="">Seleccionar producto</option>
                                    @foreach ($todosProductos as $p)
                                        <option value="{{ $p->id }}">{{ $p->codigo }} - {{ $p->nombre }} (T:{{ $p->stock_tienda }} / A:{{ $p->stock_almacen }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Ubicacion</label>
                                <select name="ubicacion" required class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                                    <option value="">Seleccionar</option>
                                    <option value="tienda">Tienda</option>
                                    <option value="almacen">Almacen</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Cantidad</label>
                                <input type="number" name="cantidad" required min="1" class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Concepto</label>
                                <input type="text" name="concepto" placeholder="Ej: Salida a tienda" class="mt-1 block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" />
                            </div>
                            <div class="border-t border-[#efe7d2] pt-4 flex justify-end gap-2">
                                <button type="button" @click="modalSalida = false" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Cancelar</button>
                                <button type="submit" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Registrar salida</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnBuscar = document.getElementById('btnBuscar');
            const btnFiltrar = document.getElementById('btnFiltrar');
            const panelFiltros = document.getElementById('panelFiltros');
            const busqueda = document.getElementById('busqueda');
            const formFiltros = document.getElementById('formFiltros');

            let filtrosAbiertos = false;
            btnFiltrar?.addEventListener('click', function () {
                filtrosAbiertos = !filtrosAbiertos;
                panelFiltros.classList.toggle('hidden', !filtrosAbiertos);
                btnFiltrar.style.filter = filtrosAbiertos ? 'brightness(0.8)' : '';
            });

            function cargarPagina(url) {
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(d => {
                        document.getElementById('tablaContainer').innerHTML = d.html;
                        document.getElementById('paginationContainer').innerHTML = d.pagination;
                        document.querySelectorAll('#paginationContainer a').forEach(a => {
                            a.addEventListener('click', function (e) {
                                e.preventDefault();
                                cargarPagina(this.href);
                            });
                        });
                    });
            }

            busqueda?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const url = '{{ route('almacen.productos') }}?q=' + encodeURIComponent(this.value);
                    cargarPagina(url);
                }
            });

            btnBuscar?.addEventListener('click', function () {
                const url = '{{ route('almacen.productos') }}?q=' + encodeURIComponent(busqueda.value);
                cargarPagina(url);
            });

            formFiltros?.addEventListener('submit', function (e) {
                e.preventDefault();
                const params = new URLSearchParams(new FormData(this));
                const q = busqueda.value;
                if (q) params.set('q', q);
                cargarPagina('{{ route('almacen.productos') }}?' + params.toString());
            });
        });
    </script>
</x-app-layout>
