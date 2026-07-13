# Regla 15 - Modulo Produccion (actualizado)

## Flujo del orfebre

```
Orfebre entra a modulo Produccion
    Ve pedidos con estado = en_produccion o produciendo

    Boton "Ver detalle" (cyan) -> Modal con info completa del pedido
      - Info cliente, tipo producto, cantidad, estado
      - Tabla de productos
      - Archivos de referencia y de diseno
      - Viewer carrusel de modelos

    Boton "Iniciar produccion" (sky-600, solo si en_produccion)
      -> Modal de confirmacion
      -> POST AJAX a produccion/{pedido}/iniciar
      -> Cambia estado: en_produccion -> produciendo
      -> Notifica al vendedor

    Boton "Notificar repartidor" (amber-600, solo si produciendo)
      -> Modal de confirmacion
      -> POST AJAX a produccion/{pedido}/notificar
      -> Cambia estado: produciendo -> listo_entrega
      -> Notifica a TODOS los repartidores
      -> Notifica al vendedor
```

## Index - Barra de busqueda y filtros

### Busqueda
- Campo de texto con placeholder "Buscar por codigo, cliente o producto"
- Busqueda ilike en: codigo, nombre_cliente, nombre de productos
- Boton azul (buscar.ico) para enviar

### Filtros
- Boton celeste (filtros.ico) con dropdown
- Filtro por estado: Todos, En produccion, Produciendo
- Indicador ring cuando hay filtro activo
- Boton "Limpiar" condicional

### Paginacion
- 10 registros por pagina
- Preserva query strings (busqueda y filtros)

## Tabla - Columnas

| Columna | Contenido |
|---------|-----------|
| Codigo | Codigo unico del pedido |
| Cliente | Nombre del cliente |
| Productos | Lista de nombres (o uno solo) |
| Estado | Badge: En produccion (amber) / Produciendo (sky) |
| Estado diseno | Badge: Aprobado (emerald), En revision (sky), En diseno (amber), otro |
| Acciones | Botones de accion alineados a la derecha |

## Botones de accion por fila

| Boton | Color | Visible cuando | Accion |
|-------|-------|----------------|--------|
| Ver detalle | Cyan (#0891B2) | Siempre | Abre modal detalle |
| Iniciar produccion | Sky-600 | Solo si estado = en_produccion | Abre modal confirmar |
| Notificar repartidor | Amber-600 | Solo si estado = produciendo | Abre modal confirmar |

## Modales de confirmacion

### Modal "Iniciar produccion"
- Icono sky-100 con play
- Texto: "Deseas iniciar la produccion del pedido CODIGO?"
- Botones: Cancelar / Confirmar (sky-600)
- Fetch AJAX -> recarga pagina

### Modal "Notificar repartidor"
- Icono amber-100 con campana
- Texto: "Marcar el pedido CODIGO como listo y notificar al repartidor para que lo recoja?"
- Botones: Cancelar / Confirmar (amber-600)
- Fetch AJAX -> recarga pagina

### Modal Exito
- Icono emerald-100 con palomita verde
- Mensaje dinamico
- Boton "Entendido" (negro)

### Modal Error
- Icono red-100 con alerta roja
- Mensaje dinamico
- Boton "Entendido" (negro)

## Show (detalle del pedido)

- Info completa: cliente, tipo, cantidad, estado, estado diseno, detalle trabajo, observaciones
- Tabla de productos con cantidad
- Archivos de referencia y diseno en cards separadas
- Viewer carrusel con tabs
- Botones de accion segun estado:
  - en_produccion -> "Iniciar produccion" (sky)
  - produciendo -> "Notificar repartidor" (amber)
  - Boton "Volver" al index
- Todos los botones usan AJAX con modales de confirmacion

## Rutas

| Metodo | Ruta | Nombre | Accion |
|--------|------|--------|--------|
| GET | /produccion | produccion.index | Listado con busqueda/filtro |
| GET | /produccion/{pedido} | produccion.show | Detalle del pedido |
| POST | /produccion/{pedido}/iniciar | produccion.iniciar | Cambiar a produciendo |
| POST | /produccion/{pedido}/notificar | produccion.notificar | Cambiar a listo_entrega |

## Notificaciones generadas

| Momento | Destinatario | Tipo | Titulo |
|---------|-------------|------|--------|
| Iniciar produccion | Vendedor del pedido | produccion | Produccion iniciada |
| Notificar repartidor | Todos los repartidores | repartidor | Pedido listo para recoger |
| Notificar repartidor | Vendedor del pedido | produccion | Pedido en etapa de entrega |
