<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ReportesStockController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\StockController;

use Illuminate\Support\Facades\Route;


// DASHBOARD
Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
});

// INVENTARIO / BODEGA
Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::resource('productos', ProductoController::class);

    Route::post('/productos/import', [ProductoController::class, 'import'])
        ->name('productos.import');
});

// ruta para solicitud

Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::get('/solicitudes/create', [SolicitudController::class, 'create'])
        ->name('solicitudes.create');

    Route::post('/solicitudes', [SolicitudController::class, 'store'])
        ->name('solicitudes.store');
    
    Route::resource('solicitudes', SolicitudController::class);
});
//---------------------------------------------------
    //ruta Movimiento

Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::get('/movimientos', [MovimientoController::class, 'create'])
        ->name('movimientos.create');

    Route::post('/movimientos', [MovimientoController::class, 'store'])
        ->name('movimientos.store');



});
//ruta para alerta de stock
Route::get('/alertas-stock', [ProductoController::class, 'alertasStock'])
    ->name('productos.alertas');

//rura de reporte de movimiento
Route::prefix('reportes')->group(function () {
    Route::get('/', [ReportesStockController::class, 'index'])->name('reportes.index');
    Route::get('/movimientos', [ReportesStockController::class, 'movimientos'])->name('reportes.movimientos');
    Route::get('/mensual', [ReportesStockController::class, 'mensual'])->name('reportes.mensual');
});
//ruta para configuracion
Route::get('/configuracion', [ConfiguracionController::class, 'index'])
    ->name('configuracion.index');
// ruta para stock actual
Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
Route::get('/stock/{producto}', [StockController::class, 'show'])->name('stock.show');
Route::get('/stock-exportar', [StockController::class, 'export'])->name('stock.export');


// ARRIENDOS
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {
    Route::resource('arriendos', ArriendoController::class);

    Route::post('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'cerrar'])
    ->name('arriendos.cerrar');
});

// CLIENTES
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {
    Route::resource('clientes', ClienteController::class);
});

// LOGIN
Route::controller(LoginController::class)->group(function () {

    // ?? Login en la raiz
    Route::get('/', 'show')
        ->name('login');

    // ?? Procesar login
    Route::post('/login', 'login')
        ->name('login.post');

    // ?? Logout
    Route::post('/logout', 'logout')
        ->name('logout');
});