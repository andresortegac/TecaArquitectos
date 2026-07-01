<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Imports\ProductosImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductoadminController extends Controller
{
      public function inventario()
    {
        $productos = Producto::all();

        $categorias = Producto::select('categorias')
            ->distinct()
            ->whereNotNull('categorias')
            ->pluck('categorias');

        return view('restrincion.inventario', compact('productos', 'categorias'));
    }
        
    
     
}
