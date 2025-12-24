<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Incidencia;
use App\Models\DevolucionArriendo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArriendoDevolucionController extends Controller
{
    public function create(Arriendo $arriendo)
    {
        // ✅ Cargamos todo para mostrar arriba y el historial abajo
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

        // ✅ FECHA ENTREGA: si no está, usamos fecha_inicio (igual que tu cerrar)
        $fechaEntrega = $arriendo->fecha_entrega ?? $arriendo->fecha_inicio;
        $fechaEntrega = Carbon::parse($fechaEntrega)->toDateString();

        // ✅ FECHA DEVOLUCIÓN PARCIAL
        $fechaDevol = Carbon::parse($data['fecha_devolucion'])->toDateString();

        // ✅ VALIDACIÓN: devolución no puede ser antes de entrega
        if (Carbon::parse($fechaDevol)->lt(Carbon::parse($fechaEntrega))) {
            return back()
                ->withErrors(['fecha_devolucion' => 'La fecha de devolución no puede ser anterior a la fecha de entrega/inicio.'])
                ->withInput();
        }

        // ============================================================
        // ✅ DÍAS TRANSCURRIDOS (NO se cobra el día de devolución)
        // - Se cobra desde fechaEntrega hasta (fechaDevol - 1)
        // - Si entrega == devolución, se cobra 1
        // ============================================================
        $start = Carbon::parse($fechaEntrega)->startOfDay();
        $end   = Carbon::parse($fechaDevol)->startOfDay(); // fin NO incluido

        $diasTrans = $start->diffInDays($end);
        if ($diasTrans === 0) $diasTrans = 1;

        // ✅ DOMINGOS AUTOMÁTICOS (sin incluir el día de devolución)
        $domingos = $this->contarDomingosExcluyendoFin($fechaEntrega, $fechaDevol);

        // ✅ LLUVIA MANUAL (se descuenta)
        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);

        // ✅ DÍAS COBRABLES
        $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);

        // ✅ TARIFA DIARIA (producto->costo)
        $tarifa = (float)($arriendo->producto->costo ?? 0);

        // ✅ TOTAL ALQUILER SOLO POR LO DEVUELTO
        $totalAlquilerParcial = $diasCobrables * $tarifa * $cantidadDevuelta;

        // ✅ MERMA SOLO PARA ESTA DEVOLUCIÓN (si aplica)
        $totalMermaParcial = (float)($data['costo_merma'] ?? 0);

        // ✅ TOTAL COBRADO DE ESTA DEVOLUCIÓN
        $totalCobradoParcial = $totalAlquilerParcial + $totalMermaParcial;

        // ✅ PAGO EN ESTA DEVOLUCIÓN (opcional)
        $pago = (float)($data['pago'] ?? 0);

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

        // ✅ Guardar incidencias si corresponde (igual que tu cierre)
        $desc = $data['descripcion_incidencia'] ?? null;

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
                'fecha_devolucion_real' => $fechaDevol, // si existe el campo en tu tabla

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

        // ✅ Guardar registro en historial (devoluciones_arriendos)
        DevolucionArriendo::create([
            'arriendo_id' => $arriendo->id,
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
            'nota' => $data['nota'] ?? null,
        ]);

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
