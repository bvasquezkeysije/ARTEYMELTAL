# Arte y Metal Chiclayo E.I.R.L. - Sistema ERP

## Roles del Sistema

| Rol | Slug | Descripción |
|---|---|---|
| Administrador | administrador | Acceso total al sistema |
| Vendedor | vendedor | Gestión comercial, clientes, ventas y cobro de pedidos |
| Diseñador | disenador | Crea y sube diseños para pedidos personalizados |
| Orfebre | orfebre | Fabrica las piezas según diseños aprobados |
| Repartidor | repartidor | Recoge productos terminados y los traslada al almacén |
| Almacenero | almacenero | Control de catálogo, stock, entradas y salidas de almacén |

## Flujo Operativo Completo

### Pedidos Personalizados

```
registrado → en_produccion → listo_entrega → en_transporte → en_almacen → entregado
```

#### Estado de personalización (paralelo)

```
sin_iniciar → en_diseno → en_revision → aprobado → en_produccion → listo_entrega → entregado
```

#### Estado de pago (50/50)

```
pendiente_adelanto → adelanto_pagado → pagado_completo
```

### Responsabilidades por etapa

1. **Vendedor/Administrador** — Registra el pedido, asigna monto y cobra adelanto obligatorio (50%) antes de producción.
2. **Diseñador** — Toma pedidos en `sin_iniciar`, sube diseños (CorelDRAW, PDF, PNG, SVG, AI, EPS, PSD), avanza a `en_diseno` → `en_revision`.
3. **Administrador/Vendedor** — Aprueba el diseño (`aprobado`).
4. **Orfebre** — Ve diseños aprobados, fabrica la pieza, marca `en_produccion` → `listo_entrega`.
5. **Repartidor** — Ve pedidos `listo_entrega`, recoge del centro de producción, marca `en_transporte`.
6. **Almacenero** — Ve pedidos `en_transporte`, registra entrada física en almacén, marca `en_almacen`.
7. **Vendedor/Administrador** — Cobra el saldo restante (50%), marca `entregado`. Se genera comprobante (factura/boleta) y se registra en Ventas.

### Modalidades de entrega final

- **Local** — El cliente recoge en tienda.
- **Delivery** — Se envía a domicilio del cliente.
- **Agencia** — Se envía por agencia externa (clientes lejanos).

### Reglas de negocio

- **Stock**: Solo se descuenta en ventas directas (productos de catálogo). Los pedidos personalizados no afectan stock.
- **Pago 50/50**: El adelanto es obligatorio antes de iniciar producción. El saldo se cobra al entregar.
- **Middleware**: `auth` → `activo` → `permiso:*` (tres capas de seguridad).
- **Trazabilidad**: Cada movimiento de almacén queda registrado en `movimientos_almacen`.

## Permisos por Rol

| Permiso | admin | vendedor | disenador | orfebre | repartidor | almacenero |
|---|---|---|---|---|---|---|---|
| dashboard.ver | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.ver | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| pedidos.gestionar | ✓ | ✗ | ✓ | ✓ | ✗ | ✗ |
| clientes.ver | ✓ | ✓ | ✓ | ✓ | ✓ | ✗ |
| clientes.gestionar | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| productos.ver | ✓ | ✓ | ✓ | ✓ | ✗ | ✓ |
| productos.gestionar | ✓ | ✗ | ✗ | ✗ | ✗ | ✓ |
| ventas.ver | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| ventas.gestionar | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| caja.ver | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| caja.gestionar | ✓ | ✓ | ✗ | ✗ | ✗ | ✗ |
| reportes.ver | ✓ | ✓ | ✓ | ✓ | ✗ | ✓ |
| usuarios.ver | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| usuarios.gestionar | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| roles.ver | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| roles.gestionar | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| configuracion.ver | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| almacen.ver | ✓ | ✗ | ✗ | ✗ | ✗ | ✓ |
| almacen.gestionar | ✓ | ✗ | ✗ | ✗ | ✗ | ✓ |

## Usuarios por Defecto

| Usuario | Contraseña | Rol |
|---|---|---|
| bvasquezkeysije | 76636255 | administrador |
| pfernandezadeli | 77684878 | administrador |
| ventas | ventas123 | vendedor |
| disenador | disenador123 | disenador |
| produccion | produccion123 | orfebre |
| repartidor | repartidor123 | repartidor |
| almacen | almacen123 | almacenero |

