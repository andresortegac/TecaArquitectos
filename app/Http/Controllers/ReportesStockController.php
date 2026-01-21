<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;
use App\Exports\ReporteMensualExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cliente;
use App\Models\Solicitud;
use App\Models\Producto;

class ReportesStockController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function movimientos(Request $request)
    {
        $query = Movimiento::with('producto')
            ->orderBy('created_at', 'desc');

        // ✅ FILTRO POR TIPO
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->get();

        return view('reportes.movimientos', compact('movimientos'));
    }

    public function entradasSalidas(Request $request)
    {
        $query = Movimiento::with('producto')
            ->orderBy('created_at', 'desc');

        // ✅ FILTRO POR CLASIFICACIÓN
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->get();

        return view('reportes.entradas_salidas', compact('movimientos'));
    }

     public function reportes()
    {
        return view('reportes.generalrep', [
            'totalProductos'   => Producto::count(),
            'totalSolicitudes' => Solicitud::count(),
            'totalMovimientos' => Movimiento::count(),
            'stockBajo'        => Producto::where('cantidad', '<=', 5)->count(),
            'sinStock'         => Producto::where('cantidad', 0)->count(),
            'pendientes' => Solicitud::where('estado','pendiente')->count(),
            'aprobadas' => Solicitud::where('estado','aprobado')->count(),
            'clientesDeuda' => Cliente::whereHas('arriendos', fn($q) =>
                $q->where('saldo','>',0)
            )->count(),
            'ultimasSolicitudes' => Solicitud::latest()->take(5)->get(),
        ]);
    }

    

    
}
