<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class ProduccionController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::query()
            ->with('cliente', 'productos.archivos', 'archivosDiseno')
            ->whereIn('estado', ['en_produccion', 'produciendo'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('produccion.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'productos.archivos', 'archivosDiseno');

        abort_if(! in_array($pedido->estado, ['en_produccion', 'produciendo'], true), 404);

        return view('produccion.show', compact('pedido'));
    }

    public function iniciarProduccion(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'orfebre'], true)) {
            abort(403);
        }

        if ($pedido->estado !== 'en_produccion') {
            return back()->with('ok', 'El pedido ya fue iniciado o no esta en estado inicial.');
        }

        $pedido->update(['estado' => 'produciendo']);

        return back()->with('ok', 'Produccion iniciada.');
    }

    public function notificarRepartidor(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'orfebre'], true)) {
            abort(403, 'Solo produccion puede notificar al repartidor.');
        }

        if ($pedido->estado !== 'produciendo') {
            return back()->with('ok', 'Debe iniciar la produccion primero.');
        }

        $pedido->update(['estado' => 'listo_entrega']);

        return back()->with('ok', 'Repartidor notificado. El pedido esta listo para recoger.');
    }
}
