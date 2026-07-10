**

4. # REQUERIMIENTOS DE SOFTWARE
    

## 4.1. Requerimientos funcionales 

  

| ID    | Nombre                      | Descripción                                                                                                                                            | Actor                                     | Prioridad | Fuente    | Criterio de aceptación                                                                            |
| ----- | --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ | ----------------------------------------- | --------- | --------- | ------------------------------------------------------------------------------------------------- |
| RF-01 | Inicio de sesión            | El sistema permitirá a los usuarios ingresar mediante correo electrónico o usuario y contraseña para acceder a las funciones autorizadas según su rol. | Administrador, Supervisor, Cajero, Ventas | Alta      | Seguridad | El usuario accede correctamente al sistema únicamente si las credenciales ingresadas son válidas. |
| RF-02 | Recuperación de contraseña  | El sistema permitirá recuperar la contraseña enviando un enlace de recuperación al correo electrónico registrado del usuario.                          | Usuario                                   | Alta      | Seguridad | El usuario recibe correctamente en su correo el enlace para restablecer su contraseña.            |
| RF-03 | Gestión de usuarios         | El sistema permitirá registrar, editar, activar y desactivar usuarios para controlar el acceso al sistema.                                             | Administrador                             | Alta      | Cliente   | El usuario queda registrado correctamente y puede ser administrado desde el módulo de usuarios.   |
| RF-04 | Gestión de roles y permisos | El sistema permitirá crear roles y asignar permisos específicos por módulo para restringir accesos según las funciones del trabajador.                 | Administrador                             | Alta      | Seguridad | Cada usuario visualiza únicamente los módulos y opciones permitidas según su rol asignado.        |

  
  
  
  
  
  
  
  
  
  
  
  

## 4.2. Requerimientos no funcionales 

|   |   |   |   |   |   |
|---|---|---|---|---|---|
|ID|Categoría|Requerimiento|Métrica|Prioridad|Fuente|
|RNF-01|Seguridad|El sistema debe proteger el acceso mediante autenticación segura de usuarios y validación de credenciales.|100% de accesos validados mediante usuario y contraseña.|Alta|Seguridad|
|RNF-02|Control de acceso|El sistema debe restringir funciones y módulos según el rol asignado a cada usuario.|Los usuarios solo acceden a funciones autorizadas.|Alta|Seguridad|
|RNF-03|Protección de información|El sistema debe proteger la información de clientes, ventas y pedidos almacenados en la base de datos.|Los datos solo pueden ser visualizados por usuarios autorizados.|Alta|Cliente|



**