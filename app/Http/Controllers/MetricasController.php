<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Payment;
use App\Models\PaymentPart;
use App\Models\Venta;
use Illuminate\Support\Carbon;

class MetricasController extends Controller
{
    public function index()
    {
        $year = (int)request('year', now()->year);

        $totalVentas = (float)Venta::sum('total');
        $totalArriendos = (float)Arriendo::sum('total_pagado');
        $totalRecaudoConfirmado = (float)Payment::where('status', 'confirmed')->sum('total_amount');
        $pagosConfirmados = (int)Payment::where('status', 'confirmed')->count();

        $avgVenta = (float)Venta::avg('total');
        $avgArriendo = (float)Arriendo::avg('total_pagado');

        // Series mensuales del aÃ±o seleccionado (1..12, siempre completas)
        $ventasRaw = Venta::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) mes, SUM(total) total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $recaudoRaw = Payment::whereYear('occurred_at', $year)
            ->where('status', 'confirmed')
            ->selectRaw('MONTH(occurred_at) mes, SUM(total_amount) total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $arriendosCountRaw = Arriendo::whereYear('fecha_inicio', $year)
            ->selectRaw('MONTH(fecha_inicio) mes, COUNT(*) total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $months = range(1, 12);
        $monthLabels = collect($months)->map(function ($m) use ($year) {
            return ucfirst(Carbon::createFromDate($year, $m, 1)->locale('es')->translatedFormat('M'));
        })->values()->all();

        $ventasSeries = collect($months)->map(fn($m) => (float)($ventasRaw[$m] ?? 0))->values()->all();
        $recaudoSeries = collect($months)->map(fn($m) => (float)($recaudoRaw[$m] ?? 0))->values()->all();
        $arriendosSeries = collect($months)->map(fn($m) => (int)($arriendosCountRaw[$m] ?? 0))->values()->all();

        return view('metricas.index', compact(
            'year',
            'totalVentas',
            'totalArriendos',
            'totalRecaudoConfirmado',
            'pagosConfirmados',
            'avgVenta',
            'avgArriendo',
            'monthLabels',
            'ventasSeries',
            'recaudoSeries',
            'arriendosSeries'
        ));
    }

    public function reporteAnual($year)
    {
        $year = (int)$year;

        $pagosPorMes = Payment::whereYear('occurred_at', $year)
            ->where('status', 'confirmed')
            ->selectRaw('MONTH(occurred_at) mes, SUM(total_amount) total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $arriendosPorMes = Arriendo::whereYear('fecha_inicio', $year)
            ->selectRaw('MONTH(fecha_inicio) mes, COUNT(*) cantidad')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

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

    public function reporteMensual($year, $month)
    {
        $year = (int)$year;
        $month = (int)$month;

        $pagosPorDia = Payment::whereYear('occurred_at', $year)
            ->whereMonth('occurred_at', $month)
            ->where('status', 'confirmed')
            ->selectRaw('DATE(occurred_at) dia, SUM(total_amount) total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $arriendosPorDia = Arriendo::whereYear('fecha_inicio', $year)
            ->whereMonth('fecha_inicio', $month)
            ->selectRaw('DATE(fecha_inicio) dia, COUNT(*) cantidad')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

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

    public function reporteSemanal($year, $week)
    {
        $year = (int)$year;
        $week = (int)$week;

        // Semana ISO (lunes a domingo)
        $inicioSemana = Carbon::now()->setISODate($year, $week)->startOfWeek(Carbon::MONDAY);
        $finSemana = (clone $inicioSemana)->endOfWeek(Carbon::SUNDAY);

        $pagosPorDia = Payment::whereBetween('occurred_at', [$inicioSemana, $finSemana])
            ->where('status', 'confirmed')
            ->selectRaw('DATE(occurred_at) dia, SUM(total_amount) total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $arriendosPorDia = Arriendo::whereBetween('fecha_inicio', [$inicioSemana, $finSemana])
            ->selectRaw('DATE(fecha_inicio) dia, COUNT(*) cantidad')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $dias = [];
        $cursor = $inicioSemana->copy();
        while ($cursor->lte($finSemana)) {
            $key = $cursor->toDateString();
            $dias[] = [
                'dia' => $key,
                'recaudo' => (float)($pagosPorDia[$key]->total ?? 0),
                'arriendos' => (int)($arriendosPorDia[$key]->cantidad ?? 0),
            ];
            $cursor->addDay();
        }

        $totalSemanal = array_sum(array_column($dias, 'recaudo'));
        $month = (int)$inicioSemana->month;
        $rangoLabel = $inicioSemana->locale('es')->translatedFormat('d M Y') . ' - ' . $finSemana->locale('es')->translatedFormat('d M Y');

        return view('metricas.reporte_semanal', compact(
            'year',
            'week',
            'month',
            'dias',
            'totalSemanal',
            'rangoLabel'
        ));
    }

    public function detalleDia($date)
    {
        try {
            $parsed = Carbon::createFromFormat('Y-m-d', (string)$date)->startOfDay();
        } catch (\Throwable $e) {
            $parsed = now()->startOfDay();
        }

        $dia = $parsed->toDateString();

        $rawPayments = Payment::whereDate('occurred_at', $dia)
            ->where('status', 'confirmed')
            ->orderBy('occurred_at')
            ->get();

        $paymentIds = $rawPayments->pluck('id')->all();

        $partsByPayment = collect();
        if (!empty($paymentIds)) {
            $partsByPayment = PaymentPart::whereIn('payment_id', $paymentIds)
                ->orderBy('payment_id')
                ->get()
                ->groupBy('payment_id');
        }

        $hourBuckets = [];
        for ($h = 0; $h < 24; $h++) {
            $hourBuckets[$h] = ['total' => 0.0, 'count' => 0];
        }

        foreach ($rawPayments as $p) {
            $hour = (int)optional($p->occurred_at)->format('G');
            $hourBuckets[$hour]['total'] += (float)($p->total_amount ?? 0);
            $hourBuckets[$hour]['count'] += 1;
        }

        $porHora = collect(range(0, 23))->map(function ($h) use ($hourBuckets) {
            return [
                'hour_label' => str_pad((string)$h, 2, '0', STR_PAD_LEFT) . ':00',
                'total' => (float)($hourBuckets[$h]['total'] ?? 0),
                'count' => (int)($hourBuckets[$h]['count'] ?? 0),
            ];
        })->all();

        $payments = $rawPayments->map(function ($p) use ($partsByPayment) {
            $partes = ($partsByPayment[$p->id] ?? collect())->map(function ($part) {
                $method = (string)($part->method ?? '-');
                $amount = (float)($part->amount ?? 0);
                return $method . ': $' . number_format($amount, 0, ',', '.');
            })->values();

            return [
                'id' => $p->id,
                'time' => optional($p->occurred_at)->format('H:i') ?? '-',
                'amount' => (float)($p->total_amount ?? 0),
                'note' => $p->note ?: '-',
                'source_type' => $p->source_type ?: '-',
                'source_id' => $p->source_id ?: '',
                'metodos' => $partes->isNotEmpty() ? $partes->implode(' | ') : '-',
            ];
        })->values()->all();

        $arriendosRaw = Arriendo::with('cliente')
            ->whereDate('fecha_inicio', $dia)
            ->orderBy('fecha_inicio')
            ->get();

        $arriendos = $arriendosRaw->map(function ($a) {
            return [
                'id' => $a->id,
                'cliente' => $a->cliente->nombre ?? '-',
                'inicio' => optional($a->fecha_inicio)->format('d/m/Y H:i') ?? '-',
                'estado' => (string)($a->estado ?? ''),
                'total' => (float)($a->precio_total ?? 0),
                'pagado' => (float)($a->total_pagado ?? 0),
                'saldo' => (float)($a->saldo ?? 0),
            ];
        })->values()->all();

        $totalDia = (float)$rawPayments->sum('total_amount');
        $countPayments = count($payments);
        $arriendosCreados = count($arriendos);
        $arriendosDevueltos = $arriendosRaw->filter(function ($a) {
            return (int)($a->cerrado ?? 0) === 1 || strtolower((string)$a->estado) === 'devuelto';
        })->count();

        $dateLabel = $parsed->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y');
        $year = (int)$parsed->year;
        $month = (int)$parsed->month;

        return view('metricas.detalle_dia', compact(
            'dia',
            'dateLabel',
            'year',
            'month',
            'porHora',
            'payments',
            'arriendos',
            'totalDia',
            'countPayments',
            'arriendosCreados',
            'arriendosDevueltos'
        ));
    }
}
