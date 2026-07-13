# Regla 2 - Lógica de ventas

## Tipos de venta

| Tipo | Descripción | Origen |
|------|-------------|--------|
| `stock` | Venta directa de mostrador | `VentaController::store` → `registrarVentaStock` |
| `pedido` | Cierre de pedido personalizado | `PedidoController` (varios métodos de cobro) |

## Venta de stock (mostrador)

### Campos del formulario
| Campo | Requisito | Notas |
|-------|-----------|-------|
| `cliente_nombre` | nullable | Nombre del cliente (opcional) |
| `producto_id[]` | required, array | IDs de productos seleccionados |
| `cantidad[]` | required, array | Cantidad por producto |
| `forma_pago` | required | `efectivo`, `yape`, `plin`, `tarjeta`, `transferencia`, `mixto` |
| `monto_recibido` | nullable | Solo requerido para efectivo |
| `tipo_comprobante` | required | `boleta` o `factura` |
| `documento_cliente` | nullable | RUC/DNI. Si boleta y vacío → `99999999` |
| `direccion_cliente` | nullable | Solo para factura |

### Cálculo de totales
```
total = SUM(cantidad × precio_unitario) de cada línea
vuelto = max(0, monto_recibido - total)   // Solo para efectivo
monto_efectivo = total (si efectivo) o 0
monto_digital = total (si digital) o 0
monto_cobrado = total
```

### Descuento de stock
- Solo se descuenta `stock_tienda` (no `stock_almacen`).
- Si `stock_tienda < cantidad`, se aborta con error 422.
- Al confirmar la venta, se ejecuta `producto->decrementarStockTienda($cantidad)`.

### Vinculación a caja
- La venta se vincula a la `caja_apertura_id` de la sesión.
- Si no hay caja abierta o está cerrada, se redirige a `ventas.index`.

## Venta de pedido (cierre)

### Momentos de cobro

| Momento | Método | Monto | Estado del pedido |
|---------|--------|-------|-------------------|
| Registro del pedido | `PedidoController::store` | `monto_adelanto` (50%) | `pendiente_adelanto` → `adelanto_pagado` |
| Confirmar pago final | `PedidoController::confirmarPagoFinal` | `monto_saldo` (restante) | `adelanto_pagado` → `pagado_completo` |
| Autorizar recoger | `PedidoController::autorizarRecoger` | `monto_saldo` si pendiente | Se cobra + se marca `listo_recoger` |

### Desglose de monto en pedido
```
monto_total = precio del pedido
monto_adelanto = monto_total / 2   (50% obligatorio)
monto_saldo = monto_total - monto_adelanto
```

## Comprobantes

| Tipo | Condición | Generado por |
|------|-----------|--------------|
| Boleta | DNI (8 dígitos) o sin documento | `ComprobanteService::emitir` |
| Factura | RUC (11 dígitos) | `ComprobanteService::emitir` |

- Cada venta puede tener **un solo comprobante** (tabla `comprobantes_venta`).
- El código del comprobante se genera automáticamente (serie + correlativo).
- El PDF se genera con `barryvdh/laravel-dompdf` y se visualiza en `ventas.comprobante`.

## UI del listado (ventas.index)

- **Selector de caja**: obligatorio antes de ver ventas. Cards con cajas abiertas.
- **Botón "Cambiar caja"**: ícono SVG switch, fondo ámbar.
- **Búsqueda**: por código, cliente o tipo de venta.
- **Filtros**: tipo (`stock`/`pedido`), scope (`mi_caja`/`todas`).
- **Paginación**: 10 registros por página, preserva filtros.

## Estados de pago del pedido

| Estado | Significado |
|--------|-------------|
| `pendiente_adelanto` | No se ha cobrado nada |
| `adelanto_pagado` | Tiene el 50% cobrado |
| `pagado_completo` | Saldo total cobrado |
