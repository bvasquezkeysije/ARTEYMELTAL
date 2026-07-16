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

| Tipo | Archivo principal |
| --- | --- |
| Unitarias | `PRUEBAS/unit/test_unitarios.py` |
| Automatizadas con Playwright | `PRUEBAS/playwright/test_automatizados.py` |
| Validación | `PRUEBAS/validation/test_validacion.py` |
| Integración | `PRUEBAS/integration/test_integracion.py` |
| Regresión | `PRUEBAS/regression/test_regresion.py` |
| Rendimiento | `PRUEBAS/performance/test_rendimiento.py` |
| Manuales | `PRUEBAS/manual/test_manuales.md` |

A continuación se indica el código o archivo correspondiente a cada caso de prueba.

## 12.3. Ejecución de pruebas unitarias

**PRUEBA N° 01: Inicio de sesión con credenciales válidas (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_login_valid_credentials`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 02: Recuperación de contraseña por código (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_password_recovery_code_generation`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 03: Crear pedido personalizado (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_create_pedido_personalizado`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 04: Cambiar estado de pedido a producción (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_change_pedido_state_to_produccion`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 05: Registrar venta directa (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_registrar_venta_directa`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 06: Crear producto (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_crear_producto`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 07: Consultar DNI por RENIEC (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_consultar_dni_reniec`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 08: Apertura de caja (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_apertura_caja`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 09: Registrar entrada de almacén (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_registrar_entrada_almacen`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 10: Exportar reporte de ventas a CSV (CP31-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/unit/test_unitarios.py::test_exportar_reporte_ventas_csv`
- **c) Resultados:** Satisfactorio

## 12.4. Ejecución de pruebas funcionales manuales

**PRUEBA N° 11: Inicio de sesión con credenciales válidas (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP01-HU01.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 12: Recuperación de contraseña por código (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP05-HU02.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 13: Crear pedido personalizado (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP08-HU03.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 14: Gestionar flujo de pedido (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP12-HU04.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 15: Registrar venta directa (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP15-HU05.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 16: Gestionar productos (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP18-HU06.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 17: Gestionar clientes (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP21-HU07.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 18: Control de caja (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP24-HU08.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 19: Gestionar almacén (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP27-HU09.
- **c) Resultados:** Satisfactorio

**PRUEBA N° 20: Consultar reportes (CP30-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** Ver matriz de pruebas manuales en `PRUEBAS/manual/test_manuales.md`, fila CP30-HU10.
- **c) Resultados:** Satisfactorio

## 12.5. Ejecución de pruebas funcionales automatizadas con Playwright

**PRUEBA N° 21: Inicio de sesión con Playwright (CP01-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_login_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 22: Recuperación de contraseña con Playwright (CP05-HU02)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_password_recovery_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 23: Crear pedido con Playwright (CP08-HU03)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_create_pedido_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 24: Derivar pedido a producción con Playwright (CP12-HU04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_derivar_pedido_a_produccion_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 25: Registrar venta directa con Playwright (CP15-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_registrar_venta_directa_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 26: Crear producto con Playwright (CP18-HU06)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_create_producto_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 27: Consultar cliente por DNI con Playwright (CP21-HU07)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_consultar_cliente_dni_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 28: Apertura y cierre de caja con Playwright (CP24-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_apertura_cierre_caja_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 29: Registrar entrada de almacén con Playwright (CP27-HU09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_registrar_entrada_almacen_with_playwright`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 30: Exportar reporte con Playwright (CP30-HU10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/playwright/test_automatizados.py::test_exportar_reporte_with_playwright`
- **c) Resultados:** Satisfactorio

## 12.6. Ejecución de pruebas de validación

**PRUEBA N° 31: Validación de campos obligatorios en login (CP03-HU01)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/validation/test_validacion.py::test_login_required_fields`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 32: Validación de stock insuficiente en venta (CP16-HU05)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/validation/test_validacion.py::test_venta_stock_insuficiente`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 33: Validación de caja ya abierta (CP26-HU08)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/validation/test_validacion.py::test_caja_ya_abierta`
- **c) Resultados:** Satisfactorio

## 12.7. Ejecución de pruebas de integración

**PRUEBA N° 34: Flujo completo pedido-venta-almacén (H03, H04, H05, H09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/integration/test_integracion.py::test_flujo_pedido_venta_almacen`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 35: Integración notificaciones y cambios de estado (H02, H04, H09)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/integration/test_integracion.py::test_notificaciones_y_cambios_de_estado`
- **c) Resultados:** Satisfactorio

## 12.8. Ejecución de pruebas de regresión

**PRUEBA N° 36: Regresión de autenticación y pedidos (H01, H03, H04)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/regression/test_regresion.py::test_regresion_autenticacion_y_pedidos`
- **c) Resultados:** Satisfactorio

**PRUEBA N° 37: Regresión de ventas, caja y reportes (H05, H08, H10)**
- **a) Captura del sistema:** [Adjuntar evidencia visual]
- **b) Código:** `PRUEBAS/regression/test_regresion.py::test_regresion_ventas_caja_reportes`
- **c) Resultados:** Satisfactorio

## 12.9. Ejecución de pruebas de rendimiento

| Escenario | Archivo de código |
| --- | --- |
| Prueba de carga | `PRUEBAS/performance/test_rendimiento.py::run_scenario("Prueba de carga", ...)` |
| Prueba de estrés | `PRUEBAS/performance/test_rendimiento.py::run_scenario("Prueba de estrés", ...)` |
| Prueba de picos | `PRUEBAS/performance/test_rendimiento.py::run_scenario("Prueba de picos", ...)` |

**
