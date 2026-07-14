<style>
    .btn-icon { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; flex-shrink: 0; color: #fff; }
    .btn-icon:active { filter: brightness(0.85); }
    .btn-icon:focus, .btn-icon:focus-visible { outline: 0 none !important; }
    .btn-icon-sm { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; flex-shrink: 0; color: #fff; }
    .btn-icon-sm:active { filter: brightness(0.85); }
    .btn-icon-sm:focus, .btn-icon-sm:focus-visible { outline: 0 none !important; }
</style>
    <div
        x-data="{
            totalProd() { let t=0; for(let p of this.productos) t+=(Number(p.precio_unitario)||0)*(Number(p.cantidad)||0); return t; },
            tipoEntrega: '{{ old('tipo_entrega', $pedido->tipo_entrega ?? 'local') }}',
            metodoPago: '{{ old('metodo_pago', 'efectivo') }}',
            productos: window._prodData,
            rowArchivos: window._archData,
            agregar() {
                this.productos.push({nombre: '', descripcion: '', precio_unitario: '', cantidad: 1});
                this.rowArchivos.push([]);
            },
            eliminar(i) {
                if (this.productos.length > 1) {
                    this.productos.splice(i, 1);
                    this.rowArchivos.splice(i, 1);
                }
            },
            onRowArchivosChange(i, e) {
                const nuevos = Array.from(e.target.files || []).map(f => ({ file: f, name: f.name }));
                this.rowArchivos[i] = [...(this.rowArchivos[i] || []), ...nuevos];
            },
            modalIndex: -1,
            eliminarArchivoRow(i, fi) {
                const archivos = this.rowArchivos[i] || [];
                const removed = archivos[fi];
                if (removed && removed.id) {
                    fetch('{{ url('pedidos/archivo-producto') }}/' + removed.id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).catch(() => {});
                }
                archivos.splice(fi, 1);
                this.rowArchivos[i] = [...archivos];
            },
            abrirVistaPrevia(archivo) {
                if (archivo.url) { window.open(archivo.url, '_blank'); return; }
                if (archivo.file) { const url = URL.createObjectURL(archivo.file); window.open(url, '_blank'); setTimeout(() => URL.revokeObjectURL(url), 60000); }
            },

        consultandoDocumento: false,
        clienteId: '{{ old('cliente_id', $pedido->cliente_id ?? '') }}',
        archivosSeleccionados: null,
        mensajeDocumento: '',
        consultaDocumentoOk: false,
        fuenteDocumento: '',
        async buscarPorDocumento() {
            const numeroRaw = (this.$refs.documentoCliente?.value || '').trim();
            const numero = numeroRaw.replace(/\D/g, '');
            if (this.$refs.documentoCliente) this.$refs.documentoCliente.value = numero;
            this.mensajeDocumento = '';
            this.consultaDocumentoOk = false;
            this.fuenteDocumento = '';

            if (!numero) {
                this.mensajeDocumento = 'Ingresa DNI o RUC para buscar.';
                return;
            }

            if (!/^[0-9]{8}$|^[0-9]{11}$/.test(numero)) {
                this.mensajeDocumento = 'El documento debe tener 8 digitos (DNI) o 11 digitos (RUC).';
                return;
            }

            this.consultandoDocumento = true;

            try {
                const url = new URL('{{ route('clientes.consulta_documento') }}', window.location.origin);
                url.searchParams.set('numero', numero);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });

                const data = await response.json();
                this.mensajeDocumento = data?.message || 'Consulta realizada.';
                this.consultaDocumentoOk = !!data?.ok;
                this.fuenteDocumento = data?.fuente || '';

                if (!data?.ok || !data?.cliente) {
                    this.clienteId = '';
                    return;
                }

                this.clienteId = data.cliente.id || '';

                if (this.$refs.nombreCliente && data.cliente.nombre) this.$refs.nombreCliente.value = data.cliente.nombre;
                if (this.$refs.documentoCliente && data.cliente.documento) this.$refs.documentoCliente.value = data.cliente.documento;
                if (this.$refs.telefonoCliente && data.cliente.telefono) this.$refs.telefonoCliente.value = data.cliente.telefono;
                if (this.$refs.correoCliente && data.cliente.correo) this.$refs.correoCliente.value = data.cliente.correo;

                if (this.tipoEntrega !== 'local') {
                    if (this.$refs.direccionEntrega && data.cliente.direccion && !this.$refs.direccionEntrega.value) {
                        this.$refs.direccionEntrega.value = data.cliente.direccion;
                    }
                    if (this.$refs.distritoEntrega && data.cliente.distrito && !this.$refs.distritoEntrega.value) {
                        this.$refs.distritoEntrega.value = data.cliente.distrito;
                    }
                }

                if (this.$refs.observaciones && data.cliente.estado && data.cliente.condicion) {
                    const nota = 'SUNAT: Estado ' + data.cliente.estado + ' / Condicion ' + data.cliente.condicion;
                    if (!this.$refs.observaciones.value.includes('SUNAT:')) {
                        this.$refs.observaciones.value = (this.$refs.observaciones.value ? this.$refs.observaciones.value + ' | ' : '') + nota;
                    }
                }
            } catch (error) {
                this.mensajeDocumento = 'Error al consultar el documento.';
                this.consultaDocumentoOk = false;
                this.fuenteDocumento = '';
            } finally {
                this.consultandoDocumento = false;
            }
        }
    }"
    class="space-y-4"
