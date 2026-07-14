<x-app-layout>
    <x-slot name="header">
        <span>Editar pedido</span>
    </x-slot>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('pedidos.update', $pedido) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pedidos._form')

            <div class="mt-6 flex gap-2">
                <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Actualizar pedido</button>
                <a href="{{ route('pedidos.index') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Volver</a>
            </div>
        </form>

    </div>
</x-app-layout>
