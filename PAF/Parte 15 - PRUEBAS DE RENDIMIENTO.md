**

1. # PRUEBAS DE RENDIMIENTO
    

15.1. Introducción

-Presenta el propósito general del informe.

-Explica por qué se realizaron las pruebas de performance y qué se busca demostrar (ej. estabilidad, capacidad de respuesta, confiabilidad).

  

15.2. Alcance

-Define los módulos, funcionalidades o procesos del sistema que fueron evaluados.

-Delimita qué sí está incluido y qué no (ej. solo backend, sin pruebas de interfaz gráfica).

  

15.3. Objetivo

Expone  el  resultado  esperado  de  las  pruebas:  validar  tiempos  de respuesta, identificar cuellos de botella, comprobar la escalabilidad del sistema.

  

15.4. Métricas a Evaluar

-Tiempos de respuesta promedio y máximo.

-Throughput (transacciones por segundo).

-Uso de CPU, memoria y disco.

-Tasa de errores y estabilidad bajo carga.

  

15.5. Estrategia por Ejecutar

-Técnica: tipo de prueba (carga, estrés, volumen, resistencia).

-Entorno: ambiente de prueba (servidores, red, base de datos).

-Hardware: especificaciones de los equipos utilizados.

-Software: herramientas de ejecución y monitoreo (ej. JMeter, LoadRunner).

-Configuración: parámetros de usuarios concurrentes, duración de pruebas, scripts de simulación.

  

15.6.  Ejecución y Resultado de pruebas de rendimiento (Prueba de Carga, Prueba de Estrés)

-Presenta los datos obtenidos en cada escenario.

-Ejemplo: tiempos de respuesta, throughput, consumo de recursos en condiciones normales y extremas.

  

15.6.1. PRUEBA DE CARGA

PRUEBA Nº: Nombre (código)

a) Captura del sistema

  

b) Código

  

c) Resultados 

  

15.6.2. PRUEBA DE ESTRÉS

PRUEBA Nº: Nombre (código)

a) Captura del sistema

  

b) Código

  

c) Resultados 

  

15.6.3. PRUEBA DE PICOS 

PRUEBA Nº: Nombre (código)

a) Captura del sistema

  

b) Código

  

c) Resultados 

  
  

15.7. Errores de Ejecución (Prueba de Carga, Prueba de Estrés)

-Documenta los fallos detectados durante las pruebas.

-Ejemplo: errores de conexión, timeouts, fallos de base de datos, degradación del servicio.

  

15.8. Conclusiones de pruebas de rendimiento 

-Resume si el sistema cumple con los criterios de performance definidos.

-Señala hallazgos clave: estabilidad bajo carga, límites de concurrencia, riesgos detectados.

  

15.9. Recomendaciones de pruebas de rendimiento 

-Propone acciones de mejora: optimización de consultas, ajuste de infraestructura, configuración de servidores.

-Sugiere repetir pruebas tras aplicar mejoras para validar resultados.

**