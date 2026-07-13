<x-app-layout>
    <x-slot name="header">
        <span>Caja</span>
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

    <div x-data="{ modalAbrir: false, cerrarId: null, detalleCaja: null, showSuccess: {{ session()->has('success') ? 'true' : 'false' }}, filtrosAbiertos: false }" class="space-y-5">
        <div x-show="showSuccess" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="showSuccess = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white px-16 pt-12 pb-12 text-center shadow-xl">
                    <div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                        <img src="{{ asset('icons/Valido-Verde.png') }}" alt="Valido" class="h-8 w-8 object-contain pointer-events-none" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ session('success') ?? 'Operacion exitosa.' }}</h3>
                    <button type="button" @click="showSuccess = false" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#111] py-3 text-sm font-semibold text-white hover:bg-[#262626]" style="padding-left:48px;padding-right:48px">Entendido</button>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif

        {{-- Barra superior: buscar + filtros + abrir caja --}}
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <form id="search-caja-form" method="GET" action="{{ route('cajas.index') }}" class="flex min-w-0 flex-1">
                        <input type="hidden" name="estado" value="{{ $filtroEstado ?? '' }}" />
                        <input type="text" name="q" value="{{ $busqueda ?? '' }}" class="min-w-0 flex-1 rounded-xl border border-[#d1be8a] bg-[#fffdf7] px-4 py-2.5 text-sm text-gray-900" placeholder="Buscar por caja o usuario" />
                    </form>
                </div>
                <button type="submit" form="search-caja-form" class="btn-icon bg-blue-600 hover:bg-blue-700" title="Buscar">
                    <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                <button
                    type="button"
                    @click="filtrosAbiertos = !filtrosAbiertos"
                    class="btn-icon bg-sky-500 hover:bg-sky-600"
                    title="Filtrar"
                    :class="{ 'is-active': filtrosAbiertos || '{{ $filtroEstado ?? '' }}' !== '' }"
                >
                    <img src="{{ asset('icons/filtros.ico') }}" alt="Filtrar" class="h-5 w-5 object-contain pointer-events-none" />
                </button>
                @if(($busqueda ?? '') !== '' || ($filtroEstado ?? '') !== '')
                    <a href="{{ route('cajas.index') }}" class="shrink-0 rounded-xl border border-[#d1be8a] px-3 py-2.5 text-sm text-[#5a4314] hover:bg-[#fff5dd]">Limpiar</a>
                @endif
                @if(auth()->user()->tienePermiso('caja.gestionar'))
                    <button type="button" @click="modalAbrir = true" class="btn-icon" style="background-color:#09090f;color:white" title="Abrir caja">
                        <img src="{{ asset('icons/nuevo.ico') }}" alt="Nuevo" class="h-5 w-5 object-contain pointer-events-none" />
                    </button>
                @endif
            </div>

            {{-- Filtros --}}
            <form x-show="filtrosAbiertos" x-transition method="GET" action="{{ route('cajas.index') }}" class="mt-4 flex flex-wrap items-end gap-4 border-t border-[#efe7d2] pt-4">
                <input type="hidden" name="q" value="{{ $busqueda ?? '' }}" />
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</label>
                    <select name="estado" class="rounded-xl border border-[#d1be8a] bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm">
                        <option value="">Todos</option>
                        <option value="abierta" @selected(($filtroEstado ?? '') === 'abierta')>Abierta</option>
                        <option value="cerrada" @selected(($filtroEstado ?? '') === 'cerrada')>Cerrada</option>
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">Filtrar</button>
            </form>
        </div>

        {{-- Tabla historial de cajas --}}
        <div class="rounded-2xl border border-[#e5dec8] bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#faf8f2] text-left text-[#5a4a2a]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Caja</th>
                            <th class="px-4 py-3 font-semibold">Usuario</th>
                            <th class="px-4 py-3 font-semibold">Apertura</th>
                            <th class="px-4 py-3 font-semibold">Inicial</th>
                            <th class="px-4 py-3 font-semibold">Cierre</th>
                            <th class="px-4 py-3 font-semibold">Efectivo Final</th>
                            <th class="px-4 py-3 font-semibold">N° Ventas</th>
                            <th class="px-4 py-3 font-semibold">Efectivo</th>
                            <th class="px-4 py-3 font-semibold">Digital</th>
                            <th class="px-4 py-3 font-semibold">Vuelto</th>
                            <th class="px-4 py-3 font-semibold">Total final</th>
                            <th class="px-4 py-3 font-semibold">Estado</th>
                            <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aperturas as $apertura)
                            @php
                                $totalEfectivo = $apertura->ventas_sum_monto_efectivo ?? 0;
                                $totalDigital = $apertura->ventas_sum_monto_digital ?? 0;
                                $totalVuelto = $apertura->ventas_sum_vuelto ?? 0;
                                $finalEfectivo = $apertura->monto_inicial + $totalEfectivo - $totalVuelto;
                                $totalFinal = $finalEfectivo + $totalDigital;
                            @endphp
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                <td class="px-4 py-3"><span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-semibold text-[#6a5122]">{{ $apertura->caja->nombre ?? $apertura->nombre ?? 'Caja #'.$apertura->id }}</span></td>
                                <td class="px-4 py-3 text-gray-900">{{ $apertura->usuario?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $apertura->fecha_apertura->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-gray-900">S/ {{ number_format($apertura->monto_inicial, 2) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $apertura->fecha_cierre?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-900 font-semibold">S/ {{ number_format($finalEfectivo, 2) }}</td>
                                <td class="px-4 py-3">{{ $apertura->ventas_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-emerald-700 font-medium">S/ {{ number_format($totalEfectivo, 2) }}</td>
                                <td class="px-4 py-3 text-sky-700 font-medium">S/ {{ number_format($totalDigital, 2) }}</td>
                                <td class="px-4 py-3 text-red-600 font-medium">-S/ {{ number_format($totalVuelto, 2) }}</td>
                                <td class="px-4 py-3 text-gray-900 font-semibold">S/ {{ number_format($totalFinal, 2) }}</td>
                                <td class="px-4 py-3">
                                    @if ($apertura->estado === 'abierta')
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Abierta</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Cerrada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @php
                                        $detalleData = [
                                            'id' => $apertura->id,
                                            'nombre' => $apertura->caja->nombre ?? $apertura->nombre ?? 'Caja #'.$apertura->id,
                                            'usuario' => $apertura->usuario?->name ?? '-',
                                            'apertura' => $apertura->fecha_apertura->format('d/m/Y H:i'),
                                            'cierre' => $apertura->fecha_cierre?->format('d/m/Y H:i') ?? '—',
                                            'inicial' => 'S/ '.number_format($apertura->monto_inicial, 2),
                                            'efectivo' => 'S/ '.number_format($totalEfectivo, 2),
                                            'digital' => 'S/ '.number_format($totalDigital, 2),
                                            'vuelto' => '-S/ '.number_format($totalVuelto, 2),
                                            'efectivo_final' => 'S/ '.number_format($finalEfectivo, 2),
                                            'total_final' => 'S/ '.number_format($totalFinal, 2),
                                            'ventas_count' => $apertura->ventas_count ?? 0,
                                            'estado' => $apertura->estado,
                                            'observaciones' => $apertura->observaciones,
                                        ];
                                        @endphp
                                        <button
                                            type="button"
                                            @click="detalleCaja = @js($detalleData)"
                                            class="btn-icon-sm" style="background-color:#0891B2"
                                            title="Ver detalle"
                                        >
                                            <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver" class="h-4 w-4 object-contain pointer-events-none" />
                                        </button>
                                        @if ($apertura->estado === 'abierta' && auth()->user()->tienePermiso('caja.gestionar'))
                                            <button type="button" @click="cerrarId = {{ $apertura->id }}" class="btn-icon-sm bg-rose-600 hover:bg-rose-700" title="Cerrar caja">
                                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-4 py-8 text-center text-sm text-gray-500">No hay registros de caja.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($aperturas->hasPages())
                <div class="border-t border-[#efe7d2] px-4 py-3">{{ $aperturas->links() }}</div>
            @endif
        </div>

        {{-- Modal abrir caja --}}
        <div x-show="modalAbrir" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" style="display: none;" @click="modalAbrir = false"></div>
        <div x-show="modalAbrir" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-base font-semibold text-gray-800">Abrir caja</h3>
                    <button type="button" @click="modalAbrir = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>
                <form method="POST" action="{{ route('cajas.store') }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label for="caja_id" class="mb-2 block text-sm font-medium text-gray-700">Seleccionar caja</label>
                        <select id="caja_id" name="caja_id" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900">
                            <option value="">Seleccione una caja</option>
                            @foreach ($cajasDisponibles as $caja)
                                <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="monto_inicial" class="mb-2 block text-sm font-medium text-gray-700">Monto inicial</label>
                        <input id="monto_inicial" name="monto_inicial" type="number" step="0.01" min="0" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="0.00" />
                    </div>
                    <div>
                        <label for="obs_abrir" class="mb-2 block text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea id="obs_abrir" name="observaciones" rows="2" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Opcional"></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-[#111] px-4 py-3 text-sm font-semibold text-white hover:bg-[#262626]">Abrir caja</button>
                </form>
            </div>
        </div>

        {{-- Modales cerrar caja --}}
        @foreach ($aperturas as $apertura)
            @if ($apertura->estado === 'abierta')
            <div x-show="cerrarId === {{ $apertura->id }}" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" style="display: none;" @click="cerrarId = null"></div>
            <div x-show="cerrarId === {{ $apertura->id }}" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                        <h3 class="text-base font-semibold text-gray-800">Cerrar caja</h3>
                        <button type="button" @click="cerrarId = null" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                            <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                        </button>
                    </div>
                    @php
                        $totalEfec = $apertura->ventas_sum_monto_efectivo ?? 0;
                        $totalDig = $apertura->ventas_sum_monto_digital ?? 0;
                        $totalVue = $apertura->ventas_sum_vuelto ?? 0;
                        $totalVentasCalc = $totalEfec + $totalDig - $totalVue;
                        $esperado = $apertura->monto_inicial + $totalVentasCalc;
                    @endphp
                    <form method="POST" action="{{ route('cajas.cerrar', $apertura) }}" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <p class="text-sm text-gray-600">Caja: <strong>{{ $apertura->caja->nombre ?? $apertura->nombre ?? 'Caja #'.$apertura->id }}</strong></p>
                            <p class="mt-1 text-sm text-gray-600">Abrió: <strong>{{ $apertura->usuario?->name ?? '-' }}</strong></p>
                            <p class="mt-1 text-sm text-gray-600">Apertura: <strong>{{ $apertura->fecha_apertura->format('d/m/Y H:i') }}</strong></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Monto inicial</span>
                                <span class="font-medium text-gray-900">S/ {{ number_format($apertura->monto_inicial, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>+ Efectivo</span>
                                <span class="font-medium text-emerald-700">S/ {{ number_format($totalEfec, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>+ Digital</span>
                                <span class="font-medium text-sky-700">S/ {{ number_format($totalDig, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>- Vuelto</span>
                                <span class="font-medium text-red-600">-S/ {{ number_format($totalVue, 2) }}</span>
                            </div>
                            <hr class="border-gray-300">
                            <div class="flex justify-between text-sm font-semibold text-gray-900">
                                <span>Monto esperado</span>
                                <span>S/ {{ number_format($esperado, 2) }}</span>
                            </div>
                        </div>
                        <div>
                            <label for="monto_final_{{ $apertura->id }}" class="mb-2 block text-sm font-medium text-gray-700">Monto final en caja</label>
                            <input id="monto_final_{{ $apertura->id }}" name="monto_final" type="number" step="0.01" min="0" required value="{{ number_format($esperado, 2, '.', '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" />
                        </div>
                        <div>
                            <label for="obs_cerrar_{{ $apertura->id }}" class="mb-2 block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea id="obs_cerrar_{{ $apertura->id }}" name="observaciones" rows="2" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Opcional"></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-700">Cerrar caja</button>
                    </form>
                </div>
            </div>
            @endif
        @endforeach

        {{-- Modal ver detalle --}}
        <div x-show="detalleCaja" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" style="display: none;" @click="detalleCaja = null"></div>
        <div x-show="detalleCaja" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-base font-semibold text-gray-800">Detalle de caja</h3>
                    <button type="button" @click="detalleCaja = null" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Caja</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.nombre"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Usuario</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.usuario"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Apertura</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.apertura"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Cierre</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.cierre"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Monto inicial</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.inicial"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Efectivo Final</p>
                        <p class="mt-1 text-gray-900 font-semibold" x-text="detalleCaja?.efectivo_final"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">N° Ventas</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.ventas_count"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Efectivo</p>
                        <p class="mt-1 text-emerald-700 font-medium" x-text="detalleCaja?.efectivo"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Digital</p>
                        <p class="mt-1 text-sky-700 font-medium" x-text="detalleCaja?.digital"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Vuelto</p>
                        <p class="mt-1 text-red-600 font-medium" x-text="detalleCaja?.vuelto"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Total final</p>
                        <p class="mt-1 text-gray-900 font-semibold" x-text="detalleCaja?.total_final"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Estado</p>
                        <p class="mt-1">
                            <template x-if="detalleCaja?.estado === 'abierta'">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Abierta</span>
                            </template>
                            <template x-if="detalleCaja?.estado !== 'abierta'">
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Cerrada</span>
                            </template>
                        </p>
                    </div>
                    <div class="md:col-span-2" x-show="detalleCaja?.observaciones">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Observaciones</p>
                        <p class="mt-1 text-gray-700" x-text="detalleCaja?.observaciones"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
