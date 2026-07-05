# Caso automatizable siguiente - Clientes

## Objetivo del caso

Validar que el sistema gestione clientes de forma consistente para soportar ventas y pedidos.

## Requerimientos relacionados

| Tipo | ID | Descripcion |
|---|---|---|
| RF | `RF-07` | Registro de clientes |
| RF | `RF-08` | Busqueda de clientes |
| RF | `RF-09` | Validacion de DNI y RUC |
| RFC | `RFC-04` | Gestion de clientes con registro, busqueda y validacion |
| HU | `HU05` | Gestion de clientes |
| HU | `HU06` | Validacion de DNI/RUC |

## Componentes reales del sistema

| Componente | Ubicacion |
|---|---|
| Rutas clientes | `routes/web.php` |
| Controlador CRUD | `app/Http/Controllers/ClienteController.php` |
| Controlador consulta documento | `app/Http/Controllers/ClienteConsultaController.php` |
| Vista index | `resources/views/clientes/index.blade.php` |
| Vista formulario | `resources/views/clientes/_form.blade.php` |

## Casos de prueba propuestos

### CP-CLI-001 - Registrar cliente valido
- crear cliente con nombre, documento, telefono, correo y direccion
- validar mensaje de exito

### CP-CLI-002 - Buscar cliente por documento
- registrar cliente de prueba
- buscarlo por `q`
- validar que aparece en la tabla

### CP-CLI-003 - Documento duplicado
- registrar un cliente
- intentar registrar otro con el mismo documento
- validar que el sistema rechaza el duplicado

### CP-CLI-004 - Consulta de documento local
- registrar cliente con documento
- consultar `GET /clientes/consulta-documento`
- validar respuesta `fuente=local`

### CP-CLI-005 - Consulta de documento invalido
- invocar consulta con longitud incorrecta
- validar respuesta `422`

## Valor de negocio

Automatizar clientes reduce errores en el paso previo a pedidos y ventas. Si clientes falla, el resto del flujo comercial se contamina.
