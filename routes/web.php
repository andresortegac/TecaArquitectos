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
use App\Http\Controllers\GastoController;
use App\Http\Controllers\controlproducto;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProductoadminController;


use Illuminate\Support\Facades\Route;

// ✅ Payment model para endpoint de recaudo hoy
use App\Models\Payment;


/*
|--------------------------------------------------------------------------
| DASHBOARD---------admin y bodega--------------------->
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| INVENTARIO / BODEGA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|bodega'])->group(function () {

    Route::resource('productos', ProductoController::class);

    Route::post('/productos', [ProductoadminController::class, 'inventario'])
        ->name('productos.inventario');


    Route::post('/productos/import', [ProductoController::class, 'import'])
        ->name('productos.import');

    
});

Route::middleware(['auth', 'role:bodega'])->group(function () {

    Route::get('/restrincion', [ProductoadminController::class, 'inventario'])
        ->name('restrincion.inventario');
    
});

Route::middleware(['auth', 'role:admin|bodega'])->group(function () {

    Route::get('/solicitudes-detalladas', 
        [SolicitudController::class, 'indexDetallado']
    )->name('solicitudes.detalladas');
});


/*
|--------------------------------------------------------------------------
| SOLICITUDES
|------------------------------------------------------------------------- 
*/
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])
        ->name('solicitudes.create');

    Route::post('/solicitudes', [SolicitudController::class, 'store'])
        ->name('solicitudes.store');

    Route::get('/solicitudes', [SolicitudController::class, 'solicitudes'])
        ->name('solicitudes.solicitudes');

    Route::get('/solicitudes/{arriendo}', [SolicitudController::class, 'show'])
        ->name('solicitudes.show');

    Route::post('/solicitudes/{arriendo}/confirmar', [SolicitudController::class, 'confirmar'])
        ->name('solicitudes.confirmar');
        //ruta de factura-solicitud
    Route::get('/arriendos/{arriendo}/pdf',[ArriendoController::class, 'pdf'])
    ->name('arriendos.pdf');

});

//---------------------------------------------------
// ruta Movimiento
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/movimientos', [MovimientoController::class, 'create'])
        ->name('movimientos.create');

    Route::post('/movimientos', [MovimientoController::class, 'store'])
        ->name('movimientos.store');
});


/*
|--------------------------------------------------------------------------
| ALERTAS STOCK
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {
    Route::get('/alertas-stock', [ProductoController::class, 'alertasStock'])
        ->name('productos.alertas');
});


/*
|--------------------------------------------------------------------------
| REPORTES (solo admin|bodega)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('reportes')->group(function () {
        Route::get('/', [ReportesStockController::class, 'index'])->name('reportes.index');
        Route::get('/movimientos', [ReportesStockController::class, 'movimientos'])->name('reportes.movimientos');
        Route::get('/controlproducto', [controlproducto::class, 'controlproducto'])->name('reportes.controlproducto');
        Route::get('reportes/generalrep', [ReportesStockController::class, 'reportes'])->name('reportes.generalrep');
        // ✅ RF-28
        Route::get('/clientes-pendientes',[ReporteController::class, 'clientesPendientes'])
        ->name('reportes.clientes-pendientes');
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

/*
|--------------------------------------------------------------------------
| STOCK + MÉTRICAS (admin|bodega|asistente)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|bodega|asistente'])->group(function () {

    // STOCK
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{producto}', [StockController::class, 'show'])->name('stock.show');
    Route::get('/stock-exportar', [StockController::class, 'export'])->name('stock.export');

    // MÉTRICAS (index)
    Route::get('/metricas', [MetricasController::class, 'index'])
        ->name('metricas.index');

    /*
    |--------------------------------------------------------------------------
    | ✅ NUEVO: DETALLES DE RECAUDO
    | - Año -> lista meses
    | - Mes -> lista días
    | - Día -> detalle por hora / pagos / arriendos
    |--------------------------------------------------------------------------
    */
    Route::get('/metricas/reporte/anual/{year}', [MetricasController::class, 'reporteAnual'])
        ->name('metricas.reporte.anual');

    Route::get('/metricas/reporte/mensual/{year}/{month}', [MetricasController::class, 'reporteMensual'])
        ->name('metricas.reporte.mensual');

    Route::get('/metricas/detalle/dia/{date}', [MetricasController::class, 'detalleDia'])
        ->name('metricas.detalle.dia');
});