## Estructura del Proyecto

- `app/Http/Controllers/PedidoController.php` — Lógica de pedidos (CRUD, personalización, pagos, transporte).
- `app/Http/Controllers/AlmacenController.php` — Módulo de almacén (dashboard, entradas/salidas, inventario).
- `app/Http/Controllers/VentaController.php` — Ventas directas y comprobantes.
- `database/seeders/SecuritySeeder.php` — Roles, permisos y usuarios base.
- `routes/web.php` — Definición de rutas con middleware por permiso.
- `resources/views/pedidos/` — Vistas del módulo pedidos.
- `resources/views/almacen/` — Vistas del módulo almacén.

## Base de Datos (PostgreSQL)

### `pedidos`
| Columna | Tipo | FK | Notas |
|---------|------|----|-------|
| `id` | int8 PK | | |
| `codigo` | varchar(30) | | |
| `nombre_cliente` | varchar(255) | | |
| `telefono_cliente` | varchar(20) | | NULL |
| `tipo_producto` | varchar(255) | | |
| `detalle_trabajo` | text | | NULL |
| `cantidad` | int4 | | default 1 |
| `estado` | varchar(30) | | `registrado` → `en_produccion` → `listo_entrega` → `en_transporte` → `en_almacen` → `entregado` |
| `estado_personalizacion` | varchar(40) | | `sin_iniciar` → `en_diseno` → `en_revision` → `aprobado` → `en_produccion` → `listo_entrega` → `entregado` |
| `estado_pago` | varchar(30) | | `pendiente_adelanto` → `adelanto_pagado` → `pagado_completo` |
| `modalidad_pago` | varchar(20) | | `dos_pagos` |
| `monto_total` | numeric | | NULL |
| `monto_adelanto` | numeric | | NULL |
| `monto_saldo` | numeric | | NULL |
| `fecha_entrega_compromiso` | date | | NULL |
| `tipo_entrega` | varchar(20) | | `local` / `delivery` |
| `direccion_entrega` | varchar(255) | | NULL |
| `distrito_entrega` | varchar(120) | | NULL |
| `referencia_entrega` | varchar(255) | | NULL |
| `nombre_recibe` | varchar(120) | | NULL |
| `telefono_recibe` | varchar(20) | | NULL |
| `costo_delivery` | numeric | | NULL |
| `documento_cliente` | varchar(25) | | NULL |
| `correo_cliente` | varchar(255) | | NULL |
| `observaciones` | text | | NULL |
| `archivo_diseno_path` | varchar(255) | | NULL |
| `fecha_inicio_diseno` | date | | NULL |
| `fecha_aprobacion_diseno` | date | | NULL |
| `observaciones_personalizacion` | text | | NULL |
| `medio_pago_adelanto` / `medio_pago_saldo` | varchar(30) | | NULL |
| `referencia_pago_adelanto` / `referencia_pago_saldo` | varchar(120) | | NULL |
| `voucher_pago_adelanto_path` / `voucher_pago_saldo_path` | varchar(255) | | NULL |
| `estado_pago_adelanto` / `estado_pago_saldo` | varchar(30) | | `pendiente` / `pagado` |
| `usuario_id` | int8 | → `users(id)` | |
| `cliente_id` | int8 | → `clientes(id)` | NULL |
| `created_at` / `updated_at` | timestamp | | |

### `productos`
| Columna | Tipo | Notas |
|---------|------|-------|
| `id` | int8 PK | |
| `codigo` | varchar(30) | |
| `nombre` | varchar(255) | |
| `categoria` | varchar(40) | |
| `descripcion` | text | NULL |
| `precio_referencia` | numeric | NULL |
| `stock_actual` | int4 | Sincronizado automáticamente = `stock_tienda + stock_almacen` |
| `stock_tienda` | int4 | Stock disponible en tienda física |
| `stock_almacen` | int4 | Stock disponible en almacén general |
| `activo` | bool | default true |

### `ventas`
| Columna | Tipo | FK | Notas |
|---------|------|----|-------|
| `id` | int8 PK | | |
| `codigo` | varchar(30) | | |
| `tipo_venta` | varchar(30) | | `stock` (directa) o `pedido` (cierre) |
| `pedido_id` | int8 | → `pedidos(id)` | NULL |
| `cliente_nombre` | varchar(255) | | NULL |
| `fecha_venta` | date | | |
| `monto_total` | numeric | | |
| `monto_cobrado` | numeric | | |
| `estado_pago` | varchar(30) | | `pagado_completo` |
| `observaciones` | text | | NULL |
| `usuario_id` | int8 | → `users(id)` | |
| `caja_apertura_id` | int8 | → `caja_aperturas(id)` | NULL, se asigna automáticamente al registrar venta con caja abierta |

