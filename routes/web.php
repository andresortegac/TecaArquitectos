<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;

// DASHBOARD
Route::middleware(['auth', 'role:admin|asistente|bodega'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});

// INVENTARIO / BODEGA
Route::middleware(['auth', 'role:admin|asistente'])->group(function () {
    Route::resource('productos', ProductoController::class);

    Route::post('/productos/import', [ProductoController::class, 'import'])
        ->name('productos.import');
});

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

// SOLICITUDES
Route::middleware(['auth', 'role:admin|bodega'])->group(function () {
    Route::resource('solicitudes', SolicitudController::class);
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