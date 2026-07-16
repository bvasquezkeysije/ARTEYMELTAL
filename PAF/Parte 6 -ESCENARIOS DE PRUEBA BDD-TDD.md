**

6. # ESCENARIOS DE PRUEBA (BDD/TDD)

## Escenario de prueba N° 01 - Inicio de sesión exitoso

| TDD | BDD |
| --- | --- |
| Se espera que la función: `LoginRequest::authenticate`<br>Devuelva: `true` y registre la sesión del usuario activo.<br>Cuando le envío: `login = "bvasquezkeysije@gmail.com"` y `password = "[contraseña válida]"`. | Dado que: el usuario existe en el sistema, tiene estado activo y posee credenciales válidas.<br>Cuando: ingresa su correo o usuario y contraseña correctos y envía el formulario de inicio de sesión.<br>Entonces: el sistema autentica al usuario y lo redirige al panel principal correspondiente a su rol. |

## Escenario de prueba N° 02 - Recuperación de contraseña por código

| TDD | BDD |
| --- | --- |
| Se espera que la función: `PasswordResetLinkController::store`<br>Devuelva: redirección con mensaje de éxito y un código de 6 dígitos almacenado en `password_reset_codes`.<br>Cuando le envío: `email = "bvasquezkeysije@gmail.com"`. | Dado que: el usuario está registrado y activo en el sistema.<br>Cuando: solicita un código de recuperación de contraseña ingresando su correo.<br>Entonces: el sistema genera un código de 6 dígitos, lo envía al correo y permite restablecer la contraseña. |

## Escenario de prueba N° 03 - Crear pedido personalizado

| TDD | BDD |
| --- | --- |
| Se espera que la función: `PedidoController::store`<br>Devuelva: un objeto `Pedido` con código `PED-XXXXXX`, monto total calculado y venta de adelanto generada.<br>Cuando le envío: datos del cliente, productos, tipo de pago y caja seleccionada. | Dado que: el vendedor tiene una caja abierta y el cliente tiene datos válidos.<br>Cuando: completa el formulario de pedido con productos personalizados y confirma.<br>Entonces: el sistema crea el pedido, calcula totales, registra el adelanto y emite el comprobante correspondiente. |

## Escenario de prueba N° 04 - Gestionar flujo de pedido

| TDD | BDD |
| --- | --- |
| Se espera que la función: `PedidoController::derivar` y métodos de producción/reparto/almacén<br>Devuelva: el pedido con el estado actualizado correctamente y notificaciones generadas.<br>Cuando le envío: el pedido en un estado válido y el rol correspondiente. | Dado que: un pedido se encuentra en un estado válido para el siguiente paso del flujo.<br>Cuando: el actor autorizado (vendedor, diseñador, orfebre, repartidor o almacenero) ejecuta la transición correspondiente.<br>Entonces: el sistema actualiza el estado, genera la notificación al siguiente responsable y registra el movimiento. |

## Escenario de prueba N° 05 - Registrar venta directa

| TDD | BDD |
| --- | --- |
| Se espera que la función: `VentaController::store`<br>Devuelva: una venta con comprobante generado y stock de tienda descontado.<br>Cuando le envío: productos con stock en tienda, cliente, medio de pago y caja abierta. | Dado que: el vendedor tiene caja abierta y los productos tienen stock en tienda.<br>Cuando: registra una venta directa con el cliente y el pago.<br>Entonces: el sistema descuenta el stock, genera el comprobante y permite descargar el PDF. |

## Escenario de prueba N° 06 - Gestionar productos

| TDD | BDD |
| --- | --- |
| Se espera que la función: `ProductoController::store`<br>Devuelva: un producto con código `PROD-XXXX` generado automáticamente y stock inicial.<br>Cuando le envío: nombre, categoría, descripción, precio de referencia y stock. | Dado que: el usuario tiene permisos de gestión de productos.<br>Cuando: registra un nuevo producto con sus datos e imágenes.<br>Entonces: el sistema guarda el producto, genera su código, almacena las imágenes y lo lista en el catálogo. |

## Escenario de prueba N° 07 - Gestionar clientes

| TDD | BDD |
| --- | --- |
| Se espera que la función: `ClienteConsultaController::consultarPorDocumento`<br>Devuelva: los datos del cliente local o de la consulta RENIEC/SUNAT.<br>Cuando le envío: `numero = "46027897"` (DNI) o `numero = "20601030013"` (RUC). | Dado que: el vendedor o administrador ingresa un documento de cliente.<br>Cuando: el sistema consulta la base de datos local o la API de Decolecta.<br>Entonces: retorna el nombre, dirección y otros datos del cliente, o permite el ingreso manual si falla la consulta. |

## Escenario de prueba N° 08 - Control de caja

| TDD | BDD |
| --- | --- |
| Se espera que la función: `CajaController::abrir` y `CajaController::cerrar`<br>Devuelva: la apertura o cierre de caja registrada con montos y totales calculados.<br>Cuando le envío: caja_id, monto_inicial o monto_final y observaciones. | Dado que: el usuario tiene permisos de caja y la caja está cerrada.<br>Cuando: abre la caja con un monto inicial y posteriormente la cierra con el monto final.<br>Entonces: el sistema registra la apertura, permite operar ventas/pedidos, y al cierre calcula el total de ventas del turno. |

## Escenario de prueba N° 09 - Gestionar almacén

| TDD | BDD |
| --- | --- |
| Se espera que la función: `AlmacenController::movimiento` y `AlmacenController::recibirPedido`<br>Devuelva: movimiento de stock registrado y pedido actualizado a estado `listo_recoger`.<br>Cuando le envío: tipo de movimiento, producto, cantidad y pedido en transporte. | Dado que: el almacenero tiene permisos de almacén y existe un pedido en transporte.<br>Cuando: registra la recepción del pedido en almacén.<br>Entonces: el sistema actualiza el stock, crea movimientos de entrada y cambia el estado del pedido a listo para recoger. |

## Escenario de prueba N° 10 - Consultar reportes

| TDD | BDD |
| --- | --- |
| Se espera que la función: `ReporteController::ventas`, `ReporteController::pedidos` y métodos de exportación<br>Devuelva: datos filtrados, gráficos y archivos CSV/Excel descargables.<br>Cuando le envío: fechas de inicio/fin, tipo y formato de exportación. | Dado que: el usuario tiene permisos de reportes y existen datos de ventas o pedidos en el período.<br>Cuando: aplica filtros y solicita visualizar o exportar el reporte.<br>Entonces: el sistema muestra los KPIs, gráficos y genera el archivo CSV o Excel correctamente. |

**
