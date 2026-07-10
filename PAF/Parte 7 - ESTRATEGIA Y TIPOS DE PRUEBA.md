**

7. # ESTRATEGIA Y TIPOS DE PRUEBA
    

## 7.1. Cuadro de estrategia:


| Ambientes de prueba | - Desarrollo: pruebas preliminares realizadas por el equipo de desarrollo del sistema.<br><br>- QA (Calidad): ejecución controlada de pruebas a cargo del equipo responsable de calidad.<br><br>- Producción: pruebas finales previas a la puesta en marcha del sistema en la empresa "Arte y Metal".                                                                                      |
| ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Datos de prueba     | - Se utilizarán datos simulados y controlados que representen casos reales de uso, tales como registros de clientes, pedidos personalizados, productos, ventas y usuarios.                                                                                                                                                                                                                 |
| Cobertura de prueba | - Se cubrirán todos los criterios de aceptación de cada historia de usuario (HU01 a HU10).<br><br>- Se incluirán casos de éxito, error, validaciones de campos y flujos alternativos.<br><br>- El objetivo es alcanzar una cobertura del 100% de las funcionalidades definidas para los módulos de inicio de sesión, gestión de usuarios, clientes, productos, pedidos, ventas y reportes. |

## 7.2. Tipos de pruebas 

  

| Tipo de prueba         | Aplicación en el proyecto                                                                                                                                                                                               |
| ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Pruebas unitarias      | Verificación individual de cada función desarrollada por los programadores, especialmente para validar la lógica y el comportamiento esperado de módulos como inicio de sesión, registro de pedidos y cálculo de pagos. |
| Pruebas funcionales    | Validan que el sistema cumpla los requisitos descritos en cada historia de usuario, incluyendo interacciones en formularios, validaciones de datos y procesos CRUD sobre clientes, productos y usuarios.                |
| Pruebas de validación  | Se revisan los datos ingresados, los mensajes de error esperados y el comportamiento del sistema ante situaciones límite, como campos vacíos, credenciales incorrectas o datos inconsistentes.                          |
| Pruebas de integración | Verifican la correcta interacción entre los diferentes módulos del sistema, como clientes, productos, pedidos, ventas, pagos y reportes.                                                                                |
| Pruebas de regresión   | Se ejecutan tras ajustes o correcciones realizadas en el sistema, para asegurar que los cambios no generen fallos en otras funcionalidades ya validadas previamente.                                                    |



**