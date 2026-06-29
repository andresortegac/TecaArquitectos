<?php

namespace App\Http\Controllers;

use App\Models\DailyClosure;
use App\Models\Gasto;
use App\Models\MonthlyClosure;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $cierreDia = DailyClosure::whereDate('business_date', $fecha->toDateString())->first();
        $cierreMes = MonthlyClosure::whereDate('month_start', $mes->toDateString())->first();
        $historial = $this->buildHistorial();

        return view('cierrecaja.cierrecaja', [
            'tipoSeleccionado' => $filters['tipo'] ?? '',
            'fecha' => $fecha,
            'mes' => $mes,
            'resumenDia' => $resumenDia,
            'resumenMes' => $resumenMes,
            'cierreDia' => $cierreDia,
            'cierreMes' => $cierreMes,
            'historial' => $historial,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|in:parcial,mensual',
            'fecha' => 'exclude_unless:tipo,parcial|required|date|before_or_equal:today',
            'mes' => 'exclude_unless:tipo,mensual|required|date_format:Y-m',
            'observacion' => 'nullable|string|max:1000',
        ]);

        if ($data['tipo'] === 'parcial') {
            $fecha = Carbon::parse($data['fecha'])->startOfDay();

            return $this->storeDailyClosure($fecha, $data['observacion'] ?? null);
        }

        $mes = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();

        if ($mes->greaterThan(now()->startOfMonth())) {
            throw ValidationException::withMessages([
                'mes' => 'No se puede cerrar un mes futuro.',
            ]);
        }

        return $this->storeMonthlyClosure($mes, $data['observacion'] ?? null);
    }

    private function storeDailyClosure(Carbon $fecha, ?string $observacion)
    {
        $exists = DailyClosure::whereDate('business_date', $fecha->toDateString())->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe un cierre parcial para esta fecha.');
        }

        $resumen = $this->buildResumen($fecha->copy()->startOfDay(), $fecha->copy()->endOfDay());

        try {
            DB::transaction(function () use ($fecha, $observacion, $resumen) {
                $cierre = DailyClosure::create([
                    'business_date' => $fecha->toDateString(),
                    'closed_at' => now(),
                    'total_amount' => (int) $resumen['ingresos'],
                    'total_gastos' => (int) $resumen['gastos'],
                    'utilidad' => (int) $resumen['utilidad'],
                    'method_breakdown' => $resumen['metodos'],
                    'closed_by' => auth()->id(),
                    'observacion' => $observacion,
                ]);

                if (!empty($resumen['payment_ids'])) {
                    $cierre->payments()->sync($resumen['payment_ids']);
                }
            });
        } catch (QueryException) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo guardar porque este cierre ya existe o los pagos ya fueron asociados.');
        }

        return redirect()
            ->route('cierrecaja.cierrecaja', [
                'tipo' => 'parcial',
                'fecha' => $fecha->toDateString(),
            ])
            ->with('success', 'Cierre parcial guardado correctamente.');
    }

    private function storeMonthlyClosure(Carbon $mes, ?string $observacion)
    {
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();
        $exists = MonthlyClosure::whereDate('month_start', $inicio->toDateString())->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe un cierre mensual para este periodo.');
        }

        $resumen = $this->buildResumen($inicio, $fin);

        try {
            DB::transaction(function () use ($inicio, $fin, $observacion, $resumen) {
                $cierre = MonthlyClosure::create([
                    'month_start' => $inicio->toDateString(),
                    'month_end' => $fin->toDateString(),
                    'closed_at' => now(),
                    'total_amount' => (int) $resumen['ingresos'],
                    'total_gastos' => (int) $resumen['gastos'],
                    'utilidad' => (int) $resumen['utilidad'],
                    'method_breakdown' => $resumen['metodos'],
                    'closed_by' => auth()->id(),
                    'observacion' => $observacion,
                ]);

                if (!empty($resumen['payment_ids'])) {
                    $cierre->payments()->sync($resumen['payment_ids']);
                }
            });
        } catch (QueryException) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo guardar porque este cierre ya existe o los pagos ya fueron asociados.');
        }

        return redirect()
            ->route('cierrecaja.cierrecaja', [
                'tipo' => 'mensual',
                'mes' => $inicio->format('Y-m'),
            ])
            ->with('success', 'Cierre mensual guardado correctamente.');
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
            'payment_ids' => (clone $paymentsQuery)->pluck('id')->all(),
        ];
    }

    private function buildHistorial(): array
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
