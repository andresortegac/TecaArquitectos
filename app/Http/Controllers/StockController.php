<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Exports\StockExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id', 'asc')->get();
        return view('stock.index', compact('productos'));
    }

    public function show(Producto $producto)
    {
        return view('stock.show', compact('producto'));
    }

    public function export()
    {
        return Excel::download(new StockExport, 'stock.xlsx');
    }
}
