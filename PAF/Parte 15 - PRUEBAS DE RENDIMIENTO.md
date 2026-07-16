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
| `htop` / `free` / `df` | Monitoreo de recursos del servidor. |

### 15.5.5. Configuración

| Escenario | Usuarios concurrentes | Total de peticiones | Duración aproximada |
| --- | :-: | :-: | --- |
| Carga | 10 | 100 | < 1 min |
| Estrés | 50 | 500 | < 3 min |
| Picos | 100 | 300 | < 2 min |

## 15.6. Ejecución y resultados de pruebas de rendimiento

### 15.6.1. Prueba de carga

| Endpoint | Usuarios | Peticiones | Exitosas | Errores | Tiempo mínimo | Tiempo máximo | Tiempo promedio |
| --- | :-: | :-: | :-: | :-: | --- | --- | --- |
| `/login` | 10 | 100 | 100 | 0 | 0.18 s | 0.95 s | 0.42 s |
| `/dashboard` | 10 | 100 | 100 | 0 | 0.22 s | 1.10 s | 0.51 s |
| `/api/reniec` | 10 | 100 | 100 | 0 | 0.35 s | 1.85 s | 0.78 s |

**Throughput:** 23.8 req/s  
**Tasa de errores:** 0 %  
**Uso de CPU:** 32 %  
**Uso de memoria:** 41 %  
**Estado:** Aprobado

### 15.6.2. Prueba de estrés

| Endpoint | Usuarios | Peticiones | Exitosas | Errores | Tiempo mínimo | Tiempo máximo | Tiempo promedio |
| --- | :-: | :-: | :-: | :-: | --- | --- | --- |
| `/login` | 50 | 500 | 498 | 2 | 0.21 s | 4.20 s | 1.85 s |
| `/dashboard` | 50 | 500 | 500 | 0 | 0.25 s | 4.55 s | 2.10 s |
| `/api/reniec` | 50 | 500 | 500 | 0 | 0.40 s | 4.90 s | 2.45 s |

**Throughput:** 18.3 req/s  
**Tasa de errores:** 0.13 %  
**Uso de CPU:** 68 %  
**Uso de memoria:** 58 %  
**Estado:** Aprobado

### 15.6.3. Prueba de picos

| Endpoint | Usuarios | Peticiones | Exitosas | Errores | Tiempo mínimo | Tiempo máximo | Tiempo promedio |
| --- | :-: | :-: | :-: | :-: | --- | --- | --- |
| `/login` | 100 | 300 | 295 | 5 | 0.24 s | 6.80 s | 3.20 s |
| `/dashboard` | 100 | 300 | 298 | 2 | 0.28 s | 7.10 s | 3.55 s |
| `/api/reniec` | 100 | 300 | 300 | 0 | 0.42 s | 5.95 s | 2.90 s |

**Throughput:** 14.7 req/s  
**Tasa de errores:** 0.78 %  
**Uso de CPU:** 79 %  
**Uso de memoria:** 67 %  
**Estado:** Aprobado con observación

## 15.7. Errores de ejecución

| Escenario | Errores detectados | Causa probable | Impacto |
| --- | --- | --- | --- |
| Prueba de estrés | 2 errores en `/login` | Tiempo de espera agotado por saturación momentánea de PHP-FPM. | Bajo |
| Prueba de picos | 7 errores distribuidos en `/login` y `/dashboard` | Límite de conexiones concurrentes alcanzado brevemente. | Medio |

Ningún error causó caída del sistema ni pérdida de datos.

## 15.8. Conclusiones de pruebas de rendimiento

- El sistema cumple con el criterio de tiempo de respuesta promedio menor a 2 segundos en condiciones normales y de carga moderada.
- Bajo prueba de picos con 100 usuarios concurrentes, el tiempo máximo superó los 5 segundos en algunas peticiones, aunque la tasa de errores se mantuvo por debajo del 1 %.
- No se detectaron cuellos de botella severos en la base de datos PostgreSQL.
- El servidor mantiene estabilidad general con uso de CPU y memoria dentro de rangos aceptables.
- El servicio externo de consulta RENIEC mostró mayor latencia, lo cual es dependiente del proveedor Decolecta.

## 15.9. Recomendaciones de pruebas de rendimiento

1. **Optimizar procesos de autenticación:** Revisar la configuración de PHP-FPM y el pool de conexiones para reducir errores bajo picos de carga.
2. **Cachear consultas frecuentes:** Implementar Redis para sesiones y consultas repetitivas al dashboard.
3. **Habilitar compresión Gzip:** Reducir el tamaño de respuestas HTML y JSON en Nginx.
4. **Ajustar timeouts:** Configurar timeouts adecuados para evitar errores 502/504 bajo estrés.
5. **Repetir pruebas tras optimizaciones:** Ejecutar nuevamente la prueba de picos después de aplicar las mejoras para validar la reducción de tiempos y errores.
6. **Escalar si crece la demanda:** Considerar aumentar a 4 vCPU y 8 GB RAM si la concurrencia real supera los 50 usuarios simultáneos.

**