>
    <input type="hidden" name="cliente_id" x-model="clienteId" />
    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Datos Cliente</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="documento_cliente" class="mb-2 block text-sm font-medium text-gray-700">Documento cliente (DNI/RUC)</label>
                <div class="flex gap-2 items-stretch">
                    <input
                        x-ref="documentoCliente"
                        id="documento_cliente"
                        name="documento_cliente"
                        type="text"
                        value="{{ old('documento_cliente', $pedido->documento_cliente ?? '') }}"
                        @keydown.enter.prevent="buscarPorDocumento()"
                        class="min-w-0 flex-1 rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900"
                        placeholder="Ejemplo: 74561230 o 20601030013"
                    />
                    <button type="button" @click="buscarPorDocumento()" :disabled="consultandoDocumento" class="rounded-xl bg-blue-600 hover:bg-blue-700 flex items-center justify-center shrink-0 px-3" title="Buscar" :class="{ 'opacity-50': consultandoDocumento }">
                        <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none" />
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Primero busca en clientes del sistema. Si no existe: DNI consulta RENIEC y RUC consulta SUNAT automaticamente.</p>
                <p x-show="mensajeDocumento" class="mt-1 text-xs" :class="consultaDocumentoOk ? 'text-emerald-700' : 'text-rose-700'" x-text="mensajeDocumento"></p>
                @error('documento_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="nombre_cliente" class="mb-2 block text-sm font-medium text-gray-700">Nombre cliente</label>
                <input x-ref="nombreCliente" id="nombre_cliente" name="nombre_cliente" type="text" value="{{ old('nombre_cliente', $pedido->nombre_cliente ?? '') }}" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Cliente" />
                @error('nombre_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="telefono_cliente" class="mb-2 block text-sm font-medium text-gray-700">Telefono cliente</label>
                <input x-ref="telefonoCliente" id="telefono_cliente" name="telefono_cliente" type="text" value="{{ old('telefono_cliente', $pedido->telefono_cliente ?? '') }}" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="999999999" />
                @error('telefono_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="correo_cliente" class="mb-2 block text-sm font-medium text-gray-700">Correo cliente</label>
                <input x-ref="correoCliente" id="correo_cliente" name="correo_cliente" type="email" value="{{ old('correo_cliente', $pedido->correo_cliente ?? '') }}" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="cliente@correo.com" />
                @error('correo_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>
    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4 mt-6">

        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Datos del Producto Personalizado</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Codigo pedido</label>
                @if(isset($pedido) && $pedido->codigo)
                    <div class="block w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500">{{ $pedido->codigo }}</div>
                @else
                    <div class="block w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500">Se genera automaticamente al guardar</div>
                @endif
            </div>

        </div>

        @php
            $productosExist = isset($pedido) && $pedido->exists ? $pedido->productos : collect();
            $productosIniciales = old('productos', $productosExist->map(fn($pp) => ['id' => $pp->id, 'nombre' => $pp->nombre, 'descripcion' => $pp->descripcion ?? '', 'precio_unitario' => $pp->precio_unitario ?? '', 'cantidad' => $pp->cantidad ?? 1])->toArray()) ?: [['nombre' => '', 'descripcion' => '', 'precio_unitario' => '', 'cantidad' => 1]];
            $archivosIniciales = old('productos_archivos', $productosExist->map(fn($pp) => $pp->archivos->map(fn($a) => ['path' => $a->archivo_path, 'name' => $a->nombre_original, 'id' => $a->id, 'url' => asset('storage/' . $a->archivo_path)])->toArray())->toArray()) ?: [];
            if (empty($archivosIniciales)) {
                $archivosIniciales = array_fill(0, count($productosIniciales), []);
            }
        @endphp
        <script>window._prodData = @json($productosIniciales); window._archData = @json($archivosIniciales);</script>
        <div class="mt-4">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-3 py-2 w-12">#</th>
                            <th class="px-3 py-2">Nombre</th>
                            <th class="px-3 py-2">Descripcion</th>
                            <th class="px-3 py-2 w-28">Precio uni.</th>
                            <th class="px-3 py-2 w-20">Cant.</th>
                            <th class="px-3 py-2 w-28">Total</th>
                            <th class="px-3 py-2 w-24">Diseno</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(p, i) in productos" :key="i">
                            <tr class="border-t border-gray-100">
                                <input type="hidden" x-model="p.id" :name="'productos['+i+'][id]'" />
                                <td class="px-3 py-2 text-center text-gray-400" x-text="i + 1"></td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="p.nombre" :name="'productos['+i+'][nombre]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="Ej: Medalla" required />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="p.descripcion" :name="'productos['+i+'][descripcion]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="Ej: 30mm x 40mm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0" x-model="p.precio_unitario" :name="'productos['+i+'][precio_unitario]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="0.00" required />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="1" x-model="p.cantidad" :name="'productos['+i+'][cantidad]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="1" required />
                                </td>
                                <td class="px-3 py-2 font-semibold text-gray-700" x-text="'S/ ' + ((Number(p.precio_unitario) || 0) * (Number(p.cantidad) || 0)).toFixed(2)"></td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-1">
                                        <input type="file" :id="'pp_archivos_'+i" :name="'productos_archivos['+i+'][]'" multiple accept=".cdr,.pdf,.jpg,.jpeg,.png,.ai,.eps,.svg,.dxf,.dwg,.step,.stp,.3dm,.stl,.obj,.fbx,.zip,.rar" @change="onRowArchivosChange(i, $event)" class="hidden" />
                                        <label :for="'pp_archivos_'+i" class="inline-flex cursor-pointer items-center justify-center rounded-lg bg-blue-600 px-2 py-1.5 text-white hover:bg-blue-700" title="Subir diseño">
                                            <svg class="h-4 w-4" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        </label>
                                        <button type="button" @click="modalIndex = i" class="inline-flex items-center justify-center rounded-lg px-2 py-1.5 text-white hover:opacity-80" style="background-color:#0891B2" title="Ver diseño">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="eliminar(i)" x-show="productos.length > 1" class="btn-icon bg-red-600 hover:bg-red-700" title="Eliminar">
                                        <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="Eliminar" class="h-4 w-4 object-contain pointer-events-none" />
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <button type="button" @click="agregar()" class="rounded-lg border border-[#d1be8a] px-3 py-1.5 text-xs font-medium text-[#5a4314] hover:bg-[#fff5dd]">+ Agregar producto</button>
                <p class="text-xs font-semibold text-gray-700">Total productos: <span class="text-amber-800" x-text="'S/ ' + totalProd().toFixed(2)"></span></p>
            </div>
            @error('productos') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

            <div x-show="modalIndex >= 0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="modalIndex = -1">
                    <div class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700">Disenos - Producto <span x-text="modalIndex + 1"></span></h3>
                            <button type="button" @click="modalIndex = -1" class="flex h-7 w-7 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600">&times;</button>
                        </div>
                        <div class="max-h-80 space-y-2 overflow-y-auto">
                            <template x-for="(archivo, fi) in (rowArchivos[modalIndex] || [])" :key="fi">
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 hover:bg-gray-50">
                                    <div class="flex min-w-0 flex-1 items-center gap-2">
                                        <svg class="h-5 w-5 flex-shrink-0 text-amber-600" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <span class="cursor-pointer truncate text-sm text-amber-800 hover:text-amber-950" x-text="archivo.name" @click="abrirVistaPrevia(archivo)" title="Vista previa"></span>
                                    </div>
                                    <div class="flex flex-shrink-0 gap-1">
                                        <button type="button" @click="abrirVistaPrevia(archivo)" class="flex h-7 w-7 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-amber-700" title="Vista previa">
                                            <svg class="h-4 w-4" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button type="button" @click="eliminarArchivoRow(modalIndex, fi)" class="flex h-7 w-7 items-center justify-center rounded-md text-red-400 hover:bg-red-50 hover:text-red-600" title="Eliminar">
                                            <svg class="h-4 w-4" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <p x-show="!(rowArchivos[modalIndex] || []).length" class="py-6 text-center text-sm text-gray-400">No hay archivos subidos para este producto.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 mt-4">
            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Resumen de Montos</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Monto total</label>
                    <span class="text-amber-800" id="monto-total-val">S/ 0.00</span>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Adelanto</label>
                    <span class="text-amber-800" id="adelanto-val">S/ 0.00</span>
                    <p class="mt-2 text-xs text-gray-500">Saldo pendiente: S/ <span class="font-semibold" id="saldo-val">0.00</span></p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 mt-4">
            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Forma de Pago</h3>
            <div class="space-y-3">
                <div class="flex gap-6">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" name="tipo_pago" value="dos_partes"
                               class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                               @checked(old('tipo_pago', $pedido->tipo_pago ?? 'dos_partes') === 'dos_partes')>
                        <span class="text-sm text-gray-700">En 2 Partes (50% + 50%)</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="radio" name="tipo_pago" value="contado"
                               class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                               @checked(old('tipo_pago', $pedido->tipo_pago ?? 'dos_partes') === 'contado')>
                        <span class="text-sm text-gray-700">Contado (100%)</span>
                    </label>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-600">Metodo de pago</label>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="metodo_pago" value="efectivo"
                                   class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                                   @change="metodoPago = 'efectivo'"
                                   @checked(old('metodo_pago', 'efectivo') === 'efectivo')>
                            <span class="text-sm text-gray-700">Efectivo</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="metodo_pago" value="yape"
                                   class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                                   @change="metodoPago = 'yape'"
                                   @checked(old('metodo_pago', 'efectivo') === 'yape')>
                            <span class="text-sm text-gray-700">Yape</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="metodo_pago" value="plin"
                                   class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                                   @change="metodoPago = 'plin'"
                                   @checked(old('metodo_pago', 'efectivo') === 'plin')>
                            <span class="text-sm text-gray-700">Plin</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="metodo_pago" value="tarjeta"
                                   class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                                   @change="metodoPago = 'tarjeta'"
                                   @checked(old('metodo_pago', 'efectivo') === 'tarjeta')>
                            <span class="text-sm text-gray-700">Tarjeta</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="metodo_pago" value="transferencia"
                                   class="rounded-full border-gray-300 text-amber-600 focus:ring-amber-500"
                                   @change="metodoPago = 'transferencia'"
                                   @checked(old('metodo_pago', 'efectivo') === 'transferencia')>
                            <span class="text-sm text-gray-700">Transferencia</span>
                        </label>
                    </div>
                    <div id="vuelto-section" style="display: none;" class="mt-2 grid grid-cols-2 gap-3">
                        <div>
                            <label for="monto-recibido" class="mb-1 block text-sm font-medium text-gray-600">Monto recibido</label>
                            <input type="number" step="0.01" min="0" name="monto_recibido" id="monto-recibido"
                                   class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900"
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-600">Vuelto</label>
                            <p class="mt-2 text-lg font-bold text-emerald-700" id="vuelto-val">S/ 0.00</p>
                            <input type="hidden" name="vuelto" id="vuelto-input" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function() {
                function calcularVuelto(adelanto) {
                    var rec = document.getElementById('monto-recibido');
                    var vEl = document.getElementById('vuelto-val');
                    var vIn = document.getElementById('vuelto-input');
                    if (!rec || !vEl || !vIn) return;
                    var recibido = Number(rec.value) || 0;
                    var vuelto = Math.max(0, recibido - adelanto);
                    vEl.textContent = 'S/ ' + vuelto.toFixed(2);
                    vIn.value = vuelto.toFixed(2);
                }
                function actualizarMontos() {
                    var t = 0;
                    (window._prodData || []).forEach(function(p) {
                        t += (Number(p.precio_unitario) || 0) * (Number(p.cantidad) || 0);
                    });
                    var radio = document.querySelector('input[name="tipo_pago"]:checked');
                    var esContado = radio && radio.value === 'contado';
                    var adelanto = esContado ? t : t * 0.5;
                    var saldo = t - adelanto;
                    var el1 = document.getElementById('monto-total-val');
                    var el2 = document.getElementById('adelanto-val');
                    var el3 = document.getElementById('saldo-val');
                    if (el1) el1.textContent = 'S/ ' + t.toFixed(2);
                    if (el2) el2.textContent = 'S/ ' + adelanto.toFixed(2);
                    if (el3) el3.textContent = saldo.toFixed(2);
                    calcularVuelto(adelanto);
                }
                function toggleVueltoSection() {
                    var sel = document.querySelector('input[name="metodo_pago"]:checked');
                    var section = document.getElementById('vuelto-section');
                    if (!section) return;
                    section.style.display = sel && sel.value === 'efectivo' ? '' : 'none';
                }
                document.addEventListener('input', actualizarMontos);
                document.querySelectorAll('input[name="metodo_pago"]').forEach(function(r) {
                    r.addEventListener('change', function() { actualizarMontos(); toggleVueltoSection(); });
                });
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() { actualizarMontos(); toggleVueltoSection(); });
                } else {
                    actualizarMontos();
                    toggleVueltoSection();
                }
            })();
        </script>

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4 mt-6">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Datos de Entrega</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="tipo_entrega" class="mb-2 block text-sm font-medium text-gray-700">Tipo entrega</label>
                <select id="tipo_entrega" name="tipo_entrega" x-model="tipoEntrega" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900">
                    <option value="local" @selected(old('tipo_entrega', $pedido->tipo_entrega ?? 'local') === 'local')>Local</option>
                    <option value="delivery" @selected(old('tipo_entrega', $pedido->tipo_entrega ?? 'local') === 'delivery')>Delivery</option>
                    <option value="agencia" @selected(old('tipo_entrega', $pedido->tipo_entrega ?? 'local') === 'agencia')>Agencia</option>
                </select>
                @error('tipo_entrega') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fecha_entrega_compromiso" class="mb-2 block text-sm font-medium text-gray-700">Fecha entrega compromiso</label>
                <input id="fecha_entrega_compromiso" name="fecha_entrega_compromiso" type="date" value="{{ old('fecha_entrega_compromiso', isset($pedido) && $pedido->fecha_entrega_compromiso ? $pedido->fecha_entrega_compromiso->format('Y-m-d') : '') }}" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" />
                @error('fecha_entrega_compromiso') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div x-show="tipoEntrega !== 'local'" x-transition class="mt-4 grid gap-4 md:grid-cols-2" style="display: none;">
            <div class="md:col-span-2">
                <label for="direccion_entrega" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Direccion destino / sede agencia' : 'Direccion entrega'"></label>
                <input x-ref="direccionEntrega" id="direccion_entrega" name="direccion_entrega" type="text" value="{{ old('direccion_entrega', $pedido->direccion_entrega ?? '') }}" :required="tipoEntrega !== 'local'" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" :placeholder="tipoEntrega === 'agencia' ? 'Direccion de destino o agencia' : 'Direccion completa de entrega'" />
                @error('direccion_entrega') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="distrito_entrega" class="mb-2 block text-sm font-medium text-gray-700">Distrito entrega</label>
                <input x-ref="distritoEntrega" id="distrito_entrega" name="distrito_entrega" type="text" value="{{ old('distrito_entrega', $pedido->distrito_entrega ?? '') }}" :required="tipoEntrega !== 'local'" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Distrito" />
                @error('distrito_entrega') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="codigo_postal_entrega" class="mb-2 block text-sm font-medium text-gray-700">Codigo postal</label>
                <input id="codigo_postal_entrega" name="codigo_postal_entrega" type="text" value="{{ old('codigo_postal_entrega', $pedido->codigo_postal_entrega ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Ejemplo: 14001" />
                @error('codigo_postal_entrega') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="referencia_entrega" class="mb-2 block text-sm font-medium text-gray-700">Referencia</label>
                <input id="referencia_entrega" name="referencia_entrega" type="text" value="{{ old('referencia_entrega', $pedido->referencia_entrega ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Frente a... / Cerca de..." />
                @error('referencia_entrega') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nombre_recibe" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Contacto de agencia / receptor' : 'Nombre quien recibe'"></label>
                <input id="nombre_recibe" name="nombre_recibe" type="text" value="{{ old('nombre_recibe', $pedido->nombre_recibe ?? '') }}" :required="tipoEntrega !== 'local'" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" :placeholder="tipoEntrega === 'agencia' ? 'Nombre del contacto en agencia o receptor' : 'Persona que recibe el pedido'" />
                @error('nombre_recibe') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="telefono_recibe" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Telefono contacto agencia/receptor' : 'Telefono quien recibe'"></label>
                <input id="telefono_recibe" name="telefono_recibe" type="text" value="{{ old('telefono_recibe', $pedido->telefono_recibe ?? '') }}" :required="tipoEntrega !== 'local'" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="999999999" />
                @error('telefono_recibe') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="costo_delivery" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Costo agencia' : 'Costo delivery'"></label>
                <input id="costo_delivery" name="costo_delivery" type="number" step="0.01" min="0" value="{{ old('costo_delivery', $pedido->costo_delivery ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="0.00" />
                @error('costo_delivery') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4 mt-6">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Orden de Compra (Opcional)</h3>
        <div class="space-y-4">
            <div>
                <label for="archivos_orden" class="mb-2 block text-sm font-medium text-gray-700">Adjuntar PDF o Word de orden de compra</label>
                <label for="archivos_orden" class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white px-4 py-6 text-gray-500 transition hover:border-[#d1be8a] hover:bg-[#fffdf5]">
                    <svg class="mb-2 h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                    </svg>
                    <p class="text-sm font-medium">Haz clic para seleccionar archivos</p>
                    <p class="mt-1 text-xs" x-text="archivosSeleccionados ? archivosSeleccionados.length + ' archivo(s) seleccionado(s)' : 'PDF o Word'"></p>
                </label>
                <input
                    id="archivos_orden"
                    name="archivos_orden[]"
                    type="file"
                    multiple
                    accept=".pdf,.doc,.docx"
                    @change="archivosSeleccionados = $event.target.files"
                    class="hidden"
                />
                <p class="mt-2 text-xs text-gray-500">Solo para pedidos que lo requieran (ejemplo: entidades del gobierno). Puedes subir varios archivos.</p>
                @error('archivos_orden') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                @error('archivos_orden.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            @if(isset($pedido) && $pedido->exists)
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Archivos registrados</p>
                    @if($pedido->archivosOrden->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($pedido->archivosOrden as $archivoOrden)
                                <a
                                    href="{{ asset('storage/' . $archivoOrden->archivo_path) }}"
                                    target="_blank"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    {{ $archivoOrden->nombre_original }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Sin archivos de orden de compra.</p>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4 mt-6">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Observaciones</h3>
        <div>
            <label for="observaciones" class="mb-2 block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea x-ref="observaciones" id="observaciones" name="observaciones" rows="3" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Notas internas">{{ old('observaciones', $pedido->observaciones ?? '') }}</textarea>
            @error('observaciones') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
    </section>

</div>
