<x-app-layout>
    <x-slot name="header">
        <span>Repartidor</span>
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
        <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Codigo</th>
                            <th class="px-4 py-3 font-semibold">Cliente</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse($pedidos as $pedido)
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $pedido->nombre_cliente }}</td>
                                <td class="px-4 py-3">
                                    @if($pedido->estado === 'listo_entrega')
                                        <span class="rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Listo para recoger</span>
                                    @elseif($pedido->estado === 'en_transporte')
                                        <span class="rounded-lg bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">En transporte</span>
                                    @else
                                        <span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-medium text-[#6a5122]">{{ $pedido->estado }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('repartidor.show', $pedido) }}" class="btn-icon-sm" style="background-color:#0891B2" title="Ver detalle">
                                            <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-[#777]">No hay pedidos pendientes.</td>
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
