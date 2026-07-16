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

## 16.4. Recomendaciones

1. **Automatización continua:** Integrar las pruebas automatizadas en un pipeline CI/CD para ejecutarlas en cada push a la rama principal.
2. **Monitoreo en producción:** Implementar herramientas de monitoreo como Laravel Telescope o Sentry para detectar errores en tiempo real.
3. **Optimización de rendimiento:** Aplicar las recomendaciones de la Parte 15 (cache, Gzip, ajuste de PHP-FPM) antes de escalar el tráfico.
4. **Pruebas de seguridad:** Realizar auditorías de seguridad (pentesting) y análisis de vulnerabilidades OWASP Top 10.
5. **Métricas de negocio:** Ampliar los reportes con métricas de productividad, rentabilidad por producto y tiempos de entrega.
6. **Capacitación:** Capacitar a los usuarios finales del sistema para garantizar la adopción correcta de los flujos de trabajo.
7. **Mejoras futuras:** Considerar la implementación de un panel de cliente, notificaciones push y una aplicación móvil para ampliar la accesibilidad.

## 16.5. Trabajos futuros

| Propuesta | Descripción | Prioridad |
| --- | --- | --- |
| Panel de cliente | Permitir a los clientes consultar el estado de sus pedidos sin acceder al panel administrativo. | Alta |
| Aplicación móvil | Desarrollar una app complementaria para notificaciones y seguimiento de pedidos. | Media |
| Inteligencia de negocio | Agregar dashboards con KPIs y análisis predictivo de ventas. | Media |
| Integración con facturación electrónica | Conectar con SUNAT para emisión de comprobantes electrónicos. | Alta |
| Chat interno | Habilitar mensajería entre clientes, diseñadores y producción. | Baja |

## 16.6. Reflexión final

El proyecto **ARTE Y METAL** cumplió con los objetivos planteados: se construyó un sistema funcional, se validó mediante pruebas estructuradas y se documentó un Plan de Aseguramiento de la Funcionalidad completo. La metodología aplicada, basada en historias de usuario, casos de prueba y seguimiento de defectos, aseguró que el producto final sea confiable y alineado con las necesidades del negocio.

## 16.7. Referencias

A continuación se presentan las referencias bibliográficas utilizadas para la elaboración de este documento, en formato IEEE.

[1] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 8th ed. New York, NY, USA: McGraw-Hill, 2015.

[2] G. J. Myers, C. Sandler, and T. Badgett, *The Art of Software Testing*, 3rd ed. Hoboken, NJ, USA: John Wiley & Sons, 2011.

[3] K. Beck, *Test Driven Development: By Example*. Boston, MA, USA: Addison-Wesley Professional, 2002.

[4] J. A. Whittaker, "What is software testing? And why is it so hard?," *IEEE Software*, vol. 17, no. 1, pp. 70-79, Jan./Feb. 2000.

[5] H. Zhu, P. A. V. Hall, and J. H. R. May, "Software unit test coverage and adequacy," *ACM Computing Surveys*, vol. 29, no. 4, pp. 366-427, Dec. 1997.

[6] V. R. Basili and B. T. Perricone, "Software errors and complexity: An empirical investigation," *Commun. ACM*, vol. 27, no. 1, pp. 42-52, Jan. 1984.

[7] B. Kitchenham, "Procedures for performing systematic reviews," Keele University, Staffordshire, U.K., Tech. Rep. TR/SE-0401, 2004. [Online]. Available: https://www.keele.ac.uk/cormack/spare.pdf

[8] N. Nagappan, E. M. Maximilien, T. Bhat, and L. Williams, "Realizing quality improvement through test driven development: results and experiences of four industrial teams," *Empirical Software Engineering*, vol. 13, no. 3, pp. 289-302, 2008.

[9] E. Daka and G. Fraser, "A survey on unit testing practices and problems," in *Proc. 2014 IEEE 25th Int. Symp. Software Reliability Engineering (ISSRE)*, Naples, Italy, 2014, pp. 201-211.

[10] D. Hamlet and R. Taylor, "Partition testing does not inspire confidence," *IEEE Trans. Software Eng.*, vol. 16, no. 12, pp. 1402-1411, Dec. 1990.

**Nota:** Las referencias [4], [5], [6], [8], [9] y [10] provienen de revistas y conferencias indexadas en Scopus, Web of Science e IEEE Xplore. Las referencias [1], [2] y [3] son obras de consulta clásicas en ingeniería de software. La referencia [7] es un informe técnico de acceso abierto disponible en línea.

**
