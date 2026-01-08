<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Incidencia;
use App\Models\DevolucionArriendo; // ✅ NUEVO
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ArriendoController extends Controller
{
    /* ============================================================
     * 1) LISTADO (INDEX) + FILTROS + SEMÁFORO ACTUALIZADO
     * ============================================================ */
    public function index(Request $request)
    {
        $query = Arriendo::with(['cliente', 'producto'])->latest();

        // ✅ FILTROS (opcionales)
        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('semaforo_pago')) {
            $query->where('semaforo_pago', $request->semaforo_pago);
        }

        $arriendos = $query->paginate(10)->withQueryString();

        // ✅ SEMÁFORO “VIVO”: recalcula mora/semáforo al mostrar el listado
        foreach ($arriendos as $a) {
            $fechaDev = $a->fecha_devolucion_real
                ? Carbon::parse($a->fecha_devolucion_real)->toDateString()
                : null;

            $sem = $this->calcularSemaforoMoroso($fechaDev, (float)$a->saldo);

            // Solo actualizamos en memoria (para mostrar).
            $a->dias_mora = $sem['dias_mora'];
            $a->semaforo_pago = $sem['semaforo'];
        }

        // Para combos de filtros en la vista
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.index', compact('arriendos', 'clientes', 'productos'));
    }

    /* ============================================================
     * 2) FORMULARIO CREAR
     * ============================================================ */
    public function create()
    {
        $clientes  = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.create', compact('clientes', 'productos'));
    }

    /* ============================================================
     * 3) GUARDAR NUEVO ARRIENDO (STORE)
     * ============================================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'fecha_inicio' => 'required|date',
            'obra_id'      => 'nullable|integer',
        ]);

        // ✅ Crear SOLO el PADRE (contrato). NO producto aquí.
        $arriendo = Arriendo::create([
            'cliente_id' => $data['cliente_id'],

            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_entrega' => $data['fecha_inicio'],
            'fecha_fin' => null,

            'obra_id' => $data['obra_id'] ?? null,

            'estado' => 'activo',
            'cerrado' => 0,

            // ✅ Totales del contrato (se recalculan sumando items)
            'precio_total' => 0,
            'dias_transcurridos' => 0,
            'domingos_desc' => 0,
            'dias_lluvia_desc' => 0,
            'dias_cobrables' => 0,
            'total_alquiler' => 0,
            'total_merma' => 0,
            'total_pagado' => 0,
            'saldo' => 0,
            'dias_mora' => 0,
            'semaforo_pago' => 'VERDE',

            'cantidad' => 0,
        ]);

        return redirect()->route('arriendos.ver', $arriendo)
            ->with('success', 'Arriendo PADRE creado. Ahora agrega productos.');
    }

    /* ============================================================
     * 4) EDITAR
     * ============================================================ */
    public function edit(Arriendo $arriendo)
    {
        $clientes  = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.edit', compact('arriendo', 'clientes', 'productos'));
    }

    // ============================================================
    // PADRE PARA PODER VER
    // ============================================================
    public function ver(Arriendo $arriendo)
    {
        $arriendo->load([
            'cliente',
            'items.producto',
        ]);

        // ✅ Totales del PADRE calculados desde los items (siempre correctos)
        $totContrato = [
            'total_alquiler' => (float)$arriendo->items->sum('total_alquiler'),
            'total_merma'    => (float)$arriendo->items->sum('total_merma'),
            'total_pagado'   => (float)$arriendo->items->sum('total_pagado'),
        ];

        $totContrato['precio_total'] = $totContrato['total_alquiler'] + $totContrato['total_merma'];
        $totContrato['saldo']        = max(0, $totContrato['precio_total'] - $totContrato['total_pagado']);

        // ✅ Total histórico del cliente (sumando todos los PADRES)
        $padresCliente = Arriendo::where('cliente_id', $arriendo->cliente_id)
            ->with('items')
            ->get();

        $totalHistorico = [
            'total_alquiler' => 0,
            'total_merma'    => 0,
            'total_pagado'   => 0,
            'precio_total'   => 0,
            'saldo'          => 0,
        ];

        foreach ($padresCliente as $p) {
            $al = (float)$p->items->sum('total_alquiler');
            $me = (float)$p->items->sum('total_merma');
            $pa = (float)$p->items->sum('total_pagado');

            $pt = $al + $me;
            $sa = max(0, $pt - $pa);

            $totalHistorico['total_alquiler'] += $al;
            $totalHistorico['total_merma']    += $me;
            $totalHistorico['total_pagado']   += $pa;
            $totalHistorico['precio_total']   += $pt;
            $totalHistorico['saldo']          += $sa;
        }

        return view('arriendos.ver', compact('arriendo', 'totContrato', 'totalHistorico'));
    }

    /* ============================================================
     * ✅ NUEVO (NECESARIO):
     *  VER REGISTROS INDIVIDUALES DE DEVOLUCIONES DEL PADRE
     * ============================================================ */
    public function devoluciones(Arriendo $arriendo)
    {
        $arriendo->load([
            'cliente',
            'items.producto',
            'items.devoluciones',
        ]);

        $registros = $arriendo->items->flatMap(function ($it) {
            return $it->devoluciones->map(function ($d) use ($it) {
                $d->_item_id = $it->id;
                $d->_producto = $it->producto->nombre ?? '—';
                return $d;
            });
        })->sortByDesc('id')->values();

        $resumen = [
            'total_registros' => (int)$registros->count(),
            'total_devuelto'  => (int)$registros->sum('cantidad_devuelta'),
            'total_cobrado'   => (float)$registros->sum('total_cobrado'),
            'total_abonado'   => (float)$registros->sum('pago_recibido'),
            'total_saldo_dev' => (float)$registros->sum(function ($d) {
                return (float)($d->saldo_devolucion ?? 0);
            }),
        ];

        return view('arriendos.devoluciones', compact('arriendo', 'registros', 'resumen'));
    }

    /* ============================================================
     * 5) ACTUALIZAR (UPDATE)
     * ============================================================ */
    public function update(Request $request, Arriendo $arriendo)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'producto_id'  => 'required|exists:productos,id',
            'cantidad'     => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'       => 'required|in:activo,devuelto,vencido',
            'obra_id'      => 'nullable|integer',
        ]);

        $arriendo->update($data);

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo actualizado correctamente');
    }

    /* ============================================================
     * 6) ELIMINAR
     * ============================================================ */
    public function destroy(Arriendo $arriendo)
    {
        $arriendo->delete();

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo eliminado');
    }

    /* ============================================================
     * 7) MOSTRAR FORMULARIO PARA CERRAR / DEVOLVER
     * ============================================================ */
    public function showCerrar(Arriendo $arriendo)
    {
        $arriendo->load(['cliente', 'producto']);
        return view('arriendos.cerrar', compact('arriendo'));
    }

    /* ============================================================
     * 8) CERRAR / DEVOLVER ARRIENDO (CÁLCULO CORREGIDO)
     * ============================================================ */
    public function cerrar(Request $request, Arriendo $arriendo)
    {
        $data = $request->validate([
            'fecha_devolucion_real' => 'required|date',
            'dias_lluvia' => 'nullable|integer|min:0',
            'costo_merma' => 'nullable|numeric|min:0',
            'descripcion_incidencia' => 'nullable|string|max:255',
            'pago' => 'nullable|numeric|min:0',
        ]);

        $arriendo->load('producto');

        $fechaEntrega = $arriendo->fecha_entrega ?? $arriendo->fecha_inicio;
        $fechaEntrega = Carbon::parse($fechaEntrega)->toDateString();

        $fechaDevol = Carbon::parse($data['fecha_devolucion_real'])->toDateString();

        if (Carbon::parse($fechaDevol)->lt(Carbon::parse($fechaEntrega))) {
            return back()
                ->withErrors(['fecha_devolucion_real' => 'La fecha de devolución no puede ser anterior a la fecha de entrega/inicio.'])
                ->withInput();
        }

        $start = Carbon::parse($fechaEntrega)->startOfDay();
        $end   = Carbon::parse($fechaDevol)->startOfDay();

        $diasTrans = $start->diffInDays($end);

        if ($diasTrans === 0) {
            $diasTrans = 1;
        }

        $domingos = $this->contarDomingosExcluyendoFin($fechaEntrega, $fechaDevol);

        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);

        $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);

        $tarifa = (float)($arriendo->producto->costo ?? 0);

        $totalAlquiler = $diasCobrables * $tarifa * (int)$arriendo->cantidad;

        $totalMerma = (float)($data['costo_merma'] ?? 0);

        $pago = (float)($data['pago'] ?? 0);
        $totalPagado = (float)($arriendo->total_pagado ?? 0) + $pago;

        $totalFinal = $totalAlquiler + $totalMerma;
        $saldo = max(0, $totalFinal - $totalPagado);

        $sem = $this->calcularSemaforoMoroso($fechaDevol, $saldo);

        $arriendo->update([
            'fecha_fin' => $fechaDevol,

            'cerrado' => 1,
            'estado' => 'devuelto',

            'dias_transcurridos' => $diasTrans,
            'domingos_desc' => $domingos,
            'dias_lluvia_desc' => $diasLluvia,
            'dias_cobrables' => $diasCobrables,

            'total_alquiler' => $totalAlquiler,
            'total_merma' => $totalMerma,
            'total_pagado' => $totalPagado,
            'saldo' => $saldo,

            'precio_total' => $totalFinal,

            'dias_mora' => $sem['dias_mora'],
            'semaforo_pago' => $sem['semaforo'],
        ]);

        $desc = $data['descripcion_incidencia'] ?? null;

        if ($diasLluvia > 0) {
            Incidencia::create([
                'arriendo_id' => $arriendo->id,
                'tipo' => 'LLUVIA',
                'dias' => $diasLluvia,
                'costo' => 0,
                'descripcion' => $desc ?? 'Incidencia por lluvia al cierre',
            ]);
        }

        if ($totalMerma > 0) {
            Incidencia::create([
                'arriendo_id' => $arriendo->id,
                'tipo' => 'DANO',
                'dias' => 0,
                'costo' => $totalMerma,
                'descripcion' => $desc ?? 'Incidencia por daño/merma al cierre',
            ]);
        }

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo cerrado. Cálculos aplicados y semáforo actualizado.');
    }

    /* ============================================================
     * 9) FUNCIONES INTERNAS
     * ============================================================ */

    private function contarDomingosExcluyendoFin(string $inicio, string $fin): int
    {
        $start = Carbon::parse($inicio)->startOfDay();
        $end   = Carbon::parse($fin)->startOfDay();

        $count = 0;
        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            if ($d->isSunday()) {
                $count++;
            }
        }
        return $count;
    }

    private function calcularSemaforoMoroso(?string $fechaDevolucionReal, float $saldo): array
    {
        if ($saldo <= 0) {
            return ['semaforo' => 'VERDE', 'dias_mora' => 0];
        }

        if (!$fechaDevolucionReal) {
            return ['semaforo' => 'VERDE', 'dias_mora' => 0];
        }

        $hoy = Carbon::today();
        $dev = Carbon::parse($fechaDevolucionReal)->startOfDay();

        $dias_mora = max(0, $dev->diffInDays($hoy));

        if ($dias_mora <= 9) {
            return ['semaforo' => 'AMARILLO', 'dias_mora' => $dias_mora];
        }

        return ['semaforo' => 'ROJO', 'dias_mora' => $dias_mora];
    }

    public function detalles(Arriendo $arriendo)
    {
        // ✅ ÚNICO CAMBIO NECESARIO: cargar ITEMS + DEVOLUCIONES (para ver todo por herramienta)
        $arriendo->load([
            'cliente',
            'items.producto',
            'items.devoluciones',
            'incidencias',
        ]);

        return view('arriendos.detalles', compact('arriendo'));
    }
    public function pdf(Arriendo $arriendo)
    {
        $arriendo->load([
            'cliente',
            'obra',
            'items.producto'
        ]);

        $pdf = Pdf::loadView('arriendos.pdf', compact('arriendo'))
                ->setPaper('A4', 'portrait');

        return $pdf->stream('acta-entrega-'.$arriendo->id.'.pdf');
    }
}
 
