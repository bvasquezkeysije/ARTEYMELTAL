**

4. # REQUERIMIENTOS DE SOFTWARE

## 4.1. Requerimientos funcionales

| ID | Nombre | Descripción | Actor | Prioridad | Fuente | Criterio de aceptación |
|---|---|---|---|---|---|---|
| RF-01 | Inicio de sesión | Permitir el acceso al sistema mediante correo electrónico o nombre de usuario y contraseña válidos. | Usuario | Alta | Análisis del sistema | El usuario autenticado es redirigido al dashboard; credenciales inválidas muestran error en modal. |
| RF-02 | Cierre de sesión | Permitir al usuario cerrar su sesión de forma segura. | Usuario | Alta | Análisis del sistema | Al cerrar sesión se invalida la sesión y se redirige al login. |
| RF-03 | Recuperación de contraseña por código | Restablecer la contraseña mediante un código numérico de 6 dígitos enviado al correo registrado. | Usuario | Alta | Análisis del sistema | El código es validado contra su expiración de 15 minutos; si es correcto, actualiza la contraseña. |
| RF-04 | Cambio de contraseña | Permitir al usuario autenticado actualizar su contraseña desde el perfil. | Usuario | Media | Análisis del sistema | Se solicita la contraseña actual y se confirma la nueva. |
| RF-05 | Gestión de usuarios | Crear, editar, listar y cambiar el estado activo/inactivo de usuarios del sistema. | Administrador | Alta | Análisis del sistema | CRUD funcional con validación de correo único y asignación de rol. |
| RF-06 | Activación/desactivación de usuarios | Cambiar el estado activo de un usuario, impidiendo el auto-bloqueo del propio usuario autenticado. | Administrador | Alta | Análisis del sistema | El sistema impide desactivar el propio usuario autenticado. |
| RF-07 | Gestión de roles | Crear, editar, listar y eliminar roles con su descripción. | Administrador | Alta | Análisis del sistema | No se permite eliminar roles con usuarios asignados. |
| RF-08 | Asignación de permisos a roles | Asociar permisos específicos a cada rol mediante panel de permisos. | Administrador | Alta | Análisis del sistema | Los cambios de permisos se reflejan inmediatamente en el acceso a rutas. |
| RF-09 | Panel de datos de roles | Obtener vía JSON la lista de roles, permisos y conteos de usuarios. | Administrador | Media | Análisis del sistema | Endpoint JSON devuelve datos correctos para el panel administrativo. |
| RF-10 | Dashboard principal | Mostrar métricas clave: pedidos totales, en producción, listos, ventas del día, clientes y productos. | Todos | Alta | Análisis del sistema | Las métricas reflejan los datos reales de la base de datos. |
| RF-11 | Gráfico de ventas | Mostrar el consolidado de ventas de los últimos 14 días. | Todos | Media | Análisis del sistema | Gráfico con fechas y montos correctos. |
| RF-12 | Gráfico de pedidos por estado | Mostrar la distribución de pedidos según su estado. | Todos | Media | Análisis del sistema | Gráfico con estados registrados, en producción, listos, entregados y cancelados. |
| RF-13 | Últimos registros | Listar los últimos 5 pedidos y últimas 5 ventas en el dashboard. | Todos | Baja | Análisis del sistema | Listados ordenados por fecha/ID descendente. |
| RF-14 | Gestión de productos | Crear, editar, listar, ver y eliminar productos del catálogo. | Administrador, Almacenero | Alta | Análisis del sistema | CRUD completo con generación automática de código PROD-. |
| RF-15 | Búsqueda y filtro de productos | Buscar productos por código, nombre o descripción y filtrar por categoría y estado activo. | Todos | Media | Análisis del sistema | Resultados paginados y filtrados correctamente. |
| RF-16 | Gestión de imágenes de productos | Subir, visualizar y eliminar imágenes asociadas a un producto. | Administrador, Almacenero | Media | Análisis del sistema | Se aceptan imágenes JPG, JPEG, PNG, WEBP hasta 10 MB. |
| RF-17 | Gestión de categorías de productos | Crear, editar, activar/inactivar categorías; al renombrar se actualizan los productos vinculados. | Administrador, Almacenero | Media | Análisis del sistema | CRUD de categorías con slug único y sincronización de productos. |
| RF-18 | Control de stock por ubicación | Registrar stock disponible en tienda y en almacén para cada producto. | Administrador, Almacenero | Alta | Análisis del sistema | El stock total es la suma de stock_tienda + stock_almacen. |
| RF-19 | Visualización de imagen de producto | Servir archivos de imagen almacenados con encabezados de cache. | Todos | Baja | Análisis del sistema | La imagen se muestra o devuelve 404 si no existe. |
| RF-20 | Gestión de clientes | Crear, editar, listar, ver y eliminar clientes. | Administrador, Vendedor | Alta | Análisis del sistema | CRUD completo con validación de documento único. |
| RF-21 | Búsqueda de clientes | Buscar clientes por nombre, teléfono, correo o documento. | Administrador, Vendedor | Media | Análisis del sistema | Resultados paginados y filtrados. |
| RF-22 | Consulta de DNI por RENIEC | Consultar datos de una persona mediante su DNI a través de Decolecta/RENIEC. | Vendedor, Administrador | Alta | Análisis del sistema | Si el servicio responde, carga nombre y documento; si falla, permite ingreso manual. |
| RF-23 | Consulta de RUC por SUNAT | Consultar datos de una empresa mediante su RUC a través de Decolecta/SUNAT. | Vendedor, Administrador | Alta | Análisis del sistema | Retorna razón social, dirección, distrito, estado y condición del contribuyente. |
| RF-24 | Consulta local de cliente por documento | Buscar cliente existente en la base de datos por DNI/RUC antes de consultar APIs externas. | Vendedor, Administrador | Media | Análisis del sistema | Si existe localmente, retorna los datos almacenados. |
| RF-25 | Gestión de pedidos | Crear, editar, listar, ver y eliminar pedidos personalizados. | Vendedor, Administrador, Diseñador, Orfebre | Alta | Análisis del sistema | CRUD completo con generación automática de código PED-. |
| RF-26 | Búsqueda y filtro de pedidos | Buscar pedidos por código, cliente, tipo o estado y filtrar por estado y personalización. | Vendedor, Administrador, Diseñador, Orfebre | Media | Análisis del sistema | Resultados paginados y filtrados. |
| RF-27 | Selección de caja para pedidos | Elegir una caja abierta del usuario para operar en el módulo de pedidos. | Vendedor, Administrador | Alta | Análisis del sistema | Sin caja abierta, el sistema impide crear/ver pedidos de venta. |
| RF-28 | Cambio de caja en pedidos | Permitir cambiar la caja seleccionada para pedidos. | Vendedor, Administrador | Media | Análisis del sistema | Al cambiar, se limpia la sesión y se solicita nueva selección. |
| RF-29 | Registro de datos del cliente en pedido | Asociar o crear cliente a partir del documento ingresado en el pedido. | Vendedor, Administrador | Alta | Análisis del sistema | Si no existe, se crea automáticamente; si existe, se vincula. |
| RF-30 | Adición de productos al pedido | Registrar múltiples productos personalizados con nombre, descripción, precio y cantidad. | Vendedor, Administrador | Alta | Análisis del sistema | Los totales se calculan automáticamente. |
| RF-31 | Cálculo de montos del pedido | Calcular monto total, adelanto del 50 % y saldo según tipo de pago contado o dos partes. | Vendedor, Administrador | Alta | Análisis del sistema | Contado cobra 100 %; dos partes cobra 50 % de adelanto. |
| RF-32 | Registro de adelanto de pedido | Al crear el pedido, registrar el pago del adelanto y generar la venta correspondiente. | Vendedor, Administrador | Alta | Análisis del sistema | Se crea venta tipo pedido con el monto del adelanto. |
| RF-33 | Emisión de comprobante de adelanto | Emitir boleta o factura automática según el tipo de documento del cliente. | Sistema | Alta | Análisis del sistema | Boleta para DNI/8 dígitos; factura para RUC/11 dígitos. |
| RF-34 | Gestión de archivos de orden de compra | Subir y eliminar archivos de orden asociados al pedido. | Vendedor, Administrador | Media | Análisis del sistema | Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF, WEBP hasta 15 MB. |
| RF-35 | Gestión de archivos por producto del pedido | Subir archivos de diseño/referencia por cada producto del pedido. | Vendedor, Administrador, Diseñador | Media | Análisis del sistema | Archivos almacenados en disco público y vinculados al producto. |
| RF-36 | Gestión de comprobantes de pago | Subir y eliminar fotos/PDF de comprobantes de pago del pedido. | Vendedor, Administrador | Media | Análisis del sistema | Formatos JPG, JPEG, PNG, GIF, WEBP, PDF hasta 5 MB. |
| RF-37 | Actualización de personalización | Modificar estado de personalización, fechas de diseño/aprobación y subir archivos de diseño. | Vendedor, Administrador, Diseñador, Orfebre | Alta | Análisis del sistema | Estados permitidos según rol del usuario. |
| RF-38 | Derivación de pedido a diseño | Cambiar el estado de personalización a "en_diseno" y notificar a diseñadores. | Vendedor, Administrador | Alta | Análisis del sistema | Solo disponible desde estado "sin_iniciar". |
| RF-39 | Derivación de pedido a producción | Cambiar el estado del pedido a "en_produccion" y notificar a orfebres. | Vendedor, Administrador | Alta | Análisis del sistema | Solo disponible desde estado "registrado". |
| RF-40 | Marcado de pedido en transporte | Permitir al repartidor marcar el pedido como "en_transporte" desde "listo_entrega". | Repartidor, Administrador | Alta | Análisis del sistema | Estado actualizado y visible en módulo de reparto. |
| RF-41 | Marcado de pedido en almacén | Permitir al almacenero marcar el pedido como "en_almacen" desde "en_transporte". | Almacenero, Administrador | Alta | Análisis del sistema | Estado actualizado y notificación al vendedor. |
| RF-42 | Confirmación de pago final | Registrar el pago del saldo restante del pedido y generar venta final. | Vendedor, Administrador | Alta | Análisis del sistema | Solo si hay saldo pendiente; se genera comprobante. |
| RF-43 | Autorización para recoger | Cobrar saldo pendiente y cambiar estado a "listo_recoger". | Vendedor, Administrador | Alta | Análisis del sistema | Requiere adelanto previo y saldo mayor a cero. |
| RF-44 | Validación de adelanto obligatorio | Impedir avanzar el pedido si no se ha pagado el adelanto del 50 %. | Sistema | Alta | Análisis del sistema | Muestra error si se intenta cambiar estado sin adelanto pagado. |
| RF-45 | Módulo de diseño | Listar y ver pedidos en estados "en_diseno" o "en_revision". | Diseñador, Administrador | Alta | Análisis del sistema | Listado filtrado por estado con búsqueda. |
| RF-46 | Subida de archivos de diseño | Subir archivos de diseño en formatos CDR, PDF, PNG, JPG, SVG, AI, EPS, PSD, WEBP. | Diseñador, Administrador | Alta | Análisis del sistema | Archivos asociados al pedido y producto correspondiente. |
| RF-47 | Eliminación de archivos de diseño | Eliminar archivos de diseño subidos previamente. | Diseñador, Administrador | Media | Análisis del sistema | Se elimina registro y archivo físico. |
| RF-48 | Notificación de diseño listo | Notificar a vendedores/administradores que el diseño está listo para revisión. | Diseñador, Administrador | Media | Análisis del sistema | Se genera notificación interna con enlace al pedido. |
| RF-49 | Módulo de producción | Listar y ver pedidos en estados "en_produccion" o "produciendo". | Orfebre, Administrador | Alta | Análisis del sistema | Listado filtrado con búsqueda. |
| RF-50 | Inicio de producción | Cambiar el estado del pedido de "en_produccion" a "produciendo". | Orfebre, Administrador | Alta | Análisis del sistema | Notifica al vendedor que inició producción. |
| RF-51 | Notificación de pedido listo para entrega | Cambiar estado a "listo_entrega" y notificar a repartidores. | Orfebre, Administrador | Alta | Análisis del sistema | Notificación interna con enlace al reparto. |
| RF-52 | Módulo de reparto | Listar y ver pedidos en estados "listo_entrega" o "en_transporte". | Repartidor, Administrador | Alta | Análisis del sistema | Listado filtrado con búsqueda. |
| RF-53 | Recojo de pedido en producción | Registrar las cantidades recogidas de cada producto y marcar como "en_transporte". | Repartidor, Administrador | Alta | Análisis del sistema | La cantidad recogida no supera la cantidad del pedido. |
| RF-54 | Entrega de pedido en almacén | Marcar pedido como "en_almacen" y notificar a almaceneros. | Repartidor, Administrador | Alta | Análisis del sistema | Estado actualizado y notificación enviada. |
| RF-55 | Dashboard de almacén | Visualizar pedidos pendientes, listos para recoger, stock total y movimientos del día. | Almacenero, Administrador | Alta | Análisis del sistema | Métricas de entradas, salidas y stock actualizadas. |
| RF-56 | Gestión de inventario | Listar productos con filtros por categoría, stock bajo, sin stock y con stock. | Almacenero, Administrador | Alta | Análisis del sistema | Resultados paginados con indicadores de stock. |
| RF-57 | Registro de entrada de almacén | Registrar ingreso de unidades a tienda o almacén y actualizar stock. | Almacenero, Administrador | Alta | Análisis del sistema | Se crea movimiento de tipo entrada con stock resultante. |
| RF-58 | Registro de salida de almacén | Registrar egreso de unidades de tienda o almacén validando stock suficiente. | Almacenero, Administrador | Alta | Análisis del sistema | Aborta si no hay stock suficiente; crea movimiento de salida. |
| RF-59 | Historial de movimientos de almacén | Listar movimientos filtrados por tipo, producto y rango de fechas. | Almacenero, Administrador | Media | Análisis del sistema | Resultados paginados con usuario y stock resultante. |
| RF-60 | Recepción de pedido en almacén | Registrar la entrada física de un pedido transportado y crear producto/stock si no existe. | Almacenero, Administrador | Alta | Análisis del sistema | Estado cambia a "listo_recoger"; se generan movimientos de entrada. |
| RF-61 | Entrega de pedido al cliente | Marcar pedido como "entregado" y descontar stock del almacén. | Almacenero, Administrador | Alta | Análisis del sistema | Requiere pago completo; genera movimientos de salida y notifica al vendedor. |
| RF-62 | Gestión de ventas directas | Crear ventas directas de productos con stock en tienda. | Vendedor, Administrador | Alta | Análisis del sistema | Descuenta stock_tienda y genera comprobante. |
| RF-63 | Listado y búsqueda de ventas | Listar ventas, buscar por código, cliente o tipo y filtrar por tipo. | Vendedor, Administrador | Media | Análisis del sistema | Resultados paginados por caja seleccionada. |
| RF-64 | Selección de caja para ventas | Elegir una caja abierta del usuario para operar en el módulo de ventas. | Vendedor, Administrador | Alta | Análisis del sistema | Sin caja abierta no se permite acceder a ventas. |
| RF-65 | Cambio de caja en ventas | Permitir cambiar la caja seleccionada para ventas. | Vendedor, Administrador | Media | Análisis del sistema | Se limpia la sesión y se solicita nueva selección. |
| RF-66 | Cálculo de vuelto en ventas | Calcular automáticamente el vuelto para pagos en efectivo. | Vendedor, Administrador | Media | Análisis del sistema | Vuelto = monto recibido - total de la venta. |
| RF-67 | Validación de comprobante de venta | Validar datos del cliente según el tipo de comprobante boleta/factura. | Sistema | Alta | Análisis del sistema | Factura requiere RUC de 11 dígitos y razón social. |
| RF-68 | Emisión manual de comprobante | Permitir emitir comprobante para ventas que no lo tengan. | Vendedor, Administrador | Media | Análisis del sistema | Genera comprobante con tipo según documento del pedido. |
| RF-69 | Generación de PDF de comprobante | Generar y descargar comprobante en PDF para una venta. | Vendedor, Administrador | Alta | Análisis del sistema | PDF en formato A4 con datos de venta, detalles y comprobante. |
| RF-70 | Gestión de cajas | Listar cajas predefinidas, editar nombre/estado y eliminar cajas sin aperturas. | Vendedor, Administrador | Media | Análisis del sistema | No se elimina caja con registros de apertura. |
| RF-71 | Apertura de caja | Registrar apertura de caja con monto inicial y observaciones. | Vendedor, Administrador | Alta | Análisis del sistema | No permite abrir una caja ya abierta. |
| RF-72 | Cierre de caja | Registrar cierre de caja con monto final y cálculo automático de total ventas. | Vendedor, Administrador | Alta | Análisis del sistema | Al cerrar, se limpia la sesión si es la caja activa. |
| RF-73 | Detalle de caja | Visualizar ventas asociadas a una apertura de caja con totales por medio de pago. | Vendedor, Administrador | Media | Análisis del sistema | Muestra efectivo, digital, vuelto y cantidad de ventas. |
| RF-74 | Reporte de ventas | Visualizar KPIs y listado de ventas filtradas por fecha y tipo. | Todos con permiso | Alta | Análisis del sistema | Total vendido, cobrado, ticket promedio y cantidad. |
| RF-75 | Reporte de pedidos | Visualizar KPIs y listado de pedidos filtrados por fecha, estado y tipo de entrega. | Todos con permiso | Alta | Análisis del sistema | Conteos por estado y pedidos atrasados. |
| RF-76 | Reporte de saldos pendientes | Listar pedidos con saldo pendiente y monto total adeudado. | Todos con permiso | Alta | Análisis del sistema | Muestra adelanto, saldo y porcentaje cancelado. |
| RF-77 | Reporte de stock bajo | Listar productos con stock igual o inferior al umbral configurable. | Todos con permiso | Alta | Análisis del sistema | Umbral por defecto 10; conteo de productos y unidades en riesgo. |
| RF-78 | Gráficos del reporte | Mostrar gráficos de ventas por día, ventas por tipo, pedidos por estado, pedidos por entrega y stock crítico. | Todos con permiso | Media | Análisis del sistema | Gráficos renderizados con datos filtrados. |
| RF-79 | Exportación de reportes a CSV | Exportar reportes de ventas, pedidos, saldos y stock en formato CSV. | Todos con permiso | Media | Análisis del sistema | Archivo CSV con codificación UTF-8 y separador punto y coma. |
| RF-80 | Exportación de reportes a Excel | Exportar reportes de ventas, pedidos, saldos y stock en formato XLSX. | Todos con permiso | Media | Análisis del sistema | Archivo Excel válido con encabezados y autofiltro. |
| RF-81 | Centro de notificaciones | Listar notificaciones internas del usuario ordenadas por fecha. | Todos | Alta | Análisis del sistema | Paginación de 20 notificaciones. |
| RF-82 | Notificaciones no leídas | Obtener conteo y listado de notificaciones no leídas vía JSON. | Todos | Alta | Análisis del sistema | Endpoint JSON devuelve count y listado. |
| RF-83 | Marcado de notificación como leída | Cambiar estado de una notificación a leída. | Todos | Media | Análisis del sistema | Solo el propietario puede marcarla como leída. |
| RF-84 | Marcado de todas las notificaciones como leídas | Marcar todas las notificaciones no leídas del usuario como leídas. | Todos | Media | Análisis del sistema | Actualización masiva con fecha de lectura. |
| RF-85 | Generación automática de notificaciones | Crear notificaciones automáticas ante derivaciones a diseño, producción, reparto y entregas. | Sistema | Alta | Análisis del sistema | Notificaciones enviadas a roles correspondientes. |
| RF-86 | Configuración de perfil | Editar nombre y correo del perfil del usuario autenticado. | Todos | Media | Análisis del sistema | Al cambiar el correo se invalida la verificación. |
| RF-87 | Eliminación de cuenta | Permitir al usuario eliminar su propia cuenta confirmando contraseña actual. | Todos | Baja | Análisis del sistema | Se cierra sesión, invalida sesión y elimina usuario. |

