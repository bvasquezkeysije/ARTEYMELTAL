# Backend - mapa funcional

Este bloque conecta la logica del negocio con la estructura real del proyecto Laravel `Sistema-ArteyMetal`.

## Stack

- Framework: Laravel
- Rutas: `routes/web.php`
- Controladores: `app/Http/Controllers`
- Modelos: `app/Models`
- Servicios: `app/Services`
- Base de datos: `database/migrations` y `database/seeders`

## Modulos del backend

| Modulo | Entrada principal | Responsabilidad |
|---|---|---|
| Dashboard | `DashboardController` | KPIs y resumen operativo |
| Clientes | `ClienteController`, `ClienteConsultaController`, `SunatController` | CRUD y consulta de documento |
| Productos | `ProductoController` | Catalogo, categorias, imagenes y stock |
| Pedidos | `PedidoController` | Registro, personalizacion, pagos y cierre |
| Ventas | `VentaController` | Venta directa y comprobantes |
| Reportes | `ReporteController` | KPIs y exportaciones |
| Usuarios y roles | `UsuarioController`, `RolController` | Acceso, permisos y administracion |

## Flujo tecnico

### Autorizacion
- Toda ruta protegida pasa por `auth`.
- Luego pasa por `activo`.
- Finalmente se valida `permiso:*`.

### Pedido personalizado
1. Se crea el pedido.
2. Se guardan snapshots del cliente.
3. Se actualiza la personalizacion por estados.
4. Se bloquea la produccion si no existe adelanto.
5. El pago final crea una venta de tipo `pedido`.
6. El comprobante se emite automaticamente.

### Venta directa
1. Se seleccionan productos.
2. Se valida stock.
3. Se crea la venta.
4. Se generan detalles.
5. Se descuenta stock.
6. Se emite comprobante.

## Entidades principales

- `Cliente`
- `Producto`
- `CategoriaProducto`
- `Pedido`
- `PedidoDisenoArchivo`
- `PedidoOrdenArchivo`
- `Venta`
- `VentaDetalle`
- `ComprobanteVenta`
- `User`
- `Rol`
- `Permiso`

## Reglas de implementacion que no se deben romper

- `PROD-XXXX` solo se genera al crear productos.
- `PED-XXXXXX` solo se genera al crear pedidos.
- `VEN-XXXXXX` solo se genera al crear ventas.
- La logica de cobro del pedido es fija: `50/50`.
- Una venta de tipo `pedido` no vuelve a descontar stock.
- Un rol sin permisos deja al usuario sin acceso efectivo.

## Documentos de apoyo

- [[../Sistema/4.- Logica del negocio|Logica del negocio detallada]]
- [[../Sistema/2.- Sql|Modelo de datos detallado]]
- [[../04-Reglas/README|Reglas que afectan implementacion y UI]]
