<?php

namespace App\Http\Controllers;

use App\Models\DailyClosure;
use App\Models\MonthlyClosure;
use App\Services\CierreCajaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CierreCajaController extends Controller
{
    public function __construct(private CierreCajaService $cierres)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'tipo' => 'nullable|in:parcial,mensual',
            'fecha' => 'nullable|date',
            'mes' => 'nullable|date_format:Y-m',
        ]);

        $today = now('America/Bogota');
        $fecha = Carbon::parse($filters['fecha'] ?? $today->toDateString());
        $mes = Carbon::createFromFormat('Y-m', $filters['mes'] ?? $today->format('Y-m'))->startOfMonth();

        $resumenDia = $this->cierres->buildResumen($fecha->copy()->startOfDay(), $fecha->copy()->endOfDay());
        $resumenMes = $this->cierres->buildResumen($mes->copy()->startOfMonth(), $mes->copy()->endOfMonth());
        $cierreDia = DailyClosure::whereDate('business_date', $fecha->toDateString())->first();
        $cierreMes = MonthlyClosure::whereDate('month_start', $mes->toDateString())->first();
        $historial = $this->cierres->buildHistorial();

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

            if ($fecha->greaterThan(now('America/Bogota')->startOfDay())) {
                throw ValidationException::withMessages([
                    'fecha' => 'No se puede cerrar una fecha futura.',
                ]);
            }

            $result = $this->cierres->closeDaily($fecha, auth()->id(), $data['observacion'] ?? null);

            if (!$result['created']) {
                return back()
                    ->withInput()
                    ->with('error', $result['message']);
            }

            return redirect()
                ->route('cierrecaja.cierrecaja', [
                    'tipo' => 'parcial',
                    'fecha' => $fecha->toDateString(),
                ])
                ->with('success', $result['message']);
        }

        $mes = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();

        if ($mes->greaterThan(now('America/Bogota')->startOfMonth())) {
            throw ValidationException::withMessages([
                'mes' => 'No se puede cerrar un mes futuro.',
            ]);
        }

        $result = $this->cierres->closeMonthly($mes, auth()->id(), $data['observacion'] ?? null);

        if (!$result['created']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('cierrecaja.cierrecaja', [
                'tipo' => 'mensual',
                'mes' => $mes->format('Y-m'),
            ])
            ->with('success', $result['message']);
    }
}
