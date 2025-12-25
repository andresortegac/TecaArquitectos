<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Producto;
use App\Models\Arriendo;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('solicitudes.index', compact('solicitudes'));
    }



    public function solicitudes()
    {
        $solicitudes = Arriendo::with('cliente')
            ->whereNull('producto_id')   // 👈 clave
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
        'nombre_cliente'   => 'required',
        'telefono_cliente' => 'required',
        'fecha_solicitud'  => 'required|date',
    ]);

    $solicitud = Solicitud::create([
        'nombre_cliente'   => $request->nombre_cliente,
        'telefono_cliente' => $request->telefono_cliente,
        'fecha_solicitud'  => $request->fecha_solicitud,
        'estado'           => 'en_revision',
    ]);

    foreach ($request->productos as $productoId => $cantidad) {
        if ($cantidad > 0) {
            $solicitud->productos()->attach($productoId, [
                'cantidad_solicitada' => $cantidad
            ]);
        }
    }

    return redirect()
        ->route('solicitudes.index')
        ->with('success', 'Solicitud enviada correctamente');
}


    public function show(Solicitud $solicitud)
    {
        $solicitud->load('productos.producto');
        return view('solicitudes.show', compact('solicitud'));
    }
}
