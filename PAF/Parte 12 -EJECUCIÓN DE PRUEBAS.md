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

> **Nota sobre Selenium:** No se utilizará Selenium para las pruebas automatizadas del proyecto. Actualmente ha sido superado por herramientas más modernas como **Playwright**, las cuales ofrecen mejor estabilidad, ejecución más rápida, soporte nativo para múltiples navegadores y generación automática de evidencias.

El entorno de ejecución será una **laptop personal** con Python 3.10+, utilizando como **target** el **servidor en la nube** donde está desplegada la aplicación (`https://arteymetal.online`).

## 12.2. Ejecución de pruebas unitarias

**PRUEBA N° 01: Inicio de sesión con credenciales válidas (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST a /login con correo y contraseña válidos; verificar redirección al dashboard.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 02: Recuperación de contraseña por código (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar correo registrado a /forgot-password; verificar que se genere código en password_reset_codes.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 03: Crear pedido personalizado (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST a /pedidos con cliente, productos y caja abierta; verificar código PED- generado.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 04: Cambiar estado de pedido a producción (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST para derivar pedido aprobado en diseño a producción; verificar estado en_produccion.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 05: Registrar venta directa (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST a /ventas con producto en stock y caja abierta; verificar descuento de stock_tienda.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 06: Crear producto (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST a /productos con nombre, categoría y stock; verificar código PROD- generado.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 07: Consultar DNI por RENIEC (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición GET a /consulta-documento con número de DNI; verificar respuesta con nombre completo.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 08: Apertura de caja (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST a /cajas/{id}/abrir con monto inicial; verificar estado abierto.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 09: Registrar entrada de almacén (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición POST a /almacen/movimiento con tipo entrada, producto y cantidad; verificar stock incrementado.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 10: Exportar reporte de ventas a CSV (CP31-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** Enviar petición GET a /reportes/ventas/exportar/csv; verificar descarga de archivo CSV válido.
- **c) Resultados:** Satisfactorio

## 12.3. Ejecución de pruebas funcionales manuales

**PRUEBA N° 11: Inicio de sesión con credenciales válidas (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Ingresar al login. 2) Escribir correo y contraseña válidos. 3) Hacer clic en Iniciar sesión. 4) Verificar redirección al dashboard.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 12: Recuperación de contraseña por código (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Ingresar a Olvidaste tu contraseña. 2) Escribir correo registrado. 3) Revisar bandeja de entrada. 4) Ingresar código y nueva contraseña.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 13: Crear pedido personalizado (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Pedidos > Nuevo. 2) Seleccionar caja abierta. 3) Ingresar cliente y productos. 4) Guardar y verificar código PED-.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 14: Gestionar flujo de pedido (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir pedido en estado sin_iniciar. 2) Derivar a diseño. 3) Aprobar diseño. 4) Derivar a producción. 5) Verificar notificaciones.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 15: Registrar venta directa (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Ventas > Nuevo. 2) Seleccionar caja abierta. 3) Agregar producto con stock. 4) Registrar pago. 5) Generar comprobante.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 16: Gestionar productos (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Productos > Nuevo. 2) Completar datos. 3) Subir imagen. 4) Guardar y buscar el producto.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 17: Gestionar clientes (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Clientes > Nuevo. 2) Ingresar DNI. 3) Consultar RENIEC. 4) Guardar cliente.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 18: Control de caja (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Cajas. 2) Seleccionar caja cerrada. 3) Ingresar monto inicial. 4) Abrir caja. 5) Registrar venta. 6) Cerrar caja.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 19: Gestionar almacén (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Almacén > Movimientos > Entrada. 2) Seleccionar producto. 3) Ingresar cantidad. 4) Guardar y verificar stock.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 20: Consultar reportes (CP30-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir Reportes > Ventas. 2) Seleccionar fechas. 3) Aplicar filtros. 4) Verificar KPIs y gráficos.
- **c) Resultados:** Satisfactorio

## 12.4. Ejecución de pruebas funcionales automatizadas con Playwright

**PRUEBA N° 21: Inicio de sesión con Playwright (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Ejecutar script de Playwright. 2) Navegar a /login. 3) Ingresar credenciales. 4) Verificar redirección al dashboard.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 22: Recuperación de contraseña con Playwright (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Ejecutar script. 2) Solicitar código. 3) Leer correo. 4) Ingresar código y nueva contraseña.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 23: Crear pedido con Playwright (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como vendedor. 2) Abrir Pedidos > Nuevo. 3) Completar formulario. 4) Guardar y verificar código generado.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 24: Derivar pedido a producción con Playwright (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como vendedor. 2) Abrir pedido en diseño aprobado. 3) Derivar a producción. 4) Verificar estado y notificación.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 25: Registrar venta directa con Playwright (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como vendedor. 2) Abrir Ventas > Nuevo. 3) Agregar producto. 4) Pagar y generar comprobante.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 26: Crear producto con Playwright (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como administrador. 2) Abrir Productos > Nuevo. 3) Completar datos. 4) Subir imagen. 5) Guardar.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 27: Consultar cliente por DNI con Playwright (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como vendedor. 2) Abrir Clientes > Nuevo. 3) Ingresar DNI. 4) Consultar RENIEC. 5) Guardar.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 28: Apertura y cierre de caja con Playwright (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como vendedor. 2) Abrir caja. 3) Registrar venta. 4) Cerrar caja y verificar totales.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 29: Registrar entrada de almacén con Playwright (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como almacenero. 2) Abrir Almacén > Entrada. 3) Seleccionar producto. 4) Ingresar cantidad. 5) Guardar.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 30: Exportar reporte con Playwright (CP30-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Iniciar sesión como administrador. 2) Abrir Reportes > Ventas. 3) Aplicar filtros. 4) Exportar CSV.
- **c) Resultados:** Satisfactorio

## 12.5. Ejecución de pruebas de validación

**PRUEBA N° 31: Validación de campos obligatorios en login (CP03-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Dejar campos vacíos. 2) Enviar formulario. 3) Verificar mensajes de validación.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 32: Validación de stock insuficiente en venta (CP16-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Intentar vender producto sin stock. 2) Verificar mensaje de advertencia.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 33: Validación de caja ya abierta (CP26-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Intentar abrir una caja ya abierta. 2) Verificar mensaje de error.
- **c) Resultados:** Satisfactorio

## 12.6. Ejecución de pruebas de integración

**PRUEBA N° 34: Flujo completo pedido-venta-almacén (H03, H04, H05, H09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Crear pedido. 2) Derivar a producción y entregar. 3) Vender producto relacionado. 4) Verificar stock en almacén.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 35: Integración notificaciones y cambios de estado (H02, H04, H09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Derivar pedido. 2) Verificar notificación al rol destino. 3) Entregar pedido. 4) Verificar notificación al vendedor.
- **c) Resultados:** Satisfactorio

## 12.7. Ejecución de pruebas de regresión

**PRUEBA N° 36: Regresión de autenticación y pedidos (H01, H03, H04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Ejecutar pruebas de login. 2) Crear pedido. 3) Derivar por todo el flujo. 4) Verificar que no aparezcan errores nuevos.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 37: Regresión de ventas, caja y reportes (H05, H08, H10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Pasos ejecutados:** 1) Abrir caja. 2) Registrar venta. 3) Cerrar caja. 4) Generar reporte. 5) Verificar consistencia de datos.
- **c) Resultados:** Satisfactorio

**
