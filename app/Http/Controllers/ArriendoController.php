<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Incidencia;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
     *    - IMPORTANTE: aquí NO se calcula el total final
     *    - El total se calcula al CERRAR (cuando devuelven)
     * ============================================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'producto_id'  => 'required|exists:productos,id',
            'cantidad'     => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'obra_id'      => 'nullable|integer',
        ]);

        Arriendo::create([
            'cliente_id' => $data['cliente_id'],
            'producto_id' => $data['producto_id'],
            'cantidad' => $data['cantidad'],

            // Inicio del arriendo
            'fecha_inicio' => $data['fecha_inicio'],

            // ✅ Inicio real del cobro (si lo manejas así)
            'fecha_entrega' => $data['fecha_inicio'],

            // Aún no ha devuelto
            'fecha_fin' => null,

            // obra
            'obra_id' => $data['obra_id'] ?? null,

            // Estados
            'estado' => 'activo',
            'cerrado' => 0,

            // Totales iniciales (todo en 0 hasta que se cierre)
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
        ]);

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo creado correctamente');
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
     *    REGLAS:
     *    - NO se cobra el día de devolución
     *    - Si devuelven el mismo día => se cobra 1
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

        // ✅ FECHA ENTREGA: si no está, usamos fecha_inicio
        $fechaEntrega = $arriendo->fecha_entrega ?? $arriendo->fecha_inicio;
        $fechaEntrega = Carbon::parse($fechaEntrega)->toDateString();

        // ✅ FECHA DEVOLUCIÓN REAL (la que digitó el usuario al cerrar)
        $fechaDevol = Carbon::parse($data['fecha_devolucion_real'])->toDateString();

        // ✅ VALIDACIÓN EXTRA: devolución no puede ser antes de entrega
        if (Carbon::parse($fechaDevol)->lt(Carbon::parse($fechaEntrega))) {
            return back()
                ->withErrors(['fecha_devolucion_real' => 'La fecha de devolución no puede ser anterior a la fecha de entrega/inicio.'])
                ->withInput();
        }

        // ============================================================
        // ✅ DÍAS TRANSCURRIDOS (NO se cobra el día de devolución)
        // - Se cobra desde fechaEntrega hasta (fechaDevol - 1)
        // - Si entrega == devolución, se cobra 1
        // ============================================================
        $start = Carbon::parse($fechaEntrega)->startOfDay();
        $end   = Carbon::parse($fechaDevol)->startOfDay(); // fin NO incluido (no se cobra devolución)

        $diasTrans = $start->diffInDays($end); // 23/12 -> 23/01 = 31

        if ($diasTrans === 0) {
            $diasTrans = 1; // mismo día => cobra 1
        }

        // ✅ DOMINGOS AUTOMÁTICOS (sin incluir el día de devolución)
        $domingos = $this->contarDomingosExcluyendoFin($fechaEntrega, $fechaDevol);

        // ✅ LLUVIA MANUAL (se descuenta)
        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);

        // ✅ DÍAS COBRABLES
        $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);

        // ✅ TARIFA DIARIA POR PRODUCTO (según tu BD: costo)
        $tarifa = (float)($arriendo->producto->costo ?? 0);

        // ✅ TOTAL ALQUILER
        $totalAlquiler = $diasCobrables * $tarifa * (int)$arriendo->cantidad;

        // ✅ TOTAL MERMA
        $totalMerma = (float)($data['costo_merma'] ?? 0);

        // ✅ PAGO EN EL CIERRE (opcional)
        $pago = (float)($data['pago'] ?? 0);
        $totalPagado = (float)($arriendo->total_pagado ?? 0) + $pago;

        // ✅ TOTAL FINAL Y SALDO
        $totalFinal = $totalAlquiler + $totalMerma;
        $saldo = max(0, $totalFinal - $totalPagado);

        // ✅ SEMÁFORO MOROSOS
        $sem = $this->calcularSemaforoMoroso($fechaDevol, $saldo);

        // ✅ ACTUALIZAR ARRIENDO
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

        // ✅ INCIDENCIAS (opcional)
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

    // ✅ Cuenta domingos entre dos fechas EXCLUYENDO la fecha FIN (no se cobra devolución)
    private function contarDomingosExcluyendoFin(string $inicio, string $fin): int
    {
        $start = Carbon::parse($inicio)->startOfDay();
        $end   = Carbon::parse($fin)->startOfDay(); // fin NO incluido

        $count = 0;
        for ($d = $start->copy(); $d->lt($end); $d->addDay()) {
            if ($d->isSunday()) {
                $count++;
            }
        }
        return $count;
    }

    // ✅ Aplica tu regla: 🟡 0–9 | 🔴 10+ (solo si saldo>0 y ya devolvió)
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
    $arriendo->load([
        'cliente',
        'producto',
        'incidencias',   // asegúrate de tener esta relación en Arriendo.php
        'devoluciones'   // relación hacia devoluciones_arriendo
    ]);

    return view('arriendos.detalles', compact('arriendo'));
}


}
