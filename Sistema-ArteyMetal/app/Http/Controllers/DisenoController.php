<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProductoArchivo;
use Illuminate\Http\Request;

class DisenoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::query()
            ->with('cliente', 'productos.archivosCliente', 'productos.archivosDisenador', 'archivosDiseno')
            ->whereIn('estado_personalizacion', ['en_diseno', 'en_revision'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('diseno.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'productos.archivosCliente', 'productos.archivosDisenador', 'archivosDiseno');

        return view('diseno.show', compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'disenador'], true)) {
            abort(403, 'No tienes permiso para actualizar disenos.');
        }

        $request->validate([
            'estado_personalizacion' => ['required', 'string', 'in:en_diseno,en_revision,aprobado'],
            'archivos_producto' => ['nullable', 'array'],
            'archivos_producto.*' => ['nullable', 'array'],
            'archivos_producto.*.*' => ['file', 'max:10240', 'mimes:cdr,pdf,png,jpg,jpeg,svg,ai,eps,psd,webp'],
        ]);

        if ($request->input('estado_personalizacion') === 'aprobado') {
            $pedido->update([
                'estado_personalizacion' => 'aprobado',
                'fecha_aprobacion_diseno' => now()->toDateString(),
            ]);
        } else {
            $pedido->update([
                'estado_personalizacion' => $request->input('estado_personalizacion'),
            ]);
        }

        if ($request->hasFile('archivos_producto')) {
            foreach ($request->file('archivos_producto') as $productoId => $archivos) {
                foreach ($archivos as $archivo) {
                    if (! $archivo->isValid()) continue;
                    $path = $archivo->store('disenos_producto', 'public');

                    PedidoProductoArchivo::create([
                        'pedido_producto_id' => $productoId,
                        'tipo' => 'disenador',
                        'archivo_path' => $path,
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'mime_type' => $archivo->getMimeType(),
                        'tamano_bytes' => $archivo->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('diseno.show', $pedido)->with('ok', 'Diseno actualizado correctamente.');
    }
}
