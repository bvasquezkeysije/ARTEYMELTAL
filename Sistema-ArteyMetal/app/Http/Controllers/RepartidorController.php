<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepartidorController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('q', '');
        $filtroEstado = $request->input('estado', '');

        $pedidos = Pedido::query()
            ->with('cliente', 'productos')
            ->whereIn('estado', ['listo_entrega', 'en_transporte'])
            ->when($busqueda, fn ($q) => $q->where(function ($sub) use ($busqueda) {
                $sub->where('codigo', 'ilike', %{$busqueda}%)
                    ->orWhere('nombre_cliente', 'ilike', %{$busqueda}%);
            }))
            ->when($filtroEstado, fn ($q) => $q->where('estado', $filtroEstado))
            ->orderByDesc('id')
            ->paginate(10)
            ->appends(['q' => $busqueda, 'estado' => $filtroEstado]);

        return view('repartidor.index', compact('pedidos', 'busqueda', 'filtroEstado'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'productos.archivos', 'archivosDiseno');

        abort_if(! in_array($pedido->estado, ['listo_entrega', 'en_transporte'], true), 404);

        return view('repartidor.show', compact('pedido'));
    }

    public function recoger(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['repartidor', 'administrador'], true)) {
            abort(403, 'Solo el repartidor puede recoger pedidos.');
        }

        if ($pedido->estado !== 'listo_entrega') {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'El pedido debe estar listo para entrega.']);
            }
            return back()->with('ok', 'El pedido debe estar listo para entrega.');
        }

        $request->validate([
            'productos' => ['required', 'array'],
            'productos.*.id' => ['required', 'integer', 'exists:pedido_productos,id'],
            'productos.*.cantidad_recoge' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($pedido, $request) {
            foreach ($request->input('productos') as $item) {
                $pp = PedidoProducto::findOrFail($item['id']);
                $cantidadRecoge = min((int) $item['cantidad_recoge'], $pp->cantidad);
                $pp->update(['cantidad_recoge' => $cantidadRecoge]);
            }

            $pedido->update(['estado' => 'en_transporte']);
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pedido recogido correctamente.']);
        }

        return redirect()->route('repartidor.index')->with('ok', 'Pedido recogido correctamente.');
    }

    public function entregarAlmacen(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['repartidor', 'administrador'], true)) {
            abort(403, 'Solo el repartidor puede entregar en almacen.');
        }

        if ($pedido->estado !== 'en_transporte') {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'El pedido debe estar en transporte para entregar en almacen.']);
            }
            return back()->with('ok', 'El pedido debe estar en transporte para entregar en almacen.');
        }

        $pedido->update(['estado' => 'en_almacen']);

        $almaceneros = User::whereHas('rol', fn ($q) => $q->where('nombre', 'almacenero'))->get();
        foreach ($almaceneros as $almacenero) {
            NotificationController::create(
                userId: $almacenero->id,
                type: 'almacen',
                title: 'Pedido recibido en almacen',
                body: El pedido {$pedido- de {$pedido- esta listo para recibir en almacen.,
                actionUrl: route('almacen.pedidos', [], false),
            );
        }

        NotificationController::create(
            userId: $pedido->usuario_id,
            type: 'repartidor',
            title: 'Pedido entregado en almacen',
            body: El pedido {$pedido- ha sido entregado en almacen por el repartidor.,
            actionUrl: route('pedidos.show', $pedido, false),
        );

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pedido entregado en almacen. Almacen notificado.']);
        }

        return back()->with('ok', 'Pedido entregado en almacen. Almacen notificado.');
    }
}
