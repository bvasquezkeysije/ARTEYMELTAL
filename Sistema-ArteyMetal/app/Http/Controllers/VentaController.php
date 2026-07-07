<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\CajaApertura;
use App\Services\ComprobanteVentaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function __construct(
        private readonly ComprobanteVentaService $comprobanteService
    )
    {
    }

    public function index()
    {
        $cajaAperturaId = session('caja_apertura_id');

        if (! $cajaAperturaId) {
            return $this->redirectToCajaSelection();
        }

        $caja = CajaApertura::find($cajaAperturaId);

        if (! $caja || $caja->estado !== 'abierta' || $caja->usuario_id !== auth()->id()) {
            session()->forget('caja_apertura_id');
            return $this->redirectToCajaSelection();
        }

        $busqueda = request('q');
        $tipo = request('tipo');

        $ventas = Venta::query()
            ->with(['pedido', 'detalles', 'comprobante'])
            ->where('caja_apertura_id', $cajaAperturaId)
            ->where(function ($q) use ($busqueda) {
                $q->when($busqueda, function ($query) use ($busqueda) {
                    $query->where('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('cliente_nombre', 'like', "%{$busqueda}%")
                        ->orWhere('tipo_venta', 'like', "%{$busqueda}%");
                });
            })
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('tipo_venta', $tipo);
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('ventas.index', compact('ventas', 'busqueda', 'tipo', 'caja') + ['cajasAbiertas' => collect(), 'sinCaja' => false]);
    }

    private function redirectToCajaSelection()
    {
        $cajasAbiertas = CajaApertura::query()
            ->where('usuario_id', auth()->id())
            ->where('estado', 'abierta')
            ->get();

        session()->forget('caja_apertura_id');

        if ($cajasAbiertas->isEmpty()) {
            return view('ventas.index', [
                'ventas' => collect(),
                'busqueda' => '',
                'tipo' => '',
                'caja' => null,
                'cajasAbiertas' => collect(),
                'sinCaja' => true,
            ]);
        }

        return view('ventas.index', [
            'ventas' => collect(),
            'busqueda' => '',
            'tipo' => '',
            'caja' => null,
            'cajasAbiertas' => $cajasAbiertas,
        ]);
    }

    public function seleccionarCaja(CajaApertura $cajaApertura)
    {
        if ($cajaApertura->usuario_id !== auth()->id() || $cajaApertura->estado !== 'abierta') {
            return redirect()->route('ventas.index')
                ->withErrors(['caja' => 'Caja no valida.']);
        }

        session(['caja_apertura_id' => $cajaApertura->id]);
        return redirect()->route('ventas.index');
    }

    public function cambiarCaja()
    {
        session()->forget('caja_apertura_id');
        return redirect()->route('ventas.index');
    }

    public function create()
    {
        $cajaAperturaId = session('caja_apertura_id');

        if (! $cajaAperturaId) {
            return redirect()->route('ventas.index');
        }

        $cajaAbierta = CajaApertura::find($cajaAperturaId);

        if (! $cajaAbierta || $cajaAbierta->estado !== 'abierta' || $cajaAbierta->usuario_id !== auth()->id()) {
            session()->forget('caja_apertura_id');
            return redirect()->route('ventas.index');
        }

        $productos = Producto::query()
            ->with('imagenes')
            ->where('activo', true)
            ->where('stock_actual', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('ventas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $cajaAperturaId = session('caja_apertura_id');

        if (! $cajaAperturaId) {
            return redirect()->route('ventas.index');
        }

        $cajaAbierta = CajaApertura::find($cajaAperturaId);

        if (! $cajaAbierta || $cajaAbierta->estado !== 'abierta' || $cajaAbierta->usuario_id !== auth()->id()) {
            session()->forget('caja_apertura_id');
            return redirect()->route('ventas.index');
        }

        $datosBase = $request->validate([
            'observaciones' => ['nullable', 'string'],
            'tipo_comprobante' => ['required', 'string', 'in:boleta,factura'],
            'documento_cliente' => ['nullable', 'string', 'max:20'],
            'direccion_cliente' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($datosBase['documento_cliente']) && $datosBase['tipo_comprobante'] === 'boleta') {
            $datosBase['documento_cliente'] = '99999999';
        }

        return $this->registrarVentaStock($request, $datosBase, $cajaAbierta->id);
    }

    public function show(Venta $venta)
    {
        $venta->load(['pedido', 'detalles', 'comprobante']);

        return view('ventas.show', compact('venta'));
    }

    public function comprobante(Venta $venta)
    {
        $venta->load(['detalles', 'comprobante', 'usuario']);
        abort_unless($venta->comprobante, 404);

        $subtotalItems = (float) $venta->detalles->sum(fn ($d) => (float) $d->subtotal);
        $totalComprobante = $subtotalItems > 0 ? $subtotalItems : (float) $venta->monto_cobrado;
        $filename = 'comprobante-'.$venta->comprobante->codigo.'.pdf';

        $pdf = Pdf::loadView('ventas.comprobante_pdf', [
            'venta' => $venta,
            'subtotalItems' => $subtotalItems,
            'totalComprobante' => $totalComprobante,
        ])->setPaper('a4');

        return $pdf->stream($filename);
    }

    public function emitirComprobante(Venta $venta)
    {
        $venta->load(['comprobante', 'pedido']);

        if ($venta->comprobante) {
            return redirect()->route('ventas.index')->with('ok', 'Esta venta ya tiene comprobante emitido.');
        }

        $documento = preg_replace('/\D/', '', (string) ($venta->pedido?->documento_cliente ?? ''));
        $tipoComprobante = strlen($documento) === 11 ? 'factura' : 'boleta';

        $this->comprobanteService->emitir($venta, [
            'tipo_comprobante' => $tipoComprobante,
            'documento_cliente' => $documento !== '' ? $documento : null,
            'nombre_cliente' => $venta->cliente_nombre ?: 'Cliente mostrador',
            'direccion_cliente' => $venta->pedido?->direccion_entrega,
        ]);

        return redirect()->route('ventas.index')->with('ok', 'Comprobante emitido correctamente para la venta '.$venta->codigo.'.');
    }

    private function registrarVentaStock(Request $request, array $datosBase, int $cajaAperturaId)
    {
        $datos = $request->validate([
            'cliente_nombre' => ['nullable', 'string', 'max:120'],
            'producto_id' => ['required', 'array', 'min:1'],
            'producto_id.*' => ['required', 'integer', 'exists:productos,id'],
            'cantidad' => ['required', 'array', 'min:1'],
            'cantidad.*' => ['required', 'integer', 'min:1'],
        ]);

        $lineas = [];
        $total = 0.0;

        foreach ($datos['producto_id'] as $i => $productoId) {
            $cantidad = (int) ($datos['cantidad'][$i] ?? 0);
            if ($cantidad <= 0) {
                continue;
            }

            $producto = Producto::findOrFail($productoId);

            $stockTienda = (int) ($producto->stock_tienda ?? 0);
            if ($stockTienda < $cantidad) {
                return back()->withInput()->withErrors([
                    "cantidad.$i" => "Stock insuficiente en tienda para {$producto->nombre}. Stock en tienda: {$stockTienda}.",
                ]);
            }

            $precio = (float) ($producto->precio_referencia ?? 0);
            $subtotal = round($precio * $cantidad, 2);
            $total += $subtotal;

            $lineas[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($lineas)) {
            return back()->withInput()->withErrors(['producto_id' => 'Debes agregar al menos un producto con cantidad valida.']);
        }

        $erroresComprobante = $this->validarDatosComprobante($datosBase, $datos);
        if ($erroresComprobante !== []) {
            return back()->withInput()->withErrors($erroresComprobante);
        }

            DB::transaction(function () use ($lineas, $datos, $datosBase, $total, $request, $cajaAperturaId) {
            $venta = Venta::create([
                'codigo' => $this->generarCodigoVenta(),
                'tipo_venta' => 'stock',
                'pedido_id' => null,
                'cliente_nombre' => $datos['cliente_nombre'] ?? null,
                'fecha_venta' => now()->toDateString(),
                'monto_total' => $total,
                'monto_cobrado' => $total,
                'estado_pago' => 'pagado_completo',
                'observaciones' => $datosBase['observaciones'] ?? null,
                'usuario_id' => $request->user()->id,
                'caja_apertura_id' => $cajaAperturaId,
            ]);

            foreach ($lineas as $linea) {
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $linea['producto']->id,
                    'producto_nombre' => $linea['producto']->nombre,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $producto = $linea['producto'];
                $producto->stock_tienda -= $linea['cantidad'];
                $producto->save();
            }

            $this->comprobanteService->emitir($venta, [
                'tipo_comprobante' => $datosBase['tipo_comprobante'],
                'documento_cliente' => $datosBase['documento_cliente'] ?: null,
                'nombre_cliente' => $datos['cliente_nombre'] ?: 'Cliente mostrador',
                'direccion_cliente' => $datosBase['direccion_cliente'] ?: null,
            ]);
        });

        return redirect()->route('ventas.index')->with('ok', 'Venta de productos registrada correctamente.');
    }

    private function validarDatosComprobante(array $datosBase, array $datosVenta): array
    {
        $errores = [];
        $tipo = $datosBase['tipo_comprobante'] ?? 'boleta';
        $documento = preg_replace('/\D/', '', (string) ($datosBase['documento_cliente'] ?? ''));
        $nombreCliente = trim((string) ($datosVenta['cliente_nombre'] ?? ''));

        if ($tipo === 'factura') {
            if ($documento === '' || strlen($documento) !== 11) {
                $errores['documento_cliente'] = 'Para factura debes ingresar RUC de 11 digitos.';
            }

            if ($nombreCliente === '') {
                $errores['cliente_nombre'] = 'Para factura debes ingresar razon social o nombre del cliente.';
            }
        }

        if ($documento !== '' && ! in_array(strlen($documento), [8, 11], true)) {
            $errores['documento_cliente'] = 'El documento debe tener 8 digitos (DNI) o 11 digitos (RUC).';
        }

        return $errores;
    }

    private function generarCodigoVenta(): string
    {
        $ultimoId = Venta::max('id') ?? 0;
        $siguiente = $ultimoId + 1;

        return 'VEN-' . str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT);
    }
}
