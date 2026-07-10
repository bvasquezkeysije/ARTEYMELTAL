<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Models\CategoriaProducto;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoDisenoArchivo;
use App\Models\PedidoOrdenArchivo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\PedidoProducto;
use App\Models\PedidoProductoArchivo;
use App\Models\User;
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
        $cajaAperturaId = session('pedido_caja_apertura_id');

        if (! $cajaAperturaId) {
            return $this->redirectToCajaSelection();
        }

        $caja = CajaApertura::find($cajaAperturaId);

        if (! $caja || $caja->estado !== 'abierta' || $caja->usuario_id !== auth()->id()) {
            session()->forget('pedido_caja_apertura_id');
            return $this->redirectToCajaSelection();
        }

        $busqueda = request('q');
        $filtroEstado = request('estado');
        $filtroPersonalizacion = request('estado_personalizacion');

        $pedidos = Pedido::query()
            ->with('cliente', 'productos.archivos')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('codigo', 'like', "%{$busqueda}%")
                    ->orWhere('nombre_cliente', 'like', "%{$busqueda}%")
                    ->orWhere('tipo_producto', 'like', "%{$busqueda}%")
                    ->orWhere('estado', 'like', "%{$busqueda}%");
            })
            ->when($filtroEstado, function ($query) use ($filtroEstado) {
                $query->where('estado', $filtroEstado);
            })
            ->when($filtroPersonalizacion, function ($query) use ($filtroPersonalizacion) {
                $query->where('estado_personalizacion', $filtroPersonalizacion);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pedidos.index', compact('pedidos', 'busqueda', 'filtroEstado', 'filtroPersonalizacion', 'caja') + ['cajasAbiertas' => collect(), 'sinCaja' => false]);
    }

    public function create()
    {
        $cajaAperturaId = session('pedido_caja_apertura_id');

        if (! $cajaAperturaId) {
            return redirect()->route('pedidos.index');
        }

        $cajaAbierta = CajaApertura::find($cajaAperturaId);

        if (! $cajaAbierta || $cajaAbierta->estado !== 'abierta' || $cajaAbierta->usuario_id !== auth()->id()) {
            session()->forget('pedido_caja_apertura_id');
            return redirect()->route('pedidos.index');
        }

        $clientes = Cliente::orderBy('nombre_completo')->get();
        $categorias = CategoriaProducto::where('activo', true)->orderBy('nombre')->get();
        $pedido = new Pedido();

        return view('pedidos.create', compact('clientes', 'categorias', 'pedido'));
    }

    public function store(Request $request)
    {
        $cajaAperturaId = session('pedido_caja_apertura_id');

        if (! $cajaAperturaId) {
            return redirect()->route('pedidos.index');
        }

        $cajaAbierta = CajaApertura::find($cajaAperturaId);

        if (! $cajaAbierta || $cajaAbierta->estado !== 'abierta' || $cajaAbierta->usuario_id !== auth()->id()) {
            session()->forget('pedido_caja_apertura_id');
            return redirect()->route('pedidos.index');
        }

        $datos = $this->validarPedido($request);
        $montos = $this->calcularMontosDesdeProductos($request);
        $datos['monto_total'] = $montos['monto_total'];
        $datos['monto_adelanto'] = $montos['monto_adelanto'];

        $datos = $this->completarDatosCliente($datos);
        $datos = $this->sincronizarClientePorDocumento($datos);
        $datos = $this->normalizarDatosEntrega($datos);
        $datos['codigo'] = $this->generarCodigoPedido();
        $datos['usuario_id'] = $request->user()->id;
        $datos['estado'] = 'registrado';
        $datos['estado_pago'] = 'adelanto_pagado';
        $datos['monto_saldo'] = round($datos['monto_total'] - $datos['monto_adelanto'], 2);

        DB::transaction(function () use ($datos, $request, $cajaAperturaId) {
            $pedido = Pedido::create($datos);
            $this->guardarArchivosOrden($request, $pedido);
            $this->guardarProductos($request, $pedido);

            $metodoPago = $datos['metodo_pago'] ?? 'efectivo';
            $montoAdelanto = $datos['monto_adelanto'];

            $venta = Venta::create([
                'codigo' => $this->generarCodigoVenta(),
                'tipo_venta' => 'pedido',
                'pedido_id' => $pedido->id,
                'cliente_nombre' => $datos['nombre_cliente'],
                'fecha_venta' => now()->toDateString(),
                'monto_total' => $montoAdelanto,
                'monto_cobrado' => $montoAdelanto,
                'estado_pago' => 'pagado_completo',
                'metodo_pago' => $metodoPago,
                'vuelto' => $datos['vuelto'] ?? null,
                'monto_efectivo' => $metodoPago === 'efectivo' ? $montoAdelanto : 0,
                'monto_digital' => in_array($metodoPago, ['yape', 'plin', 'transferencia', 'tarjeta']) ? $montoAdelanto : 0,
                'observaciones' => 'Adelanto pedido '.$pedido->codigo,
                'usuario_id' => $request->user()->id,
                'caja_apertura_id' => $cajaAperturaId,
            ]);

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => null,
                'producto_nombre' => 'Adelanto pedido '.$pedido->codigo,
                'cantidad' => 1,
                'precio_unitario' => $datos['monto_adelanto'],
                'subtotal' => $datos['monto_adelanto'],
            ]);

            $documento = preg_replace('/\D/', '', (string) ($datos['documento_cliente'] ?? ''));
            $tipoComprobante = strlen($documento) === 11 ? 'factura' : 'boleta';

            $this->comprobanteService->emitir($venta, [
                'tipo_comprobante' => $tipoComprobante,
                'documento_cliente' => $documento !== '' ? $documento : null,
                'nombre_cliente' => $datos['nombre_cliente'] ?: 'Cliente',
                'direccion_cliente' => $datos['direccion_entrega'] ?? null,
            ]);
        });

        return redirect()->route('pedidos.index')->with('ok', 'Pedido registrado correctamente.');
    }

    public function seleccionarCaja(CajaApertura $cajaApertura)
    {
        if ($cajaApertura->usuario_id !== auth()->id() || $cajaApertura->estado !== 'abierta') {
            return redirect()->route('pedidos.index')
                ->withErrors(['caja' => 'Caja no valida.']);
        }

        session(['pedido_caja_apertura_id' => $cajaApertura->id]);
        return redirect()->route('pedidos.index');
    }

    public function cambiarCaja()
    {
        session()->forget('pedido_caja_apertura_id');
        return redirect()->route('pedidos.index');
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'archivosDiseno', 'archivosOrden', 'productos.archivos');

        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido)
    {
        $pedido->load('productos.archivos');
        $clientes = Cliente::orderBy('nombre_completo')->get();
        $categorias = CategoriaProducto::where('activo', true)->orderBy('nombre')->get();

        return view('pedidos.edit', compact('pedido', 'clientes', 'categorias'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $datos = $this->validarPedido($request, $pedido->id);
        $montos = $this->calcularMontosDesdeProductos($request);
        $datos['monto_total'] = $montos['monto_total'];
        $datos['monto_adelanto'] = $montos['monto_adelanto'];
        $datos['monto_saldo'] = round($datos['monto_total'] - $datos['monto_adelanto'], 2);
        $datos = $this->completarDatosCliente($datos);
        $datos = $this->sincronizarClientePorDocumento($datos);
        $datos = $this->normalizarDatosEntrega($datos);
        $pedido->update($datos);
        $this->guardarArchivosOrden($request, $pedido);
        $this->guardarProductos($request, $pedido);

        return redirect()->route('pedidos.index')->with('ok', 'Pedido actualizado correctamente.');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->route('pedidos.index')->with('ok', 'Pedido eliminado correctamente.');
    }

    private function redirectToCajaSelection()
    {
        session()->forget('pedido_caja_apertura_id');

        $cajasAbiertas = CajaApertura::query()
            ->where('usuario_id', auth()->id())
            ->where('estado', 'abierta')
            ->get();

        if ($cajasAbiertas->isEmpty()) {
            return view('pedidos.index', [
                'pedidos' => collect(),
                'busqueda' => '',
                'filtroEstado' => '',
                'filtroPersonalizacion' => '',
                'caja' => null,
                'cajasAbiertas' => collect(),
                'sinCaja' => true,
            ]);
        }

        return view('pedidos.index', [
            'pedidos' => collect(),
            'busqueda' => '',
            'filtroEstado' => '',
            'filtroPersonalizacion' => '',
            'caja' => null,
            'cajasAbiertas' => $cajasAbiertas,
        ]);
    }

    public function actualizarPersonalizacion(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        $rules = [
            'estado_personalizacion' => ['required', 'string', 'in:sin_iniciar,en_diseno,en_revision,aprobado,en_produccion,listo_entrega,entregado'],
            'fecha_entrega_compromiso' => ['nullable', 'date'],
            'fecha_inicio_diseno' => ['nullable', 'date'],
            'fecha_aprobacion_diseno' => ['nullable', 'date'],
            'archivos_diseno' => ['nullable', 'array'],
            'archivos_diseno.*' => ['file', 'max:10240', 'mimes:cdr,pdf,png,jpg,jpeg,svg,ai,eps,psd,webp'],
            'observaciones_personalizacion' => ['nullable', 'string'],
        ];

        if (in_array($rol, ['administrador', 'vendedor'], true)) {
            $rules['estado'] = ['required', 'string', 'in:registrado,en_produccion,listo_entrega,en_transporte,en_almacen,listo_recoger,entregado,cancelado'];
            $rules['estado_pago'] = ['required', 'string', 'in:pendiente_adelanto,adelanto_pagado,pagado_completo'];
        }

        if ($rol === 'orfebre') {
            $rules['estado'] = ['required', 'string', 'in:en_produccion,listo_entrega'];
        }

        $datos = $request->validate($rules);

        if (isset($datos['estado_pago'])) {
            $datosPago = $this->calcularPago($pedido->monto_total, $datos['estado_pago']);
        } else {
            $datosPago = [];
        }

        $estadoActual = $datos['estado'] ?? $pedido->estado;

        if (
            isset($datos['estado_pago'])
            && in_array($estadoActual, ['en_produccion', 'listo_entrega', 'en_transporte', 'en_almacen', 'entregado'], true)
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
                    'tipo' => 'cliente',
                    'archivo_path' => $path,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'mime_type' => $archivo->getMimeType(),
                    'tamano_bytes' => $archivo->getSize(),
                ]);
            }
        }

        return redirect()->route('pedidos.show', $pedido)->with('ok', 'Personalizacion actualizada correctamente.');
    }

    public function marcarEnTransporte(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['repartidor', 'administrador'], true)) {
            abort(403, 'Solo el repartidor puede marcar recogido de produccion.');
        }

        if ($pedido->estado !== 'listo_entrega') {
            return back()->with('ok', 'El pedido debe estar en estado listo entrega para recogerlo.');
        }

        $pedido->update([
            'estado' => 'en_transporte',
        ]);

        return redirect()->route('pedidos.show', $pedido)->with('ok', 'Pedido recogido de produccion, en transporte al almacen.');
    }

    public function marcarEnAlmacen(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['almacenero', 'administrador'], true)) {
            abort(403, 'Solo el almacenero puede registrar entrada en almacen.');
        }

        if ($pedido->estado !== 'en_transporte') {
            return back()->with('ok', 'El pedido debe estar en transporte para registrarlo en almacen.');
        }

        $pedido->update([
            'estado' => 'en_almacen',
        ]);

        return redirect()->route('pedidos.show', $pedido)->with('ok', 'Pedido registrado en almacen correctamente. Se ha notificado al vendedor.');
    }

    public function confirmarPagoFinal(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'vendedor'], true)) {
            abort(403, 'No tienes permiso para cobrar el saldo final.');
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

        $request->validate([
            'metodo_pago' => ['required', 'string', 'in:efectivo,yape,plin,tarjeta,transferencia'],
            'vuelto' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($pedido, $request) {
            $cajaAperturaId = session('pedido_caja_apertura_id');
            $saldoPendiente = round((float) ($pedido->monto_saldo ?? 0), 2);
            $metodoPago = $request->input('metodo_pago');

            $venta = Venta::create([
                'codigo' => $this->generarCodigoVenta(),
                'tipo_venta' => 'pedido',
                'pedido_id' => $pedido->id,
                'cliente_nombre' => $pedido->nombre_cliente,
                'fecha_venta' => now()->toDateString(),
                'monto_total' => $saldoPendiente,
                'monto_cobrado' => $saldoPendiente,
                'estado_pago' => 'pagado_completo',
                'metodo_pago' => $metodoPago,
                'vuelto' => $request->input('vuelto'),
                'monto_efectivo' => $metodoPago === 'efectivo' ? $saldoPendiente : 0,
                'monto_digital' => in_array($metodoPago, ['yape', 'plin', 'transferencia', 'tarjeta']) ? $saldoPendiente : 0,
                'observaciones' => 'Pago final pedido '.$pedido->codigo,
                'usuario_id' => $request->user()->id,
                'caja_apertura_id' => $cajaAperturaId,
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

            $pedido->update([
                'estado_pago' => 'pagado_completo',
                'monto_saldo' => 0,
            ]);
        });

        return redirect()->route('pedidos.index')->with('ok', 'Pago registrado correctamente.');
    }

    public function autorizarRecoger(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'vendedor'], true)) {
            abort(403, 'No tienes permiso para autorizar recoger pedido.');
        }

        if ($pedido->estado !== 'en_almacen') {
            return back()->with('ok', 'El pedido debe estar en almacen para autorizar recoger.');
        }

        if (($pedido->estado_pago ?? 'pendiente_adelanto') === 'pendiente_adelanto') {
            return back()->with('ok', 'Primero debes registrar el adelanto del 50% en personalizacion.');
        }

        if ((float) ($pedido->monto_total ?? 0) <= 0) {
            return back()->with('ok', 'El pedido no tiene monto total valido.');
        }

        // Si ya se cobro el saldo, solo autorizar recoger
        if (($pedido->estado_pago ?? null) === 'pagado_completo' || (float) ($pedido->monto_saldo ?? 0) <= 0) {
            $pedido->update(['estado' => 'listo_recoger']);
            return redirect()->route('pedidos.index')->with('ok', 'Pedido habilitado para recoger en almacen.');
        }

        $request->validate([
            'metodo_pago' => ['required', 'string', 'in:efectivo,yape,plin,tarjeta,transferencia'],
            'vuelto' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($pedido, $request) {
            $cajaAperturaId = session('pedido_caja_apertura_id');
            $saldoPendiente = round((float) ($pedido->monto_saldo ?? 0), 2);
            $metodoPago = $request->input('metodo_pago');

            $venta = Venta::create([
                'codigo' => $this->generarCodigoVenta(),
                'tipo_venta' => 'pedido',
                'pedido_id' => $pedido->id,
                'cliente_nombre' => $pedido->nombre_cliente,
                'fecha_venta' => now()->toDateString(),
                'monto_total' => $saldoPendiente,
                'monto_cobrado' => $saldoPendiente,
                'estado_pago' => 'pagado_completo',
                'metodo_pago' => $metodoPago,
                'vuelto' => $request->input('vuelto'),
                'monto_efectivo' => $metodoPago === 'efectivo' ? $saldoPendiente : 0,
                'monto_digital' => in_array($metodoPago, ['yape', 'plin', 'transferencia', 'tarjeta']) ? $saldoPendiente : 0,
                'observaciones' => 'Pago final + autorizar recoger '.$pedido->codigo,
                'usuario_id' => $request->user()->id,
                'caja_apertura_id' => $cajaAperturaId,
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

            $pedido->update([
                'estado' => 'listo_recoger',
                'estado_pago' => 'pagado_completo',
                'monto_saldo' => 0,
            ]);
        });

        return redirect()->route('pedidos.index')->with('ok', 'Pago registrado y pedido habilitado para recoger en almacen.');
    }

    public function registrarLlegadaTienda(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'vendedor'], true)) {
            abort(403, 'Solo el vendedor puede confirmar llegada a tienda.');
        }

        if ($pedido->estado !== 'en_tienda') {
            return back()->with('ok', 'El pedido debe estar despachado a tienda.');
        }

        $pedido->update(['estado' => 'entregado']);

        return redirect()->route('pedidos.show', $pedido)->with('ok', 'Pedido recibido en tienda.');
    }

    public function derivar(Request $request, Pedido $pedido)
    {
        $rol = $request->user()->rol->nombre;

        if (! in_array($rol, ['administrador', 'vendedor'], true)) {
            abort(403, 'No tienes permiso para derivar pedidos.');
        }

        $request->validate([
            'destino' => ['required', 'string', 'in:diseno,produccion'],
        ]);

        $destino = $request->input('destino');

        if ($destino === 'diseno') {
            $permitidos = ['sin_iniciar'];
            if (! in_array($pedido->estado_personalizacion, $permitidos, true)) {
                return back()->with('ok', 'El pedido ya fue derivado a diseno o se encuentra en una etapa posterior.');
            }
            $pedido->update(['estado_personalizacion' => 'en_diseno']);
            $mensaje = 'Pedido derivado a Diseno correctamente.';

            $disenadores = User::whereHas('rol', fn($q) => $q->where('nombre', 'disenador'))
                ->where('activo', true)
                ->get();
            foreach ($disenadores as $disenador) {
                NotificationController::create(
                    userId: $disenador->id,
                    type: 'derivar_diseno',
                    title: 'Nuevo pedido para diseno',
                    body: "El pedido {$pedido->codigo} - {$pedido->nombre_cliente} ha sido derivado a diseno.",
                    icon: '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                    actionUrl: route('diseno.show', $pedido),
                );
            }
        } else {
            $permitidos = ['registrado'];
            if (! in_array($pedido->estado, $permitidos, true)) {
                return back()->with('ok', 'El pedido ya fue derivado a produccion o se encuentra en una etapa posterior.');
            }
            $pedido->update(['estado' => 'en_produccion']);
            $mensaje = 'Pedido derivado a Produccion correctamente.';

            $orfebres = User::whereHas('rol', fn($q) => $q->where('nombre', 'orfebre'))
                ->where('activo', true)
                ->get();
            foreach ($orfebres as $orfebre) {
                NotificationController::create(
                    userId: $orfebre->id,
                    type: 'derivar_produccion',
                    title: 'Nuevo pedido para produccion',
                    body: "El pedido {$pedido->codigo} - {$pedido->nombre_cliente} ha sido derivado a produccion.",
                    icon: '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                    actionUrl: route('produccion.show', $pedido),
                );
            }
        }

        return redirect()->route('pedidos.index')->with('ok', $mensaje);
    }

    private function validarPedido(Request $request, ?int $pedidoId = null): array
    {
        return $request->validate([
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'nombre_cliente' => ['required', 'string', 'max:120'],
            'telefono_cliente' => ['nullable', 'string', 'max:20'],
            'documento_cliente' => ['nullable', 'string', 'max:25'],
            'correo_cliente' => ['nullable', 'string', 'email', 'max:120'],
            'nombre_producto' => ['nullable', 'string', 'max:255'],
            'detalle_trabajo' => ['nullable', 'string'],
            'tipo_producto' => ['nullable', 'string', 'exists:categorias_producto,slug'],
            'tipo_entrega' => ['required', 'string', 'in:local,delivery,agencia'],
            'direccion_entrega' => ['nullable', 'string', 'max:255', 'required_unless:tipo_entrega,local'],
            'referencia_entrega' => ['nullable', 'string', 'max:255'],
            'distrito_entrega' => ['nullable', 'string', 'max:120', 'required_unless:tipo_entrega,local'],
            'codigo_postal_entrega' => ['nullable', 'string', 'max:12', 'required_unless:tipo_entrega,local'],
            'nombre_recibe' => ['nullable', 'string', 'max:120', 'required_unless:tipo_entrega,local'],
            'telefono_recibe' => ['nullable', 'string', 'max:20', 'required_unless:tipo_entrega,local'],
            'costo_delivery' => ['nullable', 'numeric', 'min:0', 'required_unless:tipo_entrega,local'],
            'fecha_entrega_compromiso' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
            'archivos_orden' => ['nullable', 'array'],
            'archivos_orden.*' => ['file', 'max:15360', 'mimes:pdf,doc,docx'],
            'productos' => ['nullable', 'array'],
            'productos.*.nombre' => ['required', 'string', 'max:255'],
            'productos.*.descripcion' => ['nullable', 'string', 'max:255'],
            'productos.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
            'tipo_pago' => ['required', 'string', 'in:contado,dos_partes'],
            'metodo_pago' => ['required', 'string', 'in:efectivo,yape,plin,tarjeta,transferencia'],
            'vuelto' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function calcularMontosDesdeProductos(Request $request): array
    {
        $total = 0;
        foreach ($request->input('productos', []) as $p) {
            $total += (float) ($p['precio_unitario'] ?? 0) * (int) ($p['cantidad'] ?? 0);
        }
        $tipoPago = $request->input('tipo_pago', 'dos_partes');
        $adelanto = $tipoPago === 'contado' ? round($total, 2) : round($total * 0.5, 2);
        return [
            'monto_total' => round($total, 2),
            'monto_adelanto' => $adelanto,
        ];
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

    private function guardarProductos(Request $request, Pedido $pedido): void
    {
        if (! $request->has('productos')) {
            return;
        }

        $productosData = $request->input('productos', []);
        $archivosPorProducto = $request->file('productos_archivos', []);

        $idsActuales = $pedido->productos()->pluck('id')->toArray();
        $idsRecibidos = [];

        foreach ($productosData as $i => $data) {
            $data['pedido_id'] = $pedido->id;
            $data['total'] = round(((float) ($data['precio_unitario'] ?? 0)) * ((int) ($data['cantidad'] ?? 0)), 2);
            $data['orden'] = $i;

            if (! empty($data['id']) && in_array($data['id'], $idsActuales)) {
                $producto = PedidoProducto::find($data['id']);
                if ($producto) {
                    $producto->update($data);
                } else {
                    unset($data['id']);
                    $producto = PedidoProducto::create($data);
                }
            } else {
                unset($data['id']);
                $producto = PedidoProducto::create($data);
            }
            $idsRecibidos[] = $producto->id;

            if (isset($archivosPorProducto[$i])) {
                foreach ($archivosPorProducto[$i] as $archivo) {
                    if (! $archivo->isValid()) continue;
                    $path = $archivo->store('disenos_producto', 'public');
                    PedidoProductoArchivo::create([
                        'pedido_producto_id' => $producto->id,
                        'archivo_path' => $path,
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'mime_type' => $archivo->getMimeType(),
                        'tamano_bytes' => $archivo->getSize(),
                    ]);
                }
            }
        }

        $idsAEliminar = array_diff($idsActuales, $idsRecibidos);
        if (! empty($idsAEliminar)) {
            PedidoProducto::whereIn('id', $idsAEliminar)->delete();
        }
    }

    public function eliminarArchivoProducto(PedidoProductoArchivo $pedidoProductoArchivo): \Illuminate\Http\JsonResponse
    {
        $pedidoProductoArchivo->delete();
        return response()->json(['ok' => true]);
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
