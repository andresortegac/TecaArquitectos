<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Producto;
use App\Models\Arriendo;
use App\Models\ArriendoItem;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::orderBy('created_at', 'desc')
            ->paginate(10);

        $aprobadasPorArriendo = Solicitud::query()
            ->whereIn('solicitud_id', $solicitudes->pluck('id'))
            ->where('estado', 'aprobado')
            ->selectRaw('solicitud_id, COUNT(*) as total')
            ->groupBy('solicitud_id')
            ->pluck('total', 'solicitud_id');

        return view('solicitudes.index', compact('solicitudes', 'aprobadasPorArriendo'));
    }



    public function solicitudes()
    {
        $solicitudes = Arriendo::with('cliente')
            ->whereNull('producto_id')   // ðŸ‘ˆ clave
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $aprobadasPorArriendo = Solicitud::query()
            ->whereIn('solicitud_id', $solicitudes->pluck('id'))
            ->where('estado', 'aprobado')
            ->selectRaw('solicitud_id, COUNT(*) as total')
            ->groupBy('solicitud_id')
            ->pluck('total', 'solicitud_id');

        return view('solicitudes.index', compact('solicitudes', 'aprobadasPorArriendo'));
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


    public function show(Arriendo $arriendo)
    {
        $arriendo->load([
            'cliente',
            'obra',
            'items.producto'
        ]);

        $aprobados = Solicitud::query()
            ->where('solicitud_id', $arriendo->id)
            ->where('estado', 'aprobado')
            ->pluck('producto_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('solicitudes.show', compact('arriendo', 'aprobados'));
    }

    public function confirmar(Request $request, Arriendo $arriendo)
    {
        // ya viene por Route Model Binding
        $arriendo->load(['items', 'cliente']);

        foreach ($request->items ?? [] as $itemId => $data) {

            // Solo si fue aprobado
            if (!isset($data['aprobado'])) {
                continue;
            }

            $item = ArriendoItem::findOrFail($itemId);

            Solicitud::updateOrCreate(
                [
                    'solicitud_id' => $arriendo->id, // FK a arriendos
                    'producto_id'  => $item->producto_id,
                ],
                [
                    'cliente_id'          => $arriendo->cliente_id,
                    'obra_id'             => $arriendo->obra_id,
                    'cantidad_solicitada' => $data['cantidad_solicitada'],
                    'cantidad_aprobada'   => $data['cantidad_solicitada'],
                    'estado'              => 'aprobado',
                    'fecha_aprobado'      => now(),
                ]
            );
        }

        return redirect()
            ->route('solicitudes.solicitudes')
            ->with('success', 'Aprobado con exito');
    }

    public function indexDetallado()
    {
        $solicitudes = \App\Models\Solicitud::with([
                'cliente',
                'obra',
                'producto',
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('cliente_id');

        return view('solicitudes.detallado', compact('solicitudes'));
    }


}

