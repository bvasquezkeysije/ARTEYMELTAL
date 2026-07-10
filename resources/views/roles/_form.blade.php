<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="nombre" class="mb-2 block text-sm font-medium text-[#4d4026]">Nombre del rol</label>
        <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $rol->nombre ?? '') }}" required class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12]" placeholder="Ejemplo: caja" />
        @error('nombre') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="activo" class="mb-2 block text-sm font-medium text-[#4d4026]">Estado</label>
        <select id="activo" name="activo" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12]">
            <option value="1" @selected(old('activo', isset($rol) ? (int) $rol->activo : 1) == 1)>Activo</option>
            <option value="0" @selected(old('activo', isset($rol) ? (int) $rol->activo : 1) == 0)>Inactivo</option>
        </select>
        @error('activo') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="descripcion" class="mb-2 block text-sm font-medium text-[#4d4026]">Descripcion</label>
    <textarea id="descripcion" name="descripcion" rows="2" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-[#251e12]" placeholder="Descripcion corta del rol">{{ old('descripcion', $rol->descripcion ?? '') }}</textarea>
    @error('descripcion') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
</div>

<div class="mt-4">
    <p class="mb-2 block text-sm font-medium text-[#4d4026]">Permisos del rol</p>
    <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
        @php
            $seleccionados = collect(old('permisos', isset($rol) ? $rol->permisos->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
        @endphp
        @foreach ($permisos as $permiso)
            <label class="flex items-center gap-2 rounded-xl border border-[#e5dec8] bg-[#fffdf7] px-3 py-2 text-sm text-[#3f3420]">
                <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}" @checked(in_array($permiso->id, $seleccionados, true)) class="rounded border-[#b9943d] text-[#b9943d] focus:ring-[#b9943d]">
                <span>{{ $permiso->nombre }}</span>
            </label>
        @endforeach
    </div>
    @error('permisos') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    @error('permisos.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
</div>