/*
|--------------------------------------------------------------------------
| ARRIENDOS (admin|asistente)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {

    Route::resource('arriendos', ArriendoController::class);

    Route::post('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'cerrar'])
        ->name('arriendos.cerrar');

    Route::get('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'showCerrar'])
        ->name('arriendos.cerrar.form');

    Route::get('/arriendos/{arriendo}/detalles', [ArriendoController::class, 'detalles'])
        ->name('arriendos.detalles');

    // routes/web.php
    Route::get('clientes/{cliente}/obras', function ($clienteId) {
         return \App\Models\Obra::where('cliente_id', $clienteId)->get();

    });


    // PADRE
    Route::get('/arriendos/{arriendo}/ver', [ArriendoController::class, 'ver'])
        ->name('arriendos.ver');

    // ✅ NUEVO: TRANSPORTES (múltiples por arriendo)
    Route::post('/arriendos/{arriendo}/transportes', [App\Http\Controllers\ArriendoTransporteController::class, 'store'])
        ->name('arriendos.transportes.store');

    Route::delete('/arriendos/transportes/{transporte}', [App\Http\Controllers\ArriendoTransporteController::class, 'destroy'])
        ->name('arriendos.transportes.destroy');

    // ✅ API KPI "Recaudado hoy"
    Route::get('/api/recaudado-hoy', function () {
        return response()->json([
            'total' => Payment::where('business_date', now()->toDateString())
                ->where('status', 'confirmed')
                ->sum('total_amount'),
        ]);
    })->name('api.recaudado_hoy');

    // DEVOLUCIÓN PADRE (queda dentro del middleware ✅)
    Route::get('/arriendos/{arriendo}/devolucion', [ArriendoDevolucionController::class, 'create'])
        ->name('arriendos.devolucion.create');

    Route::post('/arriendos/{arriendo}/devolucion', [ArriendoDevolucionController::class, 'store'])
        ->name('arriendos.devolucion.store');

        //ARRIENDO PADRE E HIJOS

// PADRE (⚠️ ya existe arriba; lo dejo SIN BORRAR, pero lo comento para evitar duplicado)
// Route::get('/arriendos/{arriendo}/ver', [App\Http\Controllers\ArriendoController::class, 'ver'])
//     ->name('arriendos.ver');


    // ITEMS (agregar producto dentro del padre)
    Route::get('/arriendos/{arriendo}/items/create', [App\Http\Controllers\ArriendoItemController::class, 'create'])
        ->name('arriendos.items.create');

    Route::post('/arriendos/{arriendo}/items', [App\Http\Controllers\ArriendoItemController::class, 'store'])
        ->name('arriendos.items.store');

    // eliminar item
    Route::delete('/arriendos/items/{item}', [App\Http\Controllers\ArriendoItemController::class, 'destroy'])
        ->name('arriendos.items.destroy');

    // VER DEVOLUCIONES DEL PADRE
    Route::get('/arriendos/{arriendo}/devoluciones', [App\Http\Controllers\ArriendoController::class, 'devoluciones'])
        ->name('arriendos.devoluciones');

    // DEVOLVER POR ITEM
    Route::get('/items/{item}/devolucion', [App\Http\Controllers\ItemDevolucionController::class, 'create'])
        ->name('items.devolucion.create');

    Route::post('/items/{item}/devolucion', [App\Http\Controllers\ItemDevolucionController::class, 'store'])
        ->name('items.devolucion.store');

    // routes/web.php - obras por cliente (AJAX)
    Route::get('clientes/{cliente}/obras', function ($clienteId) {
        return \App\Models\Obra::where('cliente_id', $clienteId)->get();
    });
});


/*
|--------------------------------------------------------------------------
| CLIENTES (admin|asistente)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {

    Route::resource('clientes', ClienteController::class);

    Route::get('clientes/{cliente}/obras/create', [ObraController::class, 'create'])
        ->name('obras.create');

    Route::post('clientes/{cliente}/obras', [ObraController::class, 'store'])
        ->name('obras.store');

    Route::get('/clientes/{cliente}/obras', [ClienteController::class, 'obras']);
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('gastos', GastoController::class)->only([
    'index', 'create', 'store'
    ]);
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::delete('clientes/{cliente}/obras/{obra}', [ObraController::class, 'destroy'])
        ->name('obras.destroy');
});


Route::middleware(['auth', 'role:admin|asistente'])->group(function () {

    Route::get('clientes/{cliente}/obras/{obra}/edit', [ObraController::class, 'edit'])
        ->name('obras.edit');

    Route::put('clientes/{cliente}/obras/{obra}', [ObraController::class, 'update'])
        ->name('obras.update');
});



/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
Route::controller(LoginController::class)->group(function () {

    Route::get('/', 'show')
        ->name('login');

    Route::post('/login', 'login')
        ->name('login.post');

    Route::post('/logout', 'logout')
        ->name('logout');
});
