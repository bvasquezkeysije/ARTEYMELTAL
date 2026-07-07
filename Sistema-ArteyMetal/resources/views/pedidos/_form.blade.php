@php
    $estados = [
        'registrado' => 'Registrado',
        'en_produccion' => 'En produccion',
        'listo_entrega' => 'Listo para entrega',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];
@endphp

    <div
        x-data="{
            monto: '{{ old('monto_total', $pedido->monto_total ?? '') }}',
            adelanto: '{{ old('monto_adelanto', $pedido->monto_adelanto ?? '') }}',
            tipoEntrega: '{{ old('tipo_entrega', $pedido->tipo_entrega ?? 'local') }}',
            init() { this.adelanto = (Number(this.monto) * 0.5).toFixed(2); this.$watch('monto', value => { this.adelanto = (Number(value) * 0.5).toFixed(2); }); },
        consultandoDocumento: false,
        clienteId: '{{ old('cliente_id', $pedido->cliente_id ?? '') }}',
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
                <input x-ref="telefonoCliente" id="telefono_cliente" name="telefono_cliente" type="text" value="{{ old('telefono_cliente', $pedido->telefono_cliente ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="999999999" />
                @error('telefono_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="correo_cliente" class="mb-2 block text-sm font-medium text-gray-700">Correo cliente</label>
                <input x-ref="correoCliente" id="correo_cliente" name="correo_cliente" type="email" value="{{ old('correo_cliente', $pedido->correo_cliente ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="cliente@correo.com" />
                @error('correo_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Datos del Producto</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Codigo pedido</label>
                @if(isset($pedido) && $pedido->codigo)
                    <div class="block w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500">{{ $pedido->codigo }}</div>
                @else
                    <div class="block w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500">Se genera automaticamente al guardar</div>
                @endif
            </div>

            <div class="md:col-span-2">
                <label for="detalle_trabajo" class="mb-2 block text-sm font-medium text-gray-700">Descripcion</label>
                <textarea id="detalle_trabajo" name="detalle_trabajo" rows="3" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Describe lo que incluye el pedido. Ej: 20 medallas de oro + 10 trofeos de plata">{{ old('detalle_trabajo', $pedido->detalle_trabajo ?? '') }}</textarea>
                @error('detalle_trabajo') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-4" x-data="{
            productos: @js(old('productos_personalizados', $pedido->productos_personalizados ?? [['nombre' => '', 'descripcion' => '', 'materiales' => '', 'precio_unitario' => '', 'cantidad' => 1]])),
            agregar() { this.productos.push({nombre: '', descripcion: '', materiales: '', precio_unitario: '', cantidad: 1}); },
            eliminar(i) { if (this.productos.length > 1) this.productos.splice(i, 1); },
            get totalGeneral() { return this.productos.reduce((s, p) => s + ((Number(p.precio_unitario) || 0) * (Number(p.cantidad) || 0)), 0); }
        }">
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-3 py-2 w-12">#</th>
                            <th class="px-3 py-2">Nombre</th>
                            <th class="px-3 py-2">Descripcion</th>
                            <th class="px-3 py-2">Materiales</th>
                            <th class="px-3 py-2 w-28">Precio uni.</th>
                            <th class="px-3 py-2 w-20">Cant.</th>
                            <th class="px-3 py-2 w-28">Total</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(p, i) in productos" :key="i">
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-center text-gray-400" x-text="i + 1"></td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="p.nombre" :name="'productos_personalizados['+i+'][nombre]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="Ej: Medalla" required />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="p.descripcion" :name="'productos_personalizados['+i+'][descripcion]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="Ej: 30mm x 40mm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="p.materiales" :name="'productos_personalizados['+i+'][materiales]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="Ej: Oro 18k" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0" x-model="p.precio_unitario" :name="'productos_personalizados['+i+'][precio_unitario]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="0.00" required />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="1" x-model="p.cantidad" :name="'productos_personalizados['+i+'][cantidad]'" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm" placeholder="1" required />
                                </td>
                                <td class="px-3 py-2 font-semibold text-gray-700" x-text="'S/ ' + ((Number(p.precio_unitario) || 0) * (Number(p.cantidad) || 0)).toFixed(2)"></td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="eliminar(i)" x-show="productos.length > 1" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Eliminar">
                                        <img src="{{ asset('icons/eliminar-desactivar.ico') }}" alt="Eliminar" class="h-3 w-3 object-contain pointer-events-none" />
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <button type="button" @click="agregar()" class="rounded-lg border border-[#d1be8a] px-3 py-1.5 text-xs font-medium text-[#5a4314] hover:bg-[#fff5dd]">+ Agregar producto</button>
                <p class="text-xs font-semibold text-gray-700">Total productos: <span class="text-amber-800" x-text="'S/ ' + totalGeneral.toFixed(2)"></span></p>
            </div>
            @error('productos_personalizados') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <section class="mt-4 rounded-xl border border-gray-200 bg-white p-3">
            <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Modelo / Diseno</h4>
        <div x-data="{
            files: [],
            previewFile: null,
            previewUrl: null,
            onFileChange(e) {
                this.files = Array.from(e.target.files || []).map(f => ({ file: f, name: f.name, size: f.size, type: f.type }));
            },
            removeFile(i) {
                const input = this.$refs.fileInput;
                const dt = new DataTransfer();
                const arr = Array.from(input.files);
                arr.splice(i, 1);
                arr.forEach(f => dt.items.add(f));
                input.files = dt.files;
                this.files = Array.from(input.files).map(f => ({ file: f, name: f.name, size: f.size, type: f.type }));
            },
            abrirVistaPrevia(file) {
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewFile = file;
                this.previewUrl = URL.createObjectURL(file.file);
            },
            cerrarVistaPrevia() {
                if (this.previewUrl) { URL.revokeObjectURL(this.previewUrl); this.previewUrl = null; }
                this.previewFile = null;
            },
            esImagen(file) {
                return ['image/jpeg','image/png','image/svg+xml','image/gif','image/webp'].includes(file.type);
            },
            esPdf(file) {
                return file.type === 'application/pdf';
            }
        }">
            <input
                id="archivos_modelo"
                x-ref="fileInput"
                name="archivos_modelo[]"
                type="file"
                multiple
                accept=".cdr,.pdf,.jpg,.jpeg,.png,.ai,.eps,.svg,.dxf,.dwg,.step,.stp,.3dm,.stl,.obj,.fbx,.zip,.rar"
                @change="onFileChange($event)"
                class="hidden"
            />
            <div class="grid gap-4" style="grid-template-columns: 2fr 3fr; min-height: 200px;">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <label for="archivos_modelo" class="flex h-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-white px-4 py-6 text-gray-500 transition hover:border-[#d1be8a] hover:bg-[#fffdf5]">
                        <svg class="mb-2 h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16V4m0 0L8 8m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                        </svg>
                        <p class="text-sm font-medium">Elegir archivos</p>
                        <p class="mt-1 text-xs">CDR, PDF, JPG, PNG, AI, EPS y mas</p>
                    </label>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 flex flex-col h-full min-h-0">
                    <p class="mb-2 shrink-0 text-xs font-semibold uppercase tracking-wider text-gray-500">Archivos seleccionados <span class="font-normal text-gray-400">(max 10)</span></p>
                    <div class="flex-1 overflow-y-auto space-y-1 min-h-0">
                        <template x-for="(file, i) in files" :key="i">
                            <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-sm shadow-sm">
                                <span class="cursor-pointer truncate text-gray-700 hover:text-amber-700" x-text="file.name" @click="abrirVistaPrevia(file)"></span>
                                <button type="button" @click="removeFile(i)" class="ml-2 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-red-500 hover:bg-red-100 hover:text-red-700" title="Quitar archivo">
                                    <svg class="h-4 w-4" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p x-show="files.length === 0" class="py-4 text-center text-sm text-gray-400">Ningun archivo seleccionado</p>
                </div>
            </div>

            <template x-teleport="body">
                <div x-show="previewFile" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="cerrarVistaPrevia()" x-cloak>
                    <div class="relative max-h-[85vh] w-full max-w-3xl overflow-hidden rounded-2xl bg-white p-2 shadow-2xl">
                        <button type="button" @click="cerrarVistaPrevia()" class="absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-gray-600 shadow hover:bg-white hover:text-gray-900">
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <template x-if="previewFile && esImagen(previewFile)">
                            <img :src="previewUrl" class="max-h-[80vh] w-full rounded-xl object-contain" />
                        </template>
                        <template x-if="previewFile && esPdf(previewFile)">
                            <iframe :src="previewUrl" class="h-[80vh] w-full rounded-xl"></iframe>
                        </template>
                        <template x-if="previewFile && !esImagen(previewFile) && !esPdf(previewFile)">
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <svg class="mb-4 h-16 w-16 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <p class="text-lg font-medium text-gray-700" x-text="previewFile.name"></p>
                                <p class="mt-1 text-sm text-gray-500">Tipo: <span class="font-medium" x-text="previewFile.type || 'Desconocido'"></span></p>
                                <p class="text-sm text-gray-500">Tamano: <span class="font-medium" x-text="(previewFile.size / 1024).toFixed(1) + ' KB'"></span></p>
                                <p class="mt-4 text-xs text-gray-400">Vista previa no disponible para este tipo de archivo</p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        <p class="mt-2 text-xs text-gray-500">Sube hasta 10 archivos.</p>
        @error('archivos_modelo') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        @error('archivos_modelo.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

            @if(isset($pedido) && $pedido->exists)
                <div class="mt-4">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Archivos subidos</p>
                    @if($pedido->archivosDiseno->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($pedido->archivosDiseno as $archivo)
                                <a href="{{ asset('storage/' . $archivo->archivo_path) }}" target="_blank" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100">
                                    {{ $archivo->nombre_original }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Sin archivos de diseno.</p>
                    @endif
                </div>
            @endif
        </div>
    </section>

        <div class="grid gap-4 md:grid-cols-2 mt-4">
            <div>
                <label for="estado" class="mb-2 block text-sm font-medium text-gray-700">Estado</label>
                <select id="estado" name="estado" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900">
                    @foreach ($estados as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(old('estado', $pedido->estado ?? 'registrado') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
                @error('estado') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="monto_total" class="mb-2 block text-sm font-medium text-gray-700">Monto total</label>
                <input id="monto_total" x-model="monto" name="monto_total" type="number" step="0.01" min="0" value="{{ old('monto_total', $pedido->monto_total ?? '') }}" required class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="0.00" />
                @error('monto_total') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="monto_adelanto" class="mb-2 block text-sm font-medium text-gray-700">Adelanto</label>
                <input id="monto_adelanto" x-model="adelanto" name="monto_adelanto" type="number" step="0.01" min="0.01" value="{{ old('monto_adelanto', $pedido->monto_adelanto ?? '') }}" required readonly class="block w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-700" placeholder="0.00" />
                <p class="mt-2 text-xs text-gray-500">El adelanto es automatico (50% del total). Saldo pendiente: <span class="font-semibold" x-text="'S/ ' + Math.max(0, Number(monto || 0) - Number(adelanto || 0)).toFixed(2)"></span></p>
                @error('monto_adelanto') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

    </section>

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
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
                <input id="fecha_entrega_compromiso" name="fecha_entrega_compromiso" type="date" value="{{ old('fecha_entrega_compromiso', isset($pedido) && $pedido->fecha_entrega_compromiso ? $pedido->fecha_entrega_compromiso->format('Y-m-d') : '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" />
                @error('fecha_entrega_compromiso') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div x-show="tipoEntrega !== 'local'" x-transition class="mt-4 grid gap-4 md:grid-cols-2" style="display: none;">
            <div class="md:col-span-2">
                <label for="direccion_entrega" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Direccion destino / sede agencia' : 'Direccion entrega'"></label>
                <input x-ref="direccionEntrega" id="direccion_entrega" name="direccion_entrega" type="text" value="{{ old('direccion_entrega', $pedido->direccion_entrega ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" :placeholder="tipoEntrega === 'agencia' ? 'Direccion de destino o agencia' : 'Direccion completa de entrega'" />
                @error('direccion_entrega') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="distrito_entrega" class="mb-2 block text-sm font-medium text-gray-700">Distrito entrega</label>
                <input x-ref="distritoEntrega" id="distrito_entrega" name="distrito_entrega" type="text" value="{{ old('distrito_entrega', $pedido->distrito_entrega ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Distrito" />
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
                <input id="nombre_recibe" name="nombre_recibe" type="text" value="{{ old('nombre_recibe', $pedido->nombre_recibe ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" :placeholder="tipoEntrega === 'agencia' ? 'Nombre del contacto en agencia o receptor' : 'Persona que recibe el pedido'" />
                @error('nombre_recibe') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="telefono_recibe" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Telefono contacto agencia/receptor' : 'Telefono quien recibe'"></label>
                <input id="telefono_recibe" name="telefono_recibe" type="text" value="{{ old('telefono_recibe', $pedido->telefono_recibe ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="999999999" />
                @error('telefono_recibe') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="costo_delivery" class="mb-2 block text-sm font-medium text-gray-700" x-text="tipoEntrega === 'agencia' ? 'Costo agencia' : 'Costo delivery'"></label>
                <input id="costo_delivery" name="costo_delivery" type="number" step="0.01" min="0" value="{{ old('costo_delivery', $pedido->costo_delivery ?? '') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="0.00" />
                @error('costo_delivery') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Orden de Compra (Opcional)</h3>
        <div class="space-y-4">
            <div x-data="{ archivosSeleccionados: null }">
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
                    class="sr-only"
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

    <section class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Observaciones</h3>
        <div>
            <label for="observaciones" class="mb-2 block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea x-ref="observaciones" id="observaciones" name="observaciones" rows="3" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Notas internas">{{ old('observaciones', $pedido->observaciones ?? '') }}</textarea>
            @error('observaciones') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
    </section>

</div>
