<?php

namespace Database\Seeders;

use App\Models\ComprobanteVenta;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaInicialSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioAdmin = User::query()
            ->where('email', 'bvasquezkeysije@arteymetales.online')
            ->orWhere('email', 'pfernandezadeli@arteymetales.online')
            ->orderBy('id')
            ->first();

        if (! $usuarioAdmin) {
            return;
        }

        $pedidoCerrado = Pedido::query()->where('codigo', 'PED-000001')->first();

        $producto1 = Producto::query()->where('codigo', 'PROD-0001')->first();
        $producto2 = Producto::query()->where('codigo', 'PROD-0002')->first();
        $producto3 = Producto::query()->where('codigo', 'PROD-0003')->first();

        DB::transaction(function () use ($usuarioAdmin, $pedidoCerrado, $producto1, $producto2, $producto3): void {
            $ventaStock = Venta::query()->updateOrCreate(
                ['codigo' => 'VEN-000001'],
                [
                    'tipo_venta' => 'stock',
                    'pedido_id' => null,
                    'cliente_nombre' => 'Carlos Mena',
                    'fecha_venta' => '2026-04-24',
                    'monto_total' => 67.00,
                    'monto_cobrado' => 67.00,
                    'estado_pago' => 'pagado_completo',
                    'observaciones' => 'Venta mostrador de medallas para premiacion.',
                    'usuario_id' => $usuarioAdmin->id,
                ]
            );

            $ventaStock->detalles()->delete();

            $this->crearDetalle($ventaStock, $producto1?->id, $producto1?->nombre ?? 'Medalla dorada 1er puesto', 2, 18.00);
            $this->crearDetalle($ventaStock, $producto2?->id, $producto2?->nombre ?? 'Medalla plateada 2do puesto', 1, 16.00);
            $this->crearDetalle($ventaStock, $producto3?->id, $producto3?->nombre ?? 'Medalla bronce 3er puesto', 1, 15.00);

            ComprobanteVenta::query()->updateOrCreate(
                ['venta_id' => $ventaStock->id],
                [
                    'tipo_comprobante' => 'boleta',
                    'serie' => 'B001',
                    'correlativo' => 1,
                    'codigo' => 'B001-000001',
                    'documento_cliente' => '74561230',
                    'nombre_cliente' => 'Carlos Mena',
                    'direccion_cliente' => 'Jose Leonardo Ortiz',
                ]
            );

            if ($pedidoCerrado) {
                $ventaPedido = Venta::query()->updateOrCreate(
                    ['codigo' => 'VEN-000002'],
                    [
                        'tipo_venta' => 'pedido',
                        'pedido_id' => $pedidoCerrado->id,
                        'cliente_nombre' => $pedidoCerrado->nombre_cliente,
                        'fecha_venta' => '2026-04-24',
                        'monto_total' => 800.00,
                        'monto_cobrado' => 400.00,
                        'estado_pago' => 'pagado_completo',
                        'observaciones' => 'Cierre de pedido personalizado.',
                        'usuario_id' => $usuarioAdmin->id,
                    ]
                );

                $ventaPedido->detalles()->delete();
                $this->crearDetalle($ventaPedido, null, 'Pago final pedido PED-000001', 1, 400.00);

                ComprobanteVenta::query()->updateOrCreate(
                    ['venta_id' => $ventaPedido->id],
                    [
                        'tipo_comprobante' => 'factura',
                        'serie' => 'F001',
                        'correlativo' => 1,
                        'codigo' => 'F001-000001',
                        'documento_cliente' => '20512345678',
                        'nombre_cliente' => 'Colegio San Jose',
                        'direccion_cliente' => 'Av. Saenz Pena 425, Chiclayo',
                    ]
                );
            }
        });
    }

    private function crearDetalle(Venta $venta, ?int $productoId, string $nombre, int $cantidad, float $precio): void
    {
        VentaDetalle::query()->create([
            'venta_id' => $venta->id,
            'producto_id' => $productoId,
            'producto_nombre' => $nombre,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => round($cantidad * $precio, 2),
        ]);
    }
}

