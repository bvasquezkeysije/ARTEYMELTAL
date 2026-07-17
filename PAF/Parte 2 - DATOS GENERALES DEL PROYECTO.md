2. # DATOS GENERALES DEL PROYECTO
## 2.1. Nombre del proyecto

Evaluación de la Calidad del Sistema de Gestión Empresarial “Arte y Metal” Aplicando Procesos de Software Testing

## 2.2. Integrantes 

- Campos Saucedo GianCarlo
    
- Chapoñan Alayo Rut
    
- Piñin Fernandez Adelith
    
- Temoche Cumpa Mario Ismael
    
- Silupu Becerra Nilson Jesus
    

## 2.3. Software a evaluar 

Sistema de Gestión Empresarial “Arte y Metal”

## 2.4. Descripción funcional

El sistema **Arte y Metal** es una plataforma web de gestión empresarial desarrollada para la empresa Arte y Metal Chiclayo E.I.R.L., dedicada a la fabricación y comercialización de productos personalizados de arte y metal. El sistema permite administrar de manera integral los procesos operativos, comerciales y productivos de la empresa.

Las funcionalidades principales del sistema son:

- **Gestión de usuarios y roles:** Control de acceso basado en roles (administrador, vendedor, diseñador, orfebre, repartidor y almacenero), con permisos específicos por módulo.
- **Autenticación y seguridad:** Inicio de sesión, cierre de sesión y recuperación de contraseña mediante código enviado por correo electrónico.
- **Catálogo de productos:** Registro, edición y gestión de productos del catálogo, incluyendo imágenes, categorías, stock en tienda, stock en almacén y precios de referencia.
- **Pedidos personalizados:** Flujo completo de pedidos desde la cotización, diseño, aprobación, producción, transporte, almacén y entrega, con control de estados y pagos.
- **Ventas directas:** Registro de ventas de productos en stock, generación de comprobantes de pago y control de caja.
- **Control de caja:** Apertura y cierre de caja por turno, con seguimiento de montos iniciales, finales y totales de ventas.
- **Gestión de clientes:** Registro de clientes con consulta de documentos de identidad mediante la API de Decolecta (RENIEC).
- **Producción:** Asignación y seguimiento de tareas de producción por parte de los orfebres.
- **Repartos:** Control del traslado de productos terminados desde producción hasta el almacén.
- **Almacén:** Registro de entradas, salidas y movimientos de inventario, con trazabilidad de stock.
- **Reportes y estadísticas:** Generación de reportes de ventas, pedidos, stock y saldos en formatos CSV y Excel.
- **Notificaciones:** Sistema de notificaciones en tiempo real para informar cambios de estado y eventos relevantes a los usuarios.

El sistema está desarrollado con el framework Laravel 10, utiliza PostgreSQL como base de datos, Tailwind CSS y Alpine.js para la interfaz de usuario, y se despliega en un servidor en la nube con Nginx y PHP-FPM.