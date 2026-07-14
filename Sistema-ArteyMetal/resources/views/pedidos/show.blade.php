<x-app-layout>
    <x-slot name="header">
        <span>Detalle pedido</span>
    </x-slot>

    <style>
        .btn-icon-sm:focus-visible,
        .btn-icon-sm:focus {
            outline: 0 none !important;
        }
        .btn-icon-sm:active {
            filter: brightness(0.85);
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

    <div x-data="{ modalPersonalizacion: @js($errors->any()), modalDerivar: false, derivarData: null }" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Codigo</p>
                <p class="mt-1 text-gray-900">{{ $pedido->codigo }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Estado pedido</p>
                <p class="mt-1 text-gray-900">{{ str_replace('_', ' ', $pedido->estado) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Estado personalizacion</p>
                <p class="mt-1 text-gray-900">{{ str_replace('_', ' ', $pedido->estado_personalizacion ?? 'sin_iniciar') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Estado pago</p>
                <p class="mt-1 text-gray-900">{{ str_replace('_', ' ', $pedido->estado_pago ?? 'pendiente_adelanto') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre cliente</p>
                <p class="mt-1 text-gray-900">{{ $pedido->nombre_cliente }}</p>
                @if($pedido->cliente)
                    <p class="text-xs text-gray-500">Cliente registrado: {{ $pedido->cliente->nombre_completo }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Telefono cliente</p>
                <p class="mt-1 text-gray-900">{{ $pedido->telefono_cliente ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Documento cliente</p>
                <p class="mt-1 text-gray-900">{{ $pedido->documento_cliente ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Correo cliente</p>
                <p class="mt-1 text-gray-900">{{ $pedido->correo_cliente ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo producto</p>
                <p class="mt-1 text-gray-900">{{ $pedido->tipo_producto }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo entrega</p>
                <p class="mt-1 text-gray-900">
                    @if($pedido->tipo_entrega === 'delivery')
                        Delivery
                    @elseif($pedido->tipo_entrega === 'agencia')
                        Agencia
                    @else
                        Local
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Cantidad</p>
                <p class="mt-1 text-gray-900">{{ $pedido->cantidad }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha inicio diseno</p>
                <p class="mt-1 text-gray-900">{{ optional($pedido->fecha_inicio_diseno)->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha aprobacion diseno</p>
                <p class="mt-1 text-gray-900">{{ optional($pedido->fecha_aprobacion_diseno)->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha entrega compromiso</p>
                <p class="mt-1 text-gray-900">{{ optional($pedido->fecha_entrega_compromiso)->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Monto total</p>
                <p class="mt-1 text-gray-900">{{ $pedido->monto_total !== null ? 'S/ ' . number_format((float) $pedido->monto_total, 2) : '-' }}</p>
                <p class="mt-1 text-xs text-gray-500">Adelanto (50%): {{ $pedido->monto_adelanto !== null ? 'S/ ' . number_format((float) $pedido->monto_adelanto, 2) : '-' }} | Saldo: {{ $pedido->monto_saldo !== null ? 'S/ ' . number_format((float) $pedido->monto_saldo, 2) : '-' }}</p>
            </div>
        </div>

        @if($pedido->productos->isNotEmpty())
            <div class="mt-5 overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2">Nombre</th>
                            <th class="px-3 py-2">Descripcion</th>
                            <th class="px-3 py-2">Precio uni.</th>
                            <th class="px-3 py-2">Cant.</th>
                            <th class="px-3 py-2">Total</th>
                            <th class="px-3 py-2">Diseno</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->productos as $idx => $pp)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-center text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $pp->nombre }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $pp->descripcion ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">S/ {{ number_format((float) $pp->precio_unitario, 2) }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $pp->cantidad }}</td>
                                <td class="px-3 py-2 font-semibold text-gray-900">S/ {{ number_format((float) $pp->total, 2) }}</td>
                                <td class="px-3 py-2">
                                    @if($pp->archivos->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($pp->archivos as $a)
                                                <a href="{{ asset('storage/' . $a->archivo_path) }}" target="_blank" class="inline-flex items-center rounded bg-amber-50 px-2 py-0.5 text-xs text-amber-700 hover:bg-amber-100" title="{{ $a->nombre_original }}">
                                                    {{ str($a->nombre_original)->limit(15) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($pedido->tipo_entrega !== 'local')
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $pedido->tipo_entrega === 'agencia' ? 'Direccion destino / sede agencia' : 'Direccion entrega' }}</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->direccion_entrega ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Distrito</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->distrito_entrega ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Codigo postal</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->codigo_postal_entrega ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Referencia</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->referencia_entrega ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $pedido->tipo_entrega === 'agencia' ? 'Contacto agencia / receptor' : 'Nombre quien recibe' }}</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->nombre_recibe ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $pedido->tipo_entrega === 'agencia' ? 'Telefono contacto agencia/receptor' : 'Telefono quien recibe' }}</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->telefono_recibe ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $pedido->tipo_entrega === 'agencia' ? 'Costo agencia' : 'Costo delivery' }}</p>
                    <p class="mt-1 text-gray-900">{{ $pedido->costo_delivery !== null ? 'S/ ' . number_format((float) $pedido->costo_delivery, 2) : '-' }}</p>
                </div>
            </div>
        @endif

        <div class="mt-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Detalle trabajo</p>
            <p class="mt-1 text-gray-900">{{ $pedido->detalle_trabajo ?: '-' }}</p>
        </div>

        <div class="mt-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Observaciones pedido</p>
            <p class="mt-1 text-gray-900">{{ $pedido->observaciones ?: '-' }}</p>
        </div>

        <div class="mt-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Observaciones personalizacion</p>
            <p class="mt-1 text-gray-900">{{ $pedido->observaciones_personalizacion ?: '-' }}</p>
        </div>

        <div class="mt-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Archivos de diseno</p>
            @if($pedido->archivosDiseno->isNotEmpty() || $pedido->archivo_diseno_path)
                <div class="mt-1 space-y-2">
                    @foreach($pedido->archivosDiseno as $archivo)
                        <a class="inline-flex rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100" href="{{ asset('storage/' . $archivo->archivo_path) }}" target="_blank">
                            {{ $archivo->nombre_original }}
                        </a>
                    @endforeach
                    @if($pedido->archivo_diseno_path)
                        <a class="inline-flex rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100" href="{{ asset('storage/' . $pedido->archivo_diseno_path) }}" target="_blank">
                            Archivo legado
                        </a>
                    @endif
                </div>
            @else
                <p class="mt-1 text-gray-900">-</p>
            @endif
        </div>

        <div class="mt-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Archivos orden de compra</p>
            @if($pedido->archivosOrden->isNotEmpty())
                <div class="mt-1 space-y-2">
                    @foreach($pedido->archivosOrden as $archivoOrden)
                        <a class="inline-flex rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100" href="{{ asset('storage/' . $archivoOrden->archivo_path) }}" target="_blank">
                            {{ $archivoOrden->nombre_original }}
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mt-1 text-gray-900">-</p>
            @endif
        </div>

        @php $rol = auth()->user()->rol->nombre; @endphp

        <div class="mt-6 flex flex-wrap gap-2">
            @if(in_array($rol, ['administrador', 'vendedor'], true) && $pedido->estado === 'en_almacen' && $pedido->estado_pago === 'pagado_completo')
                <form method="POST" action="{{ route('pedidos.autorizar_recoger', $pedido) }}" onsubmit="return confirm('Habilitar recoger en almacen?')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-indigo-300 px-4 py-2.5 text-sm font-medium text-indigo-700 hover:bg-indigo-50">Autorizar recoger en almacen</button>
                </form>
            @elseif(in_array($rol, ['administrador', 'vendedor'], true) && $pedido->estado === 'en_almacen' && $pedido->estado_pago === 'adelanto_pagado' && (float) ($pedido->monto_saldo ?? 0) > 0)
                <form method="POST" action="{{ route('pedidos.autorizar_recoger', $pedido) }}" onsubmit="return confirm('Cobrar saldo y habilitar recoger en almacen? Se emitira comprobante.')">
                    @csrf
                    <div class="flex items-center gap-2">
                        <select name="metodo_pago" required class="rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900">
                            <option value="">Metodo pago</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="yape">Yape</option>
                            <option value="plin">Plin</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                        <input type="number" name="vuelto" placeholder="Vuelto" step="0.01" min="0" class="rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 w-24" />
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">Cobrar y autorizar recoger</button>
                    </div>
                </form>
            @endif
            @if(in_array($rol, ['administrador', 'vendedor'], true) && in_array($pedido->estado, ['listo_entrega', 'en_tienda', 'entregado'], true) && $pedido->estado_pago === 'adelanto_pagado' && (float) ($pedido->monto_saldo ?? 0) > 0)
                <form method="POST" action="{{ route('pedidos.confirmar_pago_final', $pedido) }}" onsubmit="return confirm('Confirmar pago final y cerrar este pedido? Se registrara automaticamente en ventas.')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-emerald-300 px-4 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-50">Cobrar saldo y cerrar pedido</button>
                </form>
            @endif
            @if(auth()->user()->tienePermiso('pedidos.gestionar'))
                @if(in_array($rol, ['administrador', 'vendedor', 'disenador', 'orfebre'], true))
                    <button type="button" @click="modalPersonalizacion = true" class="rounded-xl bg-gray-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-600">Personalizacion</button>
                @endif
                @if(in_array($rol, ['administrador', 'vendedor'], true))
                    <a href="{{ route('pedidos.edit', $pedido) }}" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Editar pedido</a>
                    <button type="button"
                        @click="derivarData = @js([
                            'codigo' => $pedido->codigo,
                            'derivar_url' => route('pedidos.derivar', $pedido),
                            'estado_personalizacion_raw' => $pedido->estado_personalizacion ?? 'sin_iniciar',
                            'estado_raw' => $pedido->estado,
                        ]); modalDerivar = true"
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-white hover:brightness-110"
                        style="background-color:#7c3aed">
                        Derivar
                    </button>
                @endif
            @endif
            @if($rol === 'repartidor' && $pedido->estado === 'listo_entrega')
                <form method="POST" action="{{ route('pedidos.transportar', $pedido) }}" onsubmit="return confirm('Recoger este pedido del centro de produccion?')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-amber-400 px-4 py-2.5 text-sm font-medium text-amber-700 hover:bg-amber-50">Recoger de produccion</button>
                </form>
            @endif
            @if($rol === 'almacenero' && $pedido->estado === 'en_transporte')
                <form method="POST" action="{{ route('pedidos.recibir_almacen', $pedido) }}" onsubmit="return confirm('Registrar entrada de este pedido en el almacen?')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-sky-400 px-4 py-2.5 text-sm font-medium text-sky-700 hover:bg-sky-50">Registrar entrada en almacen</button>
                </form>
            @endif
            @if(in_array($rol, ['administrador', 'vendedor'], true) && $pedido->estado === 'en_tienda')
                <form method="POST" action="{{ route('pedidos.llegada_tienda', $pedido) }}" onsubmit="return confirm('Confirmar llegada del pedido a tienda?')">
                    @csrf
                    <button type="submit" class="rounded-xl border border-emerald-400 px-4 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-50">Registrar llegada a tienda</button>
                </form>
            @endif
            <a href="{{ route('pedidos.index') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Volver</a>
        </div>

        <div x-show="modalDerivar" style="display: none;">
            <div x-transition.opacity class="fixed inset-0 z-40 bg-black/50" @click="modalDerivar = false"></div>
            <div x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                        <h3 class="text-base font-semibold text-[#2a2419]">Derivar pedido</h3>
                        <button type="button" @click="modalDerivar = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                            <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                        </button>
                    </div>
                    <div class="space-y-3 p-5">
                        <p class="text-center text-sm text-gray-600" x-text="'Selecciona el destino para el pedido ' + (derivarData?.codigo || '')"></p>
                        <div class="grid grid-cols-2 gap-3 items-stretch" x-show="derivarData">
                            <form method="POST" x-bind:action="derivarData?.derivar_url" class="contents">
                                @csrf
                                <input type="hidden" name="destino" value="diseno">
                                <button type="submit"
                                    class="flex h-full w-full flex-col items-center justify-center gap-2 rounded-xl px-4 py-5 text-sm font-medium text-white shadow-sm bg-amber-600 hover:bg-amber-700 border-0"
                                    :style="derivarData?.estado_personalizacion_raw !== 'sin_iniciar' ? 'opacity: 0.4; cursor: not-allowed' : ''"
                                    :disabled="derivarData?.estado_personalizacion_raw !== 'sin_iniciar'">
                                    <img src="{{ asset('icons/Disenos-Blanco.png') }}" class="h-8 w-8 object-contain" alt="">
                                    <span>A Diseño</span>
                                    <span class="min-h-[1rem] text-xs text-amber-100" :style="derivarData?.estado_personalizacion_raw !== 'sin_iniciar' ? '' : 'visibility: hidden'">Ya derivado</span>
                                </button>
                            </form>
                            <form method="POST" x-bind:action="derivarData?.derivar_url" class="contents">
                                @csrf
                                <input type="hidden" name="destino" value="produccion">
                                <button type="submit"
                                    class="flex h-full w-full flex-col items-center justify-center gap-2 rounded-xl px-4 py-5 text-sm font-medium text-white shadow-sm bg-emerald-600 hover:bg-emerald-700 border-0"
                                    :style="derivarData?.estado_raw !== 'registrado' ? 'opacity: 0.4; cursor: not-allowed' : ''"
                                    :disabled="derivarData?.estado_raw !== 'registrado'">
                                    <img src="{{ asset('icons/Produccion-Blanco.png') }}" class="h-8 w-8 object-contain" alt="">
                                    <span>A Producción</span>
                                    <span class="min-h-[1rem] text-xs text-emerald-100" :style="derivarData?.estado_raw !== 'registrado' ? '' : 'visibility: hidden'">Ya derivado</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalPersonalizacion" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" style="display: none;" @click="modalPersonalizacion = false"></div>
        <div x-show="modalPersonalizacion" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl" @click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Personalizacion del pedido</h3>
                    <button type="button" @click="modalPersonalizacion = false" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>

                <form method="POST" action="{{ route('pedidos.personalizacion', $pedido) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2">
                        @if(in_array($rol, ['administrador', 'vendedor', 'orfebre'], true))
                            <div>
                                <label for="estado" class="mb-2 block text-sm font-medium text-gray-700">Estado pedido</label>
                                <select id="estado" name="estado" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900">
                                    @if(in_array($rol, ['administrador', 'vendedor'], true))
                                        <option value="registrado" @selected(old('estado', $pedido->estado) === 'registrado')>Registrado</option>
                                    @endif
                                    <option value="en_produccion" @selected(old('estado', $pedido->estado) === 'en_produccion')>En produccion</option>
                                        <option value="listo_entrega" @selected(old('estado', $pedido->estado) === 'listo_entrega')>Listo entrega</option>
                                        @if(in_array($rol, ['administrador', 'vendedor'], true))
                                            <option value="listo_recoger" @selected(old('estado', $pedido->estado) === 'listo_recoger')>Listo recoger</option>
                                            <option value="entregado" @selected(old('estado', $pedido->estado) === 'entregado')>Entregado</option>
                                            <option value="cancelado" @selected(old('estado', $pedido->estado) === 'cancelado')>Cancelado</option>
                                        @endif
                                </select>
                            </div>
                        @endif

                        <div>
                            <label for="estado_personalizacion" class="mb-2 block text-sm font-medium text-gray-700">Estado personalizacion</label>
                            <select id="estado_personalizacion" name="estado_personalizacion" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900">
                                @if(in_array($rol, ['administrador', 'vendedor', 'disenador'], true))
                                    <option value="sin_iniciar" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'sin_iniciar')>Sin iniciar</option>
                                @endif
                                @if(in_array($rol, ['administrador', 'vendedor', 'disenador'], true))
                                    <option value="en_diseno" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'en_diseno')>En diseno</option>
                                @endif
                                @if(in_array($rol, ['administrador', 'vendedor', 'disenador'], true))
                                    <option value="en_revision" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'en_revision')>En revision</option>
                                @endif
                                @if(in_array($rol, ['administrador', 'vendedor', 'orfebre'], true))
                                    <option value="aprobado" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'aprobado')>Aprobado</option>
                                @endif
                                @if(in_array($rol, ['administrador', 'vendedor', 'orfebre'], true))
                                    <option value="en_produccion" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'en_produccion')>En produccion</option>
                                @endif
                                @if(in_array($rol, ['administrador', 'vendedor', 'orfebre'], true))
                                    <option value="listo_entrega" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'listo_entrega')>Listo entrega</option>
                                @endif
                                @if(in_array($rol, ['administrador', 'vendedor'], true))
                                    <option value="entregado" @selected(old('estado_personalizacion', $pedido->estado_personalizacion) === 'entregado')>Entregado</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label for="fecha_inicio_diseno" class="mb-2 block text-sm font-medium text-gray-700">Fecha inicio diseno</label>
                            <input id="fecha_inicio_diseno" name="fecha_inicio_diseno" type="date" value="{{ old('fecha_inicio_diseno', optional($pedido->fecha_inicio_diseno)->format('Y-m-d')) }}" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900" />
                        </div>

                        <div>
                            <label for="fecha_aprobacion_diseno" class="mb-2 block text-sm font-medium text-gray-700">Fecha aprobacion diseno</label>
                            <input id="fecha_aprobacion_diseno" name="fecha_aprobacion_diseno" type="date" value="{{ old('fecha_aprobacion_diseno', optional($pedido->fecha_aprobacion_diseno)->format('Y-m-d')) }}" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900" />
                        </div>

                        <div>
                            <label for="fecha_entrega_compromiso" class="mb-2 block text-sm font-medium text-gray-700">Fecha entrega compromiso</label>
                            <input id="fecha_entrega_compromiso" name="fecha_entrega_compromiso" type="date" value="{{ old('fecha_entrega_compromiso', optional($pedido->fecha_entrega_compromiso)->format('Y-m-d')) }}" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900" />
                        </div>

                        @if(in_array($rol, ['administrador', 'vendedor'], true))
                            <div>
                                <label for="estado_pago" class="mb-2 block text-sm font-medium text-gray-700">Pago 50/50</label>
                                <select id="estado_pago" name="estado_pago" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900">
                                    <option value="pendiente_adelanto" @selected(old('estado_pago', $pedido->estado_pago) === 'pendiente_adelanto')>Pendiente adelanto</option>
                                    <option value="adelanto_pagado" @selected(old('estado_pago', $pedido->estado_pago) === 'adelanto_pagado')>Adelanto 50% pagado</option>
                                    <option value="pagado_completo" @selected(old('estado_pago', $pedido->estado_pago) === 'pagado_completo')>Pagado completo (entrega)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Se calcula automaticamente: 50% adelanto y 50% saldo.</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="archivos_diseno" class="mb-2 block text-sm font-medium text-gray-700">Archivos diseno (Corel cdr, pdf, png, jpg, svg, ai, eps, psd)</label>
                        <input id="archivos_diseno" name="archivos_diseno[]" type="file" multiple class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900" />
                        <p class="mt-1 text-xs text-gray-500">Puedes seleccionar varios archivos en una sola carga.</p>
                    </div>

                    <div>
                        <label for="observaciones_personalizacion" class="mb-2 block text-sm font-medium text-gray-700">Observaciones personalizacion</label>
                        <textarea id="observaciones_personalizacion" name="observaciones_personalizacion" rows="3" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900">{{ old('observaciones_personalizacion', $pedido->observaciones_personalizacion) }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Guardar personalizacion</button>
                        <button type="button" @click="modalPersonalizacion = false" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
