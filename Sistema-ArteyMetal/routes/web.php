<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SunatController;
use App\Http\Controllers\ClienteConsultaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\RepartidorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'activo', 'permiso:dashboard.ver'])
    ->name('dashboard');

Route::middleware(['auth', 'activo'])->group(function () {
    Route::get('clientes/consulta-documento', [ClienteConsultaController::class, 'consultarPorDocumento'])->name('clientes.consulta_documento');
    Route::get('sunat/ruc', [SunatController::class, 'consultarRuc'])->name('sunat.ruc');

    Route::middleware('permiso:pedidos.ver')->group(function () {
        Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/seleccionar-caja/{cajaApertura}', [PedidoController::class, 'seleccionarCaja'])->whereNumber('cajaApertura')->name('pedidos.seleccionar_caja');
        Route::post('pedidos/cambiar-caja', [PedidoController::class, 'cambiarCaja'])->name('pedidos.cambiar_caja');
        Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])->whereNumber('pedido')->name('pedidos.show');
        Route::post('pedidos/{pedido}/transportar', [PedidoController::class, 'marcarEnTransporte'])->whereNumber('pedido')->name('pedidos.transportar');
        Route::post('pedidos/{pedido}/recibir-almacen', [PedidoController::class, 'marcarEnAlmacen'])->whereNumber('pedido')->name('pedidos.recibir_almacen');
        Route::post('pedidos/{pedido}/derivar', [PedidoController::class, 'derivar'])->whereNumber('pedido')->name('pedidos.derivar');
    });
    Route::middleware('permiso:pedidos.gestionar')->group(function () {
        Route::get('pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::get('pedidos/{pedido}/edit', [PedidoController::class, 'edit'])->whereNumber('pedido')->name('pedidos.edit');
        Route::put('pedidos/{pedido}', [PedidoController::class, 'update'])->whereNumber('pedido')->name('pedidos.update');
        Route::delete('pedidos/{pedido}', [PedidoController::class, 'destroy'])->whereNumber('pedido')->name('pedidos.destroy');
        Route::delete('pedidos/archivo-producto/{pedidoProductoArchivo}', [PedidoController::class, 'eliminarArchivoProducto'])->name('pedidos.eliminar_archivo_producto');
        Route::put('pedidos/{pedido}/personalizacion', [PedidoController::class, 'actualizarPersonalizacion'])->whereNumber('pedido')->name('pedidos.personalizacion');
        Route::post('pedidos/{pedido}/confirmar-pago-final', [PedidoController::class, 'confirmarPagoFinal'])->whereNumber('pedido')->name('pedidos.confirmar_pago_final');
        Route::post('pedidos/{pedido}/autorizar-recoger', [PedidoController::class, 'autorizarRecoger'])->whereNumber('pedido')->name('pedidos.autorizar_recoger');
        Route::post('pedidos/{pedido}/llegada-tienda', [PedidoController::class, 'registrarLlegadaTienda'])->whereNumber('pedido')->name('pedidos.llegada_tienda');
    });

    Route::middleware('permiso:clientes.ver')->group(function () {
        Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->whereNumber('cliente')->name('clientes.show');
    });
    Route::middleware('permiso:clientes.gestionar')->group(function () {
        Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->whereNumber('cliente')->name('clientes.edit');
        Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->whereNumber('cliente')->name('clientes.update');
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->whereNumber('cliente')->name('clientes.destroy');
    });

    Route::middleware('permiso:productos.ver')->group(function () {
        Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('productos/{producto}', [ProductoController::class, 'show'])->whereNumber('producto')->name('productos.show');
        Route::get('productos/imagenes/{imagen}', [ProductoController::class, 'verImagen'])->whereNumber('imagen')->name('productos.imagen.ver');
    });
    Route::middleware('permiso:productos.gestionar')->group(function () {
        Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->whereNumber('producto')->name('productos.edit');
        Route::put('productos/{producto}', [ProductoController::class, 'update'])->whereNumber('producto')->name('productos.update');
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->whereNumber('producto')->name('productos.destroy');
        Route::delete('productos/{producto}/imagenes/{imagen}', [ProductoController::class, 'destroyImagen'])->whereNumber('producto')->whereNumber('imagen')->name('productos.imagenes.destroy');
        Route::get('productos/categorias/json', [ProductoController::class, 'categoriasJson'])->name('productos.categorias.json');
        Route::post('productos/categorias', [ProductoController::class, 'categoriaStore'])->name('productos.categorias.store');
        Route::put('productos/categorias/{categoriaProducto}', [ProductoController::class, 'categoriaUpdate'])->whereNumber('categoriaProducto')->name('productos.categorias.update');
        Route::patch('productos/categorias/{categoriaProducto}/toggle', [ProductoController::class, 'categoriaToggle'])->whereNumber('categoriaProducto')->name('productos.categorias.toggle');
    });

    // Almacen
    Route::middleware('permiso:almacen.ver')->group(function () {
        Route::get('almacen', [AlmacenController::class, 'index'])->name('almacen.index');
        Route::get('almacen/productos', [AlmacenController::class, 'productos'])->name('almacen.productos');
        Route::get('almacen/movimientos', [AlmacenController::class, 'movimientos'])->name('almacen.movimientos');
        Route::get('almacen/pedidos', [AlmacenController::class, 'pedidosPendientes'])->name('almacen.pedidos');
    });
    Route::middleware('permiso:almacen.gestionar')->group(function () {
        Route::post('almacen/entrada', [AlmacenController::class, 'storeEntrada'])->name('almacen.entrada.store');
        Route::post('almacen/salida', [AlmacenController::class, 'storeSalida'])->name('almacen.salida.store');
        Route::post('almacen/pedidos/{pedido}/recibir', [AlmacenController::class, 'recibirPedido'])->whereNumber('pedido')->name('almacen.pedidos.recibir');
        Route::post('almacen/pedidos/{pedido}/entregar-cliente', [AlmacenController::class, 'entregarCliente'])->whereNumber('pedido')->name('almacen.pedidos.entregar_cliente');
    });

    Route::middleware('permiso:ventas.ver')->group(function () {
        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/seleccionar-caja/{cajaApertura}', [VentaController::class, 'seleccionarCaja'])->whereNumber('cajaApertura')->name('ventas.seleccionar_caja');
        Route::post('ventas/cambiar-caja', [VentaController::class, 'cambiarCaja'])->name('ventas.cambiar_caja');
        Route::get('ventas/{venta}', [VentaController::class, 'show'])->whereNumber('venta')->name('ventas.show');
        Route::get('ventas/{venta}/comprobante', [VentaController::class, 'comprobante'])->whereNumber('venta')->name('ventas.comprobante');
    });
    Route::middleware('permiso:ventas.gestionar')->group(function () {
        Route::get('ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::post('ventas/{venta}/emitir-comprobante', [VentaController::class, 'emitirComprobante'])->whereNumber('venta')->name('ventas.emitir_comprobante');
    });

    Route::middleware('permiso:caja.ver')->group(function () {
        Route::get('caja', [CajaController::class, 'index'])->name('cajas.index');
        Route::get('caja/{cajaApertura}', [CajaController::class, 'show'])->whereNumber('cajaApertura')->name('cajas.show');
    });
    Route::middleware('permiso:caja.gestionar')->group(function () {
        Route::post('caja', [CajaController::class, 'store'])->name('cajas.store');
        Route::post('caja/{cajaApertura}/cerrar', [CajaController::class, 'cerrar'])->whereNumber('cajaApertura')->name('cajas.cerrar');
    });

    Route::middleware('permiso:diseno.ver')->group(function () {
        Route::get('diseno', [DisenoController::class, 'index'])->name('diseno.index');
        Route::get('diseno/{pedido}', [DisenoController::class, 'show'])->whereNumber('pedido')->name('diseno.show');
    });
    Route::middleware('permiso:diseno.gestionar')->group(function () {
        Route::put('diseno/{pedido}', [DisenoController::class, 'update'])->whereNumber('pedido')->name('diseno.update');
    });

    Route::middleware('permiso:produccion.ver')->group(function () {
        Route::get('produccion', [ProduccionController::class, 'index'])->name('produccion.index');
        Route::get('produccion/{pedido}', [ProduccionController::class, 'show'])->whereNumber('pedido')->name('produccion.show');
    });
    Route::middleware('permiso:produccion.gestionar')->group(function () {
        Route::post('produccion/{pedido}/iniciar', [ProduccionController::class, 'iniciarProduccion'])->whereNumber('pedido')->name('produccion.iniciar');
        Route::post('produccion/{pedido}/notificar', [ProduccionController::class, 'notificarRepartidor'])->whereNumber('pedido')->name('produccion.notificar');
    });

    Route::middleware('permiso:repartidor.ver')->group(function () {
        Route::get('repartidor', [RepartidorController::class, 'index'])->name('repartidor.index');
        Route::get('repartidor/{pedido}', [RepartidorController::class, 'show'])->whereNumber('pedido')->name('repartidor.show');
    });
    Route::middleware('permiso:repartidor.gestionar')->group(function () {
        Route::post('repartidor/{pedido}/recoger', [RepartidorController::class, 'recoger'])->whereNumber('pedido')->name('repartidor.recoger');
    });

    Route::middleware('permiso:reportes.ver')->group(function () {
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/ventas/csv', [ReporteController::class, 'exportarVentasCsv'])->name('reportes.ventas.csv');
        Route::get('reportes/ventas/excel', [ReporteController::class, 'exportarVentasExcel'])->name('reportes.ventas.excel');
        Route::get('reportes/pedidos/csv', [ReporteController::class, 'exportarPedidosCsv'])->name('reportes.pedidos.csv');
        Route::get('reportes/pedidos/excel', [ReporteController::class, 'exportarPedidosExcel'])->name('reportes.pedidos.excel');
        Route::get('reportes/saldos/csv', [ReporteController::class, 'exportarSaldosCsv'])->name('reportes.saldos.csv');
        Route::get('reportes/saldos/excel', [ReporteController::class, 'exportarSaldosExcel'])->name('reportes.saldos.excel');
        Route::get('reportes/stock/csv', [ReporteController::class, 'exportarStockCsv'])->name('reportes.stock.csv');
        Route::get('reportes/stock/excel', [ReporteController::class, 'exportarStockExcel'])->name('reportes.stock.excel');
    });

    Route::middleware('permiso:usuarios.ver')->group(function () {
        Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    });
    Route::middleware('permiso:usuarios.gestionar')->group(function () {
        Route::get('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->whereNumber('usuario')->name('usuarios.edit');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->whereNumber('usuario')->name('usuarios.update');
        Route::patch('usuarios/{usuario}/toggle-activo', [UsuarioController::class, 'toggleActivo'])->whereNumber('usuario')->name('usuarios.toggle_activo');
        Route::get('roles/panel-data', [RolController::class, 'panelData'])->name('roles.panel_data');
    });

    Route::middleware('permiso:roles.ver')->group(function () {
        Route::get('roles', [RolController::class, 'index'])->name('roles.index');
    });
    Route::middleware('permiso:roles.gestionar')->group(function () {
        Route::get('roles/create', [RolController::class, 'create'])->name('roles.create');
        Route::post('roles', [RolController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RolController::class, 'edit'])->whereNumber('role')->name('roles.edit');
        Route::put('roles/{role}', [RolController::class, 'update'])->whereNumber('role')->name('roles.update');
        Route::delete('roles/{role}', [RolController::class, 'destroy'])->whereNumber('role')->name('roles.destroy');
    });

    Route::middleware('permiso:configuracion.ver')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
