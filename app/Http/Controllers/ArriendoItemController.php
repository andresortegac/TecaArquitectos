<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\ArriendoItem;
use App\Models\Producto;
use Illuminate\Http\Request;

class ArriendoItemController extends Controller
{
    public function create(Arriendo $arriendo)
    {
        $arriendo->load(['cliente', 'items.producto']);

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
        ]);

        $inicioItem = $data['fecha_inicio_item'] ?? $arriendo->fecha_inicio;

        $producto = Producto::findOrFail($data['producto_id']);
        $tarifa = (float)($producto->costo ?? 0);

        ArriendoItem::create([
            'arriendo_id' => $arriendo->id,
            'producto_id' => $data['producto_id'],

            'tarifa_diaria' => $tarifa,

            'cantidad_inicial' => $data['cantidad'],
            'cantidad_actual' => $data['cantidad'],

            'fecha_inicio_item' => $inicioItem,
            'fecha_fin_item' => null,

            'cerrado' => 0,
            'estado' => 'activo',

            'precio_total' => 0,
            'total_alquiler' => 0,
            'total_merma' => 0,
            'total_pagado' => 0,
            'saldo' => 0,
        ]);

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
        $item->load(['arriendo', 'devoluciones']);

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

        $item->delete();

        // ✅ recalcular totales del padre
        $this->recalcularTotalesPadre($arriendo);

        return redirect()->route('arriendos.ver', $arriendo)
            ->with('success', 'Item eliminado correctamente.');
    }

    private function recalcularTotalesPadre(Arriendo $arriendo): void
    {
        $arriendo->load('items');

        $totalAlquiler = (float)$arriendo->items->sum('total_alquiler');
        $totalMerma    = (float)$arriendo->items->sum('total_merma');
        $totalPagado   = (float)$arriendo->items->sum('total_pagado');

        $precioTotal = $totalAlquiler + $totalMerma;
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