### `venta_detalles`
| Columna | Tipo | FK | Notas |
|---------|------|----|-------|
| `venta_id` | int8 | → `ventas(id)` | |
| `producto_id` | int8 | → `productos(id)` | NULL |
| `producto_nombre` | varchar(255) | | |
| `cantidad` | int4 | | |
| `precio_unitario` | numeric | | |
| `subtotal` | numeric | | |

### `movimientos_almacen`
| Columna | Tipo | FK | Notas |
|---------|------|----|-------|
| `producto_id` | int8 | → `productos(id)` | |
| `tipo` | varchar(20) | | `entrada` / `salida` |
| `cantidad` | int4 | | |
| `stock_resultante` | int4 | | |
| `concepto` | varchar(255) | | NULL |
| `pedido_id` | int8 | → `pedidos(id)` | NULL |
| `usuario_id` | int8 | → `users(id)` | |

### `clientes`
| Columna | Tipo | Notas |
|---------|------|-------|
| `nombre_completo` | varchar(255) | |
| `telefono` | varchar(20) | NULL |
| `correo` | varchar(255) | NULL |
| `documento` | varchar(25) | NULL |
| `direccion` | text | NULL |
| `observaciones` | text | NULL |

### `users` + `roles` + `permisos`
**users**: `id`, `name`, `email`, `password`, `rol_id` → `roles(id)`, `activo`, `trabajador_id` → `trabajadores(id)`  
**roles**: `id`, `nombre` (administrador, vendedor, disenador, orfebre, repartidor, almacenero), `descripcion`, `activo`  
**permisos**: `id`, `nombre`, `slug`  
**permiso_rol**: `rol_id` → `roles(id)`, `permiso_id` → `permisos(id)` (puente N:N)

### Otras tablas
- `categorias_producto` — Catálogo de categorías (slug, nombre)
- `comprobantes_venta` — Boletas/facturas electrónicas (venta_id, tipo, serie, correlativo, codigo)
- `cobros` — Pagos recibidos (venta_id, tipo_cobro, monto, fecha)
- `pedido_diseno_archivos` — Archivos de diseño subidos (pedido_id, archivo_path)
- `pedido_orden_archivos` — Archivos de orden de producción (pedido_id, archivo_path)
- `producto_imagenes` — Imágenes de productos catálogo (producto_id, archivo_path)
- `trabajadores` — Datos de planilla (dni, nombres, cargo, remuneración, etc.)
- `tipos_pago` — Medios de pago (nombre, descripcion)
- `cache`, `sessions`, `jobs` — Infraestructura Laravel

## Módulo Caja

Control de apertura y cierre de caja por turno de usuario.

### Flujo
1. El usuario **abre caja** al iniciar su turno (registra monto inicial).
2. Al entrar a **Ventas**, debe **seleccionar una caja** (si tiene varias abiertas) o se auto-selecciona si solo tiene una.
3. Sin caja abierta, **no se puede acceder a ventas** (VentaController redirige a cajas.index).
4. Las ventas se filtran por la caja seleccionada (solo se ven las de esa caja).
5. La caja seleccionada se guarda en `session('caja_apertura_id')`.
6. Botón "Cambiar caja" (ámbar, icono SVG switch) en ventas.index para cambiar/limpiar selección.
7. Al cerrar una caja, si es la que está en sesión, se limpia automáticamente.
8. El usuario **cierra caja** al finalizar su turno (ingresa monto final, se calcula total ventas).

### Vista `ventas.seleccionar_caja`
- Muestra tarjetas con cada caja abierta del usuario (ID, fecha, monto inicial)
- Al hacer clic, redirige a `ventas.seleccionar_caja/{caja}` → guarda en sesión → redirect a ventas.index

### Tabla `caja_aperturas`
| Columna | Tipo | Notas |
|---------|------|-------|
| `usuario_id` | FK → users | Quien abrió/cierra la caja |
| `fecha_apertura` | timestamp | Inicio del turno |
| `monto_inicial` | decimal(12,2) | Efectivo al abrir |
| `fecha_cierre` | timestamp | NULL hasta cerrar |
| `monto_final` | decimal(12,2) | Efectivo al cerrar |
| `total_ventas` | decimal(12,2) | Suma ventas del turno |
| `estado` | varchar(20) | `abierta` / `cerrada` |
| `observaciones` | text | NULL |

