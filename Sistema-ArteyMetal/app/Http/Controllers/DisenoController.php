<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoDisenoArchivo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DisenoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('q', '');
        $filtroEstado = $request->input('estado_personalizacion', '');

        $pedidos = Pedido::query()
            ->with('cliente', 'productos.archivos', 'archivosDiseno')
            ->whereIn('estado_personalizacion', ['en_diseno', 'en_revision']);

        if ($busqueda) {
            $pedidos->where(function ($q) use ($busqueda) {
                $q->where('codigo', 'ilike', "%{$busqueda}%")
                  ->orWhere('nombre_cliente', 'ilike', "%{$busqueda}%")
                  ->orWhereHas('productos', function ($q2) use ($busqueda) {
                      $q2->where('nombre', 'ilike', "%{$busqueda}%");
                  });
            });
        }

        if ($filtroEstado && in_array($filtroEstado, ['en_diseno', 'en_revision'])) {
            $pedidos->where('estado_personalizacion', $filtroEstado);
        }

        $pedidos = $pedidos->orderByDesc('id')->paginate(10)->withQueryString();

        return view('diseno.index', compact('pedidos', 'busqueda', 'filtroEstado'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'productos.archivos', 'archivosDiseno');

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

    public function notificar(Pedido $pedido)
    {
        $rol = request()->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'disenador'], true)) {
            abort(403, 'No tienes permiso para notificar.');
        }

        $vendedores = User::whereHas('rol', fn ($q) => $q->whereIn('nombre', ['administrador', 'vendedor']))->get();

        foreach ($vendedores as $vendedor) {
            NotificationController::create(
                $vendedor->id,
                'diseno',
                'Archivos de diseno listos',
                'El pedido ' . $pedido->codigo . ' de ' . $pedido->nombre_cliente . ' tiene archivos de diseno para revisar.',
                route('pedidos.show', $pedido)
            );
        }

        return back()->with('ok', 'Vendedor(es) notificado(s) correctamente.');
    }
}
