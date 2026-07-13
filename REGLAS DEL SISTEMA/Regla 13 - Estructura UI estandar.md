# Regla 13 - Estructura UI estándar (vistas índice)

## Objetivo
Definir la estructura HTML estándar que deben seguir **todas las vistas índice** del sistema (listados con tabla). Esta regla garantiza consistencia visual entre módulos.

## Estructura base

```html
<x-app-layout>
    {{-- Estilos btn-icon / btn-icon-sm --}}
    <style>...</style>

    {{-- Título del módulo --}}
    <x-slot name="header">
        <span>Nombre del Módulo</span>
    </x-slot>

    {{-- Mensaje de éxito --}}
    @if(session('ok'))
        <div class="mb-3 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('ok') }}
        </div>
    @endif

    {{-- Contenedor principal con espacio entre secciones --}}
    <div class="space-y-5">

        {{-- 1. Card de búsqueda --}}
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                {{-- Campo de búsqueda --}}
                <form id="search-form" method="GET" action="..." class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="..." class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="..." />
                </form>
                {{-- Botón buscar --}}
                <button type="submit" form="search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" ... />
                </button>
                {{-- Botón filtros --}}
                <button class="btn-icon bg-sky-500 hover:bg-sky-600" title="Filtrar">
                    <img src="{{ asset('icons/filtros.ico') }}" ... />
                </button>
                {{-- Botón limpiar (condicional) --}}
                @if($busqueda || $filtroEstado)
                    <a href="..." class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
            </div>
        </div>

        {{-- 2. Tabla --}}
        <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">...</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse($items as $item)
                            <tr>...</tr>
                        @empty
                            <tr>
                                <td colspan="N" class="px-4 py-8 text-center text-[#777]">No hay registros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3. Paginación --}}
        <div class="px-1">
            {{ $items->links() }}
        </div>

    </div>

    {{-- Modales --}}
    ...
</x-app-layout>
```

## Reglas clave

### Contenedor `space-y-5`
- **OBLIGATORIO**: La card de búsqueda, la tabla y la paginación deben estar dentro de un `<div class="space-y-5">`.
- Esto genera **1.25rem (20px)** de separación automática entre secciones.
- **NUNCA** poner la card de búsqueda fuera del `space-y-5` (quedaría pegada al header).

### Card de búsqueda
| Elemento | Clases | Notas |
|----------|--------|-------|
| Card | `rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm` | Interior con `p-4` |
| Input | `rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm` | Alto fijo por `py-2.5` |
| Botones | `btn-icon` | Alto automático 2.5rem |

### Card de tabla
| Elemento | Clases | Notas |
|----------|--------|-------|
| Card | `overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm` | **Sin `p-4`** (el contenido define el padding) |
| Overflow | `overflow-x-auto` dentro de la card | Permite scroll horizontal |
| Cabecera | `bg-[#faf8f2] text-left text-[#5a4a2a]` | Fondo beige, texto dorado |
| Filas | `divide-y divide-[#efeee9]` | Separadores sutiles |
| Celdas | `px-4 py-3` | Padding estándar |

### Header slot
- El `x-slot name="header"` solo debe contener el **título** del módulo.
- **NUNCA** poner la barra de búsqueda dentro del header slot.

## Módulos que siguen esta estructura

| Módulo | Archivo | Estructura |
|--------|---------|------------|
| Ventas | `ventas/index.blade.php` | ✓ Estándar |
| Pedidos | `pedidos/index.blade.php` | ✓ Estándar |
| Diseños | `diseno/index.blade.php` | ✓ Estándar |
| Productos | `productos/index.blade.php` | ✓ Estándar |
| Clientes | `clientes/index.blade.php` | ✓ Estándar |
| Usuarios | `usuarios/index.blade.php` | ✓ Estándar |
| Cajas | `cajas/index.blade.php` | ✓ Estándar |

## Diferencia entre `p-4` y sin `p-4`

- **Card de búsqueda**: usa `p-4` porque el contenido interno (input + botones) necesita respirar.
- **Card de tabla**: **NO** usa `p-4` porque la tabla define su propio padding por celda (`px-4 py-3`). Si se agrega `p-4` a la card de tabla, se genera doble padding y la tabla se ve desproporcionada.
