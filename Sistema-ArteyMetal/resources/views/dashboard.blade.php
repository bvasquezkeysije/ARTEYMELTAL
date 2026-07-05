<x-app-layout>
    <x-slot name="header">
        <span>Inicio</span>
    </x-slot>

    <div class="space-y-5">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Pedidos</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f1f1f]">{{ $metricas['pedidos_total'] }}</p>
                <p class="mt-1 text-sm text-[#666]">Total registrados</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Produccion</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f1f1f]">{{ $metricas['pedidos_produccion'] }}</p>
                <p class="mt-1 text-sm text-[#666]">En proceso</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Entregas</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f1f1f]">{{ $metricas['pedidos_listo_entrega'] }}</p>
                <p class="mt-1 text-sm text-[#666]">Listos para entregar</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Ventas hoy</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f1f1f]">S/ {{ number_format($metricas['ventas_hoy'], 2) }}</p>
                <p class="mt-1 text-sm text-[#666]">Cobrado del dia</p>
            </article>
        </section>

        <section class="grid gap-4 md:grid-cols-2">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Clientes registrados</p>
                <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $metricas['clientes_total'] }}</p>
            </article>
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Productos en catalogo</p>
                <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $metricas['productos_total'] }}</p>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Ventas ultimos 14 dias</h3>
                    <p class="text-sm text-[#6f6858]">Monto cobrado por dia.</p>
                </div>
                <div class="h-[280px]">
                    <canvas id="graficoVentas14Dias"></canvas>
                </div>
            </article>

            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Pedidos por estado</h3>
                    <p class="text-sm text-[#6f6858]">Distribucion actual de pedidos.</p>
                </div>
                <div class="h-[280px]">
                    <canvas id="graficoPedidosEstado"></canvas>
                </div>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            <article class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
                <div class="border-b border-[#efeee9] bg-[#faf8f2] px-4 py-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Ultimos pedidos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-[#6a5a39]">
                            <tr>
                                <th class="px-4 py-2.5">Codigo</th>
                                <th class="px-4 py-2.5">Cliente</th>
                                <th class="px-4 py-2.5">Estado</th>
                                <th class="px-4 py-2.5 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#efeee9]">
                            @forelse ($ultimosPedidos as $pedido)
                                <tr>
                                    <td class="px-4 py-2.5">{{ $pedido->codigo }}</td>
                                    <td class="px-4 py-2.5">{{ $pedido->nombre_cliente }}</td>
                                    <td class="px-4 py-2.5">{{ str_replace('_', ' ', $pedido->estado) }}</td>
                                    <td class="px-4 py-2.5 text-right">{{ $pedido->monto_total !== null ? 'S/ ' . number_format((float) $pedido->monto_total, 2) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-[#777]">No hay pedidos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
                <div class="border-b border-[#efeee9] bg-[#faf8f2] px-4 py-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Ultimas ventas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-[#6a5a39]">
                            <tr>
                                <th class="px-4 py-2.5">Codigo</th>
                                <th class="px-4 py-2.5">Tipo</th>
                                <th class="px-4 py-2.5">Cliente</th>
                                <th class="px-4 py-2.5 text-right">Cobrado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#efeee9]">
                            @forelse ($ultimasVentas as $venta)
                                <tr>
                                    <td class="px-4 py-2.5">{{ $venta->codigo }}</td>
                                    <td class="px-4 py-2.5">{{ $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock' }}</td>
                                    <td class="px-4 py-2.5">{{ $venta->cliente_nombre ?: '-' }}</td>
                                    <td class="px-4 py-2.5 text-right">S/ {{ number_format((float) $venta->monto_cobrado, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-[#777]">No hay ventas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const dataVentas = @json($graficos['ventas_14_dias']);
            const dataPedidos = @json($graficos['pedidos_estado']);

            const colorDorado = '#b9943d';
            const colorDoradoSuave = 'rgba(185, 148, 61, 0.2)';
            const colorTexto = '#5a4a2a';

            const ventasCanvas = document.getElementById('graficoVentas14Dias');
            if (ventasCanvas) {
                new Chart(ventasCanvas, {
                    type: 'line',
                    data: {
                        labels: dataVentas.labels,
                        datasets: [{
                            label: 'S/ cobrado',
                            data: dataVentas.data,
                            borderColor: colorDorado,
                            backgroundColor: colorDoradoSuave,
                            borderWidth: 2,
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                            pointBackgroundColor: colorDorado,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: {
                                ticks: { color: colorTexto },
                                grid: { color: '#f0eadc' },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: colorTexto },
                                grid: { color: '#f0eadc' },
                            },
                        },
                    },
                });
            }

            const pedidosCanvas = document.getElementById('graficoPedidosEstado');
            if (pedidosCanvas) {
                new Chart(pedidosCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: dataPedidos.labels,
                        datasets: [{
                            data: dataPedidos.data,
                            backgroundColor: ['#b9943d', '#8b6a2e', '#d6ba6b', '#1f8a63', '#be4e4e'],
                            borderColor: '#ffffff',
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: colorTexto, boxWidth: 12, usePointStyle: true },
                            },
                        },
                    },
                });
            }
        })();
    </script>
</x-app-layout>
