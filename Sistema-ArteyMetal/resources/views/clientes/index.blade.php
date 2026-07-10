<x-app-layout>
    <x-slot name="header">
        <span>Clientes</span>
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
            modalCliente: false,
            modalNuevo: false,
            clienteVista: null,
            filtrosAbiertos: false,
            documento: '{{ $filtroDocumento ?? '' }}',
            abrirNuevo() {
                this.modalNuevo = true;
                setTimeout(() => {
                    const f = document.getElementById('nuevo-cliente-form');
                    if (!f) return;
                    f.querySelectorAll('input:not([type=hidden]), select, textarea').forEach(el => {
                        if (el.tagName === 'SELECT') el.selectedIndex = 0;
                        else el.value = '';
                    });
                }, 50);
            },
            cerrarNuevo() {
                this.modalNuevo = false;
            },
        }"
    >
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <form id="search-form" method="GET" action="{{ route('clientes.index') }}" class="flex min-w-0 flex-1">
                    <input type="text" name="q" value="{{ $busqueda }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="Buscar por nombre, telefono, correo o documento" />
                </form>
                <button type="submit" form="search-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <button
                    type="button"
                    @click="filtrosAbiertos = !filtrosAbiertos"
                    class="btn-icon bg-sky-500 hover:bg-sky-600"
                    title="Filtrar"
                    :class="{ 'is-active': filtrosAbiertos || '{{ $filtroDocumento ?? '' }}' !== '' }"
                >
                    <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if($filtroDocumento || $busqueda)
                    <a href="{{ route('clientes.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
                @if(auth()->user()->tienePermiso('clientes.gestionar'))
                    <button type="button" @click="abrirNuevo()" class="btn-icon" style="background-color:#09090f;color:white" title="Nuevo cliente">
                        <img src="{{ asset('icons/nuevo.ico') }}" alt="Nuevo" class="h-5 w-5 object-contain pointer-events-none" />
                    </button>
                @endif
            </div>

            <form x-show="filtrosAbiertos" x-transition method="GET" action="{{ route('clientes.index') }}" class="mt-4 flex flex-wrap items-end gap-4 border-t border-[#efe7d2] pt-4">
                <input type="hidden" name="q" value="{{ $busqueda }}" />
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Documento</label>
                    <input type="text" name="documento" value="{{ $filtroDocumento }}" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm" placeholder="DNI o RUC" />
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">Filtrar</button>
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
                            <th class="px-4 py-3 font-semibold">Documento</th>
                            <th class="px-4 py-3 font-semibold">Telefono</th>
                            <th class="px-4 py-3 font-semibold">Correo</th>
                            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#efeee9]">
                        @forelse ($clientes as $cliente)
                            @php
                                $clienteVistaData = [
                                    'nombre_completo' => $cliente->nombre_completo,
                                    'documento' => $cliente->documento ?: '-',
                                    'telefono' => $cliente->telefono ?: '-',
                                    'correo' => $cliente->correo ?: '-',
                                    'direccion' => $cliente->direccion ?: '-',
                                    'observaciones' => $cliente->observaciones ?: '-',
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-[#2d2b24]">{{ $cliente->nombre_completo }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $cliente->documento ?: '-' }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $cliente->telefono ?: '-' }}</td>
                                <td class="px-4 py-3 text-[#4a4026]">{{ $cliente->correo ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->tienePermiso('clientes.ver'))
                                            <button
                                                type="button"
                                                @click="clienteVista = @js($clienteVistaData); modalCliente = true"
                                                class="btn-icon-sm" style="background-color:#0891B2"
                                                title="Ver detalle"
                                            >
                                                <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver detalle" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @endif
                                        @if(auth()->user()->tienePermiso('clientes.gestionar'))
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn-icon-sm bg-amber-400 hover:bg-amber-500" title="Editar">
                                                <img src="{{ asset('icons/editar.ico') }}" alt="Editar" class="h-4 w-4 object-contain pointer-events-none" />
                                            </a>
                                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('Deseas eliminar este cliente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Eliminar">
                                                    <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="Eliminar" class="h-4 w-4 object-contain pointer-events-none" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[#777]">No hay clientes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#efe7d2] px-4 py-3">{{ $clientes->links('pagination.gold') }}</div>
        </div>

        <template x-teleport="body">
            <div x-show="modalCliente" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="modalCliente = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-[#efe7d2] px-5 py-3">
                    <h3 class="text-base font-semibold text-[#2a2419]">Detalle rapido de cliente</h3>
                    <button type="button" @click="modalCliente = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>

                <div class="grid gap-4 p-5 md:grid-cols-2" x-show="clienteVista">
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Nombre completo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="clienteVista?.nombre_completo"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Documento</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="clienteVista?.documento"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Telefono</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="clienteVista?.telefono"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Correo</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="clienteVista?.correo"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Direccion</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="clienteVista?.direccion"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#8a6a2e]">Observaciones</p>
                        <p class="mt-1 text-[#1f1f1f]" x-text="clienteVista?.observaciones"></p>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </template>

        @if(auth()->user()->tienePermiso('clientes.gestionar'))
            <template x-teleport="body">
                <div x-show="modalNuevo" style="display: none;">
                    <div x-transition.opacity class="fixed inset-0 z-[70] bg-black/60" @click="cerrarNuevo()"></div>
                    <div x-transition class="fixed inset-0 z-[80] flex items-center justify-center p-3 sm:p-6">
                        <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-[#e5dec8] bg-white shadow-xl" @click.stop>
                            <div class="flex items-center justify-between border-b border-[#efe7d2] px-4 py-3">
                                <h3 class="text-base font-semibold text-[#2a2419]">Nuevo cliente</h3>
                                <button type="button" @click="cerrarNuevo()" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                                    <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                                </button>
                            </div>

                            <form id="nuevo-cliente-form" method="POST" action="{{ route('clientes.store') }}" class="p-4">
                                @csrf
                                @include('clientes._form')

                                <div class="mt-6 flex justify-end gap-2 border-t border-[#efe7d2] pt-4">
                                    <button type="button" @click="cerrarNuevo()" class="rounded-xl border border-[#d1be8a] px-4 py-2.5 text-sm font-medium text-[#5a4314] hover:bg-[#fff5dd]">Cancelar</button>
                                    <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Guardar cliente</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        @endif
    </div>
</x-app-layout>
