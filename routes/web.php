<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

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