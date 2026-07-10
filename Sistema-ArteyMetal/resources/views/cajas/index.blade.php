<x-app-layout>
    <x-slot name="header">
        <span>Caja</span>
    </x-slot>

    <style>
        .btn-icon:focus-visible,
        .btn-icon:focus,
        .btn-icon-sm:focus-visible,
        .btn-icon-sm:focus { outline: 0 none !important; }
        .btn-icon:active,
        .btn-icon-sm:active { filter: brightness(0.85); }
        .btn-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.5rem; height: 2.5rem; border-radius: 0.75rem;
            flex-shrink: 0; color: #fff;
        }
        .btn-icon-sm {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem; border-radius: 0.5rem;
            flex-shrink: 0; color: #fff;
        }
    </style>

    <div x-data="{ modalAbrir: false, cerrarId: null, montoCerrar: {}, detalleCaja: null }" class="space-y-5">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
        @endif

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Historial de caja</h2>
            <button type="button" @click="modalAbrir = true" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#262626]">Abrir caja</button>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs uppercase tracking-[0.15em] text-gray-500">
                            <th class="px-4 py-3 font-medium">Caja</th>
                            <th class="px-4 py-3 font-medium">Usuario</th>
                            <th class="px-4 py-3 font-medium">Apertura</th>
                            <th class="px-4 py-3 font-medium">Inicial</th>
                            <th class="px-4 py-3 font-medium">Cierre</th>
                            <th class="px-4 py-3 font-medium">Final</th>
                            <th class="px-4 py-3 font-medium">Ventas</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3 text-right font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aperturas as $apertura)
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                <td class="px-4 py-3"><span class="rounded-lg bg-[#f4ebd4] px-2.5 py-1 text-xs font-semibold text-[#6a5122]">{{ $apertura->nombre ?? 'Caja #'.$apertura->id }}</span></td>
                                <td class="px-4 py-3 text-gray-900">{{ $apertura->usuario?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $apertura->fecha_apertura->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-gray-900">S/ {{ number_format($apertura->monto_inicial, 2) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $apertura->fecha_cierre?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $apertura->monto_final ? 'S/ '.number_format($apertura->monto_final, 2) : '-' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $apertura->total_ventas ? 'S/ '.number_format($apertura->total_ventas, 2) : '-' }}</td>
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
                                            'nombre' => $apertura->nombre ?? 'Caja #'.$apertura->id,
                                            'usuario' => $apertura->usuario?->name ?? '-',
                                                'apertura' => $apertura->fecha_apertura->format('d/m/Y H:i'),
                                                'cierre' => $apertura->fecha_cierre?->format('d/m/Y H:i') ?? '—',
                                                'inicial' => 'S/ '.number_format($apertura->monto_inicial, 2),
                                                'ventas' => $apertura->total_ventas ? 'S/ '.number_format($apertura->total_ventas, 2) : '-',
                                                'final' => $apertura->monto_final ? 'S/ '.number_format($apertura->monto_final, 2) : '—',
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
                                        @if ($apertura->estado === 'abierta')
                                            <button type="button" @click="cerrarId = {{ $apertura->id }}" class="btn-icon-sm bg-rose-600 hover:bg-rose-700" title="Cerrar caja">
                                                <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">No hay registros de caja.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($aperturas->hasPages())
                <div class="border-t border-gray-200 px-4 py-3">{{ $aperturas->links() }}</div>
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
                        <label for="nombre" class="mb-2 block text-sm font-medium text-gray-700">Nombre de caja</label>
                        <input id="nombre" name="nombre" type="text" maxlength="100" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Ej: Caja Principal" />
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
                        $ventasTotal = $apertura->ventas_sum_monto_total ?? 0;
                        $esperado = $apertura->monto_inicial + $ventasTotal;
                    @endphp
                    <form method="POST" action="{{ route('cajas.cerrar', $apertura) }}" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <p class="text-sm text-gray-600">Caja: <strong>{{ $apertura->nombre ?? 'Caja #'.$apertura->id }}</strong></p>
                            <p class="mt-1 text-sm text-gray-600">Abrió: <strong>{{ $apertura->usuario?->name ?? '-' }}</strong></p>
                            <p class="mt-1 text-sm text-gray-600">Apertura: <strong>{{ $apertura->fecha_apertura->format('d/m/Y H:i') }}</strong></p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Monto inicial</span>
                                <span class="font-medium text-gray-900">S/ {{ number_format($apertura->monto_inicial, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>+ Ventas registradas</span>
                                <span class="font-medium text-gray-900">S/ {{ number_format($ventasTotal, 2) }}</span>
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
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Total ventas</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.ventas"></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Monto final</p>
                        <p class="mt-1 text-gray-900" x-text="detalleCaja?.final"></p>
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
