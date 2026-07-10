<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoDisenoArchivo;
use Illuminate\Http\Request;

class DisenoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::query()
            ->with('cliente', 'productos.archivos', 'archivosDiseno')
            ->whereIn('estado_personalizacion', ['en_diseno', 'en_revision'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('diseno.index', compact('pedidos'));
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
            'estado_personalizacion' => ['required', 'string', 'in:en_diseno,en_revision,aprobado'],
            'archivos_diseno' => ['nullable', 'array'],
            'archivos_diseno.*' => ['file', 'max:10240', 'mimes:cdr,pdf,png,jpg,jpeg,svg,ai,eps,psd,webp'],
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

        if ($request->hasFile('archivos_diseno')) {
            foreach ($request->file('archivos_diseno') as $archivo) {
                $path = $archivo->store('disenos_pedido', 'public');

                PedidoDisenoArchivo::create([
                    'pedido_id' => $pedido->id,
                    'archivo_path' => $path,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'mime_type' => $archivo->getMimeType(),
                    'tamano_bytes' => $archivo->getSize(),
                ]);
            }
        }

        return redirect()->route('diseno.show', $pedido)->with('ok', 'Diseno actualizado correctamente.');
    }
}
