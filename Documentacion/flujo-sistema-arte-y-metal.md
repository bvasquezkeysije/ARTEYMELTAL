# Flujo del sistema - Arte y Metal Chiclayo E.I.R.L.

Este archivo es la entrada principal para entender el proceso real del negocio.

## Idea base

La empresa opera entre tres espacios fisicos:
- `Tienda`
- `Centro de Produccion`
- `Almacen`

El sistema debe reflejar ese movimiento fisico y administrativo sin mezclar responsabilidades.

## Roles del sistema

| Rol | Responsabilidad principal |
|---|---|
| Admin | Gobierno total del sistema |
| Almacenero | Registro de productos y control de stock |
| Vendedor | Venta directa, registro del pedido y coordinacion de entrega |
| Disenador | Revision o creacion del diseno |
| Orfebre | Fabricacion del producto |
| Repartidor | Traslado y despacho |

## Ubicaciones operativas

| Ubicacion | Uso principal | Actores |
|---|---|---|
| Tienda | Atencion comercial y coordinacion con el cliente | Vendedor |
| Centro de Produccion | Diseno y fabricacion | Disenador, Orfebre |
| Almacen | Stock, recepcion de producto terminado y salida final | Almacenero, Repartidor |

El repartidor conecta fisicamente produccion, almacen y entrega.

## Flujo operativo principal

### Etapa 1. Preparacion del catalogo
- El almacenero registra productos.
- Define stock inicial.
- Sin catalogo no se puede vender ni registrar bien pedidos.

### Etapa 2. Atencion comercial

#### Caso A. Venta directa
- El vendedor elige un producto del catalogo.
- Registra la venta.
- Se emite comprobante.
- El stock baja en ese momento.

#### Caso B. Pedido personalizado
- El vendedor registra el pedido.
- Puede adjuntar un diseno del cliente o indicar que debe crearse.
- El pedido pasa al flujo de personalizacion.

### Etapa 3. Diseno
- El disenador revisa el archivo recibido o crea uno nuevo.
- Corrige, ajusta y aprueba el diseno final.
- El pedido queda listo para pasar a produccion.

### Etapa 4. Fabricacion
- El orfebre fabrica el producto con base en el diseno aprobado.
- Cuando termina, el pedido queda listo para traslado.

### Etapa 5. Traslado a almacen
- El repartidor recoge el producto terminado.
- Lo mueve al almacen.
- El sistema registra que ya esta listo para entrega.

### Etapa 6. Entrega al cliente
- El vendedor coordina recojo, delivery o envio por agencia.
- Una vez ejecutada la entrega y cobrado el saldo, el pedido se cierra.

## Reglas criticas del flujo

- No se debe empujar un pedido a produccion sin adelanto.
- El adelanto sigue una logica fija `50/50`.
- El cierre del pedido genera una venta final y un comprobante.
- Las ventas directas descuentan stock.
- Los pedidos personalizados no descuentan stock al cerrarse como venta.

## Estados que debe entender cualquier agente

### Estado general del pedido

`registrado -> en_produccion -> listo_entrega -> entregado`

El estado `cancelado` puede aparecer como salida excepcional.

### Estado de personalizacion

`sin_iniciar -> en_diseno -> en_revision -> aprobado -> en_produccion -> listo_entrega -> entregado`

### Estado de pago

`pendiente_adelanto -> adelanto_pagado -> pagado_completo`

## Resumen ejecutivo

Si un agente solo debe recordar cinco cosas del negocio, son estas:

1. El sistema tiene dos flujos separados: venta directa y pedido personalizado.
2. El pedido personalizado pasa por tienda, diseno, fabricacion, almacen y entrega.
3. El adelanto del 50% es obligatorio para avanzar.
4. El stock baja en ventas directas, no en el cierre del pedido.
5. Los permisos por rol deben respetar quien es dueno de cada etapa.

## Lectura complementaria

- [[01-Negocio/README|Resumen guiado del negocio]]
- [[Sistema/4.- Logica del negocio|Logica del negocio por modulo]]
- [[02-Backend/README|Mapa de implementacion backend]]