### Permisos
- `caja.ver` — Ver historial y detalle de caja
- `caja.gestionar` — Abrir y cerrar caja

Asignado a: **administrador**, **vendedor**.

## Convenciones de Estilo: Botones con Icono

### Clases CSS (definidas en `<style>` de cada vista que las usa)
- **`btn-icon`** — Botón cuadrado 40x40px (`2.5rem`), `border-radius: 0.75rem`, centrado, color blanco.
- **`btn-icon-sm`** — Botón cuadrado 32x32px (`2rem`), `border-radius: 0.5rem`, centrado, color blanco.

### Colores de fondo por acción
| Acción | Clase | Uso |
|--------|-------|-----|
| Buscar | `bg-blue-600 hover:bg-blue-700` | Lupa en ventas, productos, pedidos, clientes, usuarios |
| Nuevo/Crear | `background-color:#09090f` (negro) | Nuevo producto, venta, pedido, cliente, usuario |
| Filtrar | `bg-sky-500 hover:bg-sky-600` | Botón filtros en índices |
| Editar | `bg-amber-400 hover:bg-amber-500` | Botón editar registro |
| Eliminar/Desactivar | `bg-red-600 hover:bg-red-700` | Botón eliminar, desactivar, cerrar modal |
| Ver detalle | `background-color:#0891B2` (cyan) | Botón ver detalle |
| Gestión categorías | `bg-[#111] hover:bg-[#262626]` | Crear categoría en modal productos |
| Consumidor Final | `background-color:#b9943d` (dorado) | Botón en ventas create |

### Íconos
Todos los íconos están en `public/icons/` como archivos `.ico` o `.png`:
- `buscar.ico` — Lupa (Buscar)
- `nuevo.ico` — Símbolo + (Nuevo/Crear)
- `editar.ico` — Lápiz (Editar)
- `eliminar.ico` — Tacho (Eliminar)
- `eliminar-desactivar.ico` — Tacho (Eliminar/Desactivar/Inactivar)
- `filtros.ico` — Embudo (Filtrar)
- `ver-detalle.ico` — Ojo (Ver detalle)
- `imprimir.ico` — Impresora (Imprimir)
- `cerrar.ico` — X (Cerrar modal)
- `roles.ico` / `roles.svg` — Roles
- `gestionar-categorias.png` — Categorías
- `Editar-Blanco.ico` / `Eliminar-Blanco.ico` — Versiones blancas para fondos oscuros
- `Alerta-Blanco.png` — Triángulo alerta blanco
- `Alerta-Rojo.png` — Triángulo alerta rojo
- `Ventas-Blanco.png` — Icono ventas blanco

## Alertas / Modales de Confirmación
- Tarjeta blanca con `w-full max-w-md`, `rounded-2xl`, `px-16 pt-12 pb-12`
- Padding superior e inferior iguales (`pt-12` / `pb-12`)
- Padding lateral generoso (`px-16`) para que el texto no toque los bordes
- Icono de alerta con círculo `h-16 w-16` e imagen `h-8 w-8` centrada, `mb-1`
- Título `text-lg font-semibold text-gray-900`
- Descripción `text-sm text-gray-500` con `mt-2`
- Botón con `mt-6`, `bg-[#111]`, `rounded-xl`, `py-3`, `px-12`

### Reglas
- Los botones **nunca llevan texto**, solo el ícono con `title="..."` para tooltip.
- Usar `pointer-events-none` en la etiqueta `<img>` del ícono.
- Los iconos sobre fondos oscuros usan `brightness-0 invert` (ej: buscar.ico sobre fondo azul, tacho sobre fondo rojo).
- **Todos los botones en una misma fila deben tener el mismo alto.** No mezclar `btn-icon` (40px) con botones de altura diferente (ej: `h-[46px]`). Si hay un botón con texto en la fila, los botones de icono deben usar la misma altura manualmente (ej: `h-[46px] w-[46px]`) en vez de `btn-icon`.
- **Ícono `ver-detalle.ico`** es el estándar para acciones de "Ver detalle" / "Ver fotos" / "Ver información" en todos los módulos. No usar SVGs inline ni otros iconos para esta acción.
