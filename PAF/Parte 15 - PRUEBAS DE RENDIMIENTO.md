**

15. # PRUEBAS DE RENDIMIENTO

## 15.1. Introducción

El presente informe documenta las pruebas de rendimiento ejecutadas sobre el sistema **ARTE Y METAL**. El propósito es validar que la aplicación responde de manera estable y oportuna bajo diferentes niveles de carga, identificar posibles cuellos de botella y verificar la confiabilidad del entorno de producción desplegado en la nube.

## 15.2. Alcance

| Incluido | Excluido |
| --- | --- |
| Página de inicio de sesión (`/login`) | Pruebas de interfaz gráfica con usuarios reales |
| Dashboard administrativo (`/dashboard`) | Pruebas de seguridad (pentesting) |
| API de consulta RENIEC (`/api/reniec`) | Pruebas de rendimiento de Decolecta como servicio externo |
| Proceso de registro de pedidos (`/pedidos/create`) | Pruebas de carga sobre servicios de terceros |

## 15.3. Objetivo

- Validar que los tiempos de respuesta sean menores a 2 segundos bajo carga normal.
- Identificar cuellos de botella en consultas a la base de datos PostgreSQL.
- Comprobar la estabilidad del servidor ante concurrencia creciente.
- Determinar el límite de usuarios concurrentes antes de la degradación del servicio.

## 15.4. Métricas a evaluar

| Métrica | Definición | Criterio de aceptación |
| --- | --- | --- |
| Tiempo de respuesta promedio | Tiempo medio transcurrido entre la solicitud y la respuesta. | ≤ 2 s |
| Tiempo de respuesta máximo | Tiempo más alto registrado en el escenario. | ≤ 5 s |
| Throughput | Peticiones exitosas por segundo. | ≥ 10 req/s |
| Tasa de errores | Porcentaje de peticiones fallidas. | ≤ 2 % |
| Uso de CPU | Porcentaje de CPU del servidor durante la prueba. | ≤ 80 % |
| Uso de memoria | Porcentaje de RAM utilizada durante la prueba. | ≤ 80 % |

## 15.5. Estrategia por ejecutar

### 15.5.1. Técnica

| Tipo | Descripción |
| --- | --- |
| Prueba de carga | Simula el uso esperado con 10 usuarios concurrentes y 100 peticiones. |
| Prueba de estrés | Incrementa la carga a 50 usuarios concurrentes y 500 peticiones. |
| Prueba de picos | Aplica una carga repentina de 100 usuarios concurrentes durante 300 peticiones. |

### 15.5.2. Entorno

| Componente | Especificación |
| --- | --- |
| Servidor de aplicación | VPS cloud (89.116.25.190), Ubuntu 22.04 LTS |
| Servidor web | Nginx / Apache |
| Aplicación | Laravel 10.x + PHP 8.2 |
| Base de datos | PostgreSQL 14 |
| Red | Internet pública |

### 15.5.3. Hardware del servidor

| Recurso | Especificación |
| --- | --- |
| CPU | 2 vCPU |
| RAM | 4 GB |
| Almacenamiento | 80 GB SSD |

### 15.5.4. Software y herramientas

| Herramienta | Uso |
| --- | --- |
| Python 3.11 | Lenguaje de scripting para las pruebas. |
| `requests` + `concurrent.futures` | Generación de peticiones concurrentes. |
| `pytest` | Ejecución estructurada de pruebas. |
| `htop` / `free` / `df` | Monitoreo de recursos del servidor. |

**Código fuente:** Cada escenario de rendimiento tiene su propio script en la carpeta `PRUEBAS/performance/`:

| Escenario | Archivo |
| --- | --- |
| Prueba de carga | `PRUEBAS/performance/test_carga.py` |
| Prueba de estrés | `PRUEBAS/performance/test_estres.py` |
| Prueba de picos | `PRUEBAS/performance/test_picos.py` |

### 15.5.5. Configuración

| Escenario | Usuarios concurrentes | Total de peticiones | Duración aproximada |
| --- | :-: | :-: | --- |
| Carga | 10 | 100 | < 1 min |
| Estrés | 50 | 500 | < 3 min |
| Picos | 100 | 300 | < 2 min |

## 15.6. Ejecución y resultados de pruebas de rendimiento

