<table class="min-w-full text-sm">
    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
        <tr>
            <th class="px-4 py-3">Codigo</th>
            <th class="px-4 py-3">Producto</th>
            <th class="px-4 py-3">Categoria</th>
            <th class="px-4 py-3 text-right">Tienda</th>
            <th class="px-4 py-3 text-right">Almacen</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-right">Precio ref.</th>
            <th class="px-4 py-3 text-center">Estado</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-[#efeee9]">
        @forelse ($productos as $p)
            @php $total = $p->stock_tienda + $p->stock_almacen; @endphp
            <tr class="hover:bg-[#fffbee]">
                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $p->codigo }}</td>
                <td class="px-4 py-3 text-[#4a4026]">{{ $p->nombre }}</td>
                <td class="px-4 py-3 text-[#4a4026]">{{ $p->categoria }}</td>
                <td class="px-4 py-3 text-right font-medium {{ $p->stock_tienda <= 0 ? 'text-red-600' : 'text-[#2d2b24]' }}">{{ $p->stock_tienda }}</td>
                <td class="px-4 py-3 text-right font-medium {{ $p->stock_almacen <= 0 ? 'text-red-600' : 'text-[#2d2b24]' }}">{{ $p->stock_almacen }}</td>
                <td class="px-4 py-3 text-right font-medium {{ $total <= 0 ? 'text-red-600' : ($total <= 5 ? 'text-amber-600' : 'text-[#2d2b24]') }}">
                    {{ $total }}
                </td>
                <td class="px-4 py-3 text-right text-[#4a4026]">
                    @if($p->precio_referencia)
                        S/ {{ number_format($p->precio_referencia, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($p->activo)
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Activo</span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactivo</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-6 text-center text-[#777]">No hay productos registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>
