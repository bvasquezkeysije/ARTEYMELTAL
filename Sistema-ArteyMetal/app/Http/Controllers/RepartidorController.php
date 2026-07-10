<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepartidorController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::query()
            ->with('cliente', 'productos')
            ->whereIn('estado', ['listo_entrega', 'en_transporte'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('repartidor.index', compact('pedidos'));
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

        return redirect()->route('repartidor.index')->with('ok', 'Pedido recogido correctamente.');
    }
}
