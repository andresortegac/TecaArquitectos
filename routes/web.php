<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ReportesStockController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\MetricasController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\ArriendoDevolucionController;
use Illuminate\Support\Facades\Route;


// DASHBOARD---------admin y bodega--------------------->
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

// INVENTARIO / BODEGA
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::resource('productos', ProductoController::class);

    Route::post('/productos/import', [ProductoController::class, 'import'])
        ->name('productos.import');
});

// ruta para solicitud
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])
        ->name('solicitudes.create');

    Route::post('/solicitudes', [SolicitudController::class, 'store'])
        ->name('solicitudes.store');

    Route::get('/solicitudes', [SolicitudController::class, 'solicitudes'])
        ->name('solicitudes.solicitudes');
});

//---------------------------------------------------
// ruta Movimiento
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/movimientos', [MovimientoController::class, 'create'])
        ->name('movimientos.create');

    Route::post('/movimientos', [MovimientoController::class, 'store'])
        ->name('movimientos.store');
});

Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    //ruta para alerta de stock
    Route::get('/alertas-stock', [ProductoController::class, 'alertasStock'])
        ->name('productos.alertas');

});

Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    //rura de reporte de movimiento
    Route::prefix('reportes')->group(function () {
        Route::get('/', [ReportesStockController::class, 'index'])->name('reportes.index');
        Route::get('/movimientos', [ReportesStockController::class, 'movimientos'])->name('reportes.movimientos');
        Route::get('/mensual', [ReportesStockController::class, 'mensual'])->name('reportes.mensual');
    });
});

Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::get('/movimientos/export', [MovimientoController::class, 'export'])
        ->name('movimientos.export');

    Route::get('/reporte/mensual/export', [ReportesStockController::class, 'exportMensual'])
        ->name('reporte.mensual.export');

    //ruta para configuracion
    Route::get('/configuracion', [ConfigController::class, 'index'])
        ->name('configuracion.index');

    Route::post('/configuracion/stock', [ConfigController::class, 'stock'])
        ->name('config.stock');

    Route::post('/configuracion/reportes', [ConfigController::class, 'reportes'])
        ->name('config.reportes');

    Route::post('/configuracion/inventario', [ConfigController::class, 'inventario'])
        ->name('config.inventario');
});

Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    // ruta para stock actual
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{producto}', [StockController::class, 'show'])->name('stock.show');
    Route::get('/stock-exportar', [StockController::class, 'export'])->name('stock.export');

    //ruta de metricas 
    Route::get('/metricas', [MetricasController::class, 'index'])
        ->name('metricas.index');
});
//---------------------fin---------------------------------------->

//---------------solo asistente------------------------------>
// ARRIENDOS
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {
    Route::resource('arriendos', ArriendoController::class);

    Route::post('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'cerrar'])
        ->name('arriendos.cerrar');

    Route::get('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'showCerrar'])
        ->name('arriendos.cerrar.form');

    // ✅✅✅ (ÚNICO CAMBIO) - ASEGURAR QUE DETALLES ESTÉ DENTRO DEL MIDDLEWARE
    Route::get('/arriendos/{arriendo}/detalles', [ArriendoController::class, 'detalles'])
        ->name('arriendos.detalles');

    //alerta-stock_actual-metricas----
});

//DETALLES ARRIENDO (la dejas igual si quieres, pero OJO: duplicada)
// Route::get('/arriendos/{arriendo}/detalles', [App\Http\Controllers\ArriendoController::class, 'detalles'])
//     ->name('arriendos.detalles');


//ARRIENDO-DEVOLUCION
Route::get('/arriendos/{arriendo}/devolucion', [ArriendoDevolucionController::class, 'create'])
    ->name('arriendos.devolucion.create');

Route::post('/arriendos/{arriendo}/devolucion', [ArriendoDevolucionController::class, 'store'])
    ->name('arriendos.devolucion.store');

//ARRIENDO PADRE E HIJOS

// PADRE
Route::get('/arriendos/{arriendo}/ver', [App\Http\Controllers\ArriendoController::class, 'ver'])
    ->name('arriendos.ver');

// ITEMS (agregar producto dentro del padre)
Route::get('/arriendos/{arriendo}/items/create', [App\Http\Controllers\ArriendoItemController::class, 'create'])
    ->name('arriendos.items.create');

Route::post('/arriendos/{arriendo}/items', [App\Http\Controllers\ArriendoItemController::class, 'store'])
    ->name('arriendos.items.store');

//eliminar 
Route::delete('/arriendos/items/{item}', [\App\Http\Controllers\ArriendoItemController::class, 'destroy'])
    ->name('arriendos.items.destroy');

// VER DEVOLUCIONES (REGISTROS INDIVIDUALES) DEL PADRE ✅
Route::get('/arriendos/{arriendo}/devoluciones', [App\Http\Controllers\ArriendoController::class, 'devoluciones'])
    ->name('arriendos.devoluciones');

// DEVOLVER desde el PADRE pero por ITEM
Route::get('/items/{item}/devolucion', [App\Http\Controllers\ItemDevolucionController::class, 'create'])
    ->name('items.devolucion.create');

Route::post('/items/{item}/devolucion', [App\Http\Controllers\ItemDevolucionController::class, 'store'])
    ->name('items.devolucion.store');


// CLIENTES
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {

    // CLIENTES
    Route::resource('clientes', ClienteController::class);

    // OBRAS (dependen de un cliente)
    Route::get('clientes/{cliente}/obras/create', [ObraController::class, 'create'])
        ->name('obras.create');

    Route::post('clientes/{cliente}/obras', [ObraController::class, 'store'])
        ->name('obras.store');

    // este trae la informacion de la obra    
    Route::get('/clientes/{cliente}/obras', [ClienteController::class, 'obras']);
});

// LOGIN
Route::controller(LoginController::class)->group(function () {

    // ✅ Login en la raiz
    Route::get('/', 'show')
        ->name('login');

    // ✅ Procesar login
    Route::post('/login', 'login')
        ->name('login.post');

    // ✅ Logout
    Route::post('/logout', 'logout')
        ->name('logout');
});
