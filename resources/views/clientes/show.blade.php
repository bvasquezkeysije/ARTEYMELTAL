<x-app-layout>
    <x-slot name="header">
        <span>Detalle cliente</span>
    </x-slot>

    <div class="rounded-2xl border border-[#e5dec8] bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Nombre completo</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $cliente->nombre_completo }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Documento</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $cliente->documento ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Telefono</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $cliente->telefono ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Correo</p>
                <p class="mt-1 text-[#1f1f1f]">{{ $cliente->correo ?: '-' }}</p>
            </div>
        </div>

        <div class="mt-5">
            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Direccion</p>
            <p class="mt-1 text-[#1f1f1f]">{{ $cliente->direccion ?: '-' }}</p>
        </div>

        <div class="mt-5">
            <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Observaciones</p>
            <p class="mt-1 text-[#1f1f1f]">{{ $cliente->observaciones ?: '-' }}</p>
        </div>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('clientes.edit', $cliente) }}" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Editar</a>
            <a href="{{ route('clientes.index') }}" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]">Volver</a>
        </div>
    </div>
</x-app-layout>
