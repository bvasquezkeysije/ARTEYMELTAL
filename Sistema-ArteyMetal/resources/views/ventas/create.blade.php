<x-app-layout>
    <x-slot name="header">
        <span>Nueva venta</span>
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

    @php
        $categorias = [
            'medallas' => 'Medallas',
            'marbetes_distintivos' => 'Marbetes y Distintivos',
            'placas' => 'Placas',
            'reconocimientos' => 'Reconocimientos',
        ];

        $productosFrontend = $productos->map(function ($producto) use ($categorias) {
            return [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'categoria' => $producto->categoria,
                'categoria_texto' => $categorias[$producto->categoria] ?? $producto->categoria,
                'descripcion' => $producto->descripcion,
                'stock' => (int) $producto->stock_actual,
                'stock_tienda' => (int) ($producto->stock_tienda ?? 0),
                'stock_almacen' => (int) ($producto->stock_almacen ?? 0),
                'precio' => (float) ($producto->precio_referencia ?? 0),
                'imagenes' => $producto->imagenes
                    ->map(fn ($imagen) => [
                        'url' => asset('storage/' . $imagen->archivo_path),
                        'nombre' => $imagen->nombre_original,
                    ])
                    ->values(),
            ];
        })->values();

        $oldProductoIds = old('producto_id', []);
        $oldCantidades = old('cantidad', []);
        $lineasIniciales = [];

        if (is_array($oldProductoIds) && count($oldProductoIds) > 0) {
            foreach ($oldProductoIds as $i => $productoId) {
                $productoEncontrado = $productos->firstWhere('id', (int) $productoId);
                $lineasIniciales[] = [
                    'producto_id' => (string) $productoId,
                    'busqueda' => $productoEncontrado
                        ? ($productoEncontrado->codigo . ' - ' . $productoEncontrado->nombre)
                        : '',
                    'cantidad' => (int) ($oldCantidades[$i] ?? 1),
                    'abierto' => false,
                ];
            }
        }

        if (empty($lineasIniciales)) {
            $lineasIniciales[] = ['producto_id' => '', 'busqueda' => '', 'cantidad' => 1, 'abierto' => false];
        }
    @endphp

    <div x-data="{
        productos: @js($productosFrontend),
        lineas: @js($lineasIniciales),
        modalProductoAbierto: false,
        productoVista: null,
        fotoIndex: 0,
        consultandoDocumentoCliente: false,
        mensajeDocumentoCliente: '',
        consultaDocumentoClienteOk: false,
        montoRecibido: '',
        formaPago: 'efectivo',
        fotosPago: [],
        modalFotosPago: false,
        fotoPagoIndex: 0,
        get totalVenta() {
            let t = 0;
            for (const linea of this.lineas) {
                const p = this.productoPorId(linea.producto_id);
                if (p && linea.cantidad) t += Number(p.precio) * Number(linea.cantidad);
            }
            return t;
        },
        get vuelto() {
            if (!this.montoRecibido || this.montoRecibido <= 0) return 0;
            const v = parseFloat(this.montoRecibido) - this.totalVenta;
            return v > 0 ? v : 0;
        },
        initMontoRecibido() {
            if (this.formaPago !== 'efectivo') this.montoRecibido = this.totalVenta;
        },
        onFotosPagoChange(e) {
            const files = Array.from(e.target.files || []);
            this.fotosPago = files.slice(0, 5);
        },
        abrirModalFotosPago() {
            if (this.fotosPago.length > 0) this.modalFotosPago = true;
        },
        cerrarModalFotosPago() {
            this.modalFotosPago = false;
        },
        agregarLinea() {
            this.lineas.push({ producto_id: '', busqueda: '', cantidad: 1, abierto: false });
        },
        quitarLinea(i) {
            if (this.lineas.length > 1) this.lineas.splice(i, 1);
        },
        textoProducto(p) {
            return `${p.codigo} - ${p.nombre} (Stock: ${p.stock}) - S/ ${Number(p.precio).toFixed(2)}`;
        },
        filtrarProductos(texto) {
            const q = (texto || '').trim().toLowerCase();
            if (!q) return this.productos.slice(0, 8);
            return this.productos
                .filter(p => p.codigo.toLowerCase().includes(q) || p.nombre.toLowerCase().includes(q))
                .slice(0, 8);
        },
        seleccionarProducto(i, p) {
            this.lineas[i].producto_id = String(p.id);
            this.lineas[i].busqueda = `${p.codigo} - ${p.nombre}`;
            this.lineas[i].abierto = false;
        },
        productoPorId(id) {
            return this.productos.find(p => String(p.id) === String(id)) || null;
        },
        abrirVistaProducto(linea) {
            if (!linea?.producto_id) return;
            const p = this.productoPorId(linea.producto_id);
            if (!p) return;
            this.productoVista = p;
            this.fotoIndex = 0;
            this.modalProductoAbierto = true;
        },
        cerrarVistaProducto() {
            this.modalProductoAbierto = false;
            this.productoVista = null;
            this.fotoIndex = 0;
        },
        siguienteFoto() {
            if (!this.productoVista || !this.productoVista.imagenes?.length) return;
            this.fotoIndex = (this.fotoIndex + 1) % this.productoVista.imagenes.length;
        },
        anteriorFoto() {
            if (!this.productoVista || !this.productoVista.imagenes?.length) return;
            this.fotoIndex = (this.fotoIndex - 1 + this.productoVista.imagenes.length) % this.productoVista.imagenes.length;
        },
        async buscarClientePorDocumento() {
            const numeroRaw = (this.$refs.documentoClienteVenta?.value || '').trim();
            const numero = numeroRaw.replace(/\\D/g, '');
            if (this.$refs.documentoClienteVenta) this.$refs.documentoClienteVenta.value = numero;

            this.mensajeDocumentoCliente = '';
            this.consultaDocumentoClienteOk = false;

            if (!numero) {
                this.mensajeDocumentoCliente = 'Ingresa DNI o RUC para buscar.';
                return;
            }

            if (!/^[0-9]{8}$|^[0-9]{11}$/.test(numero)) {
                this.mensajeDocumentoCliente = 'El documento debe tener 8 digitos (DNI) o 11 digitos (RUC).';
                return;
            }

            this.consultandoDocumentoCliente = true;
            try {
                const url = new URL('{{ route('clientes.consulta_documento') }}', window.location.origin);
                url.searchParams.set('numero', numero);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });

                const data = await response.json();
                this.mensajeDocumentoCliente = data?.message || 'Consulta realizada.';
                this.consultaDocumentoClienteOk = !!data?.ok;

                if (data?.ok && data?.cliente && this.$refs.nombreClienteVenta) {
                    this.$refs.nombreClienteVenta.value = data.cliente.nombre || '';
                    if (this.$refs.documentoComprobante && data.cliente.documento) {
                        this.$refs.documentoComprobante.value = data.cliente.documento;
                    }
                    if (this.$refs.tipoComprobante && data.cliente.documento) {
                        const doc = String(data.cliente.documento).replace(/\D/g, '');
                        this.$refs.tipoComprobante.value = doc.length === 11 ? 'factura' : 'boleta';
                    }
                }
            } catch (error) {
                this.mensajeDocumentoCliente = 'Error al consultar el documento.';
                this.consultaDocumentoClienteOk = false;
            } finally {
                this.consultandoDocumentoCliente = false;
            }
        },
        consumidorFinal() {
            if (this.$refs.documentoClienteVenta) this.$refs.documentoClienteVenta.value = '99999999';
            if (this.$refs.documentoComprobante) this.$refs.documentoComprobante.value = '99999999';
            if (this.$refs.nombreClienteVenta) this.$refs.nombreClienteVenta.value = 'Consumidor Final';
            if (this.$refs.direccionCliente) this.$refs.direccionCliente.value = 'Consumidor Final';
            this.mensajeDocumentoCliente = 'Cliente asignado como Consumidor Final.';
            this.consultaDocumentoClienteOk = true;
        },
        cerrarLista(i) {
            setTimeout(() => { if (this.lineas[i]) this.lineas[i].abierto = false; }, 120);
        }
    }" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">Revisa los datos ingresados.</div>
        @endif

        <form method="POST" action="{{ route('ventas.store') }}" class="space-y-5">
            @csrf
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Productos</h3>
                    <div class="space-y-3">
                        <template x-for="(linea, i) in lineas" :key="i">
                            <div class="grid gap-3 md:grid-cols-[1fr_auto_120px_auto]">
                                <div class="relative">
                                    <label class="mb-2 block text-sm font-medium text-gray-700" x-text="'Producto ' + (i+1)"></label>
                                    <input type="hidden" :name="'producto_id['+i+']'" :value="linea.producto_id" />
                                    <input
                                        type="text"
                                        x-model="linea.busqueda"
                                        @focus="linea.abierto = true"
                                        @input="linea.abierto = true; linea.producto_id = ''"
                                        @blur="cerrarLista(i)"
                                        class="block h-[46px] w-full rounded-xl border border-gray-300 bg-gray-50 px-4 text-gray-900"
                                        placeholder="Buscar por codigo o nombre"
                                    />
                                    <div
                                        x-show="linea.abierto"
                                        x-transition
                                        class="absolute z-20 mt-1 max-h-56 w-full overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg"
                                        style="display: none;"
                                    >
                                        <template x-for="p in filtrarProductos(linea.busqueda)" :key="p.id">
                                            <button
                                                type="button"
                                                @mousedown.prevent="seleccionarProducto(i, p)"
                                                class="block w-full border-b border-gray-200 px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                                                x-text="textoProducto(p)"
                                            ></button>
                                        </template>
                                        <p x-show="filtrarProductos(linea.busqueda).length === 0" class="px-3 py-2 text-xs text-gray-500">Sin resultados</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Detalle</label>
                                    <button
                                        type="button"
                                        @click="abrirVistaProducto(linea)"
                                        :disabled="!linea.producto_id"
                                        class="inline-flex h-[46px] w-[46px] items-center justify-center rounded-xl border border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                                        title="Ver detalle"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z" />
                                            <circle cx="12" cy="12" r="3" stroke-width="1.9"></circle>
                                        </svg>
                                    </button>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Cantidad</label>
                                    <input :name="'cantidad['+i+']'" x-model="linea.cantidad" type="number" min="1" class="block h-[46px] w-full rounded-xl border border-gray-300 bg-gray-50 px-4 text-gray-900" />
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="quitarLinea(i)" class="inline-flex h-[46px] w-[46px] items-center justify-center rounded-xl bg-red-600 hover:bg-red-700" title="Eliminar producto">
                                        <img src="{{ asset('icons/eliminar.ico') }}" alt="Eliminar" class="h-5 w-5 object-contain pointer-events-none brightness-0 invert" />
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="flex justify-center pt-2">
                            <button type="button" @click="agregarLinea" class="inline-flex items-center gap-3 rounded-xl px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90" style="background-color:#b9943d">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Agregar producto
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Cliente</h3>
                    <div class="grid gap-3 md:grid-cols-[1fr_auto]">
                        <div>
                            <label for="documento_cliente_venta" class="mb-2 block text-sm font-medium text-gray-700">Documento cliente (DNI/RUC)</label>
                            <input
                                x-ref="documentoClienteVenta"
                                id="documento_cliente_venta"
                                name="documento_busqueda_cliente"
                                type="text"
                                value="{{ old('documento_cliente') }}"
                                @keydown.enter.prevent="buscarClientePorDocumento()"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900"
                                placeholder="Ejemplo: 76636255 o 20601030013"
                            />
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="button" @click="consumidorFinal()" class="h-[46px] rounded-xl border border-[#cba34d] px-4 py-3 text-xs font-semibold text-white hover:opacity-90" style="background-color:#b9943d">
                                Consumidor Final
                            </button>
                            <button type="button" @click="buscarClientePorDocumento()" :disabled="consultandoDocumentoCliente" class="inline-flex h-[46px] w-[46px] items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60" title="Buscar">
                                <img src="{{ asset('icons/buscar.ico') }}" alt="Buscar" class="h-5 w-5 object-contain pointer-events-none brightness-0 invert" />
                            </button>
                        </div>
                    </div>
                    <p x-show="mensajeDocumentoCliente" class="mt-1 text-xs" :class="consultaDocumentoClienteOk ? 'text-emerald-700' : 'text-rose-700'" x-text="mensajeDocumentoCliente"></p>

                    <div class="mt-3">
                        <label for="cliente_nombre" class="mb-2 block text-sm font-medium text-gray-700">Nombre cliente</label>
                        <input x-ref="nombreClienteVenta" id="cliente_nombre" name="cliente_nombre" type="text" value="{{ old('cliente_nombre') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Nombre del cliente" />
                    </div>
                    <div class="mt-3">
                        <label for="direccion_cliente" class="mb-2 block text-sm font-medium text-gray-700">Direccion cliente</label>
                        <input x-ref="direccionCliente" id="direccion_cliente" name="direccion_cliente" type="text" value="{{ old('direccion_cliente') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="Direccion del cliente" />
                        @error('direccion_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Pago</h3>
                        <p class="text-sm font-semibold text-gray-700">Total: S/ <span x-text="totalVenta.toFixed(2)">0.00</span></p>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
                        <div>
                            <label for="forma_pago" class="mb-2 block text-sm font-medium text-gray-700">Forma de pago</label>
                            <select id="forma_pago" name="forma_pago" x-model="formaPago" @change="initMontoRecibido()" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900">
                                <option value="efectivo" @selected(old('forma_pago', 'efectivo') === 'efectivo')>Efectivo</option>
                                <option value="yape" @selected(old('forma_pago') === 'yape')>Yape</option>
                                <option value="plin" @selected(old('forma_pago') === 'plin')>Plin</option>
                                <option value="transferencia" @selected(old('forma_pago') === 'transferencia')>Transferencia bancaria</option>
                                <option value="tarjeta" @selected(old('forma_pago') === 'tarjeta')>Tarjeta débito/crédito</option>
                                <option value="mixto" @selected(old('forma_pago') === 'mixto')>Mixto</option>
                            </select>
                            @error('forma_pago') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="monto_recibido" class="mb-2 block text-sm font-medium text-gray-700">Monto recibido</label>
                            <input id="monto_recibido" name="monto_recibido" type="number" step="0.01" x-model="montoRecibido" :readonly="formaPago !== 'efectivo'" :class="formaPago !== 'efectivo' ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white'" class="block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900" placeholder="0.00" />
                            @error('monto_recibido') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div x-show="formaPago === 'efectivo'">
                            <label for="vuelto" class="mb-2 block text-sm font-medium text-gray-700">Vuelto</label>
                            <input id="vuelto" type="number" step="0.01" class="block w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500" :value="vuelto.toFixed(2)" readonly />
                        </div>
                        <div x-show="formaPago !== 'efectivo'">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Comprobante de pago <span class="text-xs text-gray-400">(Opcional)</span></label>
                            <div class="flex gap-2">
                                <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-xl border border-dashed border-gray-300 bg-white px-4 py-3 text-sm text-gray-500 hover:border-[#b9943d] hover:text-[#b9943d] transition-colors">
                                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.58-4.58a1 1 0 011.41 0L12 13.5l2.59-2.59a1 1 0 011.41 0L20 16m-4-8a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                    <span x-text="fotosPago.length ? fotosPago.length + ' foto(s) seleccionada(s)' : 'Subir foto(s)'"></span>
                                    <input id="comprobante_pago" name="comprobante_pago[]" type="file" accept="image/*" multiple @change="onFotosPagoChange($event)" class="hidden" />
                                </label>
                                <button type="button" @click="abrirModalFotosPago()" :disabled="fotosPago.length === 0" class="inline-flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-xl bg-[#111] hover:bg-[#262626] disabled:opacity-40" title="Ver fotos">
                                    <img src="{{ asset('icons/ver-detalle.ico') }}" alt="Ver fotos" class="h-5 w-5 object-contain pointer-events-none brightness-0 invert" />
                                </button>
                            </div>
                            @error('comprobante_pago') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Comprobante</h3>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label for="tipo_comprobante" class="mb-2 block text-sm font-medium text-gray-700">Tipo comprobante</label>
                        <select x-ref="tipoComprobante" id="tipo_comprobante" name="tipo_comprobante" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900">
                            <option value="boleta" @selected(old('tipo_comprobante', 'boleta') === 'boleta')>Boleta</option>
                            <option value="factura" @selected(old('tipo_comprobante') === 'factura')>Factura</option>
                        </select>
                        @error('tipo_comprobante') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="documento_cliente_comprobante" class="mb-2 block text-sm font-medium text-gray-700">Documento cliente</label>
                        <input x-ref="documentoComprobante" id="documento_cliente_comprobante" name="documento_cliente" type="text" value="{{ old('documento_cliente') }}" class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900" placeholder="DNI o RUC" />
                        @error('documento_cliente') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">La factura requiere RUC de 11 digitos y nombre/razon social del cliente.</p>
            </div>

            <div>
                <label for="observaciones" class="mb-2 block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="3" class="block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900" placeholder="Notas adicionales sobre la venta">{{ old('observaciones') }}</textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-[#111] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#262626]">Registrar venta</button>
                <a href="{{ route('ventas.index') }}" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700">Cancelar</a>
            </div>
        </form>

        <div x-show="modalProductoAbierto" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" style="display: none;" @click="cerrarVistaProducto()"></div>
        <div x-show="modalProductoAbierto" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="w-full max-w-5xl rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-base font-semibold text-gray-800">Detalle de producto</h3>
                    <button type="button" @click="cerrarVistaProducto()" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>

                <div class="grid gap-5 p-5 md:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <template x-if="productoVista && productoVista.imagenes && productoVista.imagenes.length">
                            <div>
                                <img
                                    :src="productoVista.imagenes[fotoIndex].url"
                                    :alt="productoVista.imagenes[fotoIndex].nombre"
                                    class="h-72 w-full rounded-lg object-cover"
                                />
                                <div class="mt-3 flex items-center justify-between">
                                    <button type="button" @click="anteriorFoto()" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Anterior</button>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="fotoIndex + 1"></span>/<span x-text="productoVista.imagenes.length"></span>
                                    </p>
                                    <button type="button" @click="siguienteFoto()" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Siguiente</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="!productoVista || !productoVista.imagenes || !productoVista.imagenes.length">
                            <div class="flex h-72 items-center justify-center rounded-lg border border-dashed border-gray-300 text-sm text-gray-500">
                                Sin imagenes del producto
                            </div>
                        </template>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Codigo</p>
                            <p class="mt-1 text-gray-900" x-text="productoVista?.codigo || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Nombre</p>
                            <p class="mt-1 text-gray-900" x-text="productoVista?.nombre || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Categoria</p>
                            <p class="mt-1 text-gray-900" x-text="productoVista?.categoria_texto || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Stock</p>
                            <div class="mt-1 flex gap-4 text-gray-900">
                                <span>Tienda: <strong x-text="productoVista?.stock_tienda"></strong></span>
                                <span>Almacen: <strong x-text="productoVista?.stock_almacen"></strong></span>
                                <span class="text-gray-500">| Total: <strong x-text="productoVista?.stock"></strong></span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Precio referencia</p>
                            <p class="mt-1 text-gray-900" x-text="productoVista ? ('S/ ' + Number(productoVista.precio).toFixed(2)) : '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Descripcion</p>
                            <p class="mt-1 text-gray-900" x-text="productoVista?.descripcion || '-'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalFotosPago" x-transition.opacity class="fixed inset-0 z-40 bg-black/50" style="display: none;" @click="cerrarModalFotosPago()"></div>
        <div x-show="modalFotosPago" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="w-full max-w-3xl rounded-2xl border border-gray-200 bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-base font-semibold text-gray-800">Comprobantes de pago</h3>
                    <button type="button" @click="cerrarModalFotosPago()" class="btn-icon-sm bg-red-600 hover:bg-red-700" title="Cerrar">
                        <img src="{{ asset('icons/cerrar.ico') }}" alt="Cerrar" class="h-4 w-4 object-contain pointer-events-none" />
                    </button>
                </div>
                <div class="p-5">
                    <template x-if="fotosPago.length">
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                            <template x-for="(foto, i) in fotosPago" :key="i">
                                <div class="overflow-hidden rounded-xl border border-gray-200">
                                    <img :src="URL.createObjectURL(foto)" :alt="'Foto ' + (i + 1)" class="h-48 w-full object-cover" />
                                    <p class="truncate border-t border-gray-200 px-3 py-2 text-xs text-gray-500" x-text="foto.name"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!fotosPago.length">
                        <p class="py-10 text-center text-sm text-gray-500">No hay fotos seleccionadas.</p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
