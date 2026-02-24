<?php

namespace App\Http\Controllers;

use App\Exports\StockExport;
use App\Models\Configuracion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $config = Configuracion::first();
        $stockMinimo = (int) ($config?->stock_minimo ?? 10);

        $filters = $request->validate([
            'q' => 'nullable|string|max:120',
            'estado' => 'nullable|in:normal,bajo,sin_stock',
        ]);

        $query = Producto::query()->orderBy('id');

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('categorias', 'like', '%' . $filters['q'] . '%');
            });
        }

        if (($filters['estado'] ?? '') === 'sin_stock') {
            $query->where('cantidad', '<=', 0);
        } elseif (($filters['estado'] ?? '') === 'bajo') {
            $query->where('cantidad', '>', 0)->where('cantidad', '<=', $stockMinimo);
        } elseif (($filters['estado'] ?? '') === 'normal') {
            $query->where('cantidad', '>', $stockMinimo);
        }

        $productos = $query->paginate(20)->withQueryString();

        $resumen = [
            'total' => Producto::count(),
            'sin_stock' => Producto::where('cantidad', '<=', 0)->count(),
            'bajo' => Producto::where('cantidad', '>', 0)->where('cantidad', '<=', $stockMinimo)->count(),
            'normal' => Producto::where('cantidad', '>', $stockMinimo)->count(),
        ];

        return view('stock.index', compact('productos', 'config', 'stockMinimo', 'resumen', 'filters'));
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
