<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Solicitud;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalProductos' => Producto::count(),
            'totalMovimientos' => Movimiento::count(),
            'totalSolicitudes' => Solicitud::count(),
            'totalClientes' => Cliente::count(),
            'sinStock' => Producto::where('cantidad', 0)->count(),
            'stockBajo' => Producto::where('cantidad', '<=', 5)->count(),
        ]);
    }
}
