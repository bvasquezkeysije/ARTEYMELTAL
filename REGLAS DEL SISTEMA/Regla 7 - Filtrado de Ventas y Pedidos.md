# Regla 7 - Filtrado de Ventas y Pedidos

## Ventas

### Alcance del filtro
- El listado de ventas (`ventas.index`) permite filtrar entre **"Mi caja"** y **"Todas"**.
- El filtro se controla mediante el parámetro `?scope=mi_caja|todas`.

### Modo "Mi caja" (por defecto)
- Requiere una caja activa seleccionada en sesión (`caja_apertura_id`).
- Solo muestra las ventas vinculadas a esa caja.
- Si no hay caja seleccionada, redirige a la selección de caja.
- Muestra el badge con el nombre de la caja actual.

### Modo "Todas"
- No requiere caja seleccionada.
- Muestra **todas las ventas** del sistema (sin filtrar por `caja_apertura_id`).
- Los buscadores (`q`, `tipo`) funcionan normalmente.
- El badge de caja no se muestra.
- El botón "Nueva venta" sigue disponible; si no hay caja seleccionada, redirige a selección.

## Pedidos

### Alcance del filtro
- El listado de pedidos (`pedidos.index`) permite filtrar entre **"Mis pedidos"** y **"Todos"**.
- El filtro se controla mediante el parámetro `?scope=mis_pedidos|todas`.

### Modo "Mis pedidos" (por defecto)
- Solo muestra los pedidos creados por el usuario autenticado (`usuario_id`).
- Filtro aplicado: `->where('usuario_id', auth()->id())`.

### Modo "Todos"
- Muestra **todos los pedidos** del sistema (sin filtrar por `usuario_id`).
- Respeta los permisos de visión de pedidos.
- Los buscadores y filtros existentes funcionan normalmente.

## Reglas generales
- El filtro se preserva mediante un `<input type="hidden" name="scope">` en el formulario de búsqueda.
- La paginación preserva el `scope` seleccionado.
- Por defecto, el filtro activo es "Mi caja" (ventas) o "Mis pedidos" (pedidos).

### UI
- **Ventas** y **Pedidos**: el selector `scope` está dentro del formulario de filtros, etiquetado como **"Mostrar"**. Se accede haciendo clic en el botón **Filtrar** (junto al campo de búsqueda).
