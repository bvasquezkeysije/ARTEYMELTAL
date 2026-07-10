<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hoy = now()->toDateString();
        $inicioRango = Carbon::today()->subDays(13);

        $metricas = [
            'pedidos_total' => Pedido::count(),
            'pedidos_produccion' => Pedido::where('estado', 'en_produccion')->count(),
            'pedidos_listo_entrega' => Pedido::where('estado', 'listo_entrega')->count(),
            'ventas_hoy' => (float) Venta::whereDate('fecha_venta', $hoy)->sum('monto_cobrado'),
            'clientes_total' => Cliente::count(),
            'productos_total' => Producto::count(),
        ];

        $ultimosPedidos = Pedido::query()
            ->select('id', 'codigo', 'nombre_cliente', 'estado', 'estado_pago', 'monto_total', 'created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $ultimasVentas = Venta::query()
            ->select('id', 'codigo', 'tipo_venta', 'cliente_nombre', 'monto_cobrado', 'fecha_venta')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $ventasAgrupadas = Venta::query()
            ->selectRaw('DATE(fecha_venta) as fecha, COALESCE(SUM(monto_cobrado), 0) as total')
            ->whereDate('fecha_venta', '>=', $inicioRango->toDateString())
            ->groupByRaw('DATE(fecha_venta)')
            ->orderByRaw('DATE(fecha_venta)')
            ->get()
            ->keyBy('fecha');

        $labelsVentas = [];
        $valoresVentas = [];
        for ($i = 0; $i < 14; $i++) {
            $fecha = $inicioRango->copy()->addDays($i);
            $clave = $fecha->toDateString();

            $labelsVentas[] = $fecha->format('d/m');
            $valoresVentas[] = round((float) optional($ventasAgrupadas->get($clave))->total, 2);
        }

        $pedidosPorEstadoMap = Pedido::query()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadosOrden = ['registrado', 'en_produccion', 'listo_entrega', 'entregado', 'cancelado'];
        $labelsPedidos = ['Registrado', 'En produccion', 'Listo entrega', 'Entregado', 'Cancelado'];
        $valoresPedidos = [];

        foreach ($estadosOrden as $estado) {
            $valoresPedidos[] = (int) ($pedidosPorEstadoMap[$estado] ?? 0);
        }

        $graficos = [
            'ventas_14_dias' => [
                'labels' => $labelsVentas,
                'data' => $valoresVentas,
            ],
            'pedidos_estado' => [
                'labels' => $labelsPedidos,
                'data' => $valoresPedidos,
            ],
        ];

        return view('dashboard', compact('metricas', 'ultimosPedidos', 'ultimasVentas', 'graficos'));
    }
}
