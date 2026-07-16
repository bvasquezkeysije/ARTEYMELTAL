# 5. HISTORIAS DE USUARIO Y CRITERIOS DE ACEPTACIÓN

**5.1. Historia de Usuario N° 01**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H01</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Inicio de sesión</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>5</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Usuario del sistema<br>
        <strong>Quiero:</strong> Iniciar sesión ingresando mi correo electrónico o nombre de usuario y contraseña.<br>
        <strong>Para:</strong> Acceder de manera segura a las funcionalidades autorizadas según mi rol.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario tiene credenciales válidas y activas.<br>
        <strong>Cuando:</strong> Introduce su correo o usuario y contraseña correctos.<br>
        <strong>Entonces:</strong> El sistema inicia sesión y redirige al panel principal correspondiente a su rol.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">Ninguno</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El usuario introduce credenciales incorrectas.<br>
        <strong>Cuando:</strong> El sistema valida el usuario y contraseña.<br>
        <strong>Entonces:</strong> Debe mostrar un mensaje de error claro en un modal emergente.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El usuario no completa alguno de los campos requeridos.<br>
        <strong>Cuando:</strong> Intenta iniciar sesión.<br>
        <strong>Entonces:</strong> El sistema rechaza la acción y muestra un aviso de validación en el formulario.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> El usuario existe pero tiene el estado inactivo.<br>
        <strong>Cuando:</strong> Intenta iniciar sesión con credenciales válidas.<br>
        <strong>Entonces:</strong> El sistema impide el acceso e informa que la cuenta está desactivada.
      </td>
    </tr>
  </tbody>
</table>

**5.2. Historia de Usuario N° 02**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H02</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Recuperación de contraseña</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>4</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Usuario del sistema<br>
        <strong>Quiero:</strong> Recuperar mi contraseña mediante un código enviado a mi correo.<br>
        <strong>Para:</strong> Restablecer el acceso sin necesidad de contactar al administrador.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario olvidó su contraseña.<br>
        <strong>Cuando:</strong> Ingresa su correo registrado y solicita el código.<br>
        <strong>Entonces:</strong> El sistema genera un código de 6 dígitos y lo envía al correo.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El usuario ingresa un correo no registrado.<br>
        <strong>Cuando:</strong> Solicita el código de recuperación.<br>
        <strong>Entonces:</strong> El sistema muestra un mensaje indicando que no existe la cuenta.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El usuario recibe el código en su correo.<br>
        <strong>Cuando:</strong> Ingresa el código correcto y una nueva contraseña válida.<br>
        <strong>Entonces:</strong> El sistema actualiza la contraseña y redirige al login con mensaje de éxito.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> El usuario ingresa un código incorrecto o expirado.<br>
        <strong>Cuando:</strong> Intenta restablecer la contraseña.<br>
        <strong>Entonces:</strong> El sistema rechaza la acción y solicita un nuevo código.
      </td>
    </tr>
  </tbody>
</table>

**5.3. Historia de Usuario N° 03**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H03</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Crear pedido personalizado</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>5</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Vendedor<br>
        <strong>Quiero:</strong> Registrar un pedido personalizado con los datos del cliente, productos y pago.<br>
        <strong>Para:</strong> Iniciar el flujo de trabajo de diseño y producción.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">13 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El vendedor tiene una caja abierta y seleccionada.<br>
        <strong>Cuando:</strong> Completa el formulario de pedido con cliente, productos y pago.<br>
        <strong>Entonces:</strong> El sistema genera el pedido con código PED-, calcula montos y registra el adelanto.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01, H09</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El cliente no existe en el sistema.<br>
        <strong>Cuando:</strong> El vendedor ingresa el documento del cliente.<br>
        <strong>Entonces:</strong> El sistema crea automáticamente el cliente o consulta RENIEC/SUNAT si es necesario.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El vendedor no tiene caja abierta.<br>
        <strong>Cuando:</strong> Intenta crear un pedido.<br>
        <strong>Entonces:</strong> El sistema impide el acceso y solicita abrir o seleccionar una caja.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> El vendedor adjunta archivos de orden de compra.<br>
        <strong>Cuando:</strong> Guarda el pedido.<br>
        <strong>Entonces:</strong> El sistema almacena los archivos permitidos y los vincula al pedido.
      </td>
    </tr>
  </tbody>
</table>

