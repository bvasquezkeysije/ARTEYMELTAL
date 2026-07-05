# Sistema Arte y Metal - Guia de lectura

Esta boveda ya no se lee como un conjunto de archivos sueltos. Desde aqui se entra por capas para entender primero el negocio y luego la implementacion.

## Ruta recomendada para Opencode

1. [[Proyecto/Introducción|Contexto del proyecto]]
2. [[flujo-sistema-arte-y-metal|Flujo operativo del negocio]]
3. [[01-Negocio/README|Resumen de negocio]]
4. [[02-Backend/README|Mapa del backend]]
5. [[03-Frontend/README|Mapa del frontend]]
6. [[04-Reglas/README|Reglas funcionales y visuales]]
7. [[05-Requerimientos/README|Requerimientos y alcance]]

## Mapa rapido

### Negocio
- La empresa trabaja entre `Tienda`, `Centro de Produccion` y `Almacen`.
- El flujo principal es `venta directa` o `pedido personalizado`.
- Los roles operativos son `vendedor`, `disenador`, `orfebre`, `repartidor` y `almacenero`.

### Backend
- El sistema esta construido en Laravel.
- Los modulos principales son `clientes`, `productos`, `pedidos`, `ventas`, `usuarios`, `roles` y `reportes`.
- La ruta general del request es `Route -> Controller -> Model/Service -> View`.

### Frontend
- La UI usa Blade, Alpine.js y Tailwind.
- El layout maestro vive en `resources/views/layouts/app.blade.php`.
- La interfaz sigue una identidad dorado/negro con tablas, toolbar y modales consistentes.

### Regla de lectura
- Si necesitas entender el proceso del negocio: empieza por `flujo-sistema-arte-y-metal.md`.
- Si necesitas tocar logica del servidor: entra por `02-Backend/README.md`.
- Si necesitas tocar vistas o experiencia de uso: entra por `03-Frontend/README.md`.
- Si necesitas respetar estilos o patrones: revisa `04-Reglas/README.md`.

## Documentos fuente

- [[Sistema/4.- Logica del negocio|Logica del negocio detallada]]
- [[Sistema/2.- Sql|Base de datos detallada]]
- [[Sistema/3.- UI|UI detallada]]
- [[Sistema/1.- Reglas|Reglas detalladas]]
- [[Sistema/0.- Requerimientos|Requerimientos detallados]]
