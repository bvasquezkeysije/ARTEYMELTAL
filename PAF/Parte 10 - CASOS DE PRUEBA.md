**

10. # CASOS DE PRUEBA

**Caso de Prueba N° 01**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP01-HU01 |
| Título | Verificar el inicio de sesión con credenciales válidas |
| Precondiciones | El usuario debe estar registrado y activo en la aplicación. |
| Datos de Entrada | Correo: bvasquezkeysije@gmail.com<br>Contraseña: [contraseña válida] |
| Pasos a Seguir | 1. Navegar a la página de inicio de sesión.<br>2. Ingresar el correo.<br>3. Ingresar la contraseña.<br>4. Hacer clic en 'Iniciar sesión'. |
| Resultado Esperado | El sistema autentica al usuario y redirige al panel principal correspondiente a su rol. |
| Resultado Real | El usuario accede correctamente al panel principal. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 02**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP02-HU01 |
| Título | Verificar el inicio de sesión con credenciales incorrectas |
| Precondiciones | El usuario debe estar registrado en la aplicación. |
| Datos de Entrada | Correo: bvasquezkeysije@gmail.com<br>Contraseña: contrasenaIncorrecta123 |
| Pasos a Seguir | 1. Navegar a la página de inicio de sesión.<br>2. Ingresar el correo.<br>3. Ingresar una contraseña incorrecta.<br>4. Hacer clic en 'Iniciar sesión'. |
| Resultado Esperado | El sistema rechaza el acceso y muestra un mensaje de error en un modal emergente. |
| Resultado Real | Aparece el modal de error indicando credenciales incorrectas. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 03**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP03-HU01 |
| Título | Verificar la validación de campos obligatorios en login |
| Precondiciones | El usuario está en la página de inicio de sesión. |
| Datos de Entrada | Correo: (vacío)<br>Contraseña: (vacío) |
| Pasos a Seguir | 1. Navegar al login.<br>2. Dejar el correo y la contraseña vacíos.<br>3. Hacer clic en 'Iniciar sesión'. |
| Resultado Esperado | El sistema rechaza la acción y muestra avisos de validación en los campos del formulario. |
| Resultado Real | Se muestran los mensajes de validación en los campos requeridos. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 04**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP04-HU01 |
| Título | Verificar el bloqueo de usuario inactivo |
| Precondiciones | El usuario debe estar registrado pero con estado inactivo. |
| Datos de Entrada | Correo: usuario_inactivo@gmail.com<br>Contraseña: [contraseña válida] |
| Pasos a Seguir | 1. Navegar al login.<br>2. Ingresar las credenciales de un usuario inactivo.<br>3. Hacer clic en 'Iniciar sesión'. |
| Resultado Esperado | El sistema impide el acceso e informa que la cuenta está desactivada. |
| Resultado Real | El sistema muestra mensaje de cuenta desactivada. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 05**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP05-HU02 |
| Título | Verificar el envío de código de recuperación de contraseña |
| Precondiciones | El usuario debe estar registrado y activo. |
| Datos de Entrada | Correo: bvasquezkeysije@gmail.com |
| Pasos a Seguir | 1. Navegar a 'Olvidaste tu contraseña'.<br>2. Ingresar el correo registrado.<br>3. Hacer clic en 'Enviar código'. |
| Resultado Esperado | El sistema genera un código de 6 dígitos y lo envía al correo indicado. |
| Resultado Real | El correo con el código llega a la bandeja de entrada. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa; código recibido correctamente. |

