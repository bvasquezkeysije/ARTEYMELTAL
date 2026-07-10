<x-app-layout>
    <x-slot name="header">
        <span>Almacen — Pedidos</span>
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

    <div class="space-y-4">
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
                            @php $cantRecoge = $pedido->productos->sum('cantidad_recoge') ?: $pedido->productos->sum('cantidad'); @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->nombre_cliente }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">
                                    @if($pedido->productos->count() > 1)
                                        <div>
                                            @foreach($pedido->productos as $i => $p)
                                                <div>{{ $i + 1 }}. {{ $p->nombre }} ({{ $p->cantidad_recoge ?? $p->cantidad }})</div>
                                            @endforeach
                                        </div>
                                    @else
                                        @php $first = $pedido->productos->first(); @endphp
                                        {{ $first?->nombre ?? '-' }} ({{ $first?->cantidad_recoge ?? $first?->cantidad ?? $pedido->cantidad }})
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($pedido->estado === 'en_transporte')
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">En transporte</span>
                                    @elseif($pedido->estado === 'en_almacen')
                                        <span class="rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">En almacen</span>
                                    @elseif($pedido->estado === 'listo_recoger')
                                        <span class="rounded-lg bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700">Listo recoger</span>
                                    @elseif($pedido->estado === 'en_tienda')
                                        <span class="rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En tienda</span>
                                    @elseif($pedido->estado === 'entregado')
                                        <span class="rounded-lg bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-700">Entregado</span>
                                    @else
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ $pedido->estado }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($pedido->estado === 'en_transporte')
                                            <form action="{{ route('almacen.pedidos.recibir', $pedido) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">
                                                    Recibir
                                                </button>
                                            </form>
                                        @endif
                                        @if($pedido->estado === 'listo_recoger')
                                            <form action="{{ route('almacen.pedidos.entregar_cliente', $pedido) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Entregar este pedido al cliente? Se dara salida del almacen.')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-700">
                                                    Entregar al cliente
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[#777]">No hay pedidos pendientes en almacen.</td>
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
</x-app-layout>
