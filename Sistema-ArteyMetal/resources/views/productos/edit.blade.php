<x-app-layout>
    <x-slot name="header">
        <span>Editar producto</span>
    </x-slot>

    <div class="rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('productos._form')

            <div class="mt-6 flex gap-2">
                <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Actualizar producto</button>
                <a href="{{ route('productos.index') }}" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]">Volver</a>
            </div>
        </form>
    </div>
</x-app-layout>
