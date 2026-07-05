<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoDisenoArchivo;
use App\Models\PedidoOrdenArchivo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Services\ComprobanteVentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function __construct(private readonly ComprobanteVentaService $comprobanteService)
    {
    }

    public function index()
    {
        $busqueda = request('q');
        $filtroEstado = request('estado');

        $pedidos = Pedido::query()
            ->with('cliente')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('codigo', 'like', "%{$busqueda}%")
                    ->orWhere('nombre_cliente', 'like', "%{$busqueda}%")
                    ->orWhere('tipo_producto', 'like', "%{$busqueda}%")
                    ->orWhere('estado', 'like', "%{$busqueda}%");
            })
            ->when($filtroEstado, function ($query) use ($filtroEstado) {
                $query->where('estado', $filtroEstado);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pedidos.index', compact('pedidos', 'busqueda', 'filtroEstado'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();

        return view('pedidos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $datos = $this->validarPedido($request);
        $datos = $this->completarDatosCliente($datos);
        $datos = $this->sincronizarClientePorDocumento($datos);
        $datos = $this->normalizarDatosEntrega($datos);
        $datos['codigo'] = $this->generarCodigoPedido();
        $datos['usuario_id'] = $request->user()->id;

        $pedido = Pedido::create($datos);
        $this->guardarArchivosOrden($request, $pedido);

        return redirect()->route('pedidos.index')->with('ok', 'Pedido registrado correctamente.');
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'archivosDiseno', 'archivosOrden');

        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();

        return view('pedidos.edit', compact('pedido', 'clientes'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $datos = $this->validarPedido($request, $pedido->id);
        $datos = $this->completarDatosCliente($datos);
        $datos = $this->sincronizarClientePorDocumento($datos);
        $datos = $this->normalizarDatosEntrega($datos);
        $pedido->update($datos);
        $this->guardarArchivosOrden($request, $pedido);

        return redirect()->route('pedidos.index')->with('ok', 'Pedido actualizado correctamente.');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->route('pedidos.index')->with('ok', 'Pedido eliminado correctamente.');
    }

    public function actualizarPersonalizacion(Request $request, Pedido $pedido)
    {
        $datos = $request->validate([
            'estado' => ['required', 'string', 'in:registrado,en_produccion,listo_entrega,entregado,cancelado'],
            'estado_personalizacion' => ['required', 'string', 'in:sin_iniciar,en_diseno,en_revision,aprobado,en_produccion,listo_entrega,entregado'],
            'fecha_entrega_compromiso' => ['nullable', 'date'],
            'fecha_inicio_diseno' => ['nullable', 'date'],
            'fecha_aprobacion_diseno' => ['nullable', 'date'],
            'archivos_diseno' => ['nullable', 'array'],
            'archivos_diseno.*' => ['file', 'max:10240', 'mimes:cdr,pdf,png,jpg,jpeg,svg,ai,eps,psd,webp'],
            'observaciones_personalizacion' => ['nullable', 'string'],
            'estado_pago' => ['required', 'string', 'in:pendiente_adelanto,adelanto_pagado,pagado_completo'],
        ]);

        $datosPago = $this->calcularPago($pedido->monto_total, $datos['estado_pago']);

        if (
            in_array($datos['estado'], ['en_produccion', 'listo_entrega', 'entregado'], true)
            && $datos['estado_pago'] === 'pendiente_adelanto'
        ) {
            return back()
                ->withInput()
                ->withErrors(['estado_pago' => 'Para avanzar el pedido, primero debe registrarse el adelanto del 50%.']);
        }

        $pedido->update(array_merge($datos, $datosPago));

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

        return redirect()->route('pedidos.show', $pedido)->with('ok', 'Personalizacion actualizada correctamente.');
    }

    public function confirmarPagoFinal(Request $request, Pedido $pedido)
    {
        if (! in_array($pedido->estado, ['listo_entrega', 'entregado'], true)) {
            return back()->with('ok', 'Para cerrar el pedido, primero debe estar en estado listo entrega o entregado.');
        }

        if (($pedido->estado_pago ?? 'pendiente_adelanto') === 'pendiente_adelanto') {
            return back()->with('ok', 'Primero debes registrar el adelanto del 50% en personalizacion.');
        }

        if (($pedido->estado_pago ?? null) === 'pagado_completo' || (float) ($pedido->monto_saldo ?? 0) <= 0) {
            return back()->with('ok', 'Este pedido ya fue cerrado y no tiene saldo pendiente.');
        }

        if ((float) ($pedido->monto_total ?? 0) <= 0) {
            return back()->with('ok', 'El pedido no tiene monto total valido para cerrar la venta.');
        }

        DB::transaction(function () use ($pedido, $request) {
            $saldoPendiente = round((float) ($pedido->monto_saldo ?? 0), 2);

            $ventaExistente = Venta::query()
                ->where('pedido_id', $pedido->id)
                ->where('tipo_venta', 'pedido')
                ->exists();

            if (! $ventaExistente) {
                $venta = Venta::create([
                    'codigo' => $this->generarCodigoVenta(),
                    'tipo_venta' => 'pedido',
                    'pedido_id' => $pedido->id,
                    'cliente_nombre' => $pedido->nombre_cliente,
                    'fecha_venta' => now()->toDateString(),
                    'monto_total' => $pedido->monto_total,
                    'monto_cobrado' => $saldoPendiente,
                    'estado_pago' => 'pagado_completo',
                    'observaciones' => 'Cierre automatico por pago final del pedido '.$pedido->codigo,
                    'usuario_id' => $request->user()->id,
                ]);

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => null,
                    'producto_nombre' => 'Pago final pedido '.$pedido->codigo,
                    'cantidad' => 1,
                    'precio_unitario' => $saldoPendiente,
                    'subtotal' => $saldoPendiente,
                ]);

                $documento = preg_replace('/\D/', '', (string) ($pedido->documento_cliente ?? ''));
                $tipoComprobante = strlen($documento) === 11 ? 'factura' : 'boleta';

                $this->comprobanteService->emitir($venta, [
                    'tipo_comprobante' => $tipoComprobante,
                    'documento_cliente' => $documento !== '' ? $documento : null,
                    'nombre_cliente' => $pedido->nombre_cliente ?: 'Cliente',
                    'direccion_cliente' => $pedido->direccion_entrega,
                ]);
            }

            $pedido->update([
                'estado' => 'entregado',
                'estado_personalizacion' => 'entregado',
                'estado_pago' => 'pagado_completo',
                'monto_saldo' => 0,
            ]);
        });

        return redirect()->route('pedidos.show', $pedido)->with('ok', 'Pago final confirmado. Pedido cerrado y registrado en ventas.');
    }

    private function validarPedido(Request $request, ?int $pedidoId = null): array
    {
        $estados = 'registrado,en_produccion,listo_entrega,entregado,cancelado';

        return $request->validate([
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'nombre_cliente' => ['required', 'string', 'max:120'],
            'telefono_cliente' => ['nullable', 'string', 'max:20'],
            'documento_cliente' => ['nullable', 'string', 'max:25'],
            'correo_cliente' => ['nullable', 'string', 'email', 'max:120'],
            'tipo_producto' => ['required', 'string', 'max:120'],
            'tipo_entrega' => ['required', 'string', 'in:local,delivery,agencia'],
            'direccion_entrega' => ['nullable', 'string', 'max:255', 'required_unless:tipo_entrega,local'],
            'referencia_entrega' => ['nullable', 'string', 'max:255'],
            'distrito_entrega' => ['nullable', 'string', 'max:120', 'required_unless:tipo_entrega,local'],
            'codigo_postal_entrega' => ['nullable', 'string', 'max:12', 'required_unless:tipo_entrega,local'],
            'nombre_recibe' => ['nullable', 'string', 'max:120', 'required_unless:tipo_entrega,local'],
            'telefono_recibe' => ['nullable', 'string', 'max:20', 'required_unless:tipo_entrega,local'],
            'costo_delivery' => ['nullable', 'numeric', 'min:0', 'required_unless:tipo_entrega,local'],
            'detalle_trabajo' => ['nullable', 'string'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'estado' => ['required', 'string', 'in:'.$estados],
            'fecha_entrega_compromiso' => ['nullable', 'date'],
            'monto_total' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
            'archivos_orden' => ['nullable', 'array'],
            'archivos_orden.*' => ['file', 'max:15360', 'mimes:pdf,doc,docx'],
        ]);
    }

    private function guardarArchivosOrden(Request $request, Pedido $pedido): void
    {
        if (! $request->hasFile('archivos_orden')) {
            return;
        }

        foreach ($request->file('archivos_orden') as $archivo) {
            if (! $archivo->isValid()) {
                continue;
            }

            $path = $archivo->store('ordenes_compra_pedido', 'public');

            PedidoOrdenArchivo::create([
                'pedido_id' => $pedido->id,
                'archivo_path' => $path,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime_type' => $archivo->getMimeType(),
                'tamano_bytes' => $archivo->getSize(),
            ]);
        }
    }

    private function completarDatosCliente(array $datos): array
    {
        if (! empty($datos['cliente_id'])) {
            $cliente = Cliente::find($datos['cliente_id']);

            if ($cliente) {
                $datos['nombre_cliente'] = $cliente->nombre_completo;
                if (empty($datos['telefono_cliente'])) {
                    $datos['telefono_cliente'] = $cliente->telefono;
                }
                if (empty($datos['documento_cliente'])) {
                    $datos['documento_cliente'] = $cliente->documento;
                }
                if (empty($datos['correo_cliente'])) {
                    $datos['correo_cliente'] = $cliente->correo;
                }
            }
        }

        return $datos;
    }

    private function normalizarDatosEntrega(array $datos): array
    {
        if (($datos['tipo_entrega'] ?? 'local') === 'local') {
            $datos['direccion_entrega'] = null;
            $datos['referencia_entrega'] = null;
            $datos['distrito_entrega'] = null;
            $datos['codigo_postal_entrega'] = null;
            $datos['nombre_recibe'] = null;
            $datos['telefono_recibe'] = null;
            $datos['costo_delivery'] = null;
        }

        return $datos;
    }

    private function sincronizarClientePorDocumento(array $datos): array
    {
        if (! empty($datos['cliente_id'])) {
            return $datos;
        }

        $documento = trim((string) ($datos['documento_cliente'] ?? ''));
        if ($documento === '') {
            return $datos;
        }

        $cliente = Cliente::query()->where('documento', $documento)->first();

        if (! $cliente) {
            $cliente = Cliente::create([
                'nombre_completo' => $datos['nombre_cliente'] ?? 'Cliente sin nombre',
                'telefono' => $datos['telefono_cliente'] ?? null,
                'correo' => $datos['correo_cliente'] ?? null,
                'documento' => $documento,
                'direccion' => $datos['direccion_entrega'] ?? null,
                'observaciones' => 'Creado automaticamente desde pedidos.',
            ]);
        }

        $datos['cliente_id'] = $cliente->id;

        return $datos;
    }

    private function generarCodigoPedido(): string
    {
        $ultimoId = Pedido::max('id') ?? 0;
        $siguiente = $ultimoId + 1;

        return 'PED-' . str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT);
    }

    private function calcularPago(?float $montoTotal, string $estadoPago): array
    {
        if ($montoTotal === null) {
            return [
                'monto_adelanto' => null,
                'monto_saldo' => null,
            ];
        }

        $adelanto = round($montoTotal * 0.5, 2);
        $saldo = round($montoTotal - $adelanto, 2);

        if ($estadoPago === 'pendiente_adelanto') {
            return [
                'monto_adelanto' => 0,
                'monto_saldo' => round($montoTotal, 2),
            ];
        }

        if ($estadoPago === 'adelanto_pagado') {
            return [
                'monto_adelanto' => $adelanto,
                'monto_saldo' => $saldo,
            ];
        }

        return [
            'monto_adelanto' => $adelanto,
            'monto_saldo' => 0,
        ];
    }

    private function generarCodigoVenta(): string
    {
        $ultimoId = Venta::max('id') ?? 0;
        $siguiente = $ultimoId + 1;

        return 'VEN-' . str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT);
    }
}
