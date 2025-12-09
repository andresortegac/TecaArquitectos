<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ArriendoController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::view('/dashboard', 'dashboard')->name('dashboard');

// INVENTARIO / BODEGA
Route::resource('productos', ProductoController::class);

// ARRIENDOS
Route::resource('arriendos', ArriendoController::class);

// CLIENTES
Route::resource('clientes', ClienteController::class);