**5.4. Historia de Usuario N° 04**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H04</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Gestionar flujo de pedido</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>5</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Vendedor, Diseñador, Orfebre, Repartidor o Almacenero<br>
        <strong>Quiero:</strong> Cambiar el estado del pedido según mi rol y la etapa del proceso.<br>
        <strong>Para:</strong> Hacer seguimiento del pedido desde su registro hasta la entrega.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">13 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> Un pedido registrado en estado "sin_iniciar".<br>
        <strong>Cuando:</strong> El vendedor lo deriva a diseño.<br>
        <strong>Entonces:</strong> El estado cambia a "en_diseno" y se notifica al diseñador.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H03</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> Un pedido aprobado en diseño.<br>
        <strong>Cuando:</strong> El vendedor lo deriva a producción.<br>
        <strong>Entonces:</strong> El estado cambia a "en_produccion" y se notifica al orfebre.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> Un pedido listo para entrega.<br>
        <strong>Cuando:</strong> El repartidor lo transporta al almacén.<br>
        <strong>Entonces:</strong> El estado cambia a "en_transporte" y posteriormente a "en_almacen".
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> Un pedido con pago completo en almacén.<br>
        <strong>Cuando:</strong> El almacenero lo entrega al cliente.<br>
        <strong>Entonces:</strong> El estado cambia a "entregado", se descuenta stock y se notifica al vendedor.
      </td>
    </tr>
  </tbody>
</table>

**5.5. Historia de Usuario N° 05**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H05</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Registrar venta directa</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>5</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Vendedor<br>
        <strong>Quiero:</strong> Registrar una venta directa de productos en tienda.<br>
        <strong>Para:</strong> Generar el comprobante de pago y descontar stock de tienda.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El vendedor tiene una caja abierta.<br>
        <strong>Cuando:</strong> Selecciona productos en stock de tienda y registra el pago.<br>
        <strong>Entonces:</strong> El sistema genera la venta, descuenta el stock y emite el comprobante.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01, H08</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> Un producto con stock insuficiente en tienda.<br>
        <strong>Cuando:</strong> El vendedor intenta agregarlo a la venta.<br>
        <strong>Entonces:</strong> El sistema advierte que no hay stock disponible.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El cliente paga en efectivo.<br>
        <strong>Cuando:</strong> El vendedor ingresa el monto recibido.<br>
        <strong>Entonces:</strong> El sistema calcula y muestra el vuelto correcto.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> Una venta completada.<br>
        <strong>Cuando:</strong> El vendedor solicita el comprobante.<br>
        <strong>Entonces:</strong> El sistema genera el PDF del comprobante en formato A4.
      </td>
    </tr>
  </tbody>
</table>

**5.6. Historia de Usuario N° 06**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H06</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Gestionar productos</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>4</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Administrador o Almacenero<br>
        <strong>Quiero:</strong> Crear, editar y eliminar productos del catálogo.<br>
        <strong>Para:</strong> Mantener actualizado el inventario y catálogo de ventas.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario tiene permiso de gestión de productos.<br>
        <strong>Cuando:</strong> Crea un nuevo producto con nombre, categoría, precio y stock.<br>
        <strong>Entonces:</strong> El sistema genera el código PROD- y guarda el producto.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El usuario busca un producto.<br>
        <strong>Cuando:</strong> Ingresa código, nombre o categoría en el filtro.<br>
        <strong>Entonces:</strong> El sistema muestra los resultados paginados correctamente.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> Un producto con imágenes asociadas.<br>
        <strong>Cuando:</strong> El usuario sube nuevas imágenes o elimina existentes.<br>
        <strong>Entonces:</strong> El sistema actualiza las imágenes permitidas y elimina los archivos físicos.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> Un producto sin movimientos registrados.<br>
        <strong>Cuando:</strong> El usuario solicita eliminarlo.<br>
        <strong>Entonces:</strong> El sistema elimina el producto y sus imágenes permanentemente.
      </td>
    </tr>
  </tbody>
</table>

**5.7. Historia de Usuario N° 07**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H07</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Gestionar clientes</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>4</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Vendedor o Administrador<br>
        <strong>Quiero:</strong> Registrar y consultar clientes, incluyendo validación de RUC/DNI.<br>
        <strong>Para:</strong> Agilizar la creación de pedidos y ventas con datos correctos.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario ingresa un DNI o RUC válido.<br>
        <strong>Cuando:</strong> Solicita la consulta a RENIEC/SUNAT.<br>
        <strong>Entonces:</strong> El sistema carga el nombre, dirección y otros datos oficiales del contribuyente.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El servicio de consulta externa falla o el documento no existe.<br>
        <strong>Cuando:</strong> El usuario intenta consultar.<br>
        <strong>Entonces:</strong> El sistema permite el ingreso manual de los datos del cliente.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El cliente ya existe en la base de datos.<br>
        <strong>Cuando:</strong> El usuario ingresa el documento en un pedido o venta.<br>
        <strong>Entonces:</strong> El sistema recupera automáticamente los datos almacenados.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> El usuario busca un cliente.<br>
        <strong>Cuando:</strong> Ingresa nombre, teléfono, correo o documento.<br>
        <strong>Entonces:</strong> El sistema muestra resultados paginados coincidentes.
      </td>
    </tr>
  </tbody>
