<x-app-layout>
    <x-slot name="header">
        <span>Historial de movimientos - Almacen</span>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Tipo</label>
                    <select name="tipo" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ request('tipo') === 'salida' ? 'selected' : '' }}>Salida</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Producto</label>
                    <select name="producto_id" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($productos as $p)
                            <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>{{ $p->codigo }} - {{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm" />
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm" />
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">Filtrar</button>
                <a href="{{ route('almacen.movimientos') }}" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
            </form>
        </div>

        <section class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3 text-right">Stock resultante</th>
                            <th class="px-4 py-3">Concepto</th>
                            <th class="px-4 py-3">Registrado por</th>
                            <th class="px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($movimientos as $mov)
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
                                <td colspan="8" class="px-4 py-6 text-center text-[#777]">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="border-t border-[#efe7d2] px-4 py-3">
            {{ $movimientos->appends(request()->query())->links('pagination.gold') }}
        </div>
    </div>
</x-app-layout>
