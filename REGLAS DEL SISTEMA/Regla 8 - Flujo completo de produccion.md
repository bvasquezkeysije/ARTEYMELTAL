# Regla 8 - Flujo completo de produccion de pedidos

## Roles y sus modulos en el sidebar

| Rol | Modulo que ve | Slug de permiso |
|-----|---------------|-----------------|
| Administrador | Todos | (todos) |
| Vendedor | Caja, Ventas, Pedidos, Clientes | — |
| Disenador | **Diseno** | `diseno.ver`, `diseno.gestionar` |
| Orfebre | **Produccion** | `produccion.ver`, `produccion.gestionar` |
| Repartidor | **Repartidor** | `repartidor.ver`, `repartidor.gestionar` |
| Almacenero | **Almacen**, Productos | `almacen.ver`, `almacen.gestionar` |

## Flujo completo

```
Vendedor registra pedido (con productos personalizados + archivos de referencia)
    │
    ▼
Disenador ve el pedido en modulo Diseno, sube archivos de diseno (CDR, PDF, AI, etc.)
    │  (estado_personalizacion: sin_iniciar → en_diseno → en_revision)
    │
    ▼
Disenador notifica al vendedor (cambia a en_revision)
    │
    ▼
Vendedor/Admin revisa el diseno con el cliente
    │  (estado_personalizacion: en_revision → aprobado)
    │
    ▼
Vendedor/Admin deriva el pedido a Produccion
    │  (estado: registrado → en_produccion)
    │  (esto ocurre desde el modal "Derivar pedido" en Pedidos)
    │
    ▼
Orfebre ve el pedido en modulo Produccion
    │  - Marca "Iniciar produccion" cuando empieza a fabricar
    │  - Marca "Listo para recoger" cuando termina
    │  (estado: en_produccion → listo_entrega)
    │
    ▼
Repartidor ve el pedido en modulo Repartidor
    │  - Marca "Recoger" cuando recoge del taller
    │  - Marca "Entregado en almacen" cuando lo deja
    │  (estado: listo_entrega → en_transporte → en_almacen)
    │
    ▼
Almacenero registra la entrada fisica en Almacen
    │  Notifica al vendedor que el pedido esta listo
    │
    ▼
Vendedor/Admin contacta al cliente para coordinar entrega
    │  - Recoge en tienda (local)
    │  - Recoge en almacen
    │  - Delivery a domicilio
    │
    ▼
Vendedor/Admin cobra saldo restante (50%) y marca entregado
    │  (estado: en_almacen → entregado)
    │  Se genera comprobante (factura/boleta)
    ▼
FIN
```

## Estados del pedido

### Estado principal (`estado`)
```
registrado → en_produccion → listo_entrega → en_transporte → en_almacen → entregado
```

### Estado de personalizacion (paralelo, solo aplica antes de produccion)
```
sin_iniciar → en_diseno → en_revision → aprobado
```

Una vez que el pedido se deriva a produccion, el estado de personalizacion queda como `aprobado` y ya no cambia.

## Reglas por rol

### Vendedor / Administrador
- Crea el pedido con productos personalizados.
- Sube archivos de referencia por producto (fotos, bocetos, DXF, etc.).
- Revisa los disenos subidos por el disenador.
- Aprueba o rechaza el diseno (hablando con el cliente).
- Deriva el pedido a produccion.
- Coordina la entrega final con el cliente.
- Cobra el saldo y cierra el pedido.

### Disenador
- Ve SOLO el modulo Diseno en el sidebar.
- Toma pedidos con `estado_personalizacion = sin_iniciar`.
- Descarga/sube archivos de diseno (CDR, PDF, PNG, JPG, SVG, AI, EPS, PSD, WEBP).
- Cambia estado a `en_revision` cuando notifica al vendedor.
- No ve ni edita otros modulos (ventas, caja, almacen, etc.).

### Orfebre (Produccion)
- Ve SOLO el modulo Produccion en el sidebar.
- Ve pedidos con `estado = en_produccion` o `listo_entrega`.
- Marca "Iniciar produccion" cuando empieza a fabricar.
- Marca "Notificar repartidor" cuando el producto esta listo.
- Ve archivos de referencia y de diseno en modo solo lectura.

### Repartidor
- Ve SOLO el modulo Repartidor en el sidebar.
- Ve pedidos con `estado = listo_entrega` o `en_transporte`.
- Marca "Recoger" cuando retira del taller.
- Marca "Entregado en almacen" cuando lo deposita.
- No tiene acceso a diseno ni edicion de pedidos.

### Almacenero
- Ve modulo Almacen y Productos en el sidebar.
- Registra entrada fisica del producto terminado.
- Notifica al vendedor que el pedido esta disponible.
- Solo lectura en pedidos (no gestiona).

## Archivos en el flujo

| Tipo | Quien sube | Donde se almacena | Tabla |
|------|-----------|-------------------|-------|
| Archivos de referencia (por producto) | Vendedor/Admin al crear pedido | `storage/disenos_producto/` | `pedido_producto_archivos` |
| Archivos de diseno | Disenador via modulo Diseno | `storage/disenos_pedido/` | `pedido_diseno_archivos` |
| Archivos de orden de compra | Vendedor/Admin al crear pedido | `storage/ordenes_compra_pedido/` | `pedido_orden_archivos` |

## Permisos del sistema

| Permiso | admin | vendedor | disenador | orfebre | repartidor | almacenero |
|---------|-------|----------|-----------|---------|------------|------------|
| `dashboard.ver` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `pedidos.ver` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `pedidos.gestionar` | ✓ | ✓ | ✓ | ✓ | ✗ | ✗ |
| `clientes.ver` | ✓ | ✓ | ✓ | ✓ | ✓ | ✗ |
| `clientes.gestionar` | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| `productos.ver` | ✓ | ✓ | ✓ | ✓ | ✗ | ✓ |
| `productos.gestionar` | ✓ | ✗ | ✗ | ✗ | ✗ | ✓ |
| `ventas.ver` | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| `ventas.gestionar` | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| `caja.ver` | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| `caja.gestionar` | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| `reportes.ver` | ✓ | ✓ | ✓ | ✓ | ✗ | ✓ |
| `usuarios.ver` | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| `usuarios.gestionar` | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| `roles.ver` | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| `roles.gestionar` | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| `configuracion.ver` | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| `almacen.ver` | ✓ | ✗ | ✗ | ✗ | ✗ | ✓ |
| `almacen.gestionar` | ✓ | ✗ | ✗ | ✗ | ✗ | ✓ |
| `diseno.ver` | ✓ | ✗ | ✓ | ✗ | ✗ | ✗ |
| `diseno.gestionar` | ✓ | ✗ | ✓ | ✗ | ✗ | ✗ |
| `produccion.ver` | ✓ | ✗ | ✗ | ✓ | ✗ | ✗ |
| `produccion.gestionar` | ✓ | ✗ | ✗ | ✓ | ✗ | ✗ |
| `repartidor.ver` | ✓ | ✗ | ✗ | ✗ | ✓ | ✗ |
| `repartidor.gestionar` | ✓ | ✗ | ✗ | ✗ | ✓ | ✗ |
