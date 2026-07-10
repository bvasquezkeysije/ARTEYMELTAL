<?php

namespace App\Services;

use App\Models\ComprobanteVenta;
use App\Models\Venta;

class ComprobanteVentaService
{
    public function emitir(Venta $venta, array $datos): ComprobanteVenta
    {
        $tipo = ($datos['tipo_comprobante'] ?? 'boleta') === 'factura' ? 'factura' : 'boleta';
        $serie = $tipo === 'factura' ? 'F001' : 'B001';

        $correlativo = (int) ComprobanteVenta::query()
            ->where('tipo_comprobante', $tipo)
            ->where('serie', $serie)
            ->max('correlativo');

        $correlativo++;
        $codigo = $serie . '-' . str_pad((string) $correlativo, 6, '0', STR_PAD_LEFT);

        return ComprobanteVenta::create([
            'venta_id' => $venta->id,
            'tipo_comprobante' => $tipo,
            'serie' => $serie,
            'correlativo' => $correlativo,
            'codigo' => $codigo,
            'documento_cliente' => $datos['documento_cliente'] ?? null,
            'nombre_cliente' => $datos['nombre_cliente'] ?? ($venta->cliente_nombre ?: 'Cliente'),
            'direccion_cliente' => $datos['direccion_cliente'] ?? null,
        ]);
    }
}
