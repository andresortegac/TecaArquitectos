<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Incidencia;
use App\Models\DevolucionArriendo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArriendoController extends Controller
{
    /* ============================================================
     * 1) LISTADO (INDEX)
     * ============================================================ */
    public function index(Request $request)
    {
        $query = Arriendo::with([
            'cliente',
            'obra',          // 👈 AÑADIDO
        ])->latest();

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

        foreach ($arriendos as $a) {
            $fechaDev = $a->fecha_devolucion_real
                ? Carbon::parse($a->fecha_devolucion_real)->toDateString()
                : null;

            $sem = $this->calcularSemaforoMoroso($fechaDev, (float)$a->saldo);
            $a->dias_mora = $sem['dias_mora'];
            $a->semaforo_pago = $sem['semaforo'];
        }

        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.index', compact('arriendos', 'clientes', 'productos'));
    }

    /* ============================================================
     * 2) CREAR
     * ============================================================ */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('arriendos.create', compact('clientes'));
    }

    /* ============================================================
     * 3) STORE
     * ============================================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'fecha_inicio' => 'required|date',
            'obra_id'      => 'nullable|exists:obras,id',
        ]);

        $arriendo = Arriendo::create([
            'cliente_id' => $data['cliente_id'],
            'obra_id'    => $data['obra_id'] ?? null,

            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_entrega' => $data['fecha_inicio'],
            'fecha_fin' => null,

            'estado' => 'activo',
            'cerrado' => 0,

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
            ->with('success', 'Arriendo creado. Agrega productos.');
    }

    /* ============================================================
     * 4) VER (PADRE)
     * ============================================================ */
    public function ver(Arriendo $arriendo)
    {
        $arriendo->load([
            'cliente',
            'obra',                 // 👈 CLAVE
            'items.producto',
            'items.devoluciones',
        ]);

        $totContrato = [
            'total_alquiler' => (float)$arriendo->items->sum('total_alquiler'),
            'total_merma'    => (float)$arriendo->items->sum('total_merma'),
            'total_pagado'   => (float)$arriendo->items->sum('total_pagado'),
        ];

        $totContrato['precio_total'] =
            $totContrato['total_alquiler'] + $totContrato['total_merma'];

        $totContrato['saldo'] =
            max(0, $totContrato['precio_total'] - $totContrato['total_pagado']);

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
     * RESTO SIN CAMBIOS (cierres, devoluciones, etc.)
     * ============================================================ */

    private function calcularSemaforoMoroso(?string $fechaDevolucionReal, float $saldo): array
    {
        if ($saldo <= 0 || !$fechaDevolucionReal) {
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
}
