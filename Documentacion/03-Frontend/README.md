# Frontend - mapa de interfaz

Este bloque explica como esta organizada la experiencia de usuario y como debe evolucionar sin romper la consistencia visual.

## Stack de interfaz

- Blade para vistas
- Alpine.js para interactividad
- Tailwind CSS para estilos
- Vite para assets
- Chart.js para graficos del dashboard

## Estructura base

| Capa | Ubicacion |
|---|---|
| Layout principal | `resources/views/layouts/app.blade.php` |
| Componentes Blade | `resources/views/components` |
| Modulos de vistas | `resources/views/*` |
| Paginacion | `resources/views/vendor/pagination/gold.blade.php` |
| CSS | `resources/css/app.css` |
| JS | `resources/js/app.js` |
| Iconos | `public/icons` |

## Patron de pantalla

Cada modulo sigue esta forma:
1. `x-app-layout`
2. Header con titulo
3. Toolbar con busqueda y filtros
4. Tabla o vista principal
5. Modales o formularios segun el caso
6. Paginacion

## Modulos visibles en UI

- `dashboard`
- `ventas`
- `pedidos`
- `clientes`
- `productos`
- `usuarios`
- `roles`
- `reportes`
- `profile`

## Reglas de experiencia

- El sidebar es condicional por permisos.
- El toolbar usa botones iconicos consistentes.
- Los filtros viven en query string.
- Las tablas siguen la paleta dorado/negro, no gris neutro.
- Los modales pequenos se resuelven inline con Alpine.js.

## Cuando tocar frontend

- Si cambias permisos, revisa sidebar y vistas condicionadas.
- Si agregas filtros, deben persistir en URL.
- Si agregas una tabla nueva, debe respetar toolbar, panel de filtros y paginacion.
- Si agregas una accion pequena, prioriza modal antes que pantalla separada.

## Documentos de apoyo

- [[../Sistema/3.- UI|UI detallada]]
- [[../Sistema/1.- Reglas|Reglas visuales y de componentes]]
