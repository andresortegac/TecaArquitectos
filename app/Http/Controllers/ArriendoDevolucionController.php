<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Incidencia;
use App\Models\DevolucionArriendo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PaymentService;

class ArriendoDevolucionController extends Controller
{
    protected PaymentService $payments;

    public function __construct(PaymentService $payments)
    {
        $this->payments = $payments;
    }

    public function create(Arriendo $arriendo)
    {
        // ✅ Cargamos todo para mostrar arriba y el historial abajo
        // OJO: si tu Arriendo no tiene "devoluciones" como relación, aquí explotará.
        // Como tú ya la estás usando, la dejo igual.
        $arriendo->load(['cliente', 'producto', 'devoluciones']);

        // ✅ Solo arriendos activos y no cerrados
        if ((int)($arriendo->cerrado ?? 0) === 1 || $arriendo->estado !== 'activo') {
            return redirect()->route('arriendos.index')
                ->with('success', 'Este arriendo ya está cerrado o no está activo.');
        }

        // ✅ Resumen del historial
        $resumen = [
            'total_devoluciones' => $arriendo->devoluciones->count(),
            'total_devuelto'     => (int)$arriendo->devoluciones->sum('cantidad_devuelta'),
            'total_abonado'      => (float)$arriendo->devoluciones->sum('pago_recibido'),
            'total_cobrado'      => (float)$arriendo->devoluciones->sum('total_cobrado'),
        ];

        return view('arriendos.devolucion', compact('arriendo', 'resumen'));
    }

    /**
     * ✅ DEVOLUCIÓN PARCIAL FUNCIONAL + HISTORIAL
     * - Cobra SOLO la cantidad devuelta
     * - Resta cantidad al arriendo
     * - Acumula total_alquiler / total_merma / precio_total / saldo
     * - Registra un historial en devoluciones_arriendos
     * - Si cantidad queda en 0, cierra el arriendo
     * - Si hay pago: crea payment confirmado + método en payment_parts
     */
    public function store(Request $request, Arriendo $arriendo)
    {
        $data = $request->validate([
            'cantidad_devuelta'       => 'required|integer|min:1',
            'fecha_devolucion'        => 'required|date',
            'dias_lluvia'             => 'nullable|integer|min:0',
            'costo_merma'             => 'nullable|numeric|min:0',
            'descripcion_incidencia'  => 'nullable|string|max:255',
            'pago'                    => 'nullable|numeric|min:0',
            'nota'                    => 'nullable|string|max:1000',

            // ✅ método de pago
            'payment_method'          => 'nullable|in:efectivo,nequi,daviplata,transferencia',
        ]);

        $arriendo->load('producto');

        if ((int)($arriendo->cerrado ?? 0) === 1 || $arriendo->estado !== 'activo') {
            return redirect()->route('arriendos.index')
                ->with('success', 'Este arriendo ya está cerrado o no está activo.');
        }

        $cantidadDevuelta = (int)$data['cantidad_devuelta'];

        if ($cantidadDevuelta > (int)$arriendo->cantidad) {
            return back()
                ->withErrors(['cantidad_devuelta' => 'No puedes devolver más de la cantidad actualmente alquilada.'])
                ->withInput();
        }

        // ✅ FECHA ENTREGA (si no está, usamos fecha_inicio)
        $fechaEntrega = $arriendo->fecha_entrega ?? $arriendo->fecha_inicio;
        $fechaEntrega = Carbon::parse($fechaEntrega)->toDateString();

        // ✅ FECHA DEVOLUCIÓN
        $fechaDevol = Carbon::parse($data['fecha_devolucion'])->toDateString();

        if (Carbon::parse($fechaDevol)->lt(Carbon::parse($fechaEntrega))) {
            return back()
                ->withErrors(['fecha_devolucion' => 'La fecha de devolución no puede ser anterior a la fecha de entrega/inicio.'])
                ->withInput();
        }

        // ✅ variables pago
        $pago   = (float)($data['pago'] ?? 0);
        $method = $data['payment_method'] ?? 'efectivo';

        // Normalizamos extras
        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);
        $costoMerma = (float)($data['costo_merma'] ?? 0);
        $desc = $data['descripcion_incidencia'] ?? null;
        $nota = $data['nota'] ?? null;

