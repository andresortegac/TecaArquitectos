<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Producto;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::latest()->get();
        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $productos = Producto::where('cantidad', '>', 0)->get();
        return view('solicitudes.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'required',
            'obra_nombre' => 'required',
            'obra_direccion' => 'required',
            'productos' => 'required|array'
        ]);

        $solicitud = Solicitud::create([
            'cliente_nombre' => $request->cliente_nombre,
            'obra_nombre' => $request->obra_nombre,
            'obra_direccion' => $request->obra_direccion,
            'usa_transporte' => $request->usa_transporte ?? false,
            'estado' => 'en_revision'
        ]);

        foreach ($request->productos as $productoId => $cantidad) {
            if ($cantidad > 0) {
                $solicitud->productos()->create([
                    'producto_id' => $productoId,
                    'cantidad_solicitada' => $cantidad
                ]);
            }
        }

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud enviada a bodega');
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load('productos.producto');
        return view('solicitudes.show', compact('solicitud'));
    }
}
