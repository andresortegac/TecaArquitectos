<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;


Route::view('/dashboard', 'dashboard')->name('dashboard');

// INVENTARIO / BODEGA
Route::resource('productos', ProductoController::class);

Route::post('/productos/import', [ProductoController::class, 'import'])
    ->name('productos.import');

// ARRIENDOS
Route::resource('arriendos', ArriendoController::class);

// CLIENTES
Route::resource('clientes', ClienteController::class);

 Route::resource('solicitudes', SolicitudController::class);

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

/* NUEVO: cerrar/devolver arriendo (calcula domingos + lluvia + merma + saldo + semáforo) */
Route::post('/arriendos/{arriendo}/cerrar', [ArriendoController::class, 'cerrar'])
    ->name('arriendos.cerrar');
    