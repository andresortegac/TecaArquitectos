<?php

namespace App\Http\Controllers;

use App\Models\ArriendoItem;
use App\Models\Producto;
use Illuminate\Http\Request;

class controlproducto extends Controller
{
    public function controlproducto(Request $request)
    {
        $filters = $request->validate([
            'alquilados' => 'nullable|string|max:120',
            'bodega' => 'nullable|string|max:120',
        ]);

        $alquiladosQuery = ArriendoItem::query()
            ->with('producto')
            ->where('cerrado', 0)
            ->orderByDesc('fecha_inicio_item')
            ->orderByDesc('id');

        if (!empty($filters['alquilados'])) {
            $alquiladosQuery->whereHas('producto', function ($productoQuery) use ($filters) {
                $productoQuery->where('nombre', 'like', '%' . $filters['alquilados'] . '%');
            });
        }

        $bodegaQuery = Producto::query()
            ->where('estado', 'disponible')
            ->orderBy('nombre');

        if (!empty($filters['bodega'])) {
            $bodegaQuery->where('nombre', 'like', '%' . $filters['bodega'] . '%');
        }

        $alquiladosItems = $alquiladosQuery->get();
        $alquilados = $alquiladosItems
            ->groupBy('producto_id')
            ->map(function ($items) {
                $producto = $items->first()?->producto;
                $cantidadAlquilada = (int) $items->sum('cantidad_actual');
                $cantidadStock = (int) ($producto->cantidad ?? 0);
                $fechaAlquiler = $items
                    ->sortBy('fecha_inicio_item')
                    ->first()?->fecha_inicio_item;

                return (object) [
                    'nombre' => $producto->nombre ?? 'Producto no disponible',
                    'imagen' => $producto->imagen ?? null,
                    'cantidad_total' => $cantidadStock + $cantidadAlquilada,
                    'cantidad_stock' => $cantidadStock,
                    'cantidad_alquilada' => $cantidadAlquilada,
                    'fecha_alquiler' => $fechaAlquiler,
                ];
            })
            ->values();

        $bodega = $bodegaQuery->get();

        $resumen = [
            'productos_alquilados' => $alquilados->count(),
            'unidades_fuera' => $alquilados->sum(fn ($item) => (int) $item->cantidad_alquilada),
            'unidades_bodega' => $bodega->sum(fn ($producto) => (int) $producto->cantidad),
        ];
        $resumen['total_unidades'] = $resumen['unidades_fuera'] + $resumen['unidades_bodega'];

        return view('reportes.controlproducto', [
            'alquilados' => $alquilados,
            'bodega' => $bodega,
            'resumen' => $resumen,
            'filters' => $filters,
        ]);
    }
}
