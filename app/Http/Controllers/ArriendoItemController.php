<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\ArriendoItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArriendoItemController extends Controller
{
    public function create(Arriendo $arriendo)
    {
        $arriendo->load(['cliente', 'items.producto', 'transportes']);

        if ((int)($arriendo->cerrado ?? 0) === 1 || $arriendo->estado !== 'activo') {
            return redirect()->route('arriendos.index')
                ->with('success', 'Este arriendo padre ya está cerrado o no está activo.');
        }

        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.items_create', compact('arriendo', 'productos'));
    }

    public function store(Request $request, Arriendo $arriendo)
    {
        if ((int)($arriendo->cerrado ?? 0) === 1 || $arriendo->estado !== 'activo') {
            return redirect()->route('arriendos.index')
                ->with('success', 'Este arriendo padre ya está cerrado o no está activo.');
        }

        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'fecha_inicio_item' => 'nullable|date',

            // ✅ NUEVO: viene desde la vista (select)
            'cobra_domingo' => 'required|in:0,1',
        ]);

        // ✅ si no envían fecha_inicio_item, usa la del padre
        $inicioItem = $data['fecha_inicio_item'] ?? $arriendo->fecha_inicio;
        $cantidadSolicitada = (int)$data['cantidad'];
        $cobraDomingo = (int)$data['cobra_domingo']; // 0 o 1

        try {
            DB::transaction(function () use ($data, $arriendo, $inicioItem, $cantidadSolicitada, $cobraDomingo) {

                // 🔒 Bloqueo para evitar carreras (dos usuarios alquilando a la vez)
                $producto = Producto::where('id', $data['producto_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $stockDisponible = (int)($producto->cantidad ?? 0);

                // ✅ VALIDACIÓN DE STOCK
                if ($cantidadSolicitada > $stockDisponible) {
                    throw new \Exception("No hay suficiente stock de {$producto->nombre}. Disponible: {$stockDisponible}. Solicitado: {$cantidadSolicitada}.");
                }

                $tarifa = (float)($producto->costo ?? 0);

                // ✅ Crear item del arriendo
                ArriendoItem::create([
                    'arriendo_id' => $arriendo->id,
                    'producto_id' => $data['producto_id'],

                    'tarifa_diaria' => $tarifa,

                    'cantidad_inicial' => $cantidadSolicitada,
                    'cantidad_actual' => $cantidadSolicitada,

                    'fecha_inicio_item' => $inicioItem,
                    'fecha_fin_item' => null,

                    'cerrado' => 0,
                    'estado' => 'activo',

                    // ✅ NUEVO: regla domingos por item
                    'cobra_domingo' => $cobraDomingo,

                    'precio_total' => 0,
                    'total_alquiler' => 0,
                    'total_merma' => 0,
                    'total_pagado' => 0,
                    'saldo' => 0,
                ]);

                // ✅ Descontar stock
                $producto->update([
                    'cantidad' => $stockDisponible - $cantidadSolicitada
                ]);
            });

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'cantidad' => $e->getMessage()
                ]);
        }

        // ✅ Recalcular totales del PADRE después de agregar el item
        $this->recalcularTotalesPadre($arriendo->fresh());

        return redirect()->route('arriendos.ver', $arriendo)
            ->with('success', 'Producto agregado al arriendo padre.');
    }

    /**
     * ✅ BORRAR ITEM (ALQUILER) DEL PADRE
     * - Solo permite borrar si NO tiene devoluciones (historial).
     * - Recalcula totales del padre después de borrar.
     */
    public function destroy(ArriendoItem $item)
    {
        $item->load(['arriendo', 'devoluciones', 'producto']);

        // si el padre ya está cerrado, no dejar borrar
        if ((int)($item->arriendo->cerrado ?? 0) === 1 || $item->arriendo->estado !== 'activo') {
            return redirect()->route('arriendos.ver', $item->arriendo_id)
                ->with('success', 'No puedes borrar: el contrato padre está cerrado o no activo.');
        }

        // si ya tiene devoluciones, no dejar borrar
        if ($item->devoluciones && $item->devoluciones->count() > 0) {
            return redirect()->route('arriendos.ver', $item->arriendo_id)
                ->with('success', 'No puedes borrar este item porque ya tiene devoluciones registradas.');
        }

        $arriendo = $item->arriendo;

        // ✅ devolver stock al borrar el item (como no tiene devoluciones)
        try {
            DB::transaction(function () use ($item) {
                $producto = Producto::where('id', $item->producto_id)->lockForUpdate()->first();
                if ($producto) {
                    $producto->update([
                        'cantidad' => (int)($producto->cantidad ?? 0) + (int)($item->cantidad_inicial ?? 0)
                    ]);
                }

                $item->delete();
            });
        } catch (\Exception $e) {
            return redirect()->route('arriendos.ver', $arriendo)
                ->with('success', 'No se pudo eliminar el item. Intenta nuevamente.');
        }

        // ✅ recalcular totales del padre
        $this->recalcularTotalesPadre($arriendo->fresh());

        return redirect()->route('arriendos.ver', $arriendo)
            ->with('success', 'Item eliminado correctamente.');
    }

    /**
     * ✅ REGLA NUEVA:
     * Total del PADRE = (items alquiler + items merma) + (transportes) + (IVA si aplica)
     */
    private function recalcularTotalesPadre(Arriendo $arriendo): void
    {
        $arriendo->load(['items', 'transportes']);

        $totalAlquiler = (float)$arriendo->items->sum('total_alquiler');
        $totalMerma    = (float)$arriendo->items->sum('total_merma');
        $totalPagado   = (float)$arriendo->items->sum('total_pagado');

        $totalTransportes = (float)($arriendo->transportes?->sum('valor') ?? 0);

        $subtotal = $totalAlquiler + $totalMerma + $totalTransportes;

        $ivaAplica = (int)($arriendo->iva_aplica ?? 0) === 1;
        $ivaRate   = (float)($arriendo->iva_rate ?? 0.19);
        $ivaValor  = $ivaAplica ? ($subtotal * $ivaRate) : 0;

        $precioTotal = $subtotal + $ivaValor;
        $saldo       = max(0, $precioTotal - $totalPagado);

        $todosCerrados = $arriendo->items->count() > 0
            ? $arriendo->items->every(fn($it) => (int)($it->cerrado ?? 0) === 1 || $it->estado !== 'activo')
            : false;

        $arriendo->update([
            'total_alquiler' => $totalAlquiler,
            'total_merma' => $totalMerma,
            'total_pagado' => $totalPagado,
            'precio_total' => $precioTotal,
            'saldo' => $saldo,
            'estado' => $todosCerrados ? 'devuelto' : 'activo',
            'cerrado' => $todosCerrados ? 1 : 0,
        ]);
    }
}
