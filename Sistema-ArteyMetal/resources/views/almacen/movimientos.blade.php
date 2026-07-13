<x-app-layout>
    <x-slot name="header">
        <span>Historial de movimientos - Almacen</span>
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

    <div class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <form method="GET" class="flex min-w-0 flex-1" id="search-form">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por producto, concepto..."
                        class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900 placeholder:text-[#9a8e78] focus:border-[#b9943d] focus:ring-1 focus:ring-[#b9943d]/40" />
                    <input type="hidden" name="tipo" value="{{ request('tipo') }}" />
                    <input type="hidden" name="producto_id" value="{{ request('producto_id') }}" />
                    <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}" />
                    <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}" />
                </form>
                <button type="submit" form="search-form" title="Buscar"
                    class="h-10 w-10 rounded-xl bg-blue-600 hover:bg-blue-700 flex items-center justify-center shrink-0">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none">
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button x-on:click="open = !open" title="Filtrar"
                        class="btn-icon bg-sky-500 hover:bg-sky-600"
                        :class="{ 'is-active': '{{ request('tipo') }}' !== '' || '{{ request('producto_id') }}' !== '' || '{{ request('fecha_desde') }}' !== '' || '{{ request('fecha_hasta') }}' !== '' }">
                        <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none">
                    </button>
                    <div x-show="open" x-on:click.away="open = false" x-cloak
                        class="absolute right-0 z-30 mt-2 w-80 rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-xl">
                        <form method="GET" class="space-y-4">
                            <input type="hidden" name="q" value="{{ request('q') }}" />
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Tipo</label>
                                <select name="tipo" class="w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700">
                                    <option value="">Todos</option>
                                    <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                                    <option value="salida" {{ request('tipo') === 'salida' ? 'selected' : '' }}>Salida</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Producto</label>
                                <select name="producto_id" class="w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700">
                                    <option value="">Todos</option>
                                    @foreach ($productos as $p)
                                        <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>{{ $p->codigo }} - {{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Desde</label>
                                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Hasta</label>
                                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700" />
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <a href="{{ route('almacen.movimientos') }}" class="rounded-xl border border-[#d1be8a] px-4 py-2 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                                <button type="submit" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600">Aplicar filtros</button>
                            </div>
                        </form>
                    </div>
                </div>
                @if(request('tipo') || request('producto_id') || request('fecha_desde') || request('fecha_hasta') || request('q'))
                    <a href="{{ route('almacen.movimientos') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">#</th>
                            <th class="px-4 py-3 font-semibold">Producto</th>
                            <th class="px-4 py-3 font-semibold">Tipo</th>
                            <th class="px-4 py-3 font-semibold text-right">Cantidad</th>
                            <th class="px-4 py-3 font-semibold text-right">Stock</th>
                            <th class="px-4 py-3 font-semibold">Concepto</th>
                            <th class="px-4 py-3 font-semibold">Registrado por</th>
                            <th class="px-4 py-3 font-semibold">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse($movimientos as $mov)
                            <tr class="hover:bg-[#fffbee]">
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $mov->id }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-[#2d2b24]">{{ $mov->producto?->codigo }}</span>
                                    <span class="text-[#4a4026]"> - {{ $mov->producto?->nombre }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($mov->tipo === 'entrada')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Entrada</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Salida</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium">{{ $mov->cantidad }}</td>
                                <td class="px-4 py-3 text-right text-[#4a4026]">{{ $mov->stock_resultante }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $mov->concepto ?? '-' }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $mov->usuario?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-[#4a4026] whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-[#777]">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="px-1">
            {{ $movimientos->appends(request()->query())->links('pagination.gold') }}
        </div>
    </div>
</x-app-layout>
