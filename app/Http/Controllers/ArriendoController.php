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
            $fechaDev = $a->fecha_devolucion_real ? Carbon::parse($a->fecha_devolucion_real)->toDateString() : null;
            $sem = $this->calcularSemaforoMoroso($fechaDev, (float)$a->saldo);

            // Solo actualizamos en memoria (para mostrar). Si quieres persistir en BD, lo hacemos también.
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

        return view('arriendos.create', compact('clientes','productos'));
    }

    /* ============================================================
     * 3) GUARDAR NUEVO ARRIENDO (STORE)
     *    - Aquí NO se calcula el total final
     *    - Se calcula al CERRAR (cuando devuelven)
     * ============================================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'producto_id'  => 'required|exists:productos,id',
            'cantidad'     => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            // obra fija (si lo manejas)
            'obra_id'      => 'nullable|integer',
        ]);

        Arriendo::create([
            'cliente_id' => $data['cliente_id'],
            'producto_id' => $data['producto_id'],
            'cantidad' => $data['cantidad'],

            // Puedes seguir usando fecha_inicio como inicio del arriendo
            'fecha_inicio' => $data['fecha_inicio'],

            // ✅ fecha_entrega = inicio real del cobro
            'fecha_entrega' => $data['fecha_inicio'],

            // si usas fecha_fin, puedes dejarla nula al crear
            'fecha_fin' => null,

            // obra
            'obra_id' => $data['obra_id'] ?? null,

            // ✅ estados: activo, devuelto, vencido (según tu validación previa)
            'estado' => 'activo',
            'cerrado' => 0,

            // totales iniciales
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

        return view('arriendos.edit', compact('arriendo','clientes','productos'));
    }

    /* ============================================================
     * 5) ACTUALIZAR (UPDATE)
     *    - Recomendación: no permitir modificar totales calculados manualmente
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
     * 7) CERRAR / DEVOLVER ARRIENDO (LO MÁS IMPORTANTE)
     *    - Pregunta incidencias (lluvia/daño)
     *    - Resta domingos automático
     *    - Resta días lluvia manual
     *    - Suma merma
     *    - Calcula saldo
     *    - Aplica semáforo morosos: amarillo 0–9, rojo 10+
     * ============================================================ */
    public function cerrar(Request $request, Arriendo $arriendo)
    {
        $data = $request->validate([
            'fecha_devolucion_real' => 'required|date',

            // Incidencia por lluvia (manual)
            'dias_lluvia' => 'nullable|integer|min:0',

            // Incidencia por daño/merma (manual)
            'costo_merma' => 'nullable|numeric|min:0',

            // Descripción opcional
            'descripcion_incidencia' => 'nullable|string|max:255',

            // Pago opcional al momento de devolver
            'pago' => 'nullable|numeric|min:0',
        ]);

        $arriendo->load('producto');

        // ✅ FECHA ENTREGA: si no está, usamos fecha_inicio
        $fechaEntrega = $arriendo->fecha_entrega ?? $arriendo->fecha_inicio;
        $fechaEntrega = Carbon::parse($fechaEntrega)->toDateString();

        // ✅ FECHA DEVOLUCIÓN REAL
        $fechaDevol = $data['fecha_devolucion_real'];

        // ✅ DÍAS TRANSCURRIDOS (incluye ambos días)
        $diasTrans = Carbon::parse($fechaEntrega)->diffInDays(Carbon::parse($fechaDevol)) + 1;

        // ✅ DOMINGOS AUTOMÁTICOS
        $domingos = $this->contarDomingos($fechaEntrega, $fechaDevol);

        // ✅ LLUVIA MANUAL (se descuenta)
        $diasLluvia = (int)($data['dias_lluvia'] ?? 0);

        // ✅ DÍAS COBRABLES
        $diasCobrables = max(0, $diasTrans - $domingos - $diasLluvia);

        // ✅ TARIFA DIARIA POR PRODUCTO
        $tarifa = (float)($arriendo->producto->tarifa_diaria ?? 0);

        // ✅ TOTAL ALQUILER (días trabajados)
        $totalAlquiler = $diasCobrables * $tarifa;

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
            'fecha_devolucion_real' => $fechaDevol,
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

            // si usas precio_total como total final
            'precio_total' => $totalFinal,

            'dias_mora' => $sem['dias_mora'],
            'semaforo_pago' => $sem['semaforo'],
        ]);

        /* ==========================
         * GUARDAR INCIDENCIAS (opcional pero recomendado)
         * ========================== */
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
     * 8) FUNCIONES INTERNAS (DOMINGOS Y SEMÁFORO)
     * ============================================================ */

    // ✅ Cuenta domingos entre dos fechas (incluyendo inicio y fin)
    private function contarDomingos(string $inicio, string $fin): int
    {
        $start = Carbon::parse($inicio)->startOfDay();
        $end   = Carbon::parse($fin)->startOfDay();
        $count = 0;

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isSunday()) $count++;
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
            // Aún no devolvió, no aplica morosidad
            return ['semaforo' => 'VERDE', 'dias_mora' => 0];
        }

        $hoy = Carbon::today();
        $dev = Carbon::parse($fechaDevolucionReal)->startOfDay();

        // Dias desde la devolución hasta hoy
        $dias_mora = max(0, $dev->diffInDays($hoy));

        if ($dias_mora <= 9) {
            return ['semaforo' => 'AMARILLO', 'dias_mora' => $dias_mora];
        }

        return ['semaforo' => 'ROJO', 'dias_mora' => $dias_mora];
    }
}
