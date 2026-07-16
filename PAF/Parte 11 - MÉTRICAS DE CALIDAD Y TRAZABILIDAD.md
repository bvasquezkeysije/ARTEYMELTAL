**

11. # MÉTRICAS DE CALIDAD Y TRAZABILIDAD

## 11.1. Métricas de calidad

Las siguientes métricas se utilizarán para evaluar la calidad del sistema ARTE Y METAL durante y después del periodo de pruebas.

### 11.1.1. Cobertura de pruebas

| Métrica | Definición | Meta | Valor alcanzado |
| --- | --- | --- | --- |
| Cobertura de historias de usuario | Porcentaje de HU con casos de prueba ejecutados. | 100 % | 100 % (10/10) |
| Cobertura de criterios de aceptación | Porcentaje de CA validados con casos de prueba. | ≥ 90 % | 95 % (38/40) |
| Cobertura de escenarios BDD/TDD | Porcentaje de escenarios ejecutados. | 100 % | 100 % (10/10) |
| Cobertura de requerimientos funcionales críticos | Porcentaje de RF críticos probados. | 100 % | 100 % |

### 11.1.2. Métricas de ejecución

| Métrica | Definición | Meta | Valor alcanzado |
| --- | --- | --- | --- |
| Casos de prueba ejecutados | Total de casos de prueba ejecutados. | 32 | 32 |
| Casos de prueba exitosos | Casos con resultado satisfactorio. | ≥ 95 % | 32/32 (100 %) |
| Casos de prueba fallidos | Casos con defectos encontrados. | ≤ 5 % | 0/32 (0 %) |
| Casos de prueba bloqueados | Casos que no se pudieron ejecutar. | 0 | 0 |

### 11.1.3. Métricas de defectos

| Métrica | Definición | Meta | Valor alcanzado |
| --- | --- | --- | --- |
| Densidad de defectos | Defectos encontrados por historia de usuario. | ≤ 1 | 0 |
| Defectos críticos | Defectos que impiden el uso del sistema. | 0 | 0 |
| Defectos mayores | Defectos que afectan funcionalidad importante. | ≤ 2 | 0 |
| Defectos menores | Defectos cosméticos o de bajo impacto. | ≤ 5 | 0 |
| Tasa de resolución | Porcentaje de defectos corregidos. | 100 % | 100 % |

### 11.1.4. Métricas de rendimiento y disponibilidad

| Métrica | Definición | Meta | Valor alcanzado |
| --- | --- | --- | --- |
| Tiempo de carga de páginas principales | Tiempo promedio de carga en segundos. | ≤ 3 s | ≤ 2 s |
| Disponibilidad del sistema | Porcentaje de tiempo disponible en horario comercial. | ≥ 99.5 % | ≥ 99.9 % |
| Tiempo de respuesta de APIs externas | Tiempo máximo de respuesta de RENIEC/SUNAT. | ≤ 15 s | ≤ 10 s |

### 11.1.5. Calidad del proceso

| Métrica | Definición | Meta | Valor alcanzado |
| --- | --- | --- | --- |
| Documentación de pruebas | Porcentaje de partes del PAF completadas. | 100 % | 100 % |
| Cumplimiento del cronograma | Pruebas ejecutadas dentro del periodo planificado. | 100 % | 100 % |
| Retroalimentación del equipo | Encuesta de satisfacción del equipo de pruebas. | ≥ 4/5 | 4.5/5 |

## 11.2. Matriz de trazabilidad

La matriz de trazabilidad permite relacionar los requerimientos funcionales, las historias de usuario, los escenarios BDD/TDD y los casos de prueba ejecutados.

| Requerimientos relacionados | Historia de usuario | Escenario BDD/TDD | Casos de prueba | Estado |
| --- | --- | --- | --- | --- |
| RF-01, RF-02, RNF-01 a RNF-08 | H01 Inicio de sesión | EP01 | CP01, CP02, CP03, CP04 | Satisfactorio |
| RF-03, RF-04, RNF-01, RNF-02 | H02 Recuperación de contraseña | EP02 | CP05, CP06, CP07 | Satisfactorio |
| RF-25 a RF-37, RNF-28, RNF-30 | H03 Crear pedido personalizado | EP03 | CP08, CP09, CP10 | Satisfactorio |
| RF-38 a RF-44, RF-46, RNF-26, RNF-30 | H04 Gestionar flujo de pedido | EP04 | CP11, CP12, CP13, CP14 | Satisfactorio |
| RF-62 a RF-69, RNF-26, RNF-27 | H05 Registrar venta directa | EP05 | CP15, CP16, CP17 | Satisfactorio |
| RF-14 a RF-20, RNF-10 | H06 Gestionar productos | EP06 | CP18, CP19, CP20 | Satisfactorio |
| RF-21 a RF-24, RNF-29 | H07 Gestionar clientes | EP07 | CP21, CP22, CP23 | Satisfactorio |
| RF-70 a RF-73, RNF-27 | H08 Control de caja | EP08 | CP24, CP25, CP26 | Satisfactorio |
| RF-55 a RF-61, RNF-09, RNF-26 | H09 Gestionar almacén | EP09 | CP27, CP28, CP29 | Satisfactorio |
| RF-74 a RF-82 | H10 Consultar reportes | EP10 | CP30, CP31, CP32 | Satisfactorio |

### 11.2.1. Trazabilidad por módulo

| Módulo | Historias de usuario | Casos de prueba | Cobertura |
| --- | --- | --- | --- |
| Autenticación y seguridad | H01, H02 | CP01 - CP07 | 100 % |
| Pedidos | H03, H04 | CP08 - CP14 | 100 % |
| Ventas y caja | H05, H08 | CP15 - CP17, CP24 - CP26 | 100 % |
| Productos y clientes | H06, H07 | CP18 - CP23 | 100 % |
| Almacén | H09 | CP27 - CP29 | 100 % |
| Reportes | H10 | CP30 - CP32 | 100 % |

### 11.2.2. Leyenda de estados

| Estado | Descripción |
| --- | --- |
| Satisfactorio | Todos los casos de prueba asociados se ejecutaron con éxito. |
| Pendiente | Los casos de prueba aún no han sido ejecutados. |
| Fallido | Al menos un caso de prueba encontró un defecto. |
| Bloqueado | Los casos no se pudieron ejecutar por dependencias pendientes. |

**
