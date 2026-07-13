# Regla 12 - Módulo Diseños (subida de archivos por producto)

## Objetivo
Gestionar la subida, visualización y eliminación de archivos de diseño personalizado, **asociados a cada producto del pedido** (no al pedido completo).

## Base de datos

### Tabla `pedido_diseno_archivos`
| Columna | Tipo | FK | Notas |
|---------|------|----|-------|
| `id` | int8 PK | | |
| `pedido_id` | int8 | → `pedidos(id)` | Pedido al que pertenece |
| `pedido_producto_id` | int8 | → `pedido_productos(id)` | Producto específico del pedido |
| `archivo_path` | varchar(255) | | Ruta en `storage/disenos_pedido/` |
| `nombre_original` | varchar(255) | | Nombre del archivo subido |
| `mime_type` | varchar(100) | | Tipo MIME |
| `tamano_bytes` | int8 | | Tamaño en bytes |
| `created_at` | timestamp | | |

### Tabla `pedido_productos` (relación)
- Un pedido tiene muchos productos (`Pedido::productos` → HasMany PedidoProducto)
- Cada producto tiene sus propios archivos de diseño (`PedidoProducto::archivosDiseno` → HasMany PedidoDisenoArchivo)
- Cada producto tiene sus propios archivos de referencia (`PedidoProducto::archivos` → HasMany PedidoProductoArchivo)

## Controlador: `DisenoController`

### `index()` — Listado
- **Solo muestra pedidos** con `estado_personalizacion = en_diseno` o `en_revision`.
- Búsqueda por: código, nombre de cliente, nombre de producto (ilike).
- Filtro por estado de personalización: `en_diseno`, `en_revision`.
- Paginación: 10 registros.
- Eager loading: `cliente`, `productos.archivos`, `productos.archivosDiseno`.

### `update()` — Subir archivos
- **Requiere** `pedido_producto_id` (el producto al que pertenecen los archivos).
- Valida: `pedido_producto_id` existe, `archivos_diseno` es array con min 1 archivo.
- Formatos permitidos: `cdr, pdf, png, jpg, jpeg, svg, ai, eps, psd, webp`.
- Tamaño máximo: 10MB por archivo.
- Almacena en: `storage/disenos_pedido/` (disco `public`).
- **Notifica automáticamente**: cambia `estado_personalizacion` a `en_revision` al subir.
- Solo puede usar: `administrador` o `disenador`.

### `destroyArchivo()` — Eliminar archivo
- Elimina el archivo del disco y la BD.
- Solo puede usar: `administrador` o `disenador`.
- Redirige a `diseno.show` con mensaje de éxito.

## Flujo del diseñador

```
1. Diseñador entra a módulo Diseños (solo ve pedidos en_diseno o en_revision)
2. Hace clic en "Subir diseño" (ícono naranja)
3. Selecciona el producto del pedido (dropdown)
4. Selecciona archivos de diseño (múltiples)
5. Hace clic en "Subir archivos"
   → Se guardan los archivos en storage/disenos_pedido/
   → Se crea registro en pedido_diseno_archivos (con pedido_producto_id)
   → estado_personalizacion cambia a "en_revision"
   → Vendedor recibe notificación
6. Puede editar/eliminar archivos con el botón morado "Editar archivos"
```

## Conteo de archivos en tabla

```php
$totalArchivosDiseno = $pedido->productos->flatMap->archivosDiseno->count();
```

- Si hay archivos: badge ámbar con ícono de carpeta + cantidad.
- Si no hay: texto gris "Sin archivos".

## Rutas

| Método | Ruta | Nombre | Acción |
|--------|------|--------|--------|
| GET | `/diseno` | `diseno.index` | Listado con búsqueda/filtro |
| GET | `/diseno/{pedido}` | `diseno.show` | Detalle del pedido |
| PUT | `/diseno/{pedido}` | `diseno.update` | Subir archivos de diseño |
| DELETE | `/diseno/archivo/{archivo}` | `diseno.destroy_archivo` | Eliminar un archivo |

## Modal "Subir diseño"

- **Producto**: dropdown con todos los productos del pedido, mostrando conteo de archivos existentes.
- **Archivos**: input `multiple`, accept específico para formatos de diseño.
- **Hidden**: `estado_personalizacion = en_revision` (siempre notifica).
- **Botones**: "Cancelar" (gris) + "Subir archivos" (ámbar).

## Modal "Editar archivos"

- Lista agrupada por producto, cada uno en un card beige.
- Cada archivo muestra: nombre (enlace), tamaño en KB, botón eliminar (rojo).
- Botón "Cerrar" al final.
- No permite subir archivos nuevos (solo ver y eliminar).

## Archivos de referencia vs. archivos de diseño

| Tipo | Quién sube | Tabla | Storage |
|------|------------|-------|---------|
| Referencia | Vendedor al crear pedido | `pedido_producto_archivos` | `storage/disenos_producto/` |
| Diseño | Diseñador en módulo Diseños | `pedido_diseno_archivos` | `storage/disenos_pedido/` |
