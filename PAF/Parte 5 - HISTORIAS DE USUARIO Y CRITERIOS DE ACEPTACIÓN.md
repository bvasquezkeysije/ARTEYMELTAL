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
        <strong>Para:</strong> Acceder de manera segura a las funcionalidades<br>
        autorizadas según mi rol.
      </td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Estimación</strong></td>
      <td style="border: 1px solid #000; padding: 8px; vertical-align: top; white-space: nowrap;">8 puntos</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 152px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 1</strong></div>
        <strong>Dado:</strong> El usuario desea acceder al sistema.<br>
        <strong>Cuando:</strong> Introduce su correo electrónico o nombre de usuario y contraseña<br>
        correctamente.<br>
        <strong>Entonces:</strong> El sistema debe iniciar sesión exitosamente y<br>
        redirigirlo al panel principal correspondiente a su rol.
      </td>
      <td rowspan="3" style="border: 1px solid #000; padding: 8px; vertical-align: top;"><strong>Dependencias</strong></td>
      <td rowspan="3" style="border: 1px solid #000; padding: 8px; vertical-align: top;">Ninguno</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 108px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 2</strong></div>
        <strong>Dado:</strong> El usuario introduce datos inválidos.<br>
        <strong>Cuando:</strong> El sistema detecta credenciales incorrectas o una cuenta inactiva.<br>
        <strong>Entonces:</strong> Debe mostrar un mensaje claro de error en un modal emergente.
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #000; padding: 8px; height: 152px; vertical-align: top; line-height: 1.35;">
        <div style="text-align: center; margin-bottom: 22px;"><strong>Criterios de Aceptación 3</strong></div>
        <strong>Dado:</strong> El usuario no completa alguno de los campos<br>
        requeridos.<br>
        <strong>Cuando:</strong> Intenta iniciar sesión.<br>
        <strong>Entonces:</strong> El sistema debe rechazar la acción y mostrar<br>
        un aviso de validación en el formulario.
      </td>
    </tr>
  </tbody>
</table>
