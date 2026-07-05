# Negocio - resumen guiado

Este bloque explica como funciona la empresa antes de entrar al codigo.

## Objetivo del sistema

El sistema debe controlar la operacion completa de Arte y Metal Chiclayo E.I.R.L.:
- catalogo y stock
- clientes
- pedidos personalizados
- ventas de mostrador
- pagos
- entregas
- usuarios y permisos

## Flujo central

La operacion tiene dos caminos:

### 1. Venta directa
- El vendedor selecciona un producto del catalogo.
- Se registra la venta.
- Se emite boleta o factura.
- El stock baja inmediatamente.

### 2. Pedido personalizado
- El vendedor registra el pedido.
- El disenador revisa o crea el diseno.
- El orfebre fabrica el producto.
- El repartidor lo mueve al almacen.
- El vendedor coordina la entrega final.
- Cuando se cobra el saldo se genera la venta final y el pedido se cierra.

## Regla critica del negocio

- No se puede avanzar a produccion sin adelanto.
- El adelanto es obligatorio y responde a una logica fija de `50/50`.
- El stock solo se descuenta en ventas directas.
- Los pedidos conservan snapshot de datos del cliente.

## Roles y duenos del proceso

| Rol | Dueno de proceso |
|---|---|
| Admin | Gobierno completo del sistema |
| Vendedor / Ventas | Clientes, ventas directas y registro inicial del pedido |
| Disenador | Etapa de diseno del pedido |
| Orfebre | Etapa de fabricacion |
| Repartidor | Etapa de entrega y traslado |
| Almacen | Catalogo y stock |

## Que leer despues

- [[../flujo-sistema-arte-y-metal|Flujo operativo completo]]
- [[../Sistema/4.- Logica del negocio|Reglas detalladas por modulo]]
- [[../02-Backend/README|Como se refleja esto en el backend]]
