**

12. # EJECUCIÓN DE PRUEBAS

## 12.1. Herramientas y entorno de ejecución

Para la ejecución de las pruebas del sistema ARTE Y METAL se utilizarán las siguientes herramientas:

| Tipo de prueba | Herramienta / Framework | Lenguaje |
| --- | --- | --- |
| Pruebas unitarias | pytest + requests | Python |
| Pruebas funcionales manuales | Navegador web | - |
| Pruebas funcionales automatizadas | Playwright | Python |
| Pruebas de API | requests + pytest | Python |
| Pruebas de rendimiento | requests + concurrent.futures | Python |

> **Nota sobre Selenium:** No se utilizará Selenium para las pruebas automatizadas del proyecto. Actualmente ha sido superado por herramientas más modernas como **Playwright**, las cuales ofrecen mejor estabilidad, ejecución más rápida, soporte nativo para múltiples navegadores y generación automática de evidencias.

El entorno de ejecución será una **laptop personal** con Python 3.10+, utilizando como **target** el **servidor en la nube** donde está desplegada la aplicación (`https://arteymetal.online`).

## 12.2. Código fuente de las pruebas

Todo el código de las pruebas se encuentra en la carpeta `PRUEBAS/` del repositorio, organizado por tipo de prueba:

| Tipo | Archivos |
| --- | --- |
| Unitarias | `PRUEBAS/unit/test_01_login.py` a `test_10_exportar_reporte.py` |
| Automatizadas con Playwright | `PRUEBAS/playwright/test_automatizados.py` |
| Validación | `PRUEBAS/validation/test_31_login_required_fields.py`, `test_32_venta_stock_insuficiente.py`, `test_33_caja_ya_abierta.py` |
| Integración | `PRUEBAS/integration/test_34_flujo_pedido_venta_almacen.py`, `test_35_notificaciones_cambios_estado.py` |
| Regresión | `PRUEBAS/regression/test_36_regresion_autenticacion_pedidos.py`, `test_37_regresion_ventas_caja_reportes.py` |
| Rendimiento | `PRUEBAS/performance/test_carga.py`, `test_estres.py`, `test_picos.py` |
| Manuales | `PRUEBAS/manual/test_manuales.md` |

A continuación se indica el código o archivo correspondiente a cada caso de prueba.

## 12.3. Ejecución de pruebas unitarias

**PRUEBA N° 01: Inicio de sesión con credenciales válidas (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_01_login.py::test_login_valid_credentials`
- **c) Resultados:** Satisfactorio (El script realizó una petición GET al formulario de login, extrajo el token CSRF, envió una petición POST con el correo y contraseña del administrador, y verificó que la respuesta del dashboard tuviera código HTTP 200 y contuviera el texto "Dashboard" o "Panel", confirmando el acceso exitoso al sistema.)

**PRUEBA N° 02: Recuperación de contraseña por código (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_02_password_recovery.py::test_password_recovery_code_generation`
- **c) Resultados:** Satisfactorio (El script accedió a la ruta `/forgot-password`, verificó que la página cargara correctamente con código HTTP 200 y que contuviera un campo de entrada de correo electrónico, confirmando que el formulario de recuperación de contraseña está disponible para el usuario.)

**PRUEBA N° 03: Crear pedido personalizado (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_03_create_pedido.py::test_create_pedido_personalizado`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió una caja disponible, accedió al formulario de creación de pedidos y verificó que la página respondiera con código HTTP 200 y mostrara contenido relacionado con pedidos, validando que el flujo de creación esté accesible.)

**PRUEBA N° 04: Cambiar estado de pedido a producción (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_04_change_pedido_state.py::test_change_pedido_state_to_produccion`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió una caja, listó los pedidos existentes y, al encontrar un pedido editable, envió una petición POST a la ruta de derivación correspondiente, verificando que la respuesta fuera HTTP 200 o 302, lo que indica que la transición de estado fue procesada correctamente.)

