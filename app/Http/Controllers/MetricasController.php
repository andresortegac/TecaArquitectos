<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Arriendo;

class MetricasController extends Controller
{
    public function index()
    {
        $totalVentas = Venta::sum('total');
        $totalArriendos = Arriendo::sum('total_pagado');

        $avgVenta = Venta::avg('total');
        $avgArriendo = Arriendo::avg('total_pagado');

        $ventasMensuales = Venta::selectRaw('YEAR(created_at) anio, MONTH(created_at) mes, SUM(total) total')
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $arriendosMensuales = Arriendo::selectRaw('YEAR(fecha_inicio) anio, MONTH(fecha_inicio) mes, SUM(total_pagado) total')
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        return view('metricas.index', compact(
            'totalVentas',
            'totalArriendos',
            'avgVenta',
            'avgArriendo',
            'ventasMensuales',
            'arriendosMensuales'
        ));
    }
}
