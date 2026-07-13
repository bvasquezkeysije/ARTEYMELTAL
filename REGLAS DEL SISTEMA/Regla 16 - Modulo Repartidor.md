# Regla 16 - Modulo Repartidor (flujo completo)

## Flujo del repartidor

```
Produccion notifica repartidor (estado: produciendo -> listo_entrega)
    |
    v
Repartidor ve notificacion en campanita
    |
    v
Repartidor entra a modulo Repartidor
    Ve pedidos con estado = listo_entrega o en_transporte

    Boton "Ver detalle" (cyan) -> Modal con info completa
      - Info cliente, tipo producto, direccion entrega
      - Tabla de productos con cantidades
      - Archivos de referencia y diseno
      - Viewer carrusel de modelos

    Si estado = listo_entrega:
      Seccion "Recoger pedido" con tabla editable
      - Cada producto muestra cantidad pedida y campo "cantidad a recoger"
      - Boton "Marcar recogido" (sky-600)
      -> Cambia estado: listo_entrega -> en_transporte

    Si estado = en_transporte:
      Seccion "Entregar en almacen"
      - Boton "Entregar en almacen" (amber-600)
      -> Modal de confirmacion
      -> Cambia estado: en_transporte -> en_almacen
      -> Notifica al almacenero
      -> Notifica al vendedor
```

## Estados del repartidor

| Estado | Badge | Significado |
|--------|-------|-------------|
| listo_entrega | Amber - "Listo para recoger" | Produccion termino, esperando repartidor |
| en_transporte | Sky - "En transporte" | Repartidor recogio, en camino al almacen |

## Index - Estructura

### Sin barra de busqueda
El modulo repartidor NO tiene barra de busqueda ni filtros.
Solo muestra la tabla con paginacion.

### Tabla - Columnas

| Columna | Contenido |
|---------|-----------|
| Codigo | Codigo unico del pedido |
| Cliente | Nombre del cliente |
| Estado | Badge: Listo para recoger (amber) / En transporte (sky) |
| Acciones | Boton "Ver detalle" (cyan) |

## Show (detalle del pedido)

### Info del pedido
- Cliente, estado, tipo producto, tipo entrega
- Detalle del trabajo
- Direccion de entrega (si existe)

### Tabla de productos
- Columnas: #, Nombre, Descripcion, Cant. pedido, Cant. recoge
- "Cant. recoge" solo se muestra si ya fue recogido

### Archivos
- Card "Modelo del cliente / referencia" (verde)
- Card "Diseno del disenador" (amber)
- Viewer carrusel con todos los archivos

### Seccion "Recoger pedido" (solo si estado = listo_entrega)
- Tabla editable con cantidad a recoger por producto
- Campo numerico con min=1 y max=cantidad_pedido
- Boton "Marcar recogido" (sky-600)
- Usa formulario POST con redirect

### Seccion "Entregar en almacen" (solo si estado = en_transporte)
- Boton "Entregar en almacen" (amber-600)
- Modal de confirmacion AJAX
- Al confirmar: cambia a en_almacen + notifica

## Rutas

| Metodo | Ruta | Nombre | Accion |
|--------|------|--------|--------|
| GET | /repartidor | repartidor.index | Listado de pedidos |
| GET | /repartidor/{pedido} | repartidor.show | Detalle del pedido |
| POST | /repartidor/{pedido}/recoger | repartidor.recoger | Marcar como recogido |
| POST | /repartidor/{pedido}/entregar-almacen | repartidor.entregar_almacen | Entregar en almacen |

## Notificaciones generadas

| Momento | Destinatario | Tipo | Titulo |
|---------|-------------|------|--------|
| Entregar en almacen | Todos los almaceneros | almacen | Pedido recibido en almacen |
| Entregar en almacen | Vendedor del pedido | repartidor | Pedido entregado en almacen |

## Diferencia con otros modulos

| Aspecto | Repartidor | Produccion | Diseno |
|---------|-----------|------------|--------|
| Busqueda/Filtros | NO | SI | SI |
| Subir archivos | NO | NO | SI |
| Acciones por fila | 1 (ver detalle) | 3 | 5 |
| Modales de confirmacion | 1 (entregar almacen) | 2 | 1 (notificar) |
| Formulario editable | SI (cantidad a recoger) | NO | NO |
