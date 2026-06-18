<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use Illuminate\Http\Request;

class ArriendoDevolucionController extends Controller
{
    public function create(Arriendo $arriendo)
    {
        $arriendo->load(['items' => function ($query) {
            $query->where('estado', 'activo')
                ->where('cerrado', 0)
                ->where('cantidad_actual', '>', 0)
                ->orderBy('id');
        }]);

        $item = $arriendo->items->first();

        if ($item) {
            return redirect()->route('items.devolucion.create', $item);
        }

        return redirect()->route('arriendos.ver', $arriendo)
            ->with('success', 'No hay productos activos pendientes de devolución en este arriendo.');
    }

    public function store(Request $request, Arriendo $arriendo)
    {
        return redirect()->route('arriendos.devolucion.create', $arriendo)
            ->withErrors(['cantidad_devuelta' => 'La devolución debe registrarse desde un producto del arriendo.']);
    }
}
