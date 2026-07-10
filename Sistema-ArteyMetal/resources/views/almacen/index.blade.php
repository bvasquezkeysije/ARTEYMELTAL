<x-app-layout>
    <x-slot name="header">
        <span>Almacen</span>
    </x-slot>

    <div class="space-y-5">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Productos en almacen</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f1f1f]">{{ $totalProductos }}</p>
                <p class="mt-1 text-sm text-[#666]">Total registrados</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Stock total</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f1f1f]">{{ $totalStock }}</p>
                <p class="mt-1 text-sm text-[#666]">Unidades en inventario</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Entradas hoy</p>
                <p class="mt-2 text-3xl font-semibold text-green-700">{{ $entradasHoy }}</p>
                <p class="mt-1 text-sm text-[#666]">Unidades ingresadas</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Salidas hoy</p>
                <p class="mt-2 text-3xl font-semibold text-red-600">{{ $salidasHoy }}</p>
                <p class="mt-1 text-sm text-[#666]">Unidades retiradas</p>
            </article>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Con stock</p>
                <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $productosConStock }}</p>
                <p class="mt-1 text-sm text-[#666]">Productos disponibles</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-amber-600">Stock bajo (<=5)</p>
                <p class="mt-2 text-2xl font-semibold text-amber-700">{{ $stockBajo }}</p>
                <p class="mt-1 text-sm text-[#666]">Por debajo del minimo</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-red-600">Sin stock</p>
                <p class="mt-2 text-2xl font-semibold text-red-700">{{ $productosSinStock }}</p>
                <p class="mt-1 text-sm text-[#666]">Agotados</p>
            </article>
        </section>

        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->tienePermiso('almacen.gestionar'))
                <a href="{{ route('almacen.productos') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#09090f] px-4 py-2.5 text-sm font-semibold text-white hover:brightness-125">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    Ver inventario
                </a>
                <a href="{{ route('almacen.movimientos') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Historial de movimientos
                </a>
            @endif
        </div>

        <section class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="border-b border-[#efeee9] bg-[#faf8f2] px-4 py-3">
                <h3 class="font-semibold text-[#5a4a2a]">Ultimos movimientos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-[#6a5a39]">
                        <tr>
                            <th class="px-4 py-2.5">Producto</th>
                            <th class="px-4 py-2.5">Tipo</th>
                            <th class="px-4 py-2.5 text-right">Cantidad</th>
                            <th class="px-4 py-2.5 text-right">Stock resultante</th>
                            <th class="px-4 py-2.5">Concepto</th>
                            <th class="px-4 py-2.5">Registrado por</th>
                            <th class="px-4 py-2.5">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($ultimosMovimientos as $mov)
                            <tr>
                                <td class="px-4 py-2.5">
                                    <span class="font-medium text-[#2d2b24]">{{ $mov->producto?->codigo }}</span>
                                    <span class="text-[#4a4026]"> - {{ $mov->producto?->nombre }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($mov->tipo === 'entrada')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Entrada</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Salida</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right font-medium">{{ $mov->cantidad }}</td>
                                <td class="px-4 py-2.5 text-right text-[#4a4026]">{{ $mov->stock_resultante }}</td>
                                <td class="px-4 py-2.5 text-[#4a4026]">{{ $mov->concepto ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-[#4a4026]">{{ $mov->usuario?->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-[#4a4026] whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-[#777]">No hay movimientos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
