<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CierreCajaController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tipo' => 'nullable|in:parcial,mensual',
            'fecha' => 'nullable|date',
            'mes' => 'nullable|date_format:Y-m',
        ]);

        $today = now();
        $fecha = Carbon::parse($filters['fecha'] ?? $today->toDateString());
        $mes = Carbon::createFromFormat('Y-m', $filters['mes'] ?? $today->format('Y-m'))->startOfMonth();

        $resumenDia = $this->buildResumen($fecha->copy()->startOfDay(), $fecha->copy()->endOfDay());
        $resumenMes = $this->buildResumen($mes->copy()->startOfMonth(), $mes->copy()->endOfMonth());

        return view('cierrecaja.cierrecaja', [
            'tipoSeleccionado' => $filters['tipo'] ?? '',
            'fecha' => $fecha,
            'mes' => $mes,
            'resumenDia' => $resumenDia,
            'resumenMes' => $resumenMes,
        ]);
    }

    private function buildResumen(Carbon $desde, Carbon $hasta): array
    {
        $paymentsQuery = Payment::query()
            ->where('status', 'confirmed')
            ->whereDate('business_date', '>=', $desde->toDateString())
            ->whereDate('business_date', '<=', $hasta->toDateString());

        $ingresos = (float) (clone $paymentsQuery)->sum('total_amount');

        $gastos = (float) Gasto::query()
            ->whereDate('fecha', '>=', $desde->toDateString())
            ->whereDate('fecha', '<=', $hasta->toDateString())
            ->sum('monto');

        $metodos = DB::table('payment_parts as pp')
            ->join('payments as p', 'p.id', '=', 'pp.payment_id')
            ->where('p.status', 'confirmed')
            ->whereDate('p.business_date', '>=', $desde->toDateString())
            ->whereDate('p.business_date', '<=', $hasta->toDateString())
            ->select('pp.method', DB::raw('SUM(pp.amount) as total'))
            ->groupBy('pp.method')
            ->pluck('total', 'method')
            ->map(fn ($total) => (float) $total)
            ->toArray();

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'utilidad' => $ingresos - $gastos,
            'pagos' => (clone $paymentsQuery)->count(),
            'metodos' => $metodos,
        ];
    }
}
