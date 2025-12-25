<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\ArriendoItem;
use App\Models\DevolucionArriendo;
use App\Models\Incidencia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ItemDevolucionController extends Controller
{
    public function create(ArriendoItem $item)
    {
        $item->load(['arriendo.cliente', 'producto', 'devoluciones']);

        $resumen = [
            'total_devoluciones' => $item->devoluciones->count(),
            'total_devuelto'     => (int)$item->devoluciones->sum('cantidad_devuelta'),
            'total_abonado'      => (float)$item->devoluciones->sum('pago_recibido'),
            'total_cobrado'      => (float)$item->devoluciones->sum('total_cobrado'),
        ];

        return view('arriendos.devolucion', compact('item', 'resumen'));
    }

    public function store(Request $request, ArriendoItem $item)
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

        $item->load(['producto', 'arriendo.cliente', 'devoluciones']);

        if ((int)($item->cerrado ?? 0) === 1 || ($item->estado ?? '') !== 'activo') {
            return redirect()->route('items.devolucion.create', $item)
                ->with('success', 'Este producto ya está cerrado o no está activo.');
        }

        $cantidadDevuelta = (int)$data['cantidad_devuelta'];

        if ($cantidadDevuelta > (int)$item->cantidad_actual) {
            return back()
                ->withErrors(['cantidad_devuelta' => 'No puedes devolver más de la cantidad actualmente alquilada en este producto.'])
                ->withInput();
        }

        // ✅ Fecha inicio del item (si no, usamos fecha_inicio del padre)
        $fechaInicioItem = $item->fecha_inicio_item ?? $item->arriendo->fecha_inicio;
        $fechaInicioItem = Carbon::parse($fechaInicioItem)->toDateString();

        // ✅ Fecha devolución
        $fechaDevol = Carbon::parse($data['fecha_devolucion'])->toDateString();

        if (Carbon::parse($fechaDevol)->lt(Carbon::parse($fechaInicioItem))) {
            return back()
                ->withErrors(['fecha_devolucion' => 'La fecha de devolución no puede ser anterior a la fecha de inicio del producto.'])
                ->withInput();
        }

        // ✅ días transcurridos sin cobrar el día de devolución
        $start = Carbon::parse($fechaInicioItem)->startOfDay();
        $end   = Carbon::parse($fechaDevol)->startOfDay(); // fin no incluido

        $diasTrans = $start->diffInDays($end);
        if ($diasTrans === 0) $diasTrans = 1;

        // ✅ domingos (sin incluir fin)
        $domingos = $this->contarDomingosExcluyendoFin($fechaInicioItem, $fechaDevol);

        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);
        $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);

        // ✅ usa tarifa guardada en el item (si existe), si no existe, usa costo del producto
        $tarifa = (float)($item->tarifa_diaria ?? ($item->producto->costo ?? 0));

        // ✅ cobro solo por lo devuelto
        $totalAlquilerParcial = $diasCobrables * $tarifa * $cantidadDevuelta;
        $totalMermaParcial    = (float)($data['costo_merma'] ?? 0);
        $totalCobradoParcial  = $totalAlquilerParcial + $totalMermaParcial;

        $pago = (float)($data['pago'] ?? 0);

        // ✅ NUEVO: saldo SOLO de ESTA devolución (registro individual)
        $saldoDevolucion = max(0, $totalCobradoParcial - $pago);

        // ✅ acumulados del ITEM
        $nuevoTotalAlquiler = (float)($item->total_alquiler ?? 0) + $totalAlquilerParcial;
        $nuevoTotalMerma    = (float)($item->total_merma ?? 0) + $totalMermaParcial;
        $nuevoTotalPagado   = (float)($item->total_pagado ?? 0) + $pago;

        $nuevoPrecioTotal = $nuevoTotalAlquiler + $nuevoTotalMerma;
        $nuevoSaldo       = max(0, $nuevoPrecioTotal - $nuevoTotalPagado);

        $cantidadRestante = (int)$item->cantidad_actual - $cantidadDevuelta;

        // incidencias (opcional)
        $desc = $data['descripcion_incidencia'] ?? null;

        if ($diasLluvia > 0) {
            Incidencia::create([
                'arriendo_id' => $item->arriendo_id,
                'tipo' => 'LLUVIA',
                'dias' => $diasLluvia,
                'costo' => 0,
                'descripcion' => ($desc ? $desc . ' ' : '') . "(Devolución item #{$item->id})",
            ]);
        }

        if ($totalMermaParcial > 0) {
            Incidencia::create([
                'arriendo_id' => $item->arriendo_id,
                'tipo' => 'DANO',
                'dias' => 0,
                'costo' => $totalMermaParcial,
                'descripcion' => ($desc ? $desc . ' ' : '') . "(Devolución item #{$item->id})",
            ]);
        }

        // ✅ actualizar item
        if ($cantidadRestante <= 0) {
            $item->update([
                'cantidad_actual' => 0,
                'fecha_fin_item' => $fechaDevol,
                'cerrado' => 1,
                'estado' => 'devuelto',

                'precio_total' => $nuevoPrecioTotal,
                'total_alquiler' => $nuevoTotalAlquiler,
                'total_merma' => $nuevoTotalMerma,
                'total_pagado' => $nuevoTotalPagado,
                'saldo' => $nuevoSaldo,
            ]);
        } else {
            $item->update([
                'cantidad_actual' => $cantidadRestante,

                'precio_total' => $nuevoPrecioTotal,
                'total_alquiler' => $nuevoTotalAlquiler,
                'total_merma' => $nuevoTotalMerma,
                'total_pagado' => $nuevoTotalPagado,
                'saldo' => $nuevoSaldo,
            ]);
        }

        // ✅ guardar historial (tabla devoluciones_arriendos)
        DevolucionArriendo::create([
            'arriendo_id' => $item->arriendo_id,
            'arriendo_item_id' => $item->id, // requiere columna en tabla

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

            // ✅ NUEVO: saldo de ESTA devolución
            'saldo_devolucion' => $saldoDevolucion, // requiere columna en tabla

            'cantidad_restante' => max(0, $cantidadRestante),
            'saldo_resultante' => $nuevoSaldo,

            'descripcion_incidencia' => $desc,
            'nota' => $data['nota'] ?? null,
        ]);

        // ✅ actualizar totales del PADRE sumando todos sus items
        $this->recalcularTotalesPadre($item->arriendo);

        return redirect()->route('items.devolucion.create', $item)
            ->with('success', 'Devolución registrada y guardada en historial (por producto).');
    }

    private function recalcularTotalesPadre(Arriendo $arriendo): void
    {
        $arriendo->load('items');

        $totalAlquiler = (float)$arriendo->items->sum('total_alquiler');
        $totalMerma    = (float)$arriendo->items->sum('total_merma');
        $totalPagado   = (float)$arriendo->items->sum('total_pagado');

        $precioTotal = $totalAlquiler + $totalMerma;
        $saldo       = max(0, $precioTotal - $totalPagado);

        // ✅ CAMBIO MÍNIMO NECESARIO:
        // el padre queda "devuelto" solo si todos los items están realmente devueltos (cantidad_actual=0 o estado=devuelto)
        $todosDevueltos = $arriendo->items->count() > 0
            ? $arriendo->items->every(fn($it) => (int)($it->cantidad_actual ?? 0) === 0 || ($it->estado ?? '') === 'devuelto')
            : false;

        $arriendo->update([
            'total_alquiler' => $totalAlquiler,
            'total_merma' => $totalMerma,
            'total_pagado' => $totalPagado,
            'precio_total' => $precioTotal,
            'saldo' => $saldo,
            'estado' => $todosDevueltos ? 'devuelto' : 'activo',
            'cerrado' => $todosDevueltos ? 1 : 0,
        ]);
    }

    private function contarDomingosExcluyendoFin(string $inicio, string $fin): int
    {
        $start = Carbon::parse($inicio)->startOfDay();
        $end   = Carbon::parse($fin)->startOfDay();

        $count = 0;
        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            if ($d->isSunday()) $count++;
        }
        return $count;
    }
}
