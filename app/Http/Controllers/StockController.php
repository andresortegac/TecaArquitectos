<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Configuracion;
use App\Exports\StockExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller 
{
    public function index()
    {
        $productos = Producto::orderBy('id', 'asc')->get();

        // 🔑 Traer configuración global
        $config = Configuracion::first();

        return view('stock.index', compact('productos', 'config'));
    }

    public function show(Producto $producto)
    {
        $config = Configuracion::first();

        return view('stock.show', compact('producto', 'config'));
    }

    public function export()
    {
        return Excel::download(new StockExport, 'stock.xlsx');
    }
}
