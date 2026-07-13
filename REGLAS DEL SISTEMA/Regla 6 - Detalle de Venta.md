# Regla 6 - Detalle de Venta

## Información mostrada
El detalle de venta (`ventas.show`) debe mostrar:

### Cabecera
- **Código**: código único de la venta.
- **Fecha**: fecha de la venta.
- **Tipo**: "Venta stock" o "Cierre pedido".
- **Cliente**: nombre del cliente o "-".
- **Monto total**: total de la venta.
- **Cobrado**: monto cobrado.
- **Forma de pago**: badge con el método (Efectivo en verde, digitales en azul).
- **Efectivo**: monto en efectivo (solo si > 0).
- **Digital**: monto digital (solo si > 0).
- **Vuelto**: vuelto entregado en rojo con -S/ (solo si > 0).
- **Comprobante**: enlace al PDF del comprobante si existe.

### Detalles de productos
- Tabla con Item, Cantidad, Precio unitario, Subtotal.

### Pedido relacionado
- Si la venta corresponde a un pedido, se muestra el código del pedido.

## Reglas de negocio
- `metodo_pago` se mapea a labels legibles: efectivo, yape, plin, tarjeta, transferencia, mixto.
- Efectivo se muestra con badge verde (`bg-emerald-100 text-emerald-700`).
- Pagos digitales se muestran con badge azul (`bg-sky-100 text-sky-700`).
- Las secciones de Efectivo, Digital, Vuelto y Comprobante son condicionales (solo se renderizan si tienen valor > 0 o existen).
