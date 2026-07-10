<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoDisenoArchivo;
use App\Models\User;
use Illuminate\Http\Request;

class DisenoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::query()
            ->with(['cliente', 'productos.archivos', 'archivosDiseno' => function ($q) {
                $q->orderBy('tipo');
            }])
            ->whereIn('estado_personalizacion', ['en_diseno', 'en_revision'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('diseno.index', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'productos.archivos', 'archivosDiseno' => function ($q) {
            $q->orderBy('tipo');
        }]);

        $archivosCliente = $pedido->archivosDiseno->where('tipo', 'cliente');
        $archivosDisenador = $pedido->archivosDiseno->where('tipo', 'disenador');

        return view('diseno.show', compact('pedido', 'archivosCliente', 'archivosDisenador'));
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

        $nuevoEstado = $request->input('estado_personalizacion');

        if ($nuevoEstado === 'aprobado') {
            $pedido->update([
                'estado_personalizacion' => 'aprobado',
                'fecha_aprobacion_diseno' => now()->toDateString(),
            ]);

            $admins = User::whereHas('rol', fn($q) => $q->whereIn('nombre', ['administrador', 'vendedor']))
                ->where('activo', true)
                ->get();
            foreach ($admins as $admin) {
                NotificationController::create(
                    userId: $admin->id,
                    type: 'diseno_aprobado',
                    title: 'Diseno aprobado',
                    body: "El diseno del pedido {$pedido->codigo} - {$pedido->nombre_cliente} ha sido aprobado.",
                    icon: '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    actionUrl: route('pedidos.show', $pedido),
                );
            }
        } else {
            $pedido->update([
                'estado_personalizacion' => $nuevoEstado,
            ]);

            if ($nuevoEstado === 'en_revision') {
                $admins = User::whereHas('rol', fn($q) => $q->whereIn('nombre', ['administrador', 'vendedor']))
                    ->where('activo', true)
                    ->get();
                foreach ($admins as $admin) {
                    NotificationController::create(
                        userId: $admin->id,
                        type: 'diseno_en_revision',
                        title: 'Diseno listo para revision',
                        body: "El diseno del pedido {$pedido->codigo} - {$pedido->nombre_cliente} esta listo para revision.",
                        icon: '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
                        actionUrl: route('pedidos.show', $pedido),
                    );
                }
            }
        }

        if ($request->hasFile('archivos_diseno')) {
            foreach ($request->file('archivos_diseno') as $archivo) {
                $path = $archivo->store('disenos_pedido', 'public');

                PedidoDisenoArchivo::create([
                    'pedido_id' => $pedido->id,
                    'tipo' => 'disenador',
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
