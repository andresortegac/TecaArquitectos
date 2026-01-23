<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\ArriendoItem;
use App\Models\DevolucionArriendo;
use App\Models\Incidencia;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PaymentService;

class ItemDevolucionController extends Controller
{
    protected PaymentService $payments;

    public function __construct(PaymentService $payments)
    {
        $this->payments = $payments;
    }

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
            'payment_method'          => 'nullable|in:efectivo,nequi,daviplata,transferencia',
        ]);

        $item->load(['producto', 'arriendo.cliente', 'devoluciones', 'arriendo.items']);

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

        // Fecha inicio item (o fecha inicio padre)
        $fechaInicioItem = $item->fecha_inicio_item ?? $item->arriendo->fecha_inicio;
        $fechaInicioItem = Carbon::parse($fechaInicioItem)->toDateString();

        // Fecha devolución
        $fechaDevol = Carbon::parse($data['fecha_devolucion'])->toDateString();

        if (Carbon::parse($fechaDevol)->lt(Carbon::parse($fechaInicioItem))) {
            return back()
                ->withErrors(['fecha_devolucion' => 'La fecha de devolución no puede ser anterior a la fecha de inicio del producto.'])
                ->withInput();
        }

        $pago       = (float)($data['pago'] ?? 0);
        $method     = $data['payment_method'] ?? 'efectivo';
        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);
        $desc       = $data['descripcion_incidencia'] ?? null;
        $costoMerma = (float)($data['costo_merma'] ?? 0);

        // ✅ NUEVO: regla domingos por item (si cobra_domingo = 1 NO descontamos domingos)
        $cobraDomingo = (int)($item->cobra_domingo ?? 0) === 1;

        try {
            DB::transaction(function () use (
                $item,
                $cantidadDevuelta,
                $fechaInicioItem,
                $fechaDevol,
                $diasLluvia,
                $costoMerma,
                $desc,
                $pago,
                $method,
                $cobraDomingo
            ) {

                // ============================================================
                // ✅ 1) AUMENTAR STOCK DEL PRODUCTO (LO QUE DEVUELVEN)
                // ============================================================
                $producto = Producto::where('id', $item->producto_id)
                    ->lockForUpdate()
                    ->first();

                if ($producto) {
                    $producto->update([
                        'cantidad' => (int)($producto->cantidad ?? 0) + $cantidadDevuelta
                    ]);
                }

                // ========= DÍAS =========
                $start = Carbon::parse($fechaInicioItem)->startOfDay();
                $end   = Carbon::parse($fechaDevol)->startOfDay(); // fin NO incluido
                $diasTrans = $start->diffInDays($end);

                // Si inicio y devolución mismo día -> cobra 1 (tu regla)
                if ($diasTrans === 0) $diasTrans = 1;

                // Domingos (excluye fin)
                $domingos = $this->contarDomingosExcluyendoFin($fechaInicioItem, $fechaDevol);

                // ✅ cobrables: si NO cobra domingo -> restar domingos
                if ($cobraDomingo) {
                    $diasCobrables = max(0, $diasTrans - $diasLluvia);
                    $domingosDesc = 0; // para el historial (no descontó domingos)
                } else {
                    $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);
                    $domingosDesc = $domingos;
                }

                // ========= COBRO (PARCIAL DE ESTA DEVOLUCIÓN) =========
                $tarifa = (float)($item->tarifa_diaria ?? ($item->producto->costo ?? 0));
                $totalAlquilerParcial = $diasCobrables * $tarifa * $cantidadDevuelta;
                $totalMermaParcial    = $costoMerma;
                $totalCobradoParcial  = $totalAlquilerParcial + $totalMermaParcial;

                // ✅ saldo devolución REAL
                $saldoDevolucionParcial = max(0, $totalCobradoParcial - $pago);

                // ========= ACUMULADOS ITEM =========
                $nuevoTotalAlquiler = (float)($item->total_alquiler ?? 0) + $totalAlquilerParcial;
                $nuevoTotalMerma    = (float)($item->total_merma ?? 0) + $totalMermaParcial;
                $nuevoTotalPagado   = (float)($item->total_pagado ?? 0) + $pago;

                $nuevoPrecioTotal = $nuevoTotalAlquiler + $nuevoTotalMerma;
                $nuevoSaldo       = max(0, $nuevoPrecioTotal - $nuevoTotalPagado);

                $cantidadRestante = (int)$item->cantidad_actual - $cantidadDevuelta;

                // ========= INCIDENCIAS (opcional) =========
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

                // ========= UPDATE ITEM =========
                if ($cantidadRestante <= 0) {
                    $item->update([
                        'cantidad_actual' => 0,
                        'fecha_fin_item'  => $fechaDevol,
                        'cerrado' => 1,
                        'estado'  => 'devuelto',

                        'precio_total'   => $nuevoPrecioTotal,
                        'total_alquiler' => $nuevoTotalAlquiler,
                        'total_merma'    => $nuevoTotalMerma,
                        'total_pagado'   => $nuevoTotalPagado,
                        'saldo'          => $nuevoSaldo,
                    ]);
                } else {
                    $item->update([
                        'cantidad_actual' => $cantidadRestante,

                        'precio_total'   => $nuevoPrecioTotal,
                        'total_alquiler' => $nuevoTotalAlquiler,
                        'total_merma'    => $nuevoTotalMerma,
                        'total_pagado'   => $nuevoTotalPagado,
                        'saldo'          => $nuevoSaldo,
                    ]);
                }

                // ========= HISTORIAL (devoluciones_arriendos) =========
                $devol = DevolucionArriendo::create([
                    'arriendo_id'      => $item->arriendo_id,
                    'arriendo_item_id' => $item->id,

                    'fecha_devolucion'  => $fechaDevol,
                    'cantidad_devuelta' => $cantidadDevuelta,

                    'dias_transcurridos' => $diasTrans,
                    'domingos_desc'      => $domingosDesc,
                    'dias_lluvia_desc'   => $diasLluvia,
                    'dias_cobrables'     => $diasCobrables,

                    'tarifa_diaria' => $tarifa,

                    'total_alquiler' => $totalAlquilerParcial,
                    'total_merma'    => $totalMermaParcial,
                    'total_cobrado'  => $totalCobradoParcial,

                    'pago_recibido'     => $pago,
                    'saldo_devolucion'  => $saldoDevolucionParcial,

                    'cantidad_restante' => max(0, $cantidadRestante),
                    'saldo_resultante'  => $nuevoSaldo,

                    'descripcion_incidencia' => $desc,
                ]);

                // ========= RECALCULAR PADRE =========
                $this->recalcularTotalesPadre($item->arriendo);

                // ========= PAYMENT (solo si hay pago) =========
                if ($pago > 0) {
                    $this->payments->createConfirmedPayment([
                        'user_id' => Auth::id(),
                        'arriendo_id' => $item->arriendo_id,
                        'client_id' => optional($item->arriendo)->cliente_id,
                        'obra_id' => optional($item->arriendo)->obra_id,

                        'source_type' => \App\Models\DevolucionArriendo::class,
                        'source_id'   => $devol->id,

                        'note' => 'Pago devolución ITEM | arriendo #' . $item->arriendo_id
                            . ' | item #' . $item->id
                            . ' | devol #' . $devol->id,
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

        return redirect()->route('items.devolucion.create', $item)
            ->with('success', 'Devolución registrada. ✅ Stock actualizado correctamente.');
    }

    private function recalcularTotalesPadre(Arriendo $arriendo): void
    {
        $arriendo->load('items');

        $totalAlquiler = (float)$arriendo->items->sum('total_alquiler');
        $totalMerma    = (float)$arriendo->items->sum('total_merma');
        $totalPagado   = (float)$arriendo->items->sum('total_pagado');

        $precioTotal = $totalAlquiler + $totalMerma;
        $saldo       = max(0, $precioTotal - $totalPagado);

        $todosDevueltos = $arriendo->items->count() > 0
            ? $arriendo->items->every(fn($it) => (int)($it->cantidad_actual ?? 0) === 0 || ($it->estado ?? '') === 'devuelto')
            : false;

        $arriendo->update([
            'total_alquiler' => $totalAlquiler,
            'total_merma'    => $totalMerma,
            'total_pagado'   => $totalPagado,
            'precio_total'   => $precioTotal,
            'saldo'          => $saldo,
            'estado'         => $todosDevueltos ? 'devuelto' : 'activo',
            'cerrado'        => $todosDevueltos ? 1 : 0,
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
