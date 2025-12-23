<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\SolicitudController;

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
// ruta Movimiento
Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::get('/movimientos', [MovimientoController::class, 'create'])
        ->name('movimientos.create');

    Route::post('/movimientos', [MovimientoController::class, 'store'])
        ->name('movimientos.store');
});

// ARRIENDOS
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {
    Route::resource('arriendos', ArriendoController::class);

    Route::post('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'cerrar'])
        ->name('arriendos.cerrar');

    Route::get('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'showCerrar'])
        ->name('arriendos.cerrar.form');
});

// CLIENTES
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {
    Route::resource('clientes', ClienteController::class);

    // ✅ NUEVA RUTA: devolver obras de un cliente (para el select "Obra" en arriendos/create)
    Route::get('/clientes/{cliente}/obras', [ClienteController::class, 'obras'])
        ->name('clientes.obras');
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
