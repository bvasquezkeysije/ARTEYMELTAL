**

16. # CONCLUSIONES

## 16.1. Conclusiones generales

El desarrollo y validación del sistema **ARTE Y METAL** permitió implementar una plataforma web integral para la gestión de pedidos personalizados, ventas, producción, almacén y repartos de una empresa de arte y metal. A lo largo del proyecto se completaron las siguientes conclusiones:

1. **Cobertura funcional completa:** El sistema cubre los 10 procesos principales identificados en las historias de usuario H01 a H10, incluyendo autenticación, catálogo, carrito, pedidos, ventas, diseño, producción, almacén, reportes y notificaciones.

2. **Pruebas exitosas:** Se ejecutaron 37 casos de prueba entre pruebas unitarias, manuales, automatizadas, de validación, integración y regresión, alcanzando un 100 % de ejecución y un 100 % de criterios de aceptación validados.

3. **Gestión de defectos:** Se registraron 5 defectos durante el periodo de pruebas, todos de severidad media o baja, los cuales fueron corregidos y validados antes del cierre de la etapa de pruebas.

4. **Rendimiento aceptable:** Las pruebas de rendimiento demostraron que el sistema responde de manera estable bajo carga normal y moderada, con tiempos de respuesta promedio dentro de los límites establecidos.

5. **Documentación completa:** El Plan de Aseguramiento de la Funcionalidad (PAF) queda documentado con todas las partes requeridas, desde el alcance y requerimientos hasta las conclusiones y referencias.

6. **Arquitectura sostenible:** El uso de Laravel, PostgreSQL, Tailwind CSS y Alpine.js proporciona una arquitectura modular, mantenible y escalable para futuras mejoras.

7. **Integración externa:** La consulta de documentos de identidad mediante la API de Decolecta agiliza el registro de clientes y reduce errores de captura manual.

8. **Control de calidad:** La centralización de modales de retroalimentación, la estandarización de galerías y la validación de estados garantizan una experiencia de usuario consistente y confiable.

## 16.2. Conclusiones por módulo

| Módulo | Conclusión |
| --- | --- |
| Autenticación | El inicio de sesión y recuperación de contraseña por correo funcionan correctamente y cumplen con los requisitos de seguridad básica. |
| Catálogo | Los productos se gestionan, filtran y visualizan de forma eficiente, incluyendo galerías de imágenes. |
| Pedidos | El flujo de pedidos personalizados cubre desde la cotización hasta la entrega, con estados bien definidos y trazables. |
| Ventas | La integración con cajas, comprobantes de pago y comprobantes electrónicos es funcional y completa. |
| Diseño | Los diseñadores pueden subir modelos y los clientes aprobar o solicitar revisiones de manera controlada. |
| Producción | La asignación de tareas y el seguimiento del estado de producción son claros y operativos. |
| Almacén | La recepción, almacenamiento y despacho de productos se registran correctamente. |
| Reportes | Los reportes de ventas y pedidos proporcionan información útil para la toma de decisiones. |
| Notificaciones | El sistema de notificaciones en tiempo real mantiene informados a los usuarios sobre eventos relevantes. |

## 16.3. Conclusiones técnicas

- **Laravel 10 + PHP 8.2** demostraron ser una pila tecnológica robusta para aplicaciones empresariales de mediana complejidad.
- **PostgreSQL** cumplió como motor de base de datos relacional, garantizando integridad referencial y buen rendimiento en consultas.
- **Tailwind CSS + Alpine.js** permitieron una interfaz moderna, responsiva y con comportamiento dinámico sin dependencia pesada de JavaScript.
- **Git y GitHub** facilitaron el control de versiones, la colaboración y la sincronización entre el entorno local y el servidor en la nube.
- **Python + Playwright** resultaron adecuados para la automatización de pruebas funcionales del sistema web.

## 16.4. Reflexión final

El proyecto **ARTE Y METAL** cumplió con los objetivos planteados: se construyó un sistema funcional, se validó mediante pruebas estructuradas y se documentó un Plan de Aseguramiento de la Funcionalidad completo. La metodología aplicada, basada en historias de usuario, casos de prueba y seguimiento de defectos, aseguró que el producto final sea confiable y alineado con las necesidades del negocio.

**
