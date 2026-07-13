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

    Barra de busqueda: por codigo o cliente
    Filtro: Todos / Listo para recoger / En transporte

    Boton "Ver detalle" (cyan) -> Modal con info completa
      - Info cliente, tipo producto, direccion entrega
      - Tabla de productos con cantidades
      - Archivos de referencia y diseno
      - Viewer carrusel de modelos

    Boton "Recoger" (sky-600, solo si listo_entrega)
      -> Modal con tabla editable de cantidades
      -> Cambia estado: listo_entrega -> en_transporte

    Boton "Entregar en almacen" (amber-600, solo si en_transporte)
      -> Modal de confirmacion
      -> POST AJAX a repartidor/{pedido}/entregar-almacen
      -> Cambia estado: en_transporte -> en_almacen
      -> Notifica a TODOS los almaceneros
      -> Notifica al vendedor
```

## Estados del repartidor

| Estado | Badge | Significado |
|--------|-------|-------------|
| listo_entrega | Amber - "Listo para recoger" | Produccion termino, esperando repartidor |
| en_transporte | Sky - "En transporte" | Repartidor recogio, en camino al almacen |

## Index - Estructura

### Barra de busqueda
- Campo de texto "Buscar por codigo o cliente"
- Busqueda ilike en: codigo, nombre_cliente
- Boton azul (buscar.ico) para enviar
- Boton celeste (filtros.ico) con dropdown de estados
- Boton "Limpiar" condicional

### Filtros
- Todos
- Listo para recoger (amber)
- En transporte (sky)
- Indicador ring cuando hay filtro activo

### Tabla - Columnas

| Columna | Contenido |
|---------|-----------|
| Codigo | Codigo unico del pedido |
| Cliente | Nombre del cliente |
| Productos | Lista de nombres |
| Estado | Badge: Listo para recoger (amber) / En transporte (sky) |
| Acciones | Botones de accion |

### Botones de accion por fila

| Boton | Color | Visible cuando | Accion |
|-------|-------|----------------|--------|
| Ver detalle | Cyan (#0891B2) | Siempre | Modal detalle + viewer |
| Recoger | Sky-600 | Solo si listo_entrega | Modal cantidades |
| Entregar en almacen | Amber-600 | Solo si en_transporte | Modal confirmar AJAX |

## Show (detalle del pedido)

- Info completa: cliente, estado, tipo producto, tipo entrega
- Direccion de entrega
- Tabla de productos con cantidades
- Archivos de referencia y diseno
- Viewer carrusel
- Acciones segun estado:
  - listo_entrega -> Formulario "Recoger pedido"
  - en_transporte -> Boton "Entregar en almacen" (AJAX)
- Boton "Volver"

## Rutas

| Metodo | Ruta | Nombre | Accion |
|--------|------|--------|--------|
| GET | /repartidor | repartidor.index | Listado con busqueda/filtro |
| GET | /repartidor/{pedido} | repartidor.show | Detalle del pedido |
| POST | /repartidor/{pedido}/recoger | repartidor.recoger | Marcar como recogido |
| POST | /repartidor/{pedido}/entregar-almacen | repartidor.entregar_almacen | Entregar en almacen |

## Notificaciones generadas

| Momento | Destinatario | Tipo | Titulo |
|---------|-------------|------|--------|
| Entregar en almacen | Todos los almaceneros | almacen | Pedido recibido en almacen |
| Entregar en almacen | Vendedor del pedido | repartidor | Pedido entregado en almacen |
