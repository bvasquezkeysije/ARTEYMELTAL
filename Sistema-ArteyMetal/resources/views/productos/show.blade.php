@php
    $categorias = [
        'medallas' => 'Medallas',
        'marbetes_distintivos' => 'Marbetes y Distintivos',
        'placas' => 'Placas',
        'reconocimientos' => 'Reconocimientos',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <span>Detalle producto</span>
    </x-slot>

    <div class="rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Codigo</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $producto->codigo }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Estado</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $producto->activo ? 'Activo' : 'Inactivo' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Nombre</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $producto->nombre }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Categoria</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $categorias[$producto->categoria] ?? $producto->categoria }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Precio referencia</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $producto->precio_referencia !== null ? 'S/ ' . number_format((float) $producto->precio_referencia, 2) : '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Stock actual</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $producto->stock_actual }}</p>
            </div>
        </div>

        <div class="mt-5">
            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Descripcion</p>
            <p class="mt-1 text-[#1f1f1f]">{{ $producto->descripcion ?: '-' }}</p>
        </div>

        <div class="mt-6">
            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Imagenes</p>

            @if ($producto->imagenes->isEmpty())
                <p class="mt-2 text-sm text-[#777]">No hay imagenes registradas para este producto.</p>
            @else
                <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($producto->imagenes as $imagen)
                        <a href="{{ route('productos.imagen.ver', $imagen, false) }}" target="_blank" class="block overflow-hidden rounded-xl border border-[#e5dec8] bg-[#fcf9f3]">
                            <img src="{{ route('productos.imagen.ver', $imagen, false) }}" alt="{{ $imagen->nombre_original }}" class="h-40 w-full object-cover" />
                            <div class="px-3 py-2 text-xs text-[#4a4026]">{{ $imagen->nombre_original }}</div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('productos.edit', $producto) }}" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Editar</a>
            <a href="{{ route('productos.index') }}" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]">Volver</a>
        </div>
    </div>
</x-app-layout>
