<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Solicitud;
use App\Models\Movimiento;
use App\Models\Cliente;

class DashboardController extends Controller
{
    public function index()
    {
            return view('dashboard', [
        'totalProductos'   => Producto::count(),
        'totalMovimientos' => Movimiento::count(),
        'sinStock'         => Producto::where('cantidad', 0)->count(),
        'stockBajo'        => Producto::where('cantidad', '<=', 5)->count(),
    ]);

    }
       


}
