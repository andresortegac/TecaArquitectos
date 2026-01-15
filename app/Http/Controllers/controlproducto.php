<?php

namespace App\Http\Controllers;


use App\Models\Producto;
use App\Models\ArriendoItem;

class controlproducto extends Controller
{
    
    public function controlproducto()
    {
        // Productos alquilados (join con productos)
        $alquilados = ArriendoItem::with('producto')
            ->where('cerrado', 0)
            ->get();

        // Productos en bodega (disponibles)
        $bodega = Producto::where('estado', 'disponible')->get();

        return view('reportes.controlproducto', compact('alquilados', 'bodega'));
    }

}
