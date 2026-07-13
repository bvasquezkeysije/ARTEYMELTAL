# Regla 11 - Tailwind JIT no genera clases usadas solo en bindings dinámicos de Alpine.js

## Problema detectado

Tailwind CSS v3 con modo **JIT (Just-In-Time)** escanea los archivos `.blade.php` buscando nombres de clases CSS para generar solo las que se usan. Sin embargo, **solo escanea HTML estático** (atributos `class="..."`). **No escanea expresiones JavaScript dinámicas** dentro de directivas de Alpine.js como `:class="..."`.

### Clases afectadas en nuestro sistema

| Clase | Uso | Generada en CSS |
|-------|-----|-----------------|
| `invisible` | Ocultar "Ya derivado" en modal Derivar | **NO** |
| `opacity-40` | Botón deshabilitado (opacidad reducida) | **NO** |
| `cursor-not-allowed` | Cursor de no permitido en botón deshabilitado | SI (existe en otro HTML estático) |

### Código que causó el bug

```html
<!-- ANTES (ROTO) - Tailwind no genera .invisible ni .opacity-40 -->
<span :class="condicion ? '' : 'invisible'">Ya derivado</span>
<button :class="condicion ? 'opacity-40 cursor-not-allowed' : ''">
```

Al no existir `.invisible` en el CSS compilado, el texto "Ya derivado" **siempre se veía**, aunque la condición fuera `false`. El `:class` cambiaba entre `''` e `'invisible'`, pero como la clase no existía, no hacía absolutamente nada.

## Solución

Reemplazar `:class` con `:style` para clases que **solo se usen dentro de bindings dinámicos de Alpine.js**:

```html
<!-- DESPUÉS (CORRECTO) - Inline style no depende de CSS compilado -->
<span :style="condicion ? '' : 'visibility: hidden'">Ya derivado</span>
<button :style="condicion ? 'opacity: 0.4; cursor: not-allowed' : ''">
```

### Por qué `:style` funciona

- `:style` es evaluado por Alpine.js en runtime y aplica estilos inline directamente al elemento
- **No necesita que la clase CSS exista** en el CSS compilado
- `visibility: hidden` oculta el elemento pero **mantiene su espacio** (mismo efecto que `invisible`)
- `opacity: 0.4` reduce opacidad (mismo efecto que `opacity-40`)

## Regla para el futuro

### CUANDO USAR `:class` (seguro)
```html
<!-- Las clases existen en HTML estático en algún otro archivo -->
:class="activo ? 'bg-green-500' : 'bg-red-500'"
```

### CUANDO USAR `:style` (obligatorio)
```html
<!-- Las clases SOLO aparecen en bindings dinámicos de Alpine -->
:class="condicion ? '' : 'invisible'"          <!-- MAL -->
:style="condicion ? '' : 'visibility: hidden'"  <!-- BIEN -->

:class="condicion ? 'opacity-40' : ''"                      <!-- MAL -->
:style="condicion ? 'opacity: 0.4; cursor: not-allowed' : ''" <!-- BIEN -->
```

### Test rápido para detectar el problema

Si una clase CSS en un `:class` de Alpine **no produce efecto visual**, verificar si existe en el CSS compilado:

```bash
# En el servidor
grep -c 'nombre-clase' /var/www/arteymetal/Sistema-ArteyMetal/public/build/assets/app-*.css
# Si返回 0 → la clase NO existe en el CSS
```

## Verificación post-fix

1. Abrir el modal Derivar de un pedido **nuevo** (nunca derivado)
2. Ambos botones deben estar **activos** (opacidad completa, cursor normal)
3. Ambos textos "Ya derivado" deben estar **invisibles** (pero ocupando espacio para mantener simetría)
4. Al derivar a Diseño → botón Diseño se deshabilita, "Ya derivado" se vuelve visible
5. Al derivar a Producción → botón Producción se deshabilita, "Ya derivado" se vuelve visible

## Archivos afectados

- `resources/views/pedidos/index.blade.php` — Modal Derivar (líneas 550, 554, 562, 566)
