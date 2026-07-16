**

6. # ESCENARIOS DE PRUEBA (BDD/TDD)
    

## Escenario de prueba N° 01 - Inicio de sesión exitoso

| TDD                                                                                                                                                                                          | BDD                                                                                                                                                       |
| -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Se espera que la función: `LoginRequest::authenticate`<br><br>Devuelva: `true` y registre la sesión del usuario activo.<br><br>Cuando le envío: `login = "bvasquezkeysije@gmail.com"` y `password = "[contraseña válida]"`. | Dado que: el usuario existe en el sistema, tiene estado activo y posee credenciales válidas.<br><br>Cuando: ingresa su correo o usuario y contraseña correctos y envía el formulario de inicio de sesión.<br><br>Entonces: el sistema autentica al usuario y lo redirige al panel principal correspondiente a su rol. |



**
