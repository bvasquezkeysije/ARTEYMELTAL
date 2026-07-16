**

13. # REGISTRO Y SEGUIMIENTO DE DEFECTOS

## 13.1. Defectos identificados

Durante el periodo de pruebas del **01 de junio al 12 de julio de 2026** se registraron los siguientes defectos. Todos fueron corregidos y validados antes del cierre del plan de pruebas.

| ID | Caso de prueba | Descripción del defecto | Historia afectada | Severidad | Estado |
| --- | --- | --- | --- | --- | --- |
| DEF-01 | CP02-HU01 | Mensaje de error de credenciales incorrectas mostrado como banner en lugar de modal emergente. | H01 | Baja | Corregido |
| DEF-02 | CP09-HU03 | El sistema permitía visualizar el formulario de pedidos sin caja abierta, mostrando el error solo al guardar. | H03 | Media | Corregido |
| DEF-03 | CP17-HU05 | El PDF del comprobante no incluía el logo de ARTE Y METAL en la cabecera. | H05 | Baja | Corregido |
| DEF-04 | CP20-HU06 | Al eliminar una imagen de producto, la vista no se actualizaba hasta recargar la página. | H06 | Baja | Corregido |
| DEF-05 | CP29-HU09 | La recepción de pedido en almacén no generaba movimiento de entrada cuando el producto no existía previamente. | H09 | Media | Corregido |

## 13.2. Clasificación de defectos

### 13.2.1. Por severidad

| Severidad | Cantidad | Descripción |
| --- | :-: | --- |
| Crítica | 0 | Impide el uso del sistema o causa pérdida de datos. |
| Alta | 0 | Afecta funcionalidad importante sin workaround. |
| Media | 2 | Afecta funcionalidad con workaround temporal disponible. |
| Baja | 3 | Error cosmético, de usabilidad o mensaje confuso. |
| **Total** | **5** | |

### 13.2.2. Por tipo

| Tipo | Cantidad | Descripción |
| --- | :-: | --- |
| Funcional | 2 | Comportamiento incorrecto de una función. |
| Visual / UI | 2 | Problemas de interfaz, diseño o presentación. |
| Validación | 1 | Reglas de validación incompletas o incorrectas. |
| **Total** | **5** | |

### 13.2.3. Por módulo

| Módulo | Cantidad | Defectos |
| --- | :-: | --- |
| Autenticación | 1 | DEF-01 |
| Pedidos | 1 | DEF-02 |
| Ventas | 1 | DEF-03 |
| Productos | 1 | DEF-04 |
| Almacén | 1 | DEF-05 |
| **Total** | **5** | |

## 13.3. Seguimiento y estado de los defectos

| ID | Fecha de detección | Detectado por | Responsable de corrección | Fecha de corrección | Fecha de validación | Estado |
| --- | --- | --- | --- | --- | --- | --- |
| DEF-01 | 05/06/2026 | Equipo de QA | Desarrollador frontend | 06/06/2026 | 07/06/2026 | Cerrado |
| DEF-02 | 12/06/2026 | Equipo de QA | Desarrollador backend | 13/06/2026 | 14/06/2026 | Cerrado |
| DEF-03 | 20/06/2026 | Equipo de QA | Desarrollador backend | 21/06/2026 | 22/06/2026 | Cerrado |
| DEF-04 | 24/06/2026 | Equipo de QA | Desarrollador frontend | 25/06/2026 | 26/06/2026 | Cerrado |
| DEF-05 | 04/07/2026 | Equipo de QA | Desarrollador backend | 05/07/2026 | 06/07/2026 | Cerrado |

### 13.3.1. Ciclo de vida de un defecto

El seguimiento de defectos sigue el siguiente flujo:

1. **Detectado:** El equipo de QA identifica el defecto durante la ejecución de un caso de prueba.
2. **Registrado:** Se documenta en la tabla de defectos con ID, descripción, severidad y caso de prueba asociado.
3. **Asignado:** Se asigna a un desarrollador responsable de la corrección.
4. **En corrección:** El desarrollador implementa la solución.
5. **Validado:** El equipo de QA vuelve a ejecutar el caso de prueba para confirmar la corrección.
6. **Cerrado:** El defecto se cierra una vez superada la validación.

### 13.3.2. Estados utilizados

| Estado | Descripción |
| --- | --- |
| Abierto | Defecto detectado pero aún no asignado. |
| Asignado | Defecto asignado a un desarrollador. |
| En corrección | El desarrollador está trabajando en la solución. |
| En validación | La corrección está siendo probada por QA. |
| Cerrado | El defecto fue corregido y validado. |
| Reabierto | El defecto persistió después de la corrección. |

### 13.3.3. Resumen final

| Indicador | Valor |
| --- | --- |
| Total de defectos detectados | 5 |
| Defectos críticos / altos | 0 |
| Defectos corregidos | 5 |
| Defectos pendientes | 0 |
| Tasa de resolución | 100 % |
| Tiempo promedio de corrección | 1 día |

**
