@php
    $puedeGestionarRoles = auth()->user()->tienePermiso('roles.gestionar');
@endphp

<x-app-layout>
    <x-slot name="header">
        <span>Usuarios</span>
    </x-slot>

    <style>
        .btn-icon:focus-visible,
        .btn-icon:focus,
        .btn-icon-sm:focus-visible,
        .btn-icon-sm:focus {
            outline: 0 none !important;
        }
        .btn-icon:active,
        .btn-icon-sm:active {
            filter: brightness(0.85);
        }
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            flex-shrink: 0;
            color: #fff;
        }
        .btn-icon.is-active {
            filter: brightness(0.8);
        }
        .btn-icon-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            flex-shrink: 0;
            color: #fff;
        }
    </style>

    <div
        class="space-y-5"
        x-data="{
            modalNuevo: false,
            modalRoles: false,
            puedeGestionarRoles: @js($puedeGestionarRoles),
            cargandoRoles: false,
            roles: [],
            permisos: [],
            mensajeRoles: '',
            errorRoles: false,
            editandoRolId: null,
            rolForm: {
                nombre: '',
                descripcion: '',
                activo: true,
                permisos: [],
            },
            get csrf() {
                return document.querySelector('meta[name=csrf-token]')?.content || '';
            },
            async abrirModalRoles() {
                this.modalRoles = true;
                if (this.roles.length === 0 || this.permisos.length === 0) {
                    await this.cargarRoles();
                }
            },
            abrirNuevo() {
                this.modalNuevo = true;
                setTimeout(() => {
                    const f = document.getElementById('nuevo-form');
                    if (!f) return;
                    f.querySelectorAll('input:not([type=hidden]), select').forEach(el => {
                        if (el.tagName === 'SELECT') el.selectedIndex = 0;
                        else el.value = '';
                    });
                }, 50);
            },
            cerrarNuevo() {
                this.modalNuevo = false;
            },
            cerrarModalRoles() {
                this.modalRoles = false;
                this.limpiarFormularioRol();
                this.mensajeRoles = '';
                this.errorRoles = false;
            },
            limpiarFormularioRol() {
                this.editandoRolId = null;
                this.rolForm = {
                    nombre: '',
                    descripcion: '',
                    activo: true,
                    permisos: [],
                };
            },
            async cargarRoles() {
                this.cargandoRoles = true;
                try {
                    const response = await fetch('{{ route('roles.panel_data', [], false) }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await response.json();
                    if (data?.ok) {
                        this.roles = data.roles || [];
                        this.permisos = data.permisos || [];
                        this.mensajeRoles = '';
                        this.errorRoles = false;
                        return;
                    }
                    this.mensajeRoles = 'No se pudo cargar la gestion de roles.';
                    this.errorRoles = true;
                } catch (e) {
                    this.mensajeRoles = 'Error al cargar roles.';
                    this.errorRoles = true;
                } finally {
                    this.cargandoRoles = false;
                }
            },
            editarRol(rol) {
                this.editandoRolId = rol.id;
                this.rolForm.nombre = rol.nombre || '';
                this.rolForm.descripcion = rol.descripcion || '';
                this.rolForm.activo = !!rol.activo;
                this.rolForm.permisos = (rol.permisos || []).map((p) => p.id);
                this.mensajeRoles = 'Editando rol: ' + (rol.nombre || '');
                this.errorRoles = false;
            },
            async guardarRol() {
                if (!this.puedeGestionarRoles) return;
                this.mensajeRoles = '';
                this.errorRoles = false;

                const payload = {
                    nombre: (this.rolForm.nombre || '').trim(),
                    descripcion: (this.rolForm.descripcion || '').trim(),
                    activo: this.rolForm.activo ? 1 : 0,
                    permisos: this.rolForm.permisos,
                };

                if (payload.nombre === '') {
                    this.mensajeRoles = 'El nombre del rol es obligatorio.';
                    this.errorRoles = true;
                    return;
                }

                const editando = !!this.editandoRolId;
                const url = editando
                    ? `/roles/${this.editandoRolId}`
                    : `{{ route('roles.store', [], false) }}`;
                const method = editando ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json();
                    if (!response.ok || !data?.ok) {
                        this.mensajeRoles = data?.message || 'No se pudo guardar el rol.';
                        this.errorRoles = true;
                        return;
                    }

                    this.mensajeRoles = data.message || 'Rol guardado correctamente.';
                    this.errorRoles = false;
                    this.limpiarFormularioRol();
                    await this.cargarRoles();
                } catch (e) {
                    this.mensajeRoles = 'Error al guardar el rol.';
                    this.errorRoles = true;
                }
            },
            async eliminarRol(rol) {
                if (!this.puedeGestionarRoles) return;
                if (!rol?.id) return;
                if (!confirm(`Deseas eliminar el rol ${rol.nombre}?`)) return;

                try {
                    const response = await fetch(`/roles/${rol.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                    });
                    const data = await response.json();
                    if (!response.ok || !data?.ok) {
                        this.mensajeRoles = data?.message || 'No se pudo eliminar el rol.';
                        this.errorRoles = true;
                        return;
                    }

                    this.mensajeRoles = data.message || 'Rol eliminado correctamente.';
                    this.errorRoles = false;
                    if (this.editandoRolId === rol.id) {
                        this.limpiarFormularioRol();
                    }
                    await this.cargarRoles();
                } catch (e) {
                    this.mensajeRoles = 'Error al eliminar rol.';
                    this.errorRoles = true;
                }
            },
            togglePermiso(id) {
                const idx = this.rolForm.permisos.indexOf(id);
                if (idx === -1) {
                    this.rolForm.permisos.push(id);
                } else {
                    this.rolForm.permisos.splice(idx, 1);
                }
            }
        }"
    >
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm" x-data="{ filtrosAbiertos: false, rolFiltro: '{{ $filtroRol ?? '' }}', activoFiltro: '{{ $filtroActivo ?? '' }}' }">
            <div class="flex items-center gap-2">
                <form id="search-form" method="GET" action="{{ route('usuarios.index') }}" class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm" placeholder="Buscar por nombre o correo" />
                </form>
                <button type="submit" form="search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain" />
                </button>
                @if($filtroRol || $filtroActivo !== null && $filtroActivo !== '' || $busqueda)
                    <a href="{{ route('usuarios.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif

                <button
                    type="button"
                    @click="filtrosAbiertos = !filtrosAbiertos"
                    class="btn-icon bg-sky-500 hover:bg-sky-600"
                    title="Filtrar"
                    :class="{ 'is-active': filtrosAbiertos || '{{ $filtroRol || ($filtroActivo !== null && $filtroActivo !== '') }}' === '1' }"
                >
                    <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain" />
                </button>

                @if(auth()->user()->tienePermiso('roles.ver'))
                    <button
                        type="button"
                        @click="abrirModalRoles()"
                        class="btn-icon bg-violet-600 hover:bg-violet-700"
                        title="Gestionar roles"
                        aria-label="Gestionar roles"
                    >
                        <img src="{{ asset('icons/roles.ico') }}" alt="Roles" class="h-5 w-5 object-contain" />
                    </button>
                @endif

                @if(auth()->user()->tienePermiso('usuarios.gestionar'))
                    <button type="button" @click="abrirNuevo()" class="btn-icon" style="background-color:#09090f;color:white" title="Nuevo usuario">
                        <img src="{{ asset('icons/nuevo.ico') }}" alt="Nuevo" class="h-5 w-5 object-contain" />
                    </button>
                @endif
            </div>

            <form x-show="filtrosAbiertos" x-transition method="GET" action="{{ route('usuarios.index') }}" class="mt-4 flex flex-wrap items-end gap-4 border-t border-[#efe7d2] pt-4">
                <input type="hidden" name="q" value="{{ $busqueda }}" />
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Rol</label>
                    <select name="rol_id" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Todos</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" @selected((string) $filtroRol === (string) $rol->id)>{{ strtoupper($rol->nombre) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</label>
                    <select name="activo" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Todos</option>
                        <option value="1" @selected($filtroActivo === '1')>Activo</option>
                        <option value="0" @selected($filtroActivo === '0')>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus:outline-none focus-visible:outline-[none] focus:ring-2 focus:ring-sky-500">Filtrar</button>
            </form>
        </div>

        @if (session('ok'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('ok') }}</div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Nombre</th>
                            <th class="px-4 py-3 font-semibold">Correo</th>
                            <th class="px-4 py-3 font-semibold">Rol</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $usuario->name }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $usuario->email }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ strtoupper($usuario->rol->nombre ?? '-') }}</td>
                                <td class="px-4 py-3">
                                    @if($usuario->activo)
                                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Activo</span>
                                    @else
                                        <span class="rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->tienePermiso('usuarios.gestionar'))
                                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-icon-sm bg-amber-400 hover:bg-amber-500" title="Editar">
                                                <img src="{{ asset('icons/editar.ico') }}" alt="Editar" class="h-4 w-4 object-contain" />
                                            </a>
                                            <form method="POST" action="{{ route('usuarios.toggle_activo', $usuario) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                                    <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}" class="h-4 w-4 object-contain" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[#777]">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#efe7d2] px-4 py-3">{{ $usuarios->links('pagination.gold') }}</div>
        </div>

        @if(auth()->user()->tienePermiso('roles.ver'))
            <template x-teleport="body">
                <div x-show="modalRoles" style="display: none;">
                    <div x-transition.opacity class="fixed inset-0 z-[70] bg-black/60" @click="cerrarModalRoles()"></div>
                    <div x-transition class="fixed inset-0 z-[80] flex items-center justify-center p-3 sm:p-6">
                        <div class="flex h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                            <div class="flex items-center justify-between border-b border-[#efe7d2] px-4 py-3">
                                <h3 class="text-base font-semibold text-[#2a2419]">Gestion de roles</h3>
                                <button type="button" @click="cerrarModalRoles()" class="rounded-lg border border-[#d1be8a] px-3 py-1.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Cerrar</button>
                            </div>

                            <div class="grid h-full min-h-0 gap-4 overflow-hidden p-4 lg:grid-cols-[360px_minmax(0,1fr)]">
                                <div class="min-h-0 overflow-auto rounded-2xl border border-[#e5dec8] bg-[#fcf9f3] p-4">
                                    <h4 class="text-sm font-semibold text-[#2a2419]" x-text="editandoRolId ? 'Editar rol' : 'Nuevo rol'"></h4>

                                    <div class="mt-3 space-y-3">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium uppercase tracking-[0.18em] text-[#8a6a2e]">Nombre</label>
                                            <input type="text" x-model="rolForm.nombre" :disabled="!puedeGestionarRoles" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm disabled:opacity-60" placeholder="Ejemplo: caja" />
                                        </div>

                                        <div>
                                            <label class="mb-1 block text-xs font-medium uppercase tracking-[0.18em] text-[#8a6a2e]">Descripcion</label>
                                            <textarea x-model="rolForm.descripcion" rows="2" :disabled="!puedeGestionarRoles" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm disabled:opacity-60" placeholder="Descripcion corta"></textarea>
                                        </div>

                                        <div>
                                            <label class="mb-1 block text-xs font-medium uppercase tracking-[0.18em] text-[#8a6a2e]">Estado</label>
                                            <select x-model="rolForm.activo" :disabled="!puedeGestionarRoles" class="w-full rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-3 py-2 text-sm disabled:opacity-60">
                                                <option :value="true">Activo</option>
                                                <option :value="false">Inactivo</option>
                                            </select>
                                        </div>

                                        <div>
                                            <p class="mb-1 block text-xs font-medium uppercase tracking-[0.18em] text-[#8a6a2e]">Permisos</p>
                                            <div class="max-h-52 overflow-auto rounded-xl border border-[#e5dec8] bg-white p-2">
                                                <template x-for="permiso in permisos" :key="permiso.id">
                                                    <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-[#fcf9f3]">
                                                        <input
                                                            type="checkbox"
                                                            :checked="rolForm.permisos.includes(permiso.id)"
                                                            @change="togglePermiso(permiso.id)"
                                                            :disabled="!puedeGestionarRoles"
                                                            class="rounded border-[#d1be8a] text-[#8a6a2e] focus:ring-[#d1be8a]"
                                                        >
                                                        <span class="text-[#4a4026]" x-text="permiso.nombre"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>

                                        @if($puedeGestionarRoles)
                                            <div class="flex gap-2">
                                                <button type="button" @click="guardarRol()" class="rounded-xl bg-[#111] px-4 py-2 text-sm font-medium text-white hover:bg-[#262626]">
                                                    <span x-text="editandoRolId ? 'Actualizar rol' : 'Guardar rol'"></span>
                                                </button>
                                                <button type="button" @click="limpiarFormularioRol()" class="rounded-xl border border-[#d1be8a] px-4 py-2 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="min-h-0 overflow-auto rounded-2xl border border-[#e5dec8] bg-white">
                                    <div class="flex items-center justify-between border-b border-[#efe7d2] px-4 py-3">
                                        <h4 class="text-sm font-semibold text-[#2a2419]">Listado de roles</h4>
                                        <button type="button" @click="cargarRoles()" class="rounded-lg border border-[#d1be8a] px-2.5 py-1 text-xs text-[#5a4314] hover:bg-[#fff5dd]">Recargar</button>
                                    </div>

                                    <div x-show="cargandoRoles" class="p-4 text-sm text-[#777]">Cargando roles...</div>
                                    <div class="overflow-x-auto" x-show="!cargandoRoles">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                                                <tr>
                                                    <th class="px-4 py-3 font-semibold">Rol</th>
                                                    <th class="px-4 py-3 font-semibold">Permisos</th>
                                                    <th class="px-4 py-3 font-semibold">Usuarios</th>
                                                    <th class="px-4 py-3 font-semibold">Estado</th>
                                                    <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-[#efeee9]">
                                                <template x-for="rol in roles" :key="rol.id">
                                                    <tr>
                                                        <td class="px-4 py-3 font-medium text-[#2d2b24]" x-text="(rol.nombre || '').toUpperCase()"></td>
                                                        <td class="px-4 py-3 text-[#4a4026]" x-text="rol.permisos_count"></td>
                                                        <td class="px-4 py-3 text-[#4a4026]" x-text="rol.usuarios_count"></td>
                                                        <td class="px-4 py-3">
                                                            <span class="rounded-lg px-2.5 py-1 text-xs font-medium" :class="rol.activo ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'" x-text="rol.activo ? 'Activo' : 'Inactivo'"></span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <div class="flex justify-end gap-2">
                                                                @if($puedeGestionarRoles)
                                                                    <button type="button" @click="editarRol(rol)" class="btn-icon-sm bg-amber-400 hover:bg-amber-500" title="Editar rol">
                                                                        <img src="{{ asset('icons/editar.ico') }}" alt="Editar" class="h-4 w-4 object-contain" />
                                                                    </button>
                                                                    <button type="button" @click="eliminarRol(rol)" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Eliminar rol">
                                                                        <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="Eliminar" class="h-4 w-4 object-contain" />
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr x-show="!cargandoRoles && roles.length === 0">
                                                    <td colspan="5" class="px-4 py-8 text-center text-[#777]">No hay roles registrados.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-[#efe7d2] px-4 py-2.5">
                                <p class="text-xs" :class="errorRoles ? 'text-rose-700' : 'text-emerald-700'" x-text="mensajeRoles"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif

        @if(auth()->user()->tienePermiso('usuarios.gestionar'))
            <template x-teleport="body">
                <div x-show="modalNuevo" style="display: none;">
                    <div x-transition.opacity class="fixed inset-0 z-[70] bg-black/60" @click="cerrarNuevo()"></div>
                    <div x-transition class="fixed inset-0 z-[80] flex items-center justify-center p-3 sm:p-6">
                        <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                            <div class="flex items-center justify-between border-b border-[#efe7d2] px-4 py-3">
                                <h3 class="text-base font-semibold text-[#2a2419]">Nuevo usuario</h3>
                                <button type="button" @click="cerrarNuevo()" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                                    <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                                </button>
                            </div>

                            <form id="nuevo-form" method="POST" action="{{ route('usuarios.store') }}" class="p-4">
                                @csrf
                                @include('usuarios._form')

                                <div class="mt-6 border-t border-[#efe7d2] pt-4 flex justify-end gap-2">
                                    <button type="button" @click="cerrarNuevo()" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]">Cancelar</button>
                                    <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Guardar usuario</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    </div>
</x-app-layout>
