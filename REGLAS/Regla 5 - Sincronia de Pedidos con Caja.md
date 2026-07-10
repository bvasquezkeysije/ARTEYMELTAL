# Regla 5 - Sincronía de Pedidos con Caja

## Flujo general
- Los pedidos NO se vinculan directamente a caja. La sincronía ocurre a través de `Venta`.
- Cada vez que un pedido genera un cobro (adelanto o saldo), se crea una `Venta` con `tipo_venta = 'pedido'` y se vincula a la caja activa mediante `caja_apertura_id`.

## Momentos de cobro

### 1. Registro del pedido (adelanto)
- **Origen**: `PedidoController::store`.
- Se crea una `Venta` por el monto del adelanto (`monto_adelanto`).
- `monto_efectivo` o `monto_digital` se asigna según `metodo_pago`.
- `vuelto` se captura del formulario (solo aplica para efectivo).
- `observaciones = 'Adelanto pedido ' + codigo_pedido`.

### 2. Confirmar pago final
- **Origen**: `PedidoController::confirmarPagoFinal`.
- Se crea una `Venta` por el saldo pendiente (`monto_saldo`).
- `monto_efectivo` o `monto_digital` se asigna según `metodo_pago`.
- Se actualiza `pedido.estado_pago = 'pagado_completo'` y `pedido.monto_saldo = 0`.

### 3. Autorizar recoger (con cobro de saldo)
- **Origen**: `PedidoController::autorizarRecoger`.
- Si el pedido aún tiene saldo pendiente, se cobra junto con la autorización.
- Se crea una `Venta` de la misma forma que en confirmarPagoFinal.
- Se actualiza `pedido.estado = 'listo_recoger'`.

## Impacto en caja
- Las ventas de pedidos aportan a los mismos agregados de caja que las ventas de stock:
  - `SUM(monto_efectivo)` → columna Efectivo
  - `SUM(monto_digital)` → columna Digital
  - `SUM(vuelto)` → columna Vuelto
- No hay distinción visual entre ventas de stock y ventas de pedidos en el resumen de caja.

## Sesión de caja en pedidos
- `PedidoController` lee `session('pedido_caja_apertura_id')` para vincular las ventas.
- El usuario debe seleccionar una caja activa antes de operar pedidos.
- Si no hay caja o la caja está cerrada, se redirige a la selección.
