# Regla 10 - Módulos visibles por cada rol

## Objetivo
Definir qué módulos del sistema puede ver y/o gestionar cada rol. Esta regla es la fuente de verdad para validar el menú lateral y los permisos de acceso.

---

## VENDEDOR

| Módulo | Acción | Permiso requerido |
|--------|--------|-------------------|
| Inicio (Dashboard) | Ver | dashboard.ver |
| Caja | Ver historial y detalle | caja.ver |
| Caja | Abrir y cerrar caja | caja.gestionar |
| Ventas | Ver ventas | ventas.ver |
| Ventas | Crear ventas, gestionar | ventas.gestionar |
| Pedidos | Ver pedidos | pedidos.ver |
| Pedidos | Crear pedidos | pedidos.gestionar (requiere caja abierta) |
| Productos | Ver catálogo | productos.ver |
| Clientes | Ver clientes | clientes.ver |
| Clientes | Crear/editar/eliminar clientes | clientes.gestionar |
| Reportes | Ver reportes | reportes.ver |
| Notificaciones | Ver notificaciones | (acceso público autenticado) |

### Restricciones del vendedor
- NO puede gestionar productos (solo lectura)
- NO puede acceder a almacén
- NO puede acceder a usuarios ni roles
- NO puede acceder a configuración
- NO puede gestionar pedidos (solo crear y ver)

---

## ADMINISTRADOR

Acceso total a todos los módulos.

---

## DISEÑADOR

| Módulo | Acción | Permiso requerido |
|--------|--------|-------------------|
| Inicio | Ver | dashboard.ver |
| Pedidos | Ver y gestionar (diseños) | pedidos.ver, pedidos.gestionar |
| Productos | Ver | productos.ver |
| Reportes | Ver | reportes.ver |

---

## ORFEBRE

| Módulo | Acción | Permiso requerido |
|--------|--------|-------------------|
| Inicio | Ver | dashboard.ver |
| Pedidos | Ver y gestionar (producción) | pedidos.ver, pedidos.gestionar |
| Productos | Ver | productos.ver |
| Reportes | Ver | reportes.ver |

---

## REPARTIDOR

| Módulo | Acción | Permiso requerido |
|--------|--------|-------------------|
| Inicio | Ver | dashboard.ver |
| Pedidos | Ver | pedidos.ver |
| Reportes | Ver | reportes.ver |

---

## ALMACENERO

| Módulo | Acción | Permiso requerido |
|--------|--------|-------------------|
| Inicio | Ver | dashboard.ver |
| Productos | Ver y gestionar | productos.ver, productos.gestionar |
| Almacén | Ver y gestionar | almacen.ver, almacen.gestionar |
| Pedidos | Ver | pedidos.ver |
| Reportes | Ver | reportes.ver |