### 15.6.1. Prueba de carga

**Código:** `PRUEBAS/performance/test_carga.py`

| Endpoint | Usuarios | Peticiones | Exitosas | Errores | Tiempo mínimo | Tiempo máximo | Tiempo promedio |
| --- | :-: | :-: | :-: | :-: | --- | --- | --- |
| `/login` | 10 | 50 | 42 | 8 | 0.61 s | 8.11 s | 4.00 s |

**Throughput:** 0.25 req/s  
**Tasa de errores:** 16 %  
**Uso de CPU:** observado ~40 %  
**Uso de memoria:** observado ~45 %  
**Estado:** No aprobado (tiempo promedio y tasa de errores superan los criterios)

### 15.6.2. Prueba de estrés

**Código:** `PRUEBAS/performance/test_estres.py`

| Endpoint | Usuarios | Peticiones | Exitosas | Errores | Tiempo mínimo | Tiempo máximo | Tiempo promedio |
| --- | :-: | :-: | :-: | :-: | --- | --- | --- |
| `/login` | 30 | 150 | 101 | 49 | 0.60 s | 10.97 s | 1.97 s |

**Throughput:** 0.51 req/s  
**Tasa de errores:** 32.67 %  
**Uso de CPU:** observado ~55 %  
**Uso de memoria:** observado ~50 %  
**Estado:** No aprobado (alta tasa de errores por timeouts)

### 15.6.3. Prueba de picos

**Código:** `PRUEBAS/performance/test_picos.py`

| Endpoint | Usuarios | Peticiones | Exitosas | Errores | Tiempo mínimo | Tiempo máximo | Tiempo promedio |
| --- | :-: | :-: | :-: | :-: | --- | --- | --- |
| `/login` | 50 | 100 | 80 | 20 | 0.61 s | 16.02 s | 6.32 s |

**Throughput:** 0.16 req/s  
**Tasa de errores:** 20 %  
**Uso de CPU:** observado ~70 %  
**Uso de memoria:** observado ~60 %  
**Estado:** No aprobado (tiempos y tasa de errores superan los criterios)

## 15.7. Errores de ejecución

| Escenario | Errores detectados | Causa probable | Impacto |
| --- | --- | --- | --- |
| Prueba de carga | 8 errores en `/login` | Timeouts por latencia de red y respuesta lenta del servidor. | Medio |
| Prueba de estrés | 49 errores en `/login` | Saturación de PHP-FPM y timeouts en conexiones concurrentes. | Alto |
| Prueba de picos | 20 errores en `/login` | Límite de conexiones concurrentes alcanzado. | Medio |

Ningún error causó caída del sistema ni pérdida de datos.

## 15.8. Conclusiones de pruebas de rendimiento

- Los resultados reales de las pruebas muestran que el servidor actual **no cumple** con los criterios de rendimiento definidos (tiempo promedio ≤ 2 s y tasa de errores ≤ 2 %).
- La latencia de red y la configuración actual de PHP-FPM generan timeouts frecuentes bajo concurrencia moderada.
- No se detectaron cuellos de botella severos en la base de datos PostgreSQL; los errores provienen principalmente de la capa de aplicación/web.
- El servidor mantiene estabilidad general (no se cae), pero la experiencia de usuario se degrada significativamente bajo carga.
- Se recomienda ejecutar nuevamente las pruebas tras aplicar las optimizaciones indicadas en la sección 15.9.

## 15.9. Recomendaciones de pruebas de rendimiento

1. **Optimizar procesos de autenticación:** Revisar la configuración de PHP-FPM y el pool de conexiones para reducir errores bajo picos de carga.
2. **Cachear consultas frecuentes:** Implementar Redis para sesiones y consultas repetitivas al dashboard.
3. **Habilitar compresión Gzip:** Reducir el tamaño de respuestas HTML y JSON en Nginx.
4. **Ajustar timeouts:** Configurar timeouts adecuados para evitar errores 502/504 bajo estrés.
5. **Repetir pruebas tras optimizaciones:** Ejecutar nuevamente la prueba de picos después de aplicar las mejoras para validar la reducción de tiempos y errores.
6. **Escalar si crece la demanda:** Considerar aumentar a 4 vCPU y 8 GB RAM si la concurrencia real supera los 50 usuarios simultáneos.

**
