<?php
namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

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
        // luego lo conectamos con Excel
        return redirect()->back()->with('success', 'Exportación pendiente');
    }
}