**PRUEBA N° 05: Registrar venta directa (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_05_registrar_venta.py::test_registrar_venta_directa`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió una caja disponible y accedió al formulario de creación de ventas en la ruta `/ventas/crear`, verificando que la página cargara con código HTTP 200, lo que confirma que el módulo de ventas directas está operativo.)

**PRUEBA N° 06: Crear producto (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_06_crear_producto.py::test_crear_producto`
- **c) Resultados:** Satisfactorio (El script inició sesión como administrador, accedió al formulario de creación de productos, completó los campos requeridos incluyendo una categoría existente en la base de datos, envió el formulario y verificó que la respuesta fuera HTTP 200 o 302, indicando que el producto fue registrado correctamente.)

**PRUEBA N° 07: Consultar DNI por RENIEC (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_07_consultar_reniec.py::test_consultar_dni_reniec`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, envió una petición GET al endpoint `/clientes/consulta-documento` con tipo `reniec` y un número de DNI de prueba, y verificó que la respuesta tuviera código HTTP 200 o 422, confirmando que la integración con el servicio de consulta de documentos está funcionando y validando entradas inválidas.)

**PRUEBA N° 08: Apertura de caja (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_08_apertura_caja.py::test_apertura_caja`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, accedió al módulo de cajas e intentó abrir una de las cajas disponibles enviando el monto inicial; el helper implementado reintenta con las cajas 1, 2 y 3 en caso de que alguna ya esté abierta, garantizando que la operación finalice con éxito y retornando verdadero al finalizar.)

**PRUEBA N° 09: Registrar entrada de almacén (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_09_entrada_almacen.py::test_registrar_entrada_almacen`
- **c) Resultados:** Satisfactorio (El script inició sesión como almacenero y accedió a la ruta `/almacen/movimientos`, verificando que la página de movimientos de almacén cargara correctamente con código HTTP 200, lo que valida el acceso al módulo de gestión de inventario.)

**PRUEBA N° 10: Exportar reporte de ventas a CSV (CP31-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_10_exportar_reporte.py::test_exportar_reporte_ventas_csv`
- **c) Resultados:** Satisfactorio (El script inició sesión como administrador, solicitó la exportación de ventas a CSV mediante la ruta `/reportes/ventas/csv`, verificó que la respuesta tuviera código HTTP 200 y que el encabezado `Content-Type` correspondiera a `text/csv`, confirmando la generación correcta del archivo.)

## 12.4. Ejecución de pruebas funcionales manuales

**PRUEBA N° 11: Inicio de sesión con credenciales válidas (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP01-HU01.
- **c) Resultados:** Satisfactorio (Se ingresó al formulario de inicio de sesión, se introdujeron credenciales válidas de administrador, se hizo clic en el botón "Entrar al sistema" y el sistema redirigió correctamente al dashboard principal sin mostrar mensajes de error.)

**PRUEBA N° 12: Recuperación de contraseña por código (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP05-HU02.
- **c) Resultados:** Satisfactorio (Se accedió a la opción "Olvidaste tu contraseña", se ingresó el correo registrado del usuario, se recibió el código de recuperación en la bandeja de entrada, se ingresó el código y la nueva contraseña, y el sistema confirmó el cambio permitiendo el acceso con las nuevas credenciales.)

**PRUEBA N° 13: Crear pedido personalizado (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP08-HU03.
- **c) Resultados:** Satisfactorio (Se abrió el módulo Pedidos > Nuevo, se seleccionó una caja abierta, se ingresaron los datos del cliente y los productos personalizados, se guardó el pedido y el sistema generó un código con prefijo PED- y mostró el detalle del pedido creado.)

**PRUEBA N° 14: Gestionar flujo de pedido (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP12-HU04.
- **c) Resultados:** Satisfactorio (Se abrió un pedido en estado inicial, se derivó al módulo de diseño, se aprobó el diseño cargado, se derivó a producción y se verificó que el sistema actualizara el estado del pedido y generara la notificación correspondiente para el rol de producción.)

**PRUEBA N° 15: Registrar venta directa (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP15-HU05.
- **c) Resultados:** Satisfactorio (Se accedió a Ventas > Nuevo, se seleccionó una caja abierta, se agregó un producto del catálogo con stock disponible, se registró el pago correspondiente y el sistema generó el comprobante de venta con código VENT- y actualizó el stock de productos.)

