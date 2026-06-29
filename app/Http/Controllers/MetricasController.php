<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Arriendo;
use App\Models\Payment;
use App\Models\PaymentPart;
use Illuminate\Http\Request;

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

    /*
    |--------------------------------------------------------------------------
    | ✅ NUEVO: Reporte anual (mes por mes)
    |--------------------------------------------------------------------------
    */
    public function reporteAnual($year)
    {
        $year = (int)$year;

        // Pagos confirmados por mes (payments)
        $pagosPorMes = Payment::whereYear('occurred_at', $year)
            ->where('status', 'confirmed')
            ->selectRaw('MONTH(occurred_at) mes, SUM(total_amount) total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        // Arriendos creados por mes (cantidad de contratos)
        $arriendosPorMes = Arriendo::whereYear('fecha_inicio', $year)
            ->selectRaw('MONTH(fecha_inicio) mes, COUNT(*) cantidad')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        // Meses 1..12
        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $meses[] = [
                'mes' => $m,
                'recaudo' => (int)($pagosPorMes[$m]->total ?? 0),
                'arriendos' => (int)($arriendosPorMes[$m]->cantidad ?? 0),
            ];
        }

        $totalAnual = array_sum(array_column($meses, 'recaudo'));

        return view('metricas.reporte_anual', compact('year', 'meses', 'totalAnual'));
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ NUEVO: Reporte mensual (día por día)
    |--------------------------------------------------------------------------
    */
    public function reporteMensual($year, $month)
    {
        $year = (int)$year;
        $month = (int)$month;

        // Pagos confirmados por día
        $pagosPorDia = Payment::whereYear('occurred_at', $year)
            ->whereMonth('occurred_at', $month)
            ->where('status', 'confirmed')
            ->selectRaw('DATE(occurred_at) dia, SUM(total_amount) total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Arriendos iniciados por día (cantidad)
        $arriendosPorDia = Arriendo::whereYear('fecha_inicio', $year)
            ->whereMonth('fecha_inicio', $month)
            ->selectRaw('DATE(fecha_inicio) dia, COUNT(*) cantidad')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Construir días del mes
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dias = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);

            $dias[] = [
                'dia' => $date,
                'recaudo' => (int)($pagosPorDia[$date]->total ?? 0),
                'arriendos' => (int)($arriendosPorDia[$date]->cantidad ?? 0),
            ];
        }

        $totalMensual = array_sum(array_column($dias, 'recaudo'));

        return view('metricas.reporte_mensual', compact('year', 'month', 'dias', 'totalMensual'));
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ NUEVO: Detalle de un día (por hora + métodos)
    |--------------------------------------------------------------------------
    */
    public function detalleDia($date)
    {
        // Espera date en formato YYYY-MM-DD
        $dia = $date;

        // Pagos confirmados del día, con hora exacta
        $payments = Payment::whereDate('occurred_at', $dia)
            ->where('status', 'confirmed')
            ->orderBy('occurred_at')
            ->get();

        $paymentIds = $payments->pluck('id')->all();

        // Partes (métodos) por pago
        $parts = collect();
        if (!empty($paymentIds)) {
            $parts = PaymentPart::whereIn('payment_id', $paymentIds)
                ->orderBy('payment_id')
                ->get()
                ->groupBy('payment_id');
        }

        // Totales por hora (00-23)
        $porHora = [];
        for ($h = 0; $h < 24; $h++) {
            $porHora[$h] = 0;
        }

        foreach ($payments as $p) {
            $h = (int)date('G', strtotime($p->occurred_at));
            $porHora[$h] += (int)($p->total_amount ?? 0);
        }

        $totalDia = (int)$payments->sum('total_amount');

        // Arriendos iniciados ese día (para estadística)
        $arriendosDelDia = Arriendo::whereDate('fecha_inicio', $dia)
            ->orderBy('fecha_inicio')
            ->get();

        return view('metricas.detalle_dia', compact(
            'dia',
            'payments',
            'parts',
            'porHora',
            'totalDia',
            'arriendosDelDia'
        ));
    }
}
