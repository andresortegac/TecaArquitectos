<?php

namespace App\Services;

use App\Models\DailyClosure;
use App\Models\Gasto;
use App\Models\MonthlyClosure;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CierreCajaService
{
    public function buildResumen(Carbon $desde, Carbon $hasta): array
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
            'payment_ids' => (clone $paymentsQuery)->pluck('id')->all(),
        ];
    }

    public function closeDaily(Carbon $fecha, ?int $userId = null, ?string $observacion = null): array
    {
        $fecha = $fecha->copy()->startOfDay();
        $exists = DailyClosure::whereDate('business_date', $fecha->toDateString())->first();

        if ($exists) {
            return [
                'created' => false,
                'closure' => $exists,
                'message' => 'Ya existe un cierre parcial para esta fecha.',
            ];
        }

        $resumen = $this->buildResumen($fecha->copy()->startOfDay(), $fecha->copy()->endOfDay());

        try {
            $cierre = DB::transaction(function () use ($fecha, $observacion, $resumen, $userId) {
                $cierre = DailyClosure::create([
                    'business_date' => $fecha->toDateString(),
                    'closed_at' => now('America/Bogota'),
                    'total_amount' => (int) $resumen['ingresos'],
                    'total_gastos' => (int) $resumen['gastos'],
                    'utilidad' => (int) $resumen['utilidad'],
                    'method_breakdown' => $resumen['metodos'],
                    'closed_by' => $userId,
                    'observacion' => $observacion,
                ]);

                if (!empty($resumen['payment_ids'])) {
                    $cierre->payments()->sync($resumen['payment_ids']);
                }

                return $cierre;
            });
        } catch (QueryException) {
            return [
                'created' => false,
                'closure' => null,
                'message' => 'No se pudo guardar porque este cierre ya existe o los pagos ya fueron asociados.',
            ];
        }

        return [
            'created' => true,
            'closure' => $cierre,
            'message' => 'Cierre parcial guardado correctamente.',
        ];
    }

    public function closeMonthly(Carbon $mes, ?int $userId = null, ?string $observacion = null): array
    {
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();
        $exists = MonthlyClosure::whereDate('month_start', $inicio->toDateString())->first();

        if ($exists) {
            return [
                'created' => false,
                'closure' => $exists,
                'message' => 'Ya existe un cierre mensual para este periodo.',
            ];
        }

        $resumen = $this->buildResumen($inicio, $fin);

        try {
            $cierre = DB::transaction(function () use ($inicio, $fin, $observacion, $resumen, $userId) {
                $cierre = MonthlyClosure::create([
                    'month_start' => $inicio->toDateString(),
                    'month_end' => $fin->toDateString(),
                    'closed_at' => now('America/Bogota'),
                    'total_amount' => (int) $resumen['ingresos'],
                    'total_gastos' => (int) $resumen['gastos'],
                    'utilidad' => (int) $resumen['utilidad'],
                    'method_breakdown' => $resumen['metodos'],
                    'closed_by' => $userId,
                    'observacion' => $observacion,
                ]);

                if (!empty($resumen['payment_ids'])) {
                    $cierre->payments()->sync($resumen['payment_ids']);
                }

                return $cierre;
            });
        } catch (QueryException) {
            return [
                'created' => false,
                'closure' => null,
                'message' => 'No se pudo guardar porque este cierre ya existe o los pagos ya fueron asociados.',
            ];
        }

        return [
            'created' => true,
            'closure' => $cierre,
            'message' => 'Cierre mensual guardado correctamente.',
        ];
    }

    public function buildHistorial(): array
    {
        $diarios = DailyClosure::query()
            ->with('user:id,name')
            ->latest('closed_at')
            ->limit(5)
            ->get()
            ->map(fn ($cierre) => (object) [
                'tipo' => 'Parcial',
                'periodo' => $cierre->business_date?->format('d/m/Y'),
                'ingresos' => $cierre->total_amount,
                'gastos' => $cierre->total_gastos,
                'utilidad' => $cierre->utilidad,
                'usuario' => $cierre->user?->name ?? 'Sistema',
                'cerrado' => $cierre->closed_at,
            ]);

        $mensuales = MonthlyClosure::query()
            ->with('user:id,name')
            ->latest('closed_at')
            ->limit(5)
            ->get()
            ->map(fn ($cierre) => (object) [
                'tipo' => 'Mensual',
                'periodo' => $cierre->month_start?->format('m/Y'),
                'ingresos' => $cierre->total_amount,
                'gastos' => $cierre->total_gastos,
                'utilidad' => $cierre->utilidad,
                'usuario' => $cierre->user?->name ?? 'Sistema',
                'cerrado' => $cierre->closed_at,
            ]);

        return $diarios
            ->merge($mensuales)
            ->sortByDesc(fn ($cierre) => $cierre->cerrado?->timestamp ?? 0)
            ->take(8)
            ->values()
            ->all();
    }
}