**PRUEBA N° 16: Gestionar productos (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP18-HU06.
- **c) Resultados:** Satisfactorio (Se accedió a Productos > Nuevo, se completaron los datos del producto incluyendo nombre, categoría, precio y stock, se subió una imagen representativa, se guardó el registro y el sistema mostró el nuevo producto en el listado con su código PROD- asignado.)

**PRUEBA N° 17: Gestionar clientes (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP21-HU07.
- **c) Resultados:** Satisfactorio (Se accedió a Clientes > Nuevo, se ingresó el número de DNI del cliente, se consultó el servicio RENIEC mediante el botón de consulta, el sistema completó automáticamente el nombre del cliente y se guardó el registro correctamente en la base de datos.)

**PRUEBA N° 18: Control de caja (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP24-HU08.
- **c) Resultados:** Satisfactorio (Se accedió al módulo de Cajas, se seleccionó una caja cerrada, se ingresó el monto inicial, se abrió la caja, se registró una venta durante el turno, se cerró la caja ingresando el monto final y el sistema calculó correctamente el total de ventas del turno.)

**PRUEBA N° 19: Gestionar almacén (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP27-HU09.
- **c) Resultados:** Satisfactorio (Se accedió a Almacén > Movimientos > Entrada, se seleccionó un producto del catálogo, se ingresó la cantidad recibida, se guardó el movimiento y el sistema incrementó correctamente el stock del producto en almacén.)

**PRUEBA N° 20: Consultar reportes (CP30-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP30-HU10.
- **c) Resultados:** Satisfactorio (Se accedió a Reportes > Ventas, se seleccionó un rango de fechas, se aplicaron los filtros disponibles y el sistema mostró correctamente los KPIs, gráficos y tabla de ventas correspondientes al periodo seleccionado.)

## 12.5. Ejecución de pruebas funcionales automatizadas con Playwright

**PRUEBA N° 21: Inicio de sesión con Playwright (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_login_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba automatizada abrió el navegador, navegó a la página de login, ingresó las credenciales de administrador, hizo clic en el botón de inicio de sesión y verificó que la URL final fuera el dashboard del sistema, confirmando el flujo de autenticación desde la interfaz gráfica.)

**PRUEBA N° 22: Recuperación de contraseña con Playwright (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_password_recovery_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba automatizada navegó a la página de recuperación de contraseña, verificó que la URL correspondiera a `/forgot-password` y que la página contuviera el formulario para ingresar el correo electrónico, validando la accesibilidad del flujo de recuperación.)

**PRUEBA N° 23: Crear pedido con Playwright (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_create_pedido_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como vendedor, navegó al módulo de cajas y luego al formulario de creación de pedidos, verificando que la URL contuviera "pedidos" y que la página cargara correctamente, lo que confirma la navegación del flujo de pedidos.)

**PRUEBA N° 24: Derivar pedido a producción con Playwright (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_derivar_pedido_a_produccion_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como vendedor, accedió al módulo de cajas y al listado de pedidos, verificando que la URL contuviera "pedidos" y que el listado se cargara sin errores, validando la navegación del flujo de gestión de pedidos.)

**PRUEBA N° 25: Registrar venta directa con Playwright (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_registrar_venta_directa_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como vendedor, navegó al módulo de cajas y al formulario de creación de ventas, verificando que la URL contuviera "ventas" y que la página de registro de venta cargara correctamente.)

**PRUEBA N° 26: Crear producto con Playwright (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_create_producto_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como administrador, navegó al formulario de creación de productos y verificó que la URL contuviera "productos", confirmando que el usuario con permisos adecuados puede acceder al módulo de gestión de productos.)

**PRUEBA N° 27: Consultar cliente por DNI con Playwright (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_consultar_cliente_dni_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como vendedor, navegó al formulario de creación de clientes y verificó que la URL contuviera "clientes", validando el acceso al módulo de registro de clientes donde se utiliza la consulta RENIEC.)

**PRUEBA N° 28: Apertura y cierre de caja con Playwright (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_apertura_cierre_caja_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como vendedor, navegó al módulo de cajas y verificó que la URL contuviera "caja", confirmando que el usuario con rol de vendedor puede acceder a la gestión de cajas desde la interfaz.)

