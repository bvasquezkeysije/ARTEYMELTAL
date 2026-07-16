**

9. # ESTIMACIÓN Y CRONOGRAMA DE PRUEBAS

## 9.1. Periodo de ejecución

Las pruebas del sistema ARTE Y METAL se ejecutarán durante un periodo de **6 semanas**, comprendido del **01 de junio de 2026 al 12 de julio de 2026**. Durante este lapso se validarán las 10 historias de usuario definidas en la Parte 5, priorizando los módulos críticos del negocio.

> **Nota:** Las pruebas se coordinan desde una laptop personal, pero su objetivo o **target** es el **servidor en la nube** donde está desplegada la aplicación, de acuerdo con lo establecido en la Parte 7 y Parte 8 del presente plan.

## 9.2. Cronograma general por semanas

| Semana | Periodo | Foco principal | Historias cubiertas |
| ------ | ------- | -------------- | ------------------- |
| Semana 1 | 01/06/2026 - 07/06/2026 | Planificación, entorno y autenticación | H01 |
| Semana 2 | 08/06/2026 - 14/06/2026 | Seguridad y registro de pedidos | H02, H03 |
| Semana 3 | 15/06/2026 - 21/06/2026 | Flujo de pedido y ventas directas | H04, H05 |
| Semana 4 | 22/06/2026 - 28/06/2026 | Catálogo y clientes | H06, H07 |
| Semana 5 | 29/06/2026 - 05/07/2026 | Caja y almacén | H08, H09 |
| Semana 6 | 06/07/2026 - 12/07/2026 | Reportes, regresión y cierre | H10 |

## 9.3. Plan detallado de actividades

| Actividad | Semana 1 | Semana 2 | Semana 3 | Semana 4 | Semana 5 | Semana 6 |
| --- | :-: | :-: | :-: | :-: | :-: | :-: |
| Revisión del plan de pruebas | ✔ | | | | | |
| Configuración del entorno (servidor en la nube) | ✔ | | | | | |
| Diseño de casos de prueba | ✔ | ✔ | | | | |
| Pruebas de inicio de sesión (H01) | ✔ | | | | | |
| Pruebas de recuperación de contraseña (H02) | | ✔ | | | | |
| Pruebas de creación de pedido (H03) | | ✔ | | | | |
| Pruebas de flujo de pedido (H04) | | | ✔ | | | |
| Pruebas de venta directa (H05) | | | ✔ | | | |
| Pruebas de gestión de productos (H06) | | | | ✔ | | |
| Pruebas de gestión de clientes (H07) | | | | ✔ | | |
| Pruebas de control de caja (H08) | | | | | ✔ | |
| Pruebas de gestión de almacén (H09) | | | | | ✔ | |
| Pruebas de reportes (H10) | | | | | | ✔ |
| Pruebas de regresión | | | | | | ✔ |
| Elaboración del informe final | | | | | | ✔ |
| Cierre y aprobación de pruebas | | | | | | ✔ |

## 9.4. Estimación de esfuerzo por historia

| ID | Historia de usuario | Puntos de historia | Tiempo estimado de prueba | Responsable |
| --- | --- | :-: | :-: | --- |
| H01 | Inicio de sesión | 8 | 2 días | Equipo de QA |
| H02 | Recuperación de contraseña | 8 | 2 días | Equipo de QA |
| H03 | Crear pedido personalizado | 13 | 3 días | Equipo de QA |
| H04 | Gestionar flujo de pedido | 13 | 3 días | Equipo de QA |
| H05 | Registrar venta directa | 8 | 2 días | Equipo de QA |
| H06 | Gestionar productos | 8 | 2 días | Equipo de QA |
| H07 | Gestionar clientes | 8 | 2 días | Equipo de QA |
| H08 | Control de caja | 8 | 2 días | Equipo de QA |
| H09 | Gestionar almacén | 8 | 2 días | Equipo de QA |
| H10 | Consultar reportes | 8 | 2 días | Equipo de QA |
| **Total** | | **87** | **22 días** | |

## 9.5. Cronograma detallado de ejecución

