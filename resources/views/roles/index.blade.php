<x-app-layout>
    <x-slot name="header">
        <span>Roles</span>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <form method="GET" action="{{ route('roles.index') }}" class="flex w-full max-w-xl gap-2">
                    <input type="text" name="q" value="{{ $busqueda }}" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" placeholder="Buscar por nombre o descripcion" />
                    <button type="submit" class="rounded-xl border border-[#b9943d] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]">Buscar</button>
                </form>
                @if(auth()->user()->tienePermiso('roles.gestionar'))
                    <a href="{{ route('roles.create') }}" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Nuevo rol</a>
                @endif
            </div>
        </div>
        <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Rol</th>
                            <th class="px-4 py-3 font-semibold">Descripcion</th>
                            <th class="px-4 py-3 font-semibold">Permisos</th>
                            <th class="px-4 py-3 font-semibold">Usuarios</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($roles as $rol)
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ strtoupper($rol->nombre) }}</td>
                                <td class="px-4 py-3">{{ $rol->descripcion ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $rol->permisos_count }}</td>
                                <td class="px-4 py-3">{{ $rol->usuarios_count }}</td>
                                <td class="px-4 py-3">
                                    @if($rol->activo)
                                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Activo</span>
                                    @else
                                        <span class="rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->tienePermiso('roles.gestionar'))
                                            <a href="{{ route('roles.edit', $rol) }}" class="rounded-lg border border-[#d3c49f] px-2.5 py-1.5 text-xs font-medium text-[#5a4314]">Editar</a>
                                            <form method="POST" action="{{ route('roles.destroy', $rol) }}" onsubmit="return confirm('Deseas eliminar este rol?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-rose-300 px-2.5 py-1.5 text-xs font-medium text-rose-700">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-[#777]">No hay roles registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#efeee9] px-4 py-3">{{ $roles->links('pagination.gold') }}</div>
        </div>
    </div>
</x-app-layout>

