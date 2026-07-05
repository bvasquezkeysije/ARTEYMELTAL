<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $venta->comprobante->codigo }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111; font-size: 12px; margin: 0; }
        .sheet { padding: 24px 26px; }
        .head { border: 1px solid #d8c89a; border-radius: 10px; padding: 14px 16px; }
        .head table { width: 100%; border-collapse: collapse; }
        .empresa { font-size: 20px; font-weight: 700; color: #2a2419; margin-bottom: 6px; }
        .muted { color: #575757; font-size: 11px; line-height: 1.35; }
        .docbox { text-align: right; }
        .docbox .tipo { font-weight: 700; font-size: 13px; color: #6a5122; }
        .docbox .codigo { font-weight: 700; font-size: 17px; margin: 4px 0; }
        .panel { border: 1px solid #e4d7b6; border-radius: 10px; margin-top: 12px; overflow: hidden; }
        .panel-title { background: #f8f2e1; color: #6a5122; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; padding: 9px 12px; }
        .panel-body { padding: 10px 12px; }
        .meta { width: 100%; border-collapse: collapse; }
        .meta td { padding: 5px 0; vertical-align: top; }
        .k { color: #7c6430; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; }
        .v { color: #111; font-size: 12px; }
        table.detalle { width: 100%; border-collapse: collapse; }
        table.detalle th { background: #fcf8ee; color: #5a4a2a; font-size: 11px; font-weight: 700; border-bottom: 1px solid #eadfbe; padding: 8px 10px; text-align: left; }
        table.detalle td { border-bottom: 1px solid #efe6cf; padding: 8px 10px; }
        table.detalle tr:last-child td { border-bottom: none; }
        .totales { width: 320px; margin-left: auto; }
        .row { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .row td { padding: 4px 0; }
        .row .label { color: #4f4f4f; }
        .row.total { border-top: 1px solid #d8c89a; margin-top: 6px; padding-top: 6px; }
        .row.total td { font-weight: 700; font-size: 13px; color: #2a2419; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="head">
            <table>
                <tr>
                    <td style="width: 62%;">
                        <div class="empresa">ARTE Y METALES</div>
                        <div class="muted">Alfredo Lapoint 1074 - Chiclayo</div>
                        <div class="muted">RUC 20608082329</div>
                    </td>
                    <td class="docbox">
                        <div class="tipo">{{ strtoupper($venta->comprobante->tipo_comprobante) }}</div>
                        <div class="codigo">{{ $venta->comprobante->codigo }}</div>
                        <div class="muted">Fecha {{ optional($venta->fecha_venta)->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="panel">
            <div class="panel-title">Datos del cliente</div>
            <div class="panel-body">
                <table class="meta">
                    <tr>
                        <td style="width: 50%;">
                            <div class="k">Cliente</div>
                            <div class="v">{{ $venta->comprobante->nombre_cliente }}</div>
                        </td>
                        <td style="width: 50%;">
                            <div class="k">Documento</div>
                            <div class="v">{{ $venta->comprobante->documento_cliente ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="k">Direccion</div>
                            <div class="v">{{ $venta->comprobante->direccion_cliente ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="k">Operacion</div>
                            <div class="v">{{ $venta->tipo_venta === 'pedido' ? 'Cierre de pedido' : 'Venta de stock' }} - {{ $venta->codigo }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Detalle de productos</div>
            <div class="panel-body" style="padding: 0;">
                <table class="detalle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="width: 80px;">Cantidad</th>
                            <th style="width: 110px;">P. Unit.</th>
                            <th style="width: 120px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto_nombre }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>S/ {{ number_format((float) $detalle->precio_unitario, 2) }}</td>
                                <td>S/ {{ number_format((float) $detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Resumen de montos</div>
            <div class="panel-body">
                <table class="totales">
                    <tr class="row">
                        <td class="label">Subtotal items</td>
                        <td style="text-align:right;">S/ {{ number_format($subtotalItems, 2) }}</td>
                    </tr>
                    <tr class="row">
                        <td class="label">Cobrado</td>
                        <td style="text-align:right;">S/ {{ number_format((float) $venta->monto_cobrado, 2) }}</td>
                    </tr>
                    <tr class="row total">
                        <td>Total comprobante</td>
                        <td style="text-align:right;">S/ {{ number_format($totalComprobante, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
