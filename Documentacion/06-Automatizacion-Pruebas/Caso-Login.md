# Caso automatizable inicial - Login

## Objetivo del caso

Validar que el sistema permita iniciar sesion de forma segura y consistente.

## Requerimientos relacionados

| Tipo | ID | Descripcion |
|---|---|---|
| RF | `RF-01` | Inicio de sesion |
| RFC | `RFC-01` | Autenticacion segura |
| HU | `HU01` | Inicio de sesion |

## Componentes reales del sistema

| Componente | Ubicacion |
|---|---|
| Ruta login | `routes/auth.php` |
| Vista login | `resources/views/auth/login.blade.php` |
| Logica de autenticacion | `app/Http/Requests/Auth/LoginRequest.php` |
| Cierre de sesion | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |

## Casos de prueba propuestos

### CP-LGN-001 - Login con usuario valido
- Dado un usuario activo con credenciales validas
- Cuando ingresa usuario y contrasena correctos
- Entonces debe acceder al dashboard

### CP-LGN-002 - Login con correo valido
- Dado un usuario activo con correo y contrasena validos
- Cuando ingresa correo y contrasena correctos
- Entonces debe acceder al dashboard

### CP-LGN-003 - Login con credenciales invalidas
- Dado un usuario existente
- Cuando ingresa contrasena incorrecta
- Entonces el sistema debe mostrar mensaje de error

### CP-LGN-004 - Login con usuario inactivo
- Dado un usuario registrado con estado inactivo
- Cuando intenta ingresar
- Entonces el sistema debe bloquear el acceso y mostrar el mensaje correspondiente

### CP-LGN-005 - Logout
- Dado un usuario autenticado
- Cuando cierra sesion
- Entonces debe volver a la pantalla de acceso y perder acceso a rutas protegidas

## Cobertura funcional

Este caso cubre:
- autenticacion
- seguridad
- control de acceso
- persistencia de sesion
- bloqueo por estado de usuario

## Herramienta recomendada

Para este caso se recomienda `pytest + playwright` porque:

- interactua con la UI real
- valida mensajes visibles
- confirma redireccion y sesion
- es una buena demo para exposicion

## Siguiente ampliacion natural

Despues de login, los siguientes casos recomendados son:
- clientes
- pedidos
- ventas
