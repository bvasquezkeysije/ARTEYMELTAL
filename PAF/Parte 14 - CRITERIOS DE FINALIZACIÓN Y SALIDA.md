**

14. # CRITERIOS DE FINALIZACIÓN Y SALIDA

## 14.1. Criterios de suspensión y reinicio de pruebas

Las pruebas se suspenderán únicamente si se detecta un defecto crítico, es decir, un error que impida el funcionamiento correcto de un módulo esencial del sistema (como el inicio de sesión, el registro de ventas o la gestión de pedidos) o que no permita continuar con la ejecución de los casos de prueba planificados.

| Situación | Acción |
| --- | --- |
| Defecto crítico detectado | Suspender inmediatamente las pruebas y notificar al líder de QA y al desarrollador responsable. |
| Falla en el entorno de pruebas | Suspender temporalmente hasta restaurar el servidor en la nube. |
| Cambios significativos en requerimientos | Suspender las pruebas afectadas hasta actualizar los casos de prueba. |
| Corrección de defecto crítico | Reiniciar las pruebas desde el punto de suspensión y ejecutar regresión sobre módulos relacionados. |

## 14.2. Criterios de finalización

Las pruebas se considerarán completadas cuando se cumplan las siguientes condiciones:

| Criterio | Meta | Valor alcanzado |
| --- | --- | --- |
| Ejecución de casos de prueba | 100 % de los casos definidos ejecutados. | 37/37 (100 %) |
| Cobertura de historias de usuario | 100 % de las HU (H01 a H10) probadas. | 10/10 (100 %) |
| Defectos críticos pendientes | 0 defectos críticos sin corregir. | 0 |
| Defectos altos pendientes | 0 defectos altos sin corregir. | 0 |
| Defectos medios y bajos | Corregidos o documentados como mejoras futuras aceptadas. | 5/5 corregidos |
| Criterios de aceptación validados | Todos los CA de las historias validados satisfactoriamente. | 38/38 (100 %) |
| Aprobación del informe final | El líder de QA aprueba el informe de cierre. | Aprobado |

## 14.3. Criterios de salida

Para dar por concluida la etapa de pruebas del sistema ARTE Y METAL, se deberá cumplir con los siguientes criterios de salida:

1. **Ejecución completa:** Todos los casos de prueba de la Parte 10 han sido ejecutados al menos una vez.
2. **Sin defectos críticos:** No existen defectos de severidad crítica ni alta pendientes de corrección.
3. **Documentación de defectos:** Los defectos detectados están registrados en la Parte 13 con su corrección y validación.
4. **Regresión exitosa:** Las pruebas de regresión de la Parte 12 no arrojaron nuevos defectos.
5. **Entregables completos:** Se dispone del informe final, evidencias de pruebas y matriz de trazabilidad actualizada.
6. **Aprobación formal:** El equipo de QA y el responsable del proyecto firman la aceptación del cierre de pruebas.

## 14.4. Entregables de cierre

Al finalizar el periodo de pruebas se entregarán los siguientes documentos y evidencias:

| Entregable | Descripción | Ubicación |
| --- | --- | --- |
| Plan de pruebas actualizado | PAF completo con todas las partes diligenciadas. | Repositorio GitHub |
| Informe de ejecución de pruebas | Resumen de casos ejecutados, resultados y métricas. | PAF Parte 11 y 12 |
| Registro de defectos | Lista de defectos detectados, corregidos y validados. | PAF Parte 13 |
| Evidencias de pruebas | Capturas, traces, videos y reportes de consola. | Carpeta de evidencias del proyecto |
| Matriz de trazabilidad | Relación entre requerimientos, historias y casos de prueba. | PAF Parte 11 |
| Acta de cierre | Documento de aceptación firmado por QA y responsable. | Documentación del proyecto |

## 14.5. Condiciones de aceptación final

El sistema ARTE Y METAL será aceptado desde la perspectiva de pruebas si:

- El **100 % de las historias de usuario** críticas y principales han sido probadas.
- La **tasa de defectos críticos y altos es 0**.
- Los defectos medios y bajos están **corregidos o documentados** como mejoras futuras.
- El **rendimiento del sistema** cumple con las métricas definidas en la Parte 11.
- No se detectan **regresiones** tras las correcciones aplicadas.

En caso de no cumplir alguno de estos criterios, las pruebas no se darán por finalizadas y se establecerá un plan de acción para resolver los hallazgos pendientes.

**
