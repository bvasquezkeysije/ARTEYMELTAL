# Regla 1 - Modales emergentes (patrón de diseño UI)

## Tecnología
Todos los modales del sistema usan **Alpine.js** para abrir/cerrar. No se usa JavaScript vanilla ni librerías externas.

## Patrón estándar de modal

```html
<div x-data="{ open: false }"
     x-show="open"
     x-on:open-NOMBRE-{{ $id }}.window="open = true"
     x-on:keydown.escape.window="open = false"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @@click.outside="open = false" class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        {{-- Contenido --}}
    </div>
</div>
```

### Elementos obligatorios
| Elemento | Clase / Atributo | Función |
|----------|------------------|---------|
| Overlay | `fixed inset-0 z-50 bg-black/40` | Fondo oscuro semitransparente |
| Contenedor | `rounded-2xl bg-white p-6 shadow-xl` | Caja blanca del modal |
| Cierre por Escape | `x-on:keydown.escape.window` | Cierra con tecla ESC |
| Cierre por clic fuera | `@@click.outside="open = false"` | Cierra al hacer clic afuera |
| `x-cloak` | Atributo | Oculta el modal antes de que Alpine lo monte |

### Botón de cierre (×)
- Usar **siempre** `btn-icon-sm bg-red-600 hover:bg-red-700` con icono `cerrar.ico`
- **NUNCA** usar `<span>&times;</span>` ni estilos grises

```html
<button type="button" @@click="open = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
    <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none">
</button>
```

## Apertura de modales

### Desde botón en tabla (dispatch de evento)
Cada fila de la tabla tiene un botón que despacha un evento único por registro:
```html
<button @@click="$dispatch('open-detalle-{{ $pedido->id }}')">Ver detalle</button>
```

### Desde otro modal (cascada)
Un modal puede abrir otro usando el mismo patrón de dispatch:
```html
<button @@click="viewerOpen = true">Ver modelo</button>
```
El segundo modal se monta **dentro** del mismo `x-data` del primero.

## Reglas generales
- Cada modal usa `max-w-lg` (estándar) o `max-w-3xl` (detalles grandes).
- El contenido scrolleable usa `max-h-[60vh] overflow-y-auto` o `flex-1 overflow-y-auto`.
- Los modales dentro de modales usan `z-[60]` para superponerse.
- **NUNCA** renderizar modales dentro de `@if` condicionales que dependan de datos del loop — siempre renderizar y controlar con `x-show`.
