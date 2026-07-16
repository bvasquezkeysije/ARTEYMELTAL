**
**RECONOCIMIENTO DE RECURSOS Y ARQUITECTURA DE SOFTWARE**

Equipo

El equipo está conformado por los integrantes del proyecto, quienes se reparten las tareas de programación, documentación y ejecución de pruebas. Cada integrante asume un rol específico según sus habilidades, permitiendo un trabajo colaborativo y ordenado durante las etapas de evaluación del sistema.

Software

Para el desarrollo del frontend se utiliza Vite junto con HTML, CSS y JavaScript, lo que permite un entorno de desarrollo ágil y una validación en tiempo real de los datos ingresados en los formularios. Para el backend se emplea Laravel como framework principal, encargado del manejo de rutas, controladores y la lógica de negocio del sistema. Para el almacenamiento de datos se utiliza PostgreSQL, y todo el entorno se gestiona mediante contenedores Docker, lo que permite mantener una configuración estandarizada tanto en el entorno local como en el servidor en la nube.

Hardware e infraestructura de pruebas

Para la ejecución de las pruebas se utilizará una laptop personal como estación de trabajo desde donde se coordinan, documentan y lanzan las pruebas. Sin embargo, las pruebas se ejecutan directamente sobre el servidor en la nube donde está desplegado el sistema, siendo este el objetivo o "target" real de validación.

La laptop debe contar como mínimo con sistema operativo Windows 10 o superior, 8 GB de memoria RAM y conexión a internet estable para el desarrollo, coordinación y acceso remoto al servidor.

Adicionalmente, se contará con un servidor virtual en la nube destinado a la ejecución de pruebas en un entorno más cercano a producción, con las siguientes características:

| Recurso           | Especificación   |
| ----------------- | ---------------- |
| Sistema operativo | Ubuntu 24.04 LTS |
| Memoria RAM       | 8 GB             |
| Almacenamiento    | 75 GB            |
| Contenedores      | Docker           |
| Backend           | Laravel          |
| Frontend          | Vite             |
| Base de datos     | PostgreSQL       |

Con la finalidad de mantener consistencia, el código y configuraciones se sincronizan entre el ambiente local (laptop) y el servidor en la nube. Dado que las pruebas se ejecutan directamente sobre el servidor en la nube, los resultados reflejan el comportamiento real del sistema en producción y se reducen las inconsistencias entre desarrollo, QA y producción.

**