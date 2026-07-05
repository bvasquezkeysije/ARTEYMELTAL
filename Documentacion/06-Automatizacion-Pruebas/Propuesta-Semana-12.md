# Presentacion de propuesta de automatizacion de pruebas: Semana 12

## 1. Problema actual

Actualmente la validacion del sistema depende principalmente de pruebas manuales. Eso genera cuatro riesgos:

- regresiones en login, pedidos, ventas y stock
- validaciones repetitivas lentas
- dependencia del criterio manual del tester o desarrollador
- poca evidencia reutilizable para futuras entregas

## 2. Propuesta

Implementar una base de automatizacion de pruebas con Python, empezando por flujos criticos del negocio ya existentes en el sistema.

## 3. Herramientas propuestas

| Herramienta | Uso |
|---|---|
| `pytest` | Framework principal de pruebas |
| `playwright` | Automatizacion de navegador para pruebas end-to-end |
| `requests` | Pruebas de endpoints HTTP y API |
| `python-dotenv` | Manejo de variables de entorno |
| `pytest-html` | Reportes ejecutivos de resultados |

## 4. Estrategia de automatizacion

### Fase 1. Smoke tests

Automatizar flujos minimos para confirmar que el sistema puede operar:

- login valido
- login invalido
- bloqueo de usuario inactivo
- cierre de sesion

### Fase 2. Flujos criticos del negocio

- registrar cliente
- registrar pedido personalizado
- validar restriccion de adelanto antes de produccion
- registrar venta directa y verificar stock

### Fase 3. Regresion operativa

- cierre de pedido
- generacion de comprobante
- filtros y reportes clave

## 5. Primer caso elegido

El primer caso recomendado es `login` porque:

- tiene impacto transversal en todo el sistema
- esta claramente definido en requerimientos
- es simple de demostrar
- permite validar autenticacion, mensajes de error y control de acceso

## 6. Trazabilidad con requerimientos

El caso `login` cubre directamente:

| Tipo | ID | Descripcion |
|---|---|---|
| Requerimiento funcional | `RF-01` | Inicio de sesion |
| Requerimiento funcional consolidado | `RFC-01` | Autenticacion segura mediante login y recuperacion |
| Historia de usuario | `HU01` | Inicio de sesion |

## 7. Criterios de aceptacion cubiertos en login

La automatizacion inicial debe verificar:

1. ingreso mediante `usuario o correo electronico`
2. validacion correcta de contrasena
3. acceso al dashboard cuando las credenciales son validas
4. mensaje de error cuando las credenciales son invalidas
5. bloqueo de usuarios inactivos
6. mantenimiento de sesion hasta `logout`

## 8. Relacion con la implementacion actual

En el sistema actual:

- la ruta de login esta en `routes/auth.php`
- la vista de login es `resources/views/auth/login.blade.php`
- la autenticacion real esta en `app/Http/Requests/Auth/LoginRequest.php`
- el cierre de sesion esta en `AuthenticatedSessionController`

Eso permite automatizar el flujo sin cambiar la logica del negocio.

## 9. Beneficios esperados

- reducir tiempo de validacion manual
- detectar errores antes de despliegue
- tener evidencia repetible de cumplimiento funcional
- facilitar futuras integraciones con CI

## 10. Entregable minimo viable

Para esta semana, el entregable propuesto es:

- 1 documento de estrategia
- 1 caso automatizado de login en Python
- estructura base para seguir creciendo en clientes, pedidos y ventas

## 11. Conclusion

La propuesta correcta no es automatizar todo de inmediato. La propuesta correcta es construir una base estable con Python sobre flujos de alto valor. El login es el primer paso porque conecta directamente con seguridad, control de acceso y operacion diaria del sistema.