| Fecha | Escenario de prueba | Historia | Responsable | Entorno |
| --- | --- | --- | --- | --- |
| 01/06/2026 | Revisión del plan y alcance | - | Líder de QA | Servidor en la nube |
| 02/06/2026 | Configuración del entorno de pruebas | - | Líder de QA | Servidor en la nube |
| 03/06/2026 | Diseño de casos de prueba H01 | H01 | Equipo de QA | Servidor en la nube |
| 04/06/2026 | Prueba de inicio de sesión exitoso | H01 | Equipo de QA | Servidor en la nube |
| 05/06/2026 | Prueba de credenciales incorrectas | H01 | Equipo de QA | Servidor en la nube |
| 06/06/2026 | Prueba de campos obligatorios | H01 | Equipo de QA | Servidor en la nube |
| 07/06/2026 | Prueba de usuario deshabilitado | H01 | Equipo de QA | Servidor en la nube |
| 08/06/2026 | Diseño de casos de prueba H02 | H02 | Equipo de QA | Servidor en la nube |
| 09/06/2026 | Prueba de envío de código de recuperación | H02 | Equipo de QA | Servidor en la nube |
| 10/06/2026 | Prueba de código inválido o expirado | H02 | Equipo de QA | Servidor en la nube |
| 11/06/2026 | Diseño de casos de prueba H03 | H03 | Equipo de QA | Servidor en la nube |
| 12/06/2026 | Prueba de creación de pedido con adelanto | H03 | Equipo de QA | Servidor en la nube |
| 13/06/2026 | Prueba de validación de caja abierta | H03 | Equipo de QA | Servidor en la nube |
| 14/06/2026 | Prueba de adjuntos de orden de compra | H03 | Equipo de QA | Servidor en la nube |
| 15/06/2026 | Prueba de derivación a diseño | H04 | Equipo de QA | Servidor en la nube |
| 16/06/2026 | Prueba de derivación a producción | H04 | Equipo de QA | Servidor en la nube |
| 17/06/2026 | Prueba de transporte y recepción en almacén | H04 | Equipo de QA | Servidor en la nube |
| 18/06/2026 | Prueba de entrega al cliente | H04 | Equipo de QA | Servidor en la nube |
| 19/06/2026 | Prueba de venta directa exitosa | H05 | Equipo de QA | Servidor en la nube |
| 20/06/2026 | Prueba de stock insuficiente en venta | H05 | Equipo de QA | Servidor en la nube |
| 21/06/2026 | Prueba de generación de PDF de comprobante | H05 | Equipo de QA | Servidor en la nube |
| 22/06/2026 | Prueba de creación de producto | H06 | Equipo de QA | Servidor en la nube |
| 23/06/2026 | Prueba de búsqueda y filtro de productos | H06 | Equipo de QA | Servidor en la nube |
| 24/06/2026 | Prueba de gestión de imágenes de producto | H06 | Equipo de QA | Servidor en la nube |
| 25/06/2026 | Prueba de consulta de DNI por RENIEC | H07 | Equipo de QA | Servidor en la nube |
| 26/06/2026 | Prueba de consulta de RUC por SUNAT | H07 | Equipo de QA | Servidor en la nube |
| 27/06/2026 | Prueba de búsqueda local de cliente | H07 | Equipo de QA | Servidor en la nube |
| 28/06/2026 | Prueba de fallback manual ante fallo de API | H07 | Equipo de QA | Servidor en la nube |
| 29/06/2026 | Prueba de apertura de caja | H08 | Equipo de QA | Servidor en la nube |
| 30/06/2026 | Prueba de cierre de caja | H08 | Equipo de QA | Servidor en la nube |
| 01/07/2026 | Prueba de detalle de caja por medio de pago | H08 | Equipo de QA | Servidor en la nube |
| 02/07/2026 | Prueba de registro de entrada de almacén | H09 | Equipo de QA | Servidor en la nube |
| 03/07/2026 | Prueba de registro de salida de almacén | H09 | Equipo de QA | Servidor en la nube |
| 04/07/2026 | Prueba de stock insuficiente en salida | H09 | Equipo de QA | Servidor en la nube |
| 05/07/2026 | Prueba de recepción de pedido en almacén | H09 | Equipo de QA | Servidor en la nube |
| 06/07/2026 | Prueba de reporte de ventas | H10 | Equipo de QA | Servidor en la nube |
| 07/07/2026 | Prueba de reporte de pedidos | H10 | Equipo de QA | Servidor en la nube |
| 08/07/2026 | Prueba de exportación a CSV | H10 | Equipo de QA | Servidor en la nube |
| 09/07/2026 | Prueba de exportación a Excel | H10 | Equipo de QA | Servidor en la nube |
| 10/07/2026 | Pruebas de regresión generales | - | Líder de QA | Servidor en la nube |
| 11/07/2026 | Elaboración del informe final de pruebas | - | Líder de QA | Servidor en la nube |
| 12/07/2026 | Cierre y aprobación del plan de pruebas | - | Líder de QA | Servidor en la nube |

## 9.6. Consideraciones del cronograma

- Las pruebas se ejecutan de **lunes a sábado**, dejando el domingo como día de reserva para correcciones puntuales.
- Cada historia cuenta con un margen de **1 día adicional** para reprocesos o validaciones adicionales.
- Las pruebas de regresión se concentran en la última semana para validar que los ajustes no afecten funcionalidades previas.
- El informe final y la aprobación cierran el periodo el día **12/07/2026**.

**
