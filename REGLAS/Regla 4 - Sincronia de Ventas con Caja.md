# Regla 4 - Sincronía de Ventas (stock) con Caja

## Flujo de creación
- Toda venta de tipo `stock` (mostrador) se crea mediante `VentaController::store` → `registrarVentaStock`.
- La venta queda vinculada a una `caja_apertura` activa mediante `caja_apertura_id`.

## Campos de pago
- `forma_pago` (formulario): valores válidos → `efectivo`, `yape`, `plin`, `tarjeta`, `transferencia`, `mixto`.
- `monto_recibido`: monto que entrega el cliente (obligatorio para efectivo, readonly para digital).
- `vuelto`: calculado como `max(0, monto_recibido - total)` solo para efectivo.
- `monto_efectivo`: igual al total si `forma_pago = efectivo`, si no 0.
- `monto_digital`: igual al total si `forma_pago` es digital (`yape, plin, tarjeta, transferencia, mixto`), si no 0.

## Cálculo en caja
```
Efectivo (columna) = SUM(ventas.monto_efectivo)
Digital  (columna) = SUM(ventas.monto_digital)
Vuelto   (columna) = SUM(ventas.vuelto)  → se muestra en rojo con -S/
Efectivo Final     = Inicial + Efectivo - Vuelto
Total final        = Efectivo Final + Digital
```

## Vuelto
- Solo aplica a pagos en efectivo.
- Se almacena como valor positivo en la BD. La vista lo muestra con signo `-`.
- En la fórmula `Efectivo Final = Inicial + Efectivo - Vuelto`, se resta (el vuelto es dinero que sale de caja).

## Validaciones
- `forma_pago` es requerido.
- `monto_recibido` debe ser >= 0 y numérico (nullable).
- Si no hay `monto_recibido` en efectivo, el vuelto se calcula como 0.