        try {
            DB::transaction(function () use (
                $arriendo,
                $cantidadDevuelta,
                $fechaEntrega,
                $fechaDevol,
                $diasLluvia,
                $costoMerma,
                $desc,
                $nota,
                $pago,
                $method
            ) {

                // ============================================================
                // ✅ DÍAS TRANSCURRIDOS (NO se cobra el día de devolución)
                // ============================================================
                $start = Carbon::parse($fechaEntrega)->startOfDay();
                $end   = Carbon::parse($fechaDevol)->startOfDay(); // fin NO incluido

                $diasTrans = $start->diffInDays($end);
                if ($diasTrans === 0) $diasTrans = 1;

                $domingos = $this->contarDomingosExcluyendoFin($fechaEntrega, $fechaDevol);

                $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);

                // ✅ TARIFA DIARIA (producto->costo)
                $tarifa = (float)($arriendo->producto->costo ?? 0);

                // ✅ TOTAL ALQUILER SOLO POR LO DEVUELTO
                $totalAlquilerParcial = $diasCobrables * $tarifa * $cantidadDevuelta;

                // ✅ MERMA ESTA DEVOLUCIÓN
                $totalMermaParcial = $costoMerma;

                // ✅ TOTAL COBRADO ESTA DEVOLUCIÓN
                $totalCobradoParcial = $totalAlquilerParcial + $totalMermaParcial;

                // ✅ ACUMULADOS EXISTENTES
                $totalAlquilerAcum = (float)($arriendo->total_alquiler ?? 0);
                $totalMermaAcum    = (float)($arriendo->total_merma ?? 0);
                $totalPagadoAcum   = (float)($arriendo->total_pagado ?? 0);

                // ✅ NUEVOS ACUMULADOS
                $nuevoTotalAlquiler = $totalAlquilerAcum + $totalAlquilerParcial;
                $nuevoTotalMerma    = $totalMermaAcum + $totalMermaParcial;
                $nuevoTotalPagado   = $totalPagadoAcum + $pago;

                $nuevoPrecioTotal = $nuevoTotalAlquiler + $nuevoTotalMerma;
                $nuevoSaldo       = max(0, $nuevoPrecioTotal - $nuevoTotalPagado);

                // ✅ RESTAR CANTIDAD AL ARRIENDO
                $cantidadRestante = (int)$arriendo->cantidad - $cantidadDevuelta;

                // ✅ Incidencias
                if ($diasLluvia > 0) {
                    Incidencia::create([
                        'arriendo_id' => $arriendo->id,
                        'tipo' => 'LLUVIA',
                        'dias' => $diasLluvia,
                        'costo' => 0,
                        'descripcion' => ($desc ? $desc . ' ' : '') . '(Devolución parcial)',
                    ]);
                }

                if ($totalMermaParcial > 0) {
                    Incidencia::create([
                        'arriendo_id' => $arriendo->id,
                        'tipo' => 'DANO',
                        'dias' => 0,
                        'costo' => $totalMermaParcial,
                        'descripcion' => ($desc ? $desc . ' ' : '') . '(Devolución parcial)',
                    ]);
                }

                // ✅ Actualizar arriendo (si queda 0, se cierra)
                if ($cantidadRestante <= 0) {
                    $arriendo->update([
                        'cantidad' => 0,

                        'fecha_fin' => $fechaDevol,
                        'fecha_devolucion_real' => $fechaDevol, // si existe en tu tabla

                        'cerrado' => 1,
                        'estado' => 'devuelto',

                        'dias_transcurridos' => $diasTrans,
                        'domingos_desc' => $domingos,
                        'dias_lluvia_desc' => $diasLluvia,
                        'dias_cobrables' => $diasCobrables,

                        'total_alquiler' => $nuevoTotalAlquiler,
                        'total_merma' => $nuevoTotalMerma,
                        'total_pagado' => $nuevoTotalPagado,
                        'precio_total' => $nuevoPrecioTotal,
                        'saldo' => $nuevoSaldo,
                    ]);
                } else {
                    $arriendo->update([
                        'cantidad' => $cantidadRestante,

                        'dias_transcurridos' => $diasTrans,
                        'domingos_desc' => $domingos,
                        'dias_lluvia_desc' => $diasLluvia,
                        'dias_cobrables' => $diasCobrables,

                        'total_alquiler' => $nuevoTotalAlquiler,
                        'total_merma' => $nuevoTotalMerma,
                        'total_pagado' => $nuevoTotalPagado,
                        'precio_total' => $nuevoPrecioTotal,
                        'saldo' => $nuevoSaldo,
                    ]);
                }

                // ✅ Guardar HISTORIAL (devoluciones_arriendos)
                $devol = DevolucionArriendo::create([
                    'arriendo_id' => $arriendo->id,
                    // NO ponemos arriendo_item_id aquí porque esta devolución es del PADRE

                    'fecha_devolucion' => $fechaDevol,
                    'cantidad_devuelta' => $cantidadDevuelta,

                    'dias_transcurridos' => $diasTrans,
                    'domingos_desc' => $domingos,
                    'dias_lluvia_desc' => $diasLluvia,
                    'dias_cobrables' => $diasCobrables,

                    'tarifa_diaria' => $tarifa,

                    'total_alquiler' => $totalAlquilerParcial,
                    'total_merma' => $totalMermaParcial,
                    'total_cobrado' => $totalCobradoParcial,

                    'pago_recibido' => $pago,

                    'cantidad_restante' => max(0, $cantidadRestante),
                    'saldo_resultante' => $nuevoSaldo,

                    'descripcion_incidencia' => $desc,
                    'nota' => $nota,
                ]);

                // ✅ Payment confirmado (solo si hay pago)
                if ($pago > 0) {
                    $this->payments->createConfirmedPayment([
                        'user_id' => Auth::id(),
                        'arriendo_id' => $arriendo->id,
                        'client_id' => $arriendo->cliente_id,
                        'obra_id' => $arriendo->obra_id,

                        // ✅ IMPORTANTE: morph correcto (NO rompe nada)
                        'source_type' => \App\Models\DevolucionArriendo::class,
                        'source_id' => $devol->id,

                        'note' => 'Pago devolución PADRE | arriendo #' . $arriendo->id . ' | devol #' . $devol->id,
                    ], [
                        ['method' => $method, 'amount' => (int)round($pago)],
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'pago' => 'Error al guardar la devolución: ' . $e->getMessage()
            ]);
        }

        return redirect()->route('arriendos.devolucion.create', $arriendo)
            ->with('success', 'Devolución registrada y guardada en historial.');
    }

    // ✅ Cuenta domingos entre dos fechas EXCLUYENDO la fecha FIN (no se cobra devolución)
    private function contarDomingosExcluyendoFin(string $inicio, string $fin): int
    {
        $start = Carbon::parse($inicio)->startOfDay();
        $end   = Carbon::parse($fin)->startOfDay(); // fin NO incluido

        $count = 0;
        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            if ($d->isSunday()) $count++;
        }
        return $count;
    }
}
