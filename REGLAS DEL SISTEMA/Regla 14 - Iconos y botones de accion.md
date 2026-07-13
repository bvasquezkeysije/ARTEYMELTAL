# Regla 14 - Íconos y botones de acción

## Clases CSS base

### `btn-icon` — Botón cuadrado grande (40×40px)
```css
.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;       /* 40px */
    height: 2.5rem;
    border-radius: 0.75rem; /* 12px */
    flex-shrink: 0;
    color: #fff;
}
.btn-icon:active { filter: brightness(0.85); }
.btn-icon:focus, .btn-icon:focus-visible { outline: 0 none !important; }
```

### `btn-icon-sm` — Botón cuadrado pequeño (32×32px)
```css
.btn-icon-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;         /* 32px */
    height: 2rem;
    border-radius: 0.5rem; /* 8px */
    flex-shrink: 0;
    color: #fff;
}
.btn-icon-sm:active { filter: brightness(0.85); }
.btn-icon-sm:focus, .btn-icon-sm:focus-visible { outline: 0 none !important; }
```

## Reglas de uso

### Los botones NUNCA llevan texto
Solo el ícono con `title="..."` para tooltip. Ejemplo:
```html
<button class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
</button>
```

### `pointer-events-none` en el ícono
Siempre en la etiqueta `<img>` para que los clics pasen al botón padre:
```html
<img src="..." class="h-5 w-5 object-contain pointer-events-none" />
```

### Todos los botones de una fila deben tener el mismo alto
Si hay un botón con texto en la misma fila, usar altura manual en vez de `btn-icon`:
```html
<!-- Mal: mezclar btn-icon (40px) con botón de texto (46px) -->
<button class="btn-icon ...">...</button>
<button class="h-[46px] px-4 ...">Texto</button>

<!-- Bien: ambos con la misma altura -->
<button class="h-[46px] w-[46px] ...">...</button>
<button class="h-[46px] px-4 ...">Texto</button>
```

## Colores por acción

| Acción | Color de fondo | Clase | Uso principal |
|--------|---------------|-------|---------------|
| **Buscar** | Azul | `bg-blue-600 hover:bg-blue-700` | Lupa en búsquedas |
| **Nuevo/Crear** | Negro | `bg-[#09090f]` | Botón crear registro |
| **Filtrar** | Celeste | `bg-sky-500 hover:bg-sky-600` | Filtros en índices |
| **Editar** | Ámbar | `bg-amber-400 hover:bg-amber-500` | Editar registro |
| **Ver detalle** | Cyan | `bg-[#0891B2]` | Ver información/fotos |
| **Eliminar** | Rojo | `bg-red-600 hover:bg-red-700` | Eliminar/desactivar |
| **Cerrar modal** | Rojo | `bg-red-600 hover:bg-red-700` | × de cierre |
| **Subir archivo** | Naranja | `bg-amber-600 hover:bg-amber-700` | Subir diseño |
| **Ver modelo** | Verde | `bg-emerald-600 hover:bg-emerald-700` | Ver archivos modelo |
| **Editar archivos** | Morado | `bg-purple-600 hover:bg-purple-700` | Editar/eliminar archivos |
| **Gestión categorías** | Negro | `bg-[#111] hover:bg-[#262626]` | Categorías en productos |
| **Consumidor final** | Dorado | `bg-[#b9943d]` | Botón en ventas create |

## Íconos del sistema (public/icons/)

| Archivo | Descripción | Uso |
|---------|-------------|-----|
| `buscar.ico` | Lupa | Botón buscar |
| `nuevo.ico` | Símbolo + | Nuevo/crear |
| `editar.ico` | Lápiz | Editar |
| `eliminar.ico` | Tacho | Eliminar |
| `eliminar-desactivar.ico` | Tacho | Desactivar/inactivar |
| `filtros.ico` | Embudo | Filtrar |
| `ver-detalle.ico` | Ojo | Ver detalle |
| `imprimir.ico` | Impresora | Imprimir |
| `cerrar.ico` | X | Cerrar modal |
| `Subir-Blanco.png` | Subir | Subir archivos |
| `VerModelo-Blanco.png` | Modelo | Ver modelo/referencia |

## Ícono estándar para "Ver detalle"
Usar **siempre** `ver-detalle.ico` para acciones de "Ver detalle" / "Ver fotos" / "Ver información". No usar SVGs inline ni otros iconos para esta acción.

## Filtros dropdown

```html
<div x-data="{ filtrosAbiertos: false }" class="relative">
    <button @click="filtrosAbiertos = !filtrosAbiertos" class="btn-icon bg-sky-500 ...">
        <img src="{{ asset('icons/filtros.ico') }}" ... />
    </button>
    <div x-show="filtrosAbiertos" x-cloak @click.outside="filtrosAbiertos = false"
         class="absolute right-0 top-full z-30 mt-2 w-56 rounded-xl border border-[#e5dec8] bg-white p-3 shadow-lg">
        {{-- Opciones del filtro --}}
    </div>
</div>
```

### Indicador activo
Cuando un filtro está activo, el botón lleva un ring:
```html
class="btn-icon bg-sky-500 {{ $filtroActivo ? 'ring-2 ring-sky-400' : '' }}"
```

## Paginación

- Usar `{{ $items->links() }}` dentro de `<div class="px-1">`.
- La paginación se coloca **dentro** del `space-y-5`, después de la tabla.
- Los links preservan los query strings actuales automáticamente.
