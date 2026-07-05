<x-app-layout>
    <x-slot name="header">
        <span>Reportes</span>
    </x-slot>

    @php
        $queryVentas = [
            'ventas_fecha_inicio' => $filtrosVentas['fecha_inicio'],
            'ventas_fecha_fin' => $filtrosVentas['fecha_fin'],
            'ventas_tipo' => $filtrosVentas['tipo'],
        ];
        $queryPedidos = array_merge($queryVentas, [
            'pedidos_fecha_inicio' => $filtrosPedidos['fecha_inicio'],
            'pedidos_fecha_fin' => $filtrosPedidos['fecha_fin'],
            'pedidos_estado' => $filtrosPedidos['estado'],
            'pedidos_tipo_entrega' => $filtrosPedidos['tipo_entrega'],
        ]);
        $queryStock = array_merge($queryPedidos, [
            'stock_umbral' => $kpisStock['umbral'],
        ]);
    @endphp

    <div class="space-y-5">
        <section class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#2a2419]">Reporte de ventas</h2>
                    <p class="mt-1 text-sm text-[#6e6758]">Filtra ventas por fecha y tipo. Descarga CSV o Excel.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('reportes.ventas.csv', $queryVentas) }}"
                        class="rounded-xl border border-[#b9943d] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]"
                    >
                        Exportar CSV
                    </a>
                    <a
                        href="{{ route('reportes.ventas.excel', $queryVentas) }}"
                        class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100"
                    >
                        Exportar XLSX
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('reportes.index') }}" class="mt-4 grid gap-3 md:grid-cols-4">
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Fecha inicio</label>
                    <input type="date" name="ventas_fecha_inicio" value="{{ $filtrosVentas['fecha_inicio'] }}" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Fecha fin</label>
                    <input type="date" name="ventas_fecha_fin" value="{{ $filtrosVentas['fecha_fin'] }}" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Tipo</label>
                    <select name="ventas_tipo" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm">
                        <option value="">Todos</option>
                        <option value="stock" @selected($filtrosVentas['tipo'] === 'stock')>Venta stock</option>
                        <option value="pedido" @selected($filtrosVentas['tipo'] === 'pedido')>Cierre pedido</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Aplicar filtro</button>
                </div>
            </form>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Total vendido</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">S/ {{ number_format($kpisVentas['total_vendido'], 2) }}</p>
                </article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Total cobrado</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">S/ {{ number_format($kpisVentas['total_cobrado'], 2) }}</p>
                </article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Ticket promedio</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">S/ {{ number_format($kpisVentas['ticket_promedio'], 2) }}</p>
                </article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Cantidad ventas</p>
                    <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $kpisVentas['cantidad'] }}</p>
                </article>
            </div>

            <div class="mt-4 overflow-x-auto rounded-xl border border-[#e5dec8]">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Codigo</th>
                            <th class="px-4 py-2.5 font-semibold">Fecha</th>
                            <th class="px-4 py-2.5 font-semibold">Tipo</th>
                            <th class="px-4 py-2.5 font-semibold">Cliente</th>
                            <th class="px-4 py-2.5 font-semibold">Monto</th>
                            <th class="px-4 py-2.5 font-semibold">Cobrado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($ventas as $venta)
                            <tr>
                                <td class="px-4 py-2.5">{{ $venta->codigo }}</td>
                                <td class="px-4 py-2.5">{{ optional($venta->fecha_venta)->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5">{{ $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock' }}</td>
                                <td class="px-4 py-2.5">{{ $venta->cliente_nombre ?: '-' }}</td>
                                <td class="px-4 py-2.5">S/ {{ number_format((float) $venta->monto_total, 2) }}</td>
                                <td class="px-4 py-2.5">S/ {{ number_format((float) $venta->monto_cobrado, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-[#777]">Sin ventas para el filtro aplicado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Cobranza por dia</h3>
                    <p class="text-sm text-[#6f6858]">Evolucion diaria del monto cobrado en ventas.</p>
                </div>
                <div class="h-[280px]">
                    <canvas id="graficoVentasPorDia"></canvas>
                </div>
            </article>

            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Ventas por tipo</h3>
                    <p class="text-sm text-[#6f6858]">Distribucion entre stock y cierre de pedido.</p>
                </div>
                <div class="h-[280px]">
                    <canvas id="graficoVentasTipo"></canvas>
                </div>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-3">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Pedidos por estado</h3>
                    <p class="text-sm text-[#6f6858]">Conteo segun etapa operativa.</p>
                </div>
                <div class="h-[260px]">
                    <canvas id="graficoPedidosEstadoReportes"></canvas>
                </div>
            </article>

            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Pedidos por entrega</h3>
                    <p class="text-sm text-[#6f6858]">Comparativo local, delivery y agencia.</p>
                </div>
                <div class="h-[260px]">
                    <canvas id="graficoPedidosEntrega"></canvas>
                </div>
            </article>

            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-semibold text-[#5a4a2a]">Top stock critico</h3>
                    <p class="text-sm text-[#6f6858]">Productos mas cerca de quiebre.</p>
                </div>
                <div class="h-[260px]">
                    <canvas id="graficoStockCritico"></canvas>
                </div>
            </article>
        </section>

        <section class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#2a2419]">Reporte de pedidos</h2>
                    <p class="mt-1 text-sm text-[#6e6758]">Seguimiento de estados y control de entregas atrasadas.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('reportes.pedidos.csv', $queryPedidos) }}"
                        class="rounded-xl border border-[#b9943d] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]"
                    >
                        Exportar CSV
                    </a>
                    <a
                        href="{{ route('reportes.pedidos.excel', $queryPedidos) }}"
                        class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100"
                    >
                        Exportar XLSX
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('reportes.index') }}" class="mt-4 grid gap-3 md:grid-cols-5">
                <input type="hidden" name="ventas_fecha_inicio" value="{{ $filtrosVentas['fecha_inicio'] }}">
                <input type="hidden" name="ventas_fecha_fin" value="{{ $filtrosVentas['fecha_fin'] }}">
                <input type="hidden" name="ventas_tipo" value="{{ $filtrosVentas['tipo'] }}">
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Fecha inicio</label>
                    <input type="date" name="pedidos_fecha_inicio" value="{{ $filtrosPedidos['fecha_inicio'] }}" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Fecha fin</label>
                    <input type="date" name="pedidos_fecha_fin" value="{{ $filtrosPedidos['fecha_fin'] }}" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Estado</label>
                    <select name="pedidos_estado" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm">
                        <option value="">Todos</option>
                        @foreach (['registrado', 'en_produccion', 'listo_entrega', 'entregado', 'cancelado'] as $estado)
                            <option value="{{ $estado }}" @selected($filtrosPedidos['estado'] === $estado)>{{ str_replace('_', ' ', $estado) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Tipo entrega</label>
                    <select name="pedidos_tipo_entrega" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm">
                        <option value="">Todos</option>
                        @foreach (['local', 'delivery', 'agencia'] as $tipoEntrega)
                            <option value="{{ $tipoEntrega }}" @selected($filtrosPedidos['tipo_entrega'] === $tipoEntrega)>{{ ucfirst($tipoEntrega) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Aplicar filtro</button>
                </div>
            </form>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4"><p class="text-xs text-[#8a6a2e]">Total</p><p class="mt-1 text-xl font-semibold">{{ $kpisPedidos['total'] }}</p></article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4"><p class="text-xs text-[#8a6a2e]">Registrados</p><p class="mt-1 text-xl font-semibold">{{ $kpisPedidos['registrados'] }}</p></article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4"><p class="text-xs text-[#8a6a2e]">Produccion</p><p class="mt-1 text-xl font-semibold">{{ $kpisPedidos['en_produccion'] }}</p></article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4"><p class="text-xs text-[#8a6a2e]">Listo entrega</p><p class="mt-1 text-xl font-semibold">{{ $kpisPedidos['listo_entrega'] }}</p></article>
                <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4"><p class="text-xs text-[#8a6a2e]">Entregados</p><p class="mt-1 text-xl font-semibold">{{ $kpisPedidos['entregados'] }}</p></article>
                <article class="rounded-xl border border-rose-200 bg-rose-50 p-4"><p class="text-xs text-rose-700">Atrasados</p><p class="mt-1 text-xl font-semibold text-rose-800">{{ $kpisPedidos['atrasados'] }}</p></article>
            </div>

            <div class="mt-4 overflow-x-auto rounded-xl border border-[#e5dec8]">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Codigo</th>
                            <th class="px-4 py-2.5 font-semibold">Cliente</th>
                            <th class="px-4 py-2.5 font-semibold">Estado</th>
                            <th class="px-4 py-2.5 font-semibold">Entrega</th>
                            <th class="px-4 py-2.5 font-semibold">Fecha compromiso</th>
                            <th class="px-4 py-2.5 font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($pedidos as $pedido)
                            <tr>
                                <td class="px-4 py-2.5">{{ $pedido->codigo }}</td>
                                <td class="px-4 py-2.5">{{ $pedido->nombre_cliente }}</td>
                                <td class="px-4 py-2.5">{{ str_replace('_', ' ', $pedido->estado) }}</td>
                                <td class="px-4 py-2.5">{{ ucfirst($pedido->tipo_entrega ?? 'local') }}</td>
                                <td class="px-4 py-2.5">{{ optional($pedido->fecha_entrega_compromiso)->format('d/m/Y') ?: '-' }}</td>
                                <td class="px-4 py-2.5">{{ $pedido->monto_total !== null ? 'S/ ' . number_format((float) $pedido->monto_total, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-[#777]">Sin pedidos para el filtro aplicado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-[#2a2419]">Saldos pendientes</h2>
                        <p class="mt-1 text-sm text-[#6e6758]">Pedidos con pago incompleto para seguimiento de cobranza.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a
                            href="{{ route('reportes.saldos.csv', $queryPedidos) }}"
                            class="rounded-xl border border-[#b9943d] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]"
                        >
                            Exportar CSV
                        </a>
                        <a
                            href="{{ route('reportes.saldos.excel', $queryPedidos) }}"
                            class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100"
                        >
                            Exportar XLSX
                        </a>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Pedidos con saldo</p>
                        <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $kpisSaldos['pedidos_con_saldo'] }}</p>
                    </article>
                    <article class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-rose-700">Total pendiente</p>
                        <p class="mt-2 text-2xl font-semibold text-rose-800">S/ {{ number_format($kpisSaldos['total_pendiente'], 2) }}</p>
                    </article>
                </div>

                <div class="mt-4 overflow-x-auto rounded-xl border border-[#e5dec8]">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                            <tr>
                                <th class="px-3 py-2.5 font-semibold">Codigo</th>
                                <th class="px-3 py-2.5 font-semibold">Cliente</th>
                                <th class="px-3 py-2.5 font-semibold">Cancelado</th>
                                <th class="px-3 py-2.5 font-semibold">Falta</th>
                                <th class="px-3 py-2.5 font-semibold">% cancelado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#efeee9]">
                            @forelse ($saldosPendientes as $pedidoSaldo)
                                @php
                                    $total = (float) ($pedidoSaldo->monto_total ?? 0);
                                    $adelanto = (float) ($pedidoSaldo->monto_adelanto ?? 0);
                                    $saldo = (float) ($pedidoSaldo->monto_saldo ?? 0);
                                    $porcentaje = $total > 0 ? round((($total - $saldo) / $total) * 100, 2) : 0;
                                @endphp
                                <tr>
                                    <td class="px-3 py-2.5">{{ $pedidoSaldo->codigo }}</td>
                                    <td class="px-3 py-2.5">{{ $pedidoSaldo->nombre_cliente }}</td>
                                    <td class="px-3 py-2.5">S/ {{ number_format($adelanto, 2) }}</td>
                                    <td class="px-3 py-2.5">S/ {{ number_format($saldo, 2) }}</td>
                                    <td class="px-3 py-2.5">{{ number_format($porcentaje, 2) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-5 text-center text-[#777]">No hay saldos pendientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-[#2a2419]">Stock bajo</h2>
                        <p class="mt-1 text-sm text-[#6e6758]">Control de productos con inventario critico.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a
                            href="{{ route('reportes.stock.csv', $queryStock) }}"
                            class="rounded-xl border border-[#b9943d] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]"
                        >
                            Exportar CSV
                        </a>
                        <a
                            href="{{ route('reportes.stock.excel', $queryStock) }}"
                            class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100"
                        >
                            Exportar XLSX
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('reportes.index') }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_auto]">
                    <input type="hidden" name="ventas_fecha_inicio" value="{{ $filtrosVentas['fecha_inicio'] }}">
                    <input type="hidden" name="ventas_fecha_fin" value="{{ $filtrosVentas['fecha_fin'] }}">
                    <input type="hidden" name="ventas_tipo" value="{{ $filtrosVentas['tipo'] }}">
                    <input type="hidden" name="pedidos_fecha_inicio" value="{{ $filtrosPedidos['fecha_inicio'] }}">
                    <input type="hidden" name="pedidos_fecha_fin" value="{{ $filtrosPedidos['fecha_fin'] }}">
                    <input type="hidden" name="pedidos_estado" value="{{ $filtrosPedidos['estado'] }}">
                    <input type="hidden" name="pedidos_tipo_entrega" value="{{ $filtrosPedidos['tipo_entrega'] }}">
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Umbral stock</label>
                        <input type="number" min="0" name="stock_umbral" value="{{ $kpisStock['umbral'] }}" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2.5 text-sm" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Aplicar</button>
                    </div>
                </form>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Productos bajo umbral</p>
                        <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $kpisStock['productos_bajo'] }}</p>
                    </article>
                    <article class="rounded-xl border border-[#eee3c8] bg-[#fffdf8] p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-[#8a6a2e]">Unidades en riesgo</p>
                        <p class="mt-2 text-2xl font-semibold text-[#1f1f1f]">{{ $kpisStock['unidades_en_riesgo'] }}</p>
                    </article>
                </div>

                <div class="mt-4 overflow-x-auto rounded-xl border border-[#e5dec8]">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                            <tr>
                                <th class="px-3 py-2.5 font-semibold">Codigo</th>
                                <th class="px-3 py-2.5 font-semibold">Producto</th>
                                <th class="px-3 py-2.5 font-semibold">Categoria</th>
                                <th class="px-3 py-2.5 font-semibold">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#efeee9]">
                            @forelse ($stockBajo as $producto)
                                <tr>
                                    <td class="px-3 py-2.5">{{ $producto->codigo }}</td>
                                    <td class="px-3 py-2.5">{{ $producto->nombre }}</td>
                                    <td class="px-3 py-2.5">{{ $producto->categoria }}</td>
                                    <td class="px-3 py-2.5">{{ $producto->stock_actual }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-5 text-center text-[#777]">No hay productos bajo el umbral.</td>
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
            const graficos = @json($graficos);
            const colorTexto = '#5a4a2a';

            const crearLinea = (id, labels, data, label, color) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label,
                            data,
                            borderColor: color,
                            backgroundColor: color + '33',
                            borderWidth: 2,
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: colorTexto }, grid: { color: '#f0eadc' } },
                            y: { beginAtZero: true, ticks: { color: colorTexto }, grid: { color: '#f0eadc' } },
                        },
                    },
                });
            };

            const crearBarras = (id, labels, data, color) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: color,
                            borderRadius: 8,
                            maxBarThickness: 36,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: colorTexto }, grid: { display: false } },
                            y: { beginAtZero: true, ticks: { color: colorTexto }, grid: { color: '#f0eadc' } },
                        },
                    },
                });
            };

            const crearDona = (id, labels, data, colors) => {
                const canvas = document.getElementById(id);
                if (!canvas) return;
                new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: colors,
                            borderColor: '#fff',
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
            };

            crearLinea(
                'graficoVentasPorDia',
                graficos?.ventas_por_dia?.labels || [],
                graficos?.ventas_por_dia?.data || [],
                'S/ cobrado',
                '#b9943d'
            );

            crearBarras(
                'graficoVentasTipo',
                graficos?.ventas_por_tipo?.labels || [],
                graficos?.ventas_por_tipo?.data || [],
                '#8b6a2e'
            );

            crearDona(
                'graficoPedidosEstadoReportes',
                graficos?.pedidos_por_estado?.labels || [],
                graficos?.pedidos_por_estado?.data || [],
                ['#b9943d', '#8b6a2e', '#d6ba6b', '#1f8a63', '#be4e4e', '#5f6b7a']
            );

            crearDona(
                'graficoPedidosEntrega',
                graficos?.pedidos_por_entrega?.labels || [],
                graficos?.pedidos_por_entrega?.data || [],
                ['#1f8a63', '#b9943d', '#8b6a2e', '#5f6b7a']
            );

            crearBarras(
                'graficoStockCritico',
                graficos?.stock_critico?.labels || [],
                graficos?.stock_critico?.data || [],
                '#be4e4e'
            );
        })();
    </script>
</x-app-layout>