## 4.2. Requerimientos no funcionales

| ID | Categoría | Requerimiento | Métrica | Prioridad | Fuente |
|---|---|---|---|---|---|
| RNF-01 | Seguridad | Autenticación basada en sesiones con Laravel Breeze. | Sesión válida mediante cookie segura; cierre al cambiar contraseña. | Alta | Análisis del sistema |
| RNF-02 | Seguridad | Encriptación de contraseñas con bcrypt/Hash de Laravel. | No se almacenan contraseñas en texto plano. | Alta | Análisis del sistema |
| RNF-03 | Seguridad | Protección contra CSRF en todos los formularios. | Presencia de token CSRF en formularios POST/PUT/DELETE. | Alta | Análisis del sistema |
| RNF-04 | Seguridad | Prevención de inyección SQL mediante uso de Eloquent y query builder con parámetros preparados. | Sin consultas concatenadas directamente con entrada de usuario. | Alta | Análisis del sistema |
| RNF-05 | Seguridad | Validación de tipos y rangos en todos los endpoints, incluyendo IDs numéricos y estados permitidos. | Rechazo de valores fuera de dominio con error 422. | Alta | Análisis del sistema |
| RNF-06 | Control de acceso | Modelo RBAC con roles y permisos granularizados por módulo. | 6 roles y 24 permisos definidos en SecuritySeeder. | Alta | Análisis del sistema |
| RNF-07 | Control de acceso | Middleware de autenticación, usuario activo y permiso antes de ejecutar controladores. | Aborto 403 ante acceso no autorizado. | Alta | Análisis del sistema |
| RNF-08 | Control de acceso | Restricción de acciones sensibles según rol (ej. repartidor solo transporte, almacenero solo almacén). | Verificación de rol en métodos críticos del backend. | Alta | Análisis del sistema |
| RNF-09 | Protección de datos | Almacenamiento de rutas de archivos, no binarios, en base de datos. | Registros con archivo_path y metadatos; archivos en storage/app/public. | Media | Análisis del sistema |
| RNF-10 | Protección de datos | Validación de archivos subidos: tipos MIME, extensiones y tamaños máximos. | Imágenes hasta 10 MB, diseños hasta 10 MB, comprobantes hasta 5 MB. | Alta | Análisis del sistema |
| RNF-11 | Protección de datos | No exposición de datos sensibles en respuestas JSON (ej. no retornar contraseñas). | Respuestas JSON filtradas sin campos hidden. | Alta | Análisis del sistema |
| RNF-12 | Disponibilidad | El sistema debe estar disponible durante el horario comercial de la joyería. | Uptime objetivo ≥ 99.5 % en horario de atención. | Alta | Requisito del negocio |
| RNF-13 | Disponibilidad | Recuperación ante caídas del servicio sin pérdida de transacciones en curso. | Uso de transacciones DB::transaction en operaciones críticas. | Alta | Análisis del sistema |
| RNF-14 | Usabilidad | Interfaz en español con terminología del negocio de joyería y metalistería. | 100 % de etiquetas, mensajes y notificaciones en español. | Alta | Requisito del negocio |
| RNF-15 | Usabilidad | Diseño responsive que permita uso en tablets y computadoras del taller. | Layout adaptativo sin scroll horizontal en resoluciones ≥ 768 px. | Media | Requisito del negocio |
| RNF-16 | Usabilidad | Uso de iconografía consistente y botones sin texto para acciones principales. | Iconos estandarizados según convenciones del proyecto. | Baja | Análisis del sistema |
| RNF-17 | Rendimiento | Tiempo de carga de páginas principales inferior a 3 segundos en red local. | Paginación de 10-15 registros y carga diferida de imágenes. | Alta | Requisito del negocio |
| RNF-18 | Rendimiento | Consultas a APIs externas RENIEC/SUNAT con timeout de 15 segundos. | Fallback a ingreso manual si el servicio externo falla. | Alta | Análisis del sistema |
| RNF-19 | Rendimiento | Generación de reportes y exportaciones no debe bloquear la interfaz de usuario. | Exportaciones devueltas como streamed response. | Media | Análisis del sistema |
| RNF-20 | Mantenibilidad | Código organizado en controladores por módulo y modelos con relaciones Eloquent. | Estructura de archivos según estándares Laravel. | Media | Análisis del sistema |
| RNF-21 | Mantenibilidad | Uso de migraciones de base de datos para control de cambios estructurales. | Cada cambio de esquema debe reflejarse en una migración. | Alta | Análisis del sistema |
| RNF-22 | Mantenibilidad | Separación de responsabilidades mediante servicios (ej. ComprobanteVentaService). | Lógica de comprobantes centralizada en servicio dedicado. | Media | Análisis del sistema |
| RNF-23 | Escalabilidad | Arquitectura web stateless que permita escalar horizontalmente la capa de aplicación. | Sesiones manejadas por Laravel en servidor; sin estado en aplicación. | Media | Requisito técnico |
| RNF-24 | Escalabilidad | Base de datos PostgreSQL compatible con crecimiento de registros de pedidos y ventas. | Índices en campos de búsqueda frecuente (código, cliente, estado). | Media | Análisis del sistema |
| RNF-25 | Backup | Respaldo diario automatizado de la base de datos y archivos de storage. | RPO ≤ 24 horas; retención mínima de 7 días. | Alta | Requisito del negocio |
| RNF-26 | Trazabilidad | Registro de movimientos de almacén con usuario, fecha y stock resultante. | Cada entrada/salida almacena usuario_id y stock_resultante. | Alta | Análisis del sistema |
| RNF-27 | Trazabilidad | Auditoría de ventas vinculadas a caja de apertura y usuario que registró la venta. | Campos usuario_id y caja_apertura_id obligatorios en ventas. | Alta | Análisis del sistema |
| RNF-28 | Cumplimiento | Emisión de comprobantes de pago discriminados como boleta o factura según documento del cliente. | DNI genera boleta; RUC genera factura. | Alta | Requisito legal |
| RNF-29 | Cumplimiento | Integración con consulta de RUC SUNAT para validación de contribuyentes. | Respuesta de API Decolecta/SUNAT con datos oficiales. | Media | Análisis del sistema |
| RNF-30 | Confiabilidad | Validación de reglas de negocio en el backend independientemente de la interfaz. | Pagos, estados y permisos validados server-side. | Alta | Análisis del sistema |
| RNF-31 | Compatibilidad | Navegadores modernos: Chrome, Firefox, Edge y Safari en últimas 2 versiones. | Funcionalidad correcta en navegadores indicados. | Media | Requisito técnico |
| RNF-32 | Compatibilidad | Generación de PDF con biblioteca DomPDF compatible con PHP 8.2+. | PDFs renderizados correctamente en A4. | Media | Análisis del sistema |
| RNF-33 | Soporte | Documentación de despliegue, roles, permisos y flujo operativo en AGENTS.md. | Información actualizada y accesible al equipo técnico. | Baja | Análisis del sistema |

**
