<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="nombre_completo" class="mb-2 block text-sm font-medium text-[#7a6030]">Nombre completo</label>
        <input id="nombre_completo" name="nombre_completo" type="text" value="{{ old('nombre_completo', $cliente->nombre_completo ?? '') }}" required class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="Nombre del cliente" />
        @error('nombre_completo') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="documento" class="mb-2 block text-sm font-medium text-[#7a6030]">Documento</label>
        <input id="documento" name="documento" type="text" value="{{ old('documento', $cliente->documento ?? '') }}" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="DNI / RUC" />
        @error('documento') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="telefono" class="mb-2 block text-sm font-medium text-[#7a6030]">Telefono</label>
        <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $cliente->telefono ?? '') }}" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="999999999" />
        @error('telefono') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="correo" class="mb-2 block text-sm font-medium text-[#7a6030]">Correo</label>
        <input id="correo" name="correo" type="email" value="{{ old('correo', $cliente->correo ?? '') }}" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="cliente@correo.com" />
        @error('correo') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="direccion" class="mb-2 block text-sm font-medium text-[#7a6030]">Direccion</label>
    <textarea id="direccion" name="direccion" rows="2" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="Direccion del cliente">{{ old('direccion', $cliente->direccion ?? '') }}</textarea>
    @error('direccion') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
</div>

<div class="mt-4">
    <label for="observaciones" class="mb-2 block text-sm font-medium text-[#7a6030]">Observaciones</label>
    <textarea id="observaciones" name="observaciones" rows="3" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="Notas internas">{{ old('observaciones', $cliente->observaciones ?? '') }}</textarea>
    @error('observaciones') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
</div>
