# Regla 3 - Para la lógica de caja

## Apertura
- Solo se puede abrir una caja por vez (sesión activa).
- Si ya hay una caja abierta, no se permite abrir otra.
- El monto inicial es obligatorio y debe ser >= 0.

## Ventas en caja
- Cada venta se registra con monto_efectivo, monto_digital y vuelto.
- El total final de caja se calcula como:
  `Total = Inicial + Efectivo + Digital - Vuelto`
- Las columnas Efectivo, Digital y Vuelto en el listado son la suma de todas las ventas de esa caja (con `withSum`).

## Vuelto
- Vuelto representa dinero que se devuelve al cliente (resta).
- Se muestra en color rojo con prefijo `-`.

## Cierre
- Al cerrar, el modal muestra el desglose: Inicial + Efectivo + Digital - Vuelto = Monto esperado.
- El campo "Monto final en caja" se prellena con el Monto esperado.
- Si el operador ingresa un valor distinto, se guarda tal cual (puede haber diferencia por faltante/sobrante).
- Una vez cerrada, la caja no se puede reabrir ni modificar.
- La sesión de caja activa (`caja_apertura_id`) se limpia al cerrar.

## Vista
- Cada columna de la tabla se corresponde 1:1 con sumas de ventas o campos de caja_aperturas.
- Las vistas NO usan monto_total de ventas; usan la descomposición efectivo + digital - vuelto.

## Inconsistencias
- Si monto_final (manual) difiere de Total final (calculado), se muestra la diferencia.
