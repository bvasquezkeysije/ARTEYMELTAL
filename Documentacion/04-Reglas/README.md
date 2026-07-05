# Reglas - lo que no se negocia

Este bloque resume las reglas que deben respetarse en negocio, backend y frontend.

## Reglas de negocio

- Pedido personalizado y venta directa son flujos distintos.
- No se produce sin adelanto.
- El cierre del pedido crea una venta y emite comprobante.
- El stock solo cae en ventas directas de catalogo.

## Reglas de backend

- El acceso es `auth + activo + permiso`.
- Los codigos correlativos no se regeneran en edicion.
- Los borrados actuales son fisicos, no soft delete.
- Las transacciones protegen operaciones criticas.

## Reglas de frontend

- La identidad visual es dorado/negro.
- Los toolbars usan `Buscar -> Filtrar -> acciones`.
- Las tablas deben ocupar el espacio sin dejar huecos inutiles.
- Los iconos vienen de `public/icons`.
- Los filtros se reflejan en la URL.

## Regla de documentacion

Si una regla afecta implementacion y experiencia, debe quedar en dos sitios:
- documento detallado del modulo
- resumen navegable de esta boveda

## Documento fuente

- [[../Sistema/1.- Reglas|Reglas detalladas]]