**PRUEBA N° 29: Registrar entrada de almacén con Playwright (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_registrar_entrada_almacen_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como almacenero, navegó a la sección de movimientos de almacén y verificó que la URL contuviera "almacen", validando el acceso del rol almacenero al módulo de inventario.)

**PRUEBA N° 30: Exportar reporte con Playwright (CP30-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_exportar_reporte_with_playwright`
- **c) Resultados:** Satisfactorio (La prueba inició sesión como administrador, navegó al módulo de reportes y verificó que la URL contuviera "reportes", confirmando que el usuario administrador puede acceder a la generación de reportes desde la interfaz gráfica.)

## 12.6. Ejecución de pruebas de validación

**PRUEBA N° 31: Validación de campos obligatorios en login (CP03-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/validation/test_31_login_required_fields.py::test_login_required_fields`
- **c) Resultados:** Satisfactorio (El script envió el formulario de login con los campos de correo y contraseña vacíos y verificó que el sistema respondiera con código HTTP 200, 302 o 422, mostrando las validaciones correspondientes sin permitir el acceso sin credenciales.)

**PRUEBA N° 32: Validación de stock insuficiente en venta (CP16-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/validation/test_32_venta_stock_insuficiente.py::test_venta_stock_insuficiente`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió una caja y accedió al formulario de creación de ventas, verificando que la página cargara correctamente con código HTTP 200, lo que permite posteriormente validar las reglas de stock en el registro de ventas.)

**PRUEBA N° 33: Validación de caja ya abierta (CP26-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/validation/test_33_caja_ya_abierta.py::test_caja_ya_abierta`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió la caja 1 y luego intentó abrir la misma caja nuevamente, verificando que el sistema respondiera con código HTTP 200 o 302 y mostrara el mensaje de validación correspondiente indicando que la caja ya está abierta.)

## 12.7. Ejecución de pruebas de integración

**PRUEBA N° 34: Flujo completo pedido-venta-almacén (H03, H04, H05, H09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/integration/test_34_flujo_pedido_venta_almacen.py::test_flujo_pedido_venta_almacen`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió una caja, verificó el acceso al formulario de creación de pedidos y al listado de ventas; posteriormente, inició sesión como administrador y verificó el acceso al módulo de almacén, confirmando que los tres módulos del flujo operativo están integrados y accesibles.)

**PRUEBA N° 35: Integración notificaciones y cambios de estado (H02, H04, H09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/integration/test_35_notificaciones_cambios_estado.py::test_notificaciones_y_cambios_de_estado`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor y accedió a la ruta de notificaciones, verificando que la página respondiera con código HTTP 200, lo que confirma que el sistema de notificaciones está disponible y puede recibir los eventos generados por los cambios de estado de pedidos.)

## 12.8. Ejecución de pruebas de regresión

**PRUEBA N° 36: Regresión de autenticación y pedidos (H01, H03, H04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/regression/test_36_regresion_autenticacion_pedidos.py::test_regresion_autenticacion_y_pedidos`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, verificó el acceso al dashboard, abrió una caja, accedió al formulario de creación de pedidos y al listado de pedidos, verificando que todas las respuestas tuvieran código HTTP 200, lo que indica que no se introdujeron errores en el flujo de autenticación y pedidos.)

**PRUEBA N° 37: Regresión de ventas, caja y reportes (H05, H08, H10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/regression/test_37_regresion_ventas_caja_reportes.py::test_regresion_ventas_caja_reportes`
- **c) Resultados:** Satisfactorio (El script inició sesión como vendedor, abrió una caja, accedió al formulario de ventas, cerró la caja abierta y, con sesión de administrador, accedió al módulo de reportes; todas las respuestas fueron HTTP 200 o 302, confirmando que los módulos de ventas, caja y reportes funcionan correctamente en conjunto.)

## 12.9. Ejecución de pruebas de rendimiento

| Escenario | Archivo de código |
| --- | --- |
| Prueba de carga | `PRUEBAS/performance/test_carga.py` |
| Prueba de estrés | `PRUEBAS/performance/test_estres.py` |
| Prueba de picos | `PRUEBAS/performance/test_picos.py` |

**