**Caso de Prueba N° 06**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP06-HU02 |
| Título | Verificar el restablecimiento de contraseña con código válido |
| Precondiciones | El usuario recibió el código de 6 dígitos en su correo. |
| Datos de Entrada | Código: 123456<br>Nueva contraseña: NuevaPass123!<br>Confirmación: NuevaPass123! |
| Pasos a Seguir | 1. Ingresar el código recibido.<br>2. Ingresar la nueva contraseña.<br>3. Confirmar la nueva contraseña.<br>4. Hacer clic en 'Restablecer'. |
| Resultado Esperado | El sistema actualiza la contraseña y redirige al login con mensaje de éxito. |
| Resultado Real | Se muestra el modal de éxito y se redirige al login. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 07**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP07-HU02 |
| Título | Verificar el rechazo de código incorrecto o expirado |
| Precondiciones | El usuario intenta restablecer con un código incorrecto. |
| Datos de Entrada | Código: 000000<br>Nueva contraseña: NuevaPass123! |
| Pasos a Seguir | 1. Ingresar un código incorrecto.<br>2. Ingresar la nueva contraseña.<br>3. Hacer clic en 'Restablecer'. |
| Resultado Esperado | El sistema rechaza la acción y solicita un nuevo código. |
| Resultado Real | Aparece mensaje de código inválido o expirado. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 08**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP08-HU03 |
| Título | Verificar la creación de un pedido personalizado con datos válidos |
| Precondiciones | El vendedor tiene una caja abierta y seleccionada; el cliente existe o se creará. |
| Datos de Entrada | Cliente: 46027897<br>Producto: Anillo de oro personalizado<br>Precio: 500.00<br>Cantidad: 1<br>Tipo de pago: dos_partes |
| Pasos a Seguir | 1. Navegar a Pedidos > Nuevo.<br>2. Ingresar el documento del cliente.<br>3. Agregar el producto personalizado.<br>4. Seleccionar tipo de pago.<br>5. Ingresar el monto del adelanto.<br>6. Guardar el pedido. |
| Resultado Esperado | El sistema genera el pedido con código PED-, calcula totales y registra el adelanto. |
| Resultado Real | El pedido se crea con el código y los montos calculados correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 09**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP09-HU03 |
| Título | Verificar la validación de caja abierta al crear pedido |
| Precondiciones | El vendedor no tiene caja abierta. |
| Datos de Entrada | Ninguno. |
| Pasos a Seguir | 1. Navegar a Pedidos > Nuevo sin caja abierta.<br>2. Intentar acceder al formulario. |
| Resultado Esperado | El sistema impide el acceso y solicita abrir o seleccionar una caja. |
| Resultado Real | Se muestra el mensaje solicitando apertura de caja. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 10**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP10-HU03 |
| Título | Verificar la creación automática de cliente al crear pedido |
| Precondiciones | El cliente no existe en la base de datos. |
| Datos de Entrada | Documento: 46027897<br>Nombre: Juan Pérez<br>Teléfono: 999999999 |
| Pasos a Seguir | 1. Ingresar un documento no registrado.<br>2. Completar los datos del cliente.<br>3. Guardar el pedido. |
| Resultado Esperado | El sistema crea el cliente y lo vincula al pedido. |
| Resultado Real | El cliente se crea y se asocia correctamente al pedido. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 11**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP11-HU04 |
| Título | Verificar la derivación de pedido a diseño |
| Precondiciones | Existe un pedido en estado 'sin_iniciar'. |
| Datos de Entrada | Pedido: PED-000001 |
| Pasos a Seguir | 1. Abrir el detalle del pedido.<br>2. Hacer clic en 'Derivar a diseño'.<br>3. Confirmar la acción. |
| Resultado Esperado | El estado de personalización cambia a 'en_diseno' y se notifica al diseñador. |
| Resultado Real | El estado cambia y el diseñador recibe la notificación. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 12**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP12-HU04 |
| Título | Verificar la derivación de pedido a producción |
| Precondiciones | El pedido está aprobado en diseño y en estado 'registrado'. |
| Datos de Entrada | Pedido: PED-000001 |
| Pasos a Seguir | 1. Abrir el detalle del pedido.<br>2. Hacer clic en 'Derivar a producción'.<br>3. Confirmar la acción. |
| Resultado Esperado | El estado del pedido cambia a 'en_produccion' y se notifica al orfebre. |
| Resultado Real | El estado cambia y el orfebre recibe la notificación. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 13**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP13-HU04 |
| Título | Verificar el transporte y recepción de pedido en almacén |
| Precondiciones | El pedido está en estado 'listo_entrega'. |
| Datos de Entrada | Pedido: PED-000001 |
| Pasos a Seguir | 1. El repartidor recoge el pedido (listo_entrega -> en_transporte).<br>2. El almacenero recibe el pedido (en_transporte -> en_almacen). |
| Resultado Esperado | El estado se actualiza a 'en_almacen' y se generan movimientos de entrada. |
| Resultado Real | El pedido llega a almacén y se registran los movimientos. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 14**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP14-HU04 |
| Título | Verificar la entrega de pedido al cliente |
| Precondiciones | El pedido está en almacén con pago completo. |
| Datos de Entrada | Pedido: PED-000001 |
| Pasos a Seguir | 1. El almacenero abre el pedido.<br>2. Hace clic en 'Entregar al cliente'.<br>3. Confirma la entrega. |
| Resultado Esperado | El estado cambia a 'entregado', se descuenta stock y se notifica al vendedor. |
| Resultado Real | El pedido se marca como entregado y se descuenta el stock. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 15**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP15-HU05 |
| Título | Verificar la venta directa de productos con stock |
| Precondiciones | El producto tiene stock disponible en tienda y el vendedor tiene caja abierta. |
| Datos de Entrada | Producto: PROD-0001<br>Cantidad: 1<br>Medio de pago: efectivo<br>Monto recibido: 200.00 |
| Pasos a Seguir | 1. Navegar a Ventas > Nuevo.<br>2. Seleccionar el producto.<br>3. Ingresar cantidad y medio de pago.<br>4. Ingresar monto recibido.<br>5. Guardar la venta. |
| Resultado Esperado | El sistema genera la venta, descuenta stock de tienda y emite el comprobante. |
| Resultado Real | La venta se registra, el stock se descuenta y se genera el comprobante. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 16**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP16-HU05 |
| Título | Verificar la validación de stock insuficiente en venta directa |
| Precondiciones | El producto tiene stock 0 en tienda. |
| Datos de Entrada | Producto: PROD-0001<br>Cantidad: 1 |
| Pasos a Seguir | 1. Seleccionar el producto sin stock.<br>2. Intentar agregarlo a la venta. |
| Resultado Esperado | El sistema advierte que no hay stock disponible. |
| Resultado Real | Aparece el mensaje de stock insuficiente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 17**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP17-HU05 |
| Título | Verificar la generación de PDF de comprobante |
| Precondiciones | Existe una venta completada. |
| Datos de Entrada | Venta: VTA-000001 |
| Pasos a Seguir | 1. Abrir el detalle de la venta.<br>2. Hacer clic en 'Ver comprobante' o 'Descargar PDF'. |
| Resultado Esperado | El sistema genera y descarga un PDF en formato A4 con los datos del comprobante. |
| Resultado Real | El PDF se descarga correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 18**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP18-HU06 |
| Título | Verificar la creación de un producto |
| Precondiciones | El usuario tiene permisos de gestión de productos. |
| Datos de Entrada | Nombre: Anillo de plata<br>Categoría: Anillos<br>Precio: 150.00<br>Stock tienda: 5<br>Stock almacén: 10 |
| Pasos a Seguir | 1. Navegar a Productos > Nuevo.<br>2. Completar los datos del producto.<br>3. Subir imágenes opcionales.<br>4. Guardar. |
| Resultado Esperado | El sistema genera el código PROD- y guarda el producto. |
| Resultado Real | El producto se crea con el código generado. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 19**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP19-HU06 |
| Título | Verificar la búsqueda y filtro de productos |
| Precondiciones | Existen productos registrados. |
| Datos de Entrada | Búsqueda: 'Anillo'<br>Filtro categoría: 'Anillos' |
| Pasos a Seguir | 1. Navegar a Productos.<br>2. Ingresar el texto de búsqueda.<br>3. Aplicar el filtro de categoría. |
| Resultado Esperado | El sistema muestra solo los productos que coinciden con los criterios. |
| Resultado Real | Los resultados se filtran correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 20**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP20-HU06 |
| Título | Verificar la carga de imágenes de producto |
| Precondiciones | Existe un producto creado. |
| Datos de Entrada | Imagen: anillo.jpg (menor a 10 MB) |
| Pasos a Seguir | 1. Editar el producto.<br>2. Seleccionar una imagen.<br>3. Guardar. |
| Resultado Esperado | El sistema almacena la imagen y la muestra en la galería del producto. |
| Resultado Real | La imagen se sube y se visualiza correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 21**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP21-HU07 |
| Título | Verificar la consulta de DNI por RENIEC |
| Precondiciones | El servicio de Decolecta/RENIEC está disponible. |
| Datos de Entrada | DNI: 46027897 |
| Pasos a Seguir | 1. Navegar a Clientes > Nuevo.<br>2. Ingresar el DNI.<br>3. Hacer clic en consultar. |
| Resultado Esperado | El sistema carga el nombre completo del cliente desde RENIEC. |
| Resultado Real | Los datos se cargan correctamente desde la API. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 22**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP22-HU07 |
| Título | Verificar la consulta de RUC por SUNAT |
| Precondiciones | El servicio de Decolecta/SUNAT está disponible. |
| Datos de Entrada | RUC: 20601030013 |
| Pasos a Seguir | 1. Navegar a Clientes > Nuevo.<br>2. Ingresar el RUC.<br>3. Hacer clic en consultar. |
| Resultado Esperado | El sistema carga la razón social, dirección, distrito, estado y condición. |
| Resultado Real | Los datos se cargan correctamente desde la API. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 23**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP23-HU07 |
| Título | Verificar la búsqueda local de cliente por documento |
| Precondiciones | El cliente ya existe en la base de datos. |
| Datos de Entrada | Documento: 46027897 |
| Pasos a Seguir | 1. Ingresar el documento en un pedido o venta.<br>2. Esperar la búsqueda automática. |
| Resultado Esperado | El sistema recupera los datos almacenados del cliente sin consultar la API. |
| Resultado Real | Los datos del cliente se recuperan desde la base local. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 24**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP24-HU08 |
| Título | Verificar la apertura de caja |
| Precondiciones | La caja seleccionada está cerrada y pertenece al usuario. |
| Datos de Entrada | Caja: Caja Principal<br>Monto inicial: 500.00 |
| Pasos a Seguir | 1. Navegar a Cajas.<br>2. Seleccionar la caja.<br>3. Ingresar monto inicial.<br>4. Confirmar apertura. |
| Resultado Esperado | El sistema registra la apertura y permite operar ventas y pedidos. |
| Resultado Real | La caja se abre correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 25**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP25-HU08 |
| Título | Verificar el cierre de caja |
| Precondiciones | El usuario tiene una caja abierta con ventas registradas. |
| Datos de Entrada | Monto final: 1200.00 |
| Pasos a Seguir | 1. Navegar al detalle de la caja abierta.<br>2. Ingresar monto final.<br>3. Confirmar cierre. |
| Resultado Esperado | El sistema calcula el total de ventas y registra el cierre de la caja. |
| Resultado Real | El cierre se registra con los totales calculados. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 26**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP26-HU08 |
| Título | Verificar la validación de caja ya abierta |
| Precondiciones | La caja ya fue abierta previamente. |
| Datos de Entrada | Caja: Caja Principal |
| Pasos a Seguir | 1. Intentar abrir la misma caja nuevamente. |
| Resultado Esperado | El sistema impide la apertura y muestra un mensaje de caja ya abierta. |
| Resultado Real | Se muestra el mensaje de validación. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 27**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP27-HU09 |
| Título | Verificar el registro de entrada de almacén |
| Precondiciones | Existe un producto registrado. |
| Datos de Entrada | Producto: PROD-0001<br>Cantidad: 20<br>Ubicación: almacén |
| Pasos a Seguir | 1. Navegar a Almacén > Movimientos > Entrada.<br>2. Seleccionar el producto.<br>3. Ingresar cantidad y ubicación.<br>4. Guardar. |
| Resultado Esperado | El sistema incrementa el stock y registra el movimiento de entrada. |
| Resultado Real | El stock aumenta y se crea el movimiento. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 28**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP28-HU09 |
| Título | Verificar el registro de salida de almacén |
| Precondiciones | El producto tiene stock suficiente. |
| Datos de Entrada | Producto: PROD-0001<br>Cantidad: 5<br>Ubicación: tienda |
| Pasos a Seguir | 1. Navegar a Almacén > Movimientos > Salida.<br>2. Seleccionar el producto.<br>3. Ingresar cantidad y ubicación.<br>4. Guardar. |
| Resultado Esperado | El sistema decrementa el stock y registra el movimiento de salida. |
| Resultado Real | El stock disminuye y se crea el movimiento. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 29**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP29-HU09 |
| Título | Verificar la recepción de pedido en almacén |
| Precondiciones | Existe un pedido en estado 'en_transporte'. |
| Datos de Entrada | Pedido: PED-000001 |
| Pasos a Seguir | 1. Navegar a Almacén > Pedidos.<br>2. Buscar el pedido en transporte.<br>3. Hacer clic en 'Recibir en almacén'.<br>4. Confirmar cantidades. |
| Resultado Esperado | El estado cambia a 'listo_recoger' y se generan movimientos de entrada. |
| Resultado Real | El pedido se recibe y cambia de estado. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 30**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP30-HU10 |
| Título | Verificar el reporte de ventas |
| Precondiciones | Existen ventas registradas en el período. |
| Datos de Entrada | Fecha inicio: 01/06/2026<br>Fecha fin: 12/07/2026 |
| Pasos a Seguir | 1. Navegar a Reportes > Ventas.<br>2. Seleccionar el rango de fechas.<br>3. Aplicar filtros. |
| Resultado Esperado | El sistema muestra KPIs, listado de ventas y gráficos correspondientes. |
| Resultado Real | Los reportes se visualizan correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 31**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP31-HU10 |
| Título | Verificar la exportación de reporte a CSV |
| Precondiciones | Existen datos para exportar. |
| Datos de Entrada | Reporte: Ventas<br>Formato: CSV |
| Pasos a Seguir | 1. Generar el reporte filtrado.<br>2. Hacer clic en 'Exportar CSV'. |
| Resultado Esperado | El sistema descarga un archivo CSV con codificación UTF-8. |
| Resultado Real | El archivo CSV se descarga correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**Caso de Prueba N° 32**

| Campo | Descripción |
| --- | --- |
| ID del Caso de Prueba | CP32-HU10 |
| Título | Verificar la exportación de reporte a Excel |
| Precondiciones | Existen datos para exportar. |
| Datos de Entrada | Reporte: Pedidos<br>Formato: Excel |
| Pasos a Seguir | 1. Generar el reporte filtrado.<br>2. Hacer clic en 'Exportar Excel'. |
| Resultado Esperado | El sistema descarga un archivo XLSX válido con encabezados y autofiltro. |
| Resultado Real | El archivo Excel se descarga correctamente. |
| Estado | Satisfactorio |
| Notas y Comentarios | Prueba exitosa. |

**
