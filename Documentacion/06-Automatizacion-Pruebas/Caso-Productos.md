# Caso automatizable para exposicion - Productos

## Objetivo del caso

Validar el registro de productos y la consistencia del catalogo e inventario inicial.

## Requerimientos relacionados

| Tipo | ID | Descripcion |
|---|---|---|
| RF | `RF-10` | Gestion de productos |
| RF | `RF-11` | Gestion de categorias |
| RF | `RF-12` | Registro de imagenes de productos |
| RF | `RF-13` | Control de inventario |
| RFC | `RFC-05` | Gestion de productos, categorias, imagenes y control de inventario |
| HU | `HU07` | Gestion de productos |
| HU | `HU08` | Gestion de categorias |
| HU | `HU09` | Control de inventario y stock bajo |

## Casos a mostrar en diapositivas

### E2E
- registrar producto valido
- buscar producto creado en listado

### Unitarias
- generar codigo `PROD-0001`
- incrementar codigo desde el ultimo producto
- rechazar `stock_actual` negativo

### Rendimiento
- smoke de tiempos de respuesta sobre `/login` y `/productos/create`

## Archivos reales de apoyo

| Tipo | Archivo |
|---|---|
| E2E Python | `qa/python/tests/e2e/test_productos.py` |
| Unit | `tests/Unit/ProductoControllerLogicTest.php` |
| Feature | `tests/Feature/Productos/ProductoStoreTest.php` |
| Rendimiento | `qa/python/performance/perf_smoke.py` |
