<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-[#7a6030]">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name', $usuario->name ?? '') }}" required class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="Nombre del usuario" />
        @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-medium text-[#7a6030]">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email', $usuario->email ?? '') }}" required class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="usuario@correo.com" />
        @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="rol_id" class="mb-2 block text-sm font-medium text-[#7a6030]">Rol</label>
        <select id="rol_id" name="rol_id" required class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900">
            <option value="">Seleccionar rol</option>
            @foreach($roles as $rol)
                <option value="{{ $rol->id }}" @selected((int) old('rol_id', $usuario->rol_id ?? 0) === (int) $rol->id)>{{ strtoupper($rol->nombre) }}</option>
            @endforeach
        </select>
        @error('rol_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="activo" class="mb-2 block text-sm font-medium text-[#7a6030]">Estado</label>
        <select id="activo" name="activo" class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900">
            <option value="1" @selected(old('activo', isset($usuario) ? (int) $usuario->activo : 1) == 1)>Activo</option>
            <option value="0" @selected(old('activo', isset($usuario) ? (int) $usuario->activo : 1) == 0)>Inactivo</option>
        </select>
        @error('activo') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="mb-2 block text-sm font-medium text-[#7a6030]">Contrasena {{ isset($usuario) ? '(opcional)' : '' }}</label>
        <input id="password" name="password" type="password" {{ isset($usuario) ? '' : 'required' }} class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="Minimo 6 caracteres" />
        @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-[#7a6030]">Confirmar contrasena</label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ isset($usuario) ? '' : 'required' }} class="block w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-3 text-gray-900" placeholder="Repite la contrasena" />
    </div>
</div>