</table>

**5.8. Historia de Usuario N° 08**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H08</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Control de caja</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>5</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Vendedor o Administrador<br>
        <strong>Quiero:</strong> Abrir, cerrar y visualizar el detalle de caja.<br>
        <strong>Para:</strong> Controlar los ingresos por ventas y el saldo de caja.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario tiene una caja cerrada disponible.<br>
        <strong>Cuando:</strong> Registra la apertura con monto inicial y observaciones.<br>
        <strong>Entonces:</strong> El sistema activa la caja y permite operar ventas y pedidos.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El usuario tiene una caja abierta.<br>
        <strong>Cuando:</strong> Intenta abrir la misma u otra caja.<br>
        <strong>Entonces:</strong> El sistema impide abrir una caja ya abierta.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El usuario finaliza su turno.<br>
        <strong>Cuando:</strong> Cierra la caja ingresando el monto final.<br>
        <strong>Entonces:</strong> El sistema calcula el total de ventas y cierra la apertura.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> Existen ventas asociadas a una apertura de caja.<br>
        <strong>Cuando:</strong> El usuario consulta el detalle de caja.<br>
        <strong>Entonces:</strong> El sistema muestra totales por medio de pago, cantidad de ventas y vuelto.
      </td>
    </tr>
  </tbody>
</table>

**5.9. Historia de Usuario N° 09**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H09</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Gestionar almacén</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>4</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Almacenero o Administrador<br>
        <strong>Quiero:</strong> Registrar entradas, salidas y movimientos de inventario.<br>
        <strong>Para:</strong> Mantener el stock actualizado y recibir/entregar pedidos.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> Un producto registrado.<br>
        <strong>Cuando:</strong> El usuario registra una entrada de unidades a tienda o almacén.<br>
        <strong>Entonces:</strong> El sistema incrementa el stock y crea un movimiento de entrada con usuario y fecha.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01, H06</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> Un producto con stock disponible.<br>
        <strong>Cuando:</strong> El usuario registra una salida de unidades.<br>
        <strong>Entonces:</strong> El sistema decrementa el stock y crea un movimiento de salida.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> Un producto sin stock suficiente.<br>
        <strong>Cuando:</strong> El usuario intenta registrar una salida mayor al stock.<br>
        <strong>Entonces:</strong> El sistema rechaza la operación y muestra un mensaje de stock insuficiente.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> Un pedido transportado llega al almacén.<br>
        <strong>Cuando:</strong> El almacenero registra la recepción.<br>
        <strong>Entonces:</strong> El sistema cambia el estado a "listo_recoger" y genera los movimientos de entrada correspondientes.
      </td>
    </tr>
  </tbody>
</table>

**5.10. Historia de Usuario N° 10**

<table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.35; color: #000; background-color: #fff;">
  <colgroup>
    <col style="width: 27%;">
    <col style="width: 38%;">
    <col style="width: 22%;">
    <col style="width: 13%;">
  </colgroup>
  <tbody>
    <tr>
      <td style="border: 1px solid #000; padding: 8px 28px; vertical-align: top;"><strong>ID: H10</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Título: Consultar reportes</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Valor de<br>Negocio</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>3</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 102px; vertical-align: top; line-height: 1.35;">
        <strong>Como:</strong> Administrador o Vendedor<br>
        <strong>Quiero:</strong> Visualizar reportes de ventas, pedidos, saldos y stock.<br>
        <strong>Para:</strong> Tomar decisiones basadas en datos actualizados del negocio.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario tiene permiso de reportes.<br>
        <strong>Cuando:</strong> Selecciona un reporte y aplica filtros por fecha o estado.<br>
        <strong>Entonces:</strong> El sistema muestra los KPIs y el listado filtrado correctamente.
      </td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="4" style="border: 1px solid #000; padding: 8px; vertical-align: top;">H01, H03, H05, H06</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El reporte tiene datos disponibles.<br>
        <strong>Cuando:</strong> El usuario solicita la exportación a CSV.<br>
        <strong>Entonces:</strong> El sistema genera un archivo CSV con codificación UTF-8.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El reporte tiene datos disponibles.<br>
        <strong>Cuando:</strong> El usuario solicita la exportación a Excel.<br>
        <strong>Entonces:</strong> El sistema genera un archivo XLSX válido con encabezados y autofiltro.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 100px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 4</strong></div>
        <strong>Dado:</strong> El reporte incluye datos de ventas o pedidos.<br>
        <strong>Cuando:</strong> El usuario visualiza la sección de gráficos.<br>
        <strong>Entonces:</strong> El sistema renderiza gráficos de líneas, barras o doughnut con los datos filtrados.
      </td>
    </tr>
  </tbody>
</table>
