<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoDisenoArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DisenoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::query()
            ->with('cliente', 'productos.archivos', 'productos.archivosDiseno')
            ->whereIn('estado_personalizacion', ['en_diseno', 'en_revision'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('diseno.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'productos.archivos', 'productos.archivosDiseno');

        return view('diseno.show', compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'disenador'], true)) {
            abort(403, 'No tienes permiso para actualizar disenos.');
        }

        $request->validate([
            'pedido_producto_id' => ['required', 'exists:pedido_productos,id'],
            'estado_personalizacion' => ['required', 'string', 'in:en_diseno,en_revision'],
            'archivos_diseno' => ['required', 'array', 'min:1'],
            'archivos_diseno.*' => ['file', 'max:10240', 'mimes:cdr,pdf,png,jpg,jpeg,svg,ai,eps,psd,webp'],
        ]);

        $pedido->update([
            'estado_personalizacion' => $request->input('estado_personalizacion'),
        ]);

        $productoId = $request->input('pedido_producto_id');

        foreach ($request->file('archivos_diseno') as $archivo) {
            $path = $archivo->store('disenos_pedido', 'public');

            PedidoDisenoArchivo::create([
                'pedido_id' => $pedido->id,
                'pedido_producto_id' => $productoId,
                'archivo_path' => $path,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime_type' => $archivo->getMimeType(),
                'tamano_bytes' => $archivo->getSize(),
            ]);
        }

        return redirect()->route('diseno.show', $pedido)->with('ok', 'Archivos subidos correctamente.');
    }

    public function destroyArchivo(PedidoDisenoArchivo $archivo)
    {
        $rol = request()->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'disenador'], true)) {
            abort(403, 'No tienes permiso para eliminar archivos.');
        }

        if ($archivo->archivo_path && Storage::disk('public')->exists($archivo->archivo_path)) {
            Storage::disk('public')->delete($archivo->archivo_path);
        }

        $pedido = $archivo->pedido;
        $archivo->delete();

        return redirect()->route('diseno.show', $pedido)->with('ok', 'Archivo eliminado correctamente.');
    }
}
