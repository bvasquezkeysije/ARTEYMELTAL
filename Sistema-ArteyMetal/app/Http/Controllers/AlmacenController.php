<?php

namespace App\Http\Controllers;

use App\Models\MovimientoAlmacen;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlmacenController extends Controller
{
    public function index(): View
    {
        $totalProductos = Producto::count();
        $productosConStock = Producto::where('stock_actual', '>', 0)->count();
        $productosSinStock = Producto::where('stock_actual', '=', 0)->count();
        $stockBajo = Producto::where('stock_actual', '>', 0)->where('stock_actual', '<=', 5)->count();
        $totalStock = Producto::sum('stock_actual');

        $entradasHoy = MovimientoAlmacen::whereDate('created_at', today())
            ->where('tipo', 'entrada')
            ->sum('cantidad');

        $salidasHoy = MovimientoAlmacen::whereDate('created_at', today())
            ->where('tipo', 'salida')
            ->sum('cantidad');

        $ultimosMovimientos = MovimientoAlmacen::with('producto', 'usuario')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('almacen.index', compact(
            'totalProductos',
            'productosConStock',
            'productosSinStock',
            'stockBajo',
            'totalStock',
            'entradasHoy',
            'salidasHoy',
            'ultimosMovimientos'
        ));
    }

    public function productos(Request $request): View
    {
        $query = Producto::query();

        if ($busqueda = $request->get('q')) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo', 'ilike', "%{$busqueda}%")
                    ->orWhere('nombre', 'ilike', "%{$busqueda}%");
            });
        }

        if ($categoria = $request->get('categoria')) {
            $query->where('categoria', $categoria);
        }

        if ($stock = $request->get('stock')) {
            if ($stock === 'bajo') {
                $query->where('stock_actual', '>', 0)->where('stock_actual', '<=', 5);
            } elseif ($stock === 'sin') {
                $query->where('stock_actual', 0);
            } elseif ($stock === 'con') {
                $query->where('stock_actual', '>', 0);
            }
        }

        $productos = $query->orderByDesc('id')->paginate(10);
        $todosProductos = Producto::orderBy('nombre')->get(['id', 'codigo', 'nombre', 'stock_tienda', 'stock_almacen']);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('almacen._tabla_productos', compact('productos'))->render(),
                'pagination' => $productos->links('pagination.gold')->toHtml(),
            ]);
        }

        return view('almacen.productos', compact('productos', 'todosProductos'));
    }

    public function movimientos(Request $request): View
    {
        $query = MovimientoAlmacen::with('producto', 'usuario');

        if ($tipo = $request->get('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($productoId = $request->get('producto_id')) {
            $query->where('producto_id', $productoId);
        }

        if ($fechaDesde = $request->get('fecha_desde')) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta = $request->get('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        $movimientos = $query->orderByDesc('id')->paginate(15);

        $productos = Producto::orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        return view('almacen.movimientos', compact('movimientos', 'productos'));
    }

    public function storeEntrada(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'ubicacion' => 'required|in:tienda,almacen',
            'concepto' => 'nullable|string|max:255',
            'pedido_id' => 'nullable|exists:pedidos,id',
        ]);

        DB::transaction(function () use ($validated) {
            $producto = Producto::findOrFail($validated['producto_id']);

            $columna = 'stock_' . $validated['ubicacion'];
            $nuevoStockUbicacion = $producto->{$columna} + $validated['cantidad'];

            $producto->update([$columna => $nuevoStockUbicacion]);

            $producto->refresh();

            MovimientoAlmacen::create([
                'producto_id' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => $validated['cantidad'],
                'stock_resultante' => $producto->stock_actual,
                'concepto' => ($validated['ubicacion'] === 'tienda' ? '[Tienda] ' : '[Almacen] ') . ($validated['concepto'] ?? ''),
                'pedido_id' => $validated['pedido_id'] ?? null,
                'usuario_id' => auth()->id(),
            ]);
        });

        return redirect()->route('almacen.index')
            ->with('success', 'Entrada registrada correctamente.');
    }

    public function storeSalida(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'ubicacion' => 'required|in:tienda,almacen',
            'concepto' => 'nullable|string|max:255',
            'pedido_id' => 'nullable|exists:pedidos,id',
        ]);

        DB::transaction(function () use ($validated) {
            $producto = Producto::findOrFail($validated['producto_id']);

            $columna = 'stock_' . $validated['ubicacion'];

            if ($producto->{$columna} < $validated['cantidad']) {
                abort(422, "Stock insuficiente en {$validated['ubicacion']} para realizar la salida.");
            }

            $nuevoStockUbicacion = $producto->{$columna} - $validated['cantidad'];

            $producto->update([$columna => $nuevoStockUbicacion]);

            $producto->refresh();

            MovimientoAlmacen::create([
                'producto_id' => $producto->id,
                'tipo' => 'salida',
                'cantidad' => $validated['cantidad'],
                'stock_resultante' => $producto->stock_actual,
                'concepto' => ($validated['ubicacion'] === 'tienda' ? '[Tienda] ' : '[Almacen] ') . ($validated['concepto'] ?? ''),
                'pedido_id' => $validated['pedido_id'] ?? null,
                'usuario_id' => auth()->id(),
            ]);
        });

        return redirect()->route('almacen.index')
            ->with('success', 'Salida registrada correctamente.');
    }

    public function pedidosPendientes(Request $request): View
    {
        $busqueda = $request->input('q', '');
        $filtroEstado = $request->input('estado', '');

        $pedidos = Pedido::query()
            ->with('cliente', 'productos')
            ->whereIn('estado', ['en_almacen', 'listo_recoger', 'entregado'])
            ->when($busqueda, fn ($q) => $q->where(function ($sub) use ($busqueda) {
                $sub->where('codigo', 'ilike', "%{$busqueda}%")
                    ->orWhere('nombre_cliente', 'ilike', "%{$busqueda}%");
            }))
            ->when($filtroEstado, fn ($q) => $q->where('estado', $filtroEstado))
            ->orderByDesc('id')
            ->paginate(10)
            ->appends(['q' => $busqueda, 'estado' => $filtroEstado]);

        return view('almacen.pedidos', compact('pedidos', 'busqueda', 'filtroEstado'));
    }

    public function recibirPedido(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['almacenero', 'administrador'], true)) {
            abort(403, 'Solo el almacenero puede recibir pedidos.');
        }

        if ($pedido->estado !== 'en_almacen') {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'El pedido debe estar en almacen para recibirlo.']);
            }
            return back()->with('ok', 'El pedido debe estar en almacen para recibirlo.');
        }

        DB::transaction(function () use ($pedido) {
            $pedido->load('productos');

            foreach ($pedido->productos as $pp) {
                $cantidad = $pp->cantidad_recoge ?? $pp->cantidad;
                $producto = Producto::firstOrCreate(
                    ['nombre' => $pp->nombre],
                    [
                        'codigo' => 'PED-' . $pedido->id . '-' . $pp->id,
                        'categoria' => $pedido->tipo_producto ?? 'personalizado',
                        'stock_tienda' => 0,
                        'stock_almacen' => 0,
                    ]
                );

                $producto->increment('stock_almacen', $cantidad);
                $producto->refresh();

                MovimientoAlmacen::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'entrada',
                    'cantidad' => $cantidad,
                    'stock_resultante' => $producto->stock_actual,
                    'concepto' => '[Almacen] Recepcion pedido ' . $pedido->codigo,
                    'pedido_id' => $pedido->id,
                    'usuario_id' => auth()->id(),
                ]);
            }

            $pedido->update(['estado' => 'listo_recoger']);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pedido recibido en almacen correctamente.']);
        }

        return redirect()->route('almacen.pedidos')->with('success', 'Pedido recibido en almacen correctamente.');
    }

    public function entregarCliente(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['almacenero', 'administrador'], true)) {
            abort(403, 'Solo el almacenero puede entregar al cliente.');
        }

        if ($pedido->estado !== 'listo_recoger') {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'El pedido debe estar listo para recoger.']);
            }
            return back()->with('ok', 'El pedido debe estar listo para recoger.');
        }

        DB::transaction(function () use ($pedido) {
            $pedido->load('productos');

            foreach ($pedido->productos as $pp) {
                $cantidad = $pp->cantidad_recoge ?? $pp->cantidad;
                $producto = Producto::where('nombre', $pp->nombre)->first();
                if (! $producto) continue;

                if ($producto->stock_almacen < $cantidad) {
                    abort(422, "Stock insuficiente en almacen para {$pp->nombre}.");
                }

                $producto->decrement('stock_almacen', $cantidad);
                $producto->refresh();

                MovimientoAlmacen::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'salida',
                    'cantidad' => $cantidad,
                    'stock_resultante' => $producto->stock_actual,
                    'concepto' => '[Almacen] Entrega cliente pedido ' . $pedido->codigo,
                    'pedido_id' => $pedido->id,
                    'usuario_id' => auth()->id(),
                ]);
            }

            $pedido->update(['estado' => 'entregado']);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pedido entregado al cliente correctamente.']);
        }

        return redirect()->route('almacen.pedidos')->with('success', 'Pedido entregado al cliente correctamente.');
    }
}
