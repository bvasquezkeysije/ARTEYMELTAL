# Regla 17 - Modal de Exito y Error

## Donde aplica
Toda vista que recibe flash session messages (`->with("ok", ...)` o `->withErrors(...)`) DEBE mostrar modales de exito/error.

## Modal de Exito (showSuccess)

### Patron estandar
```html
<template x-teleport="body">
    <div x-show="showSuccess" style="display: none;">
        <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showSuccess = false"></div>
        <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                    <img src="{{ asset('icons/Valido-Verde.png') }}" alt="Valido" class="h-8 w-8 object-contain pointer-events-none" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ session('ok') }}</h3>
                <button type="button" @click="showSuccess = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
            </div>
        </div>
    </div>
</template>
```

### Inicializar en x-data
```js
showSuccess: {{ session()->has('ok') ? 'true' : 'false' }}
```

## Modal de Error (showErrors)

### Patron estandar
```html
<template x-teleport="body">
    <div x-show="showErrors" style="display: none;">
        <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showErrors = false"></div>
        <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-8 pt-10 pb-10 shadow-xl">
                <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                    <img src="{{ asset('icons/Alerta-Rojo.png') }}" alt="Alerta" class="h-8 w-8 object-contain pointer-events-none" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center">Se encontraron errores</h3>
                <ul class="mt-4 space-y-2 text-sm text-red-700">
                    <template x-for="(msg, idx) in errorMessages" :key="idx">
                        <li x-text="msg"></li>
                    </template>
                </ul>
                <div class="text-center">
                    <button type="button" @click="showErrors = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                </div>
            </div>
        </div>
    </div>
</template>
```

### Inicializar en x-data
```js
showErrors: {{ $errors->any() ? 'true' : 'false' }},
errorMessages: @js($errors->any() ? $errors->all() : [])
```

## Feedback AJAX (operaciones internas)
Para operaciones AJAX (DELETE archivo, etc.) que NO recargan la pagina, usar modales inline via Alpine:

```js
// En la funcion exitosa del fetch:
this.showAjaxSuccess = true;
this.ajaxSuccessMsg = 'Archivo eliminado correctamente.';
setTimeout(() => { this.showAjaxSuccess = false; }, 2500);

// En catch:
this.showAjaxError = true;
this.ajaxErrorMsg = 'Error al eliminar.';
setTimeout(() => { this.showAjaxError = false; }, 3000);
```

### Template para feedback AJAX rapido
```html
<div x-show="showAjaxSuccess" x-cloak x-transition
     class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg">
    <img src="{{ asset('icons/Valido-Verde.png') }}" alt="" class="h-5 w-5 object-contain pointer-events-none" />
    <span x-text="ajaxSuccessMsg"></span>
</div>
```

## Reglas
1. TODA vista con form que redirija con flash DEBE tener showSuccess y showErrors
2. El modal de exito usa icono verde (Valido-Verde.png) + fondo emerald-100
3. El modal de error usa icono rojo (Alerta-Rojo.png) + fondo red-100
4. El boton siempre es "Entendido" con fondo #111
5. Feedback AJAX rapido va fixed bottom-right, se oculta automaticamente
6. NUNCA usar alert() ni confirm() nativos del browser
