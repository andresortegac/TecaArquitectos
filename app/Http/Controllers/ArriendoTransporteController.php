<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\ArriendoTransporte;
use Illuminate\Http\Request;

class ArriendoTransporteController extends Controller
{
    public function store(Request $request, Arriendo $arriendo)
    {
        if ((int)($arriendo->cerrado ?? 0) === 1 || $arriendo->estado !== 'activo') {
            return back()->with('success', 'El arriendo está cerrado o no está activo.');
        }

        $data = $request->validate([
            // ✅ En "ver": solo NO o ENTREGA
            'tipo'  => 'required|in:NO,ENTREGA,no,entrega',
            'fecha' => 'required|date', // en tu vista ya es required
            'valor' => 'required|numeric|min:0',
            'nota'  => 'nullable|string|max:255',
        ]);

        // ✅ normalizamos a mayúsculas
        $data['tipo'] = strtoupper($data['tipo']);

        // Si el usuario elige "NO", no registramos transporte.
        if ($data['tipo'] === 'NO') {
            return back()->with('success', 'Sin transporte: no se registró ningún cobro.');
        }

        $arriendo->transportes()->create($data);

        // ✅ recalcula total/saldo incluyendo transportes (y si IVA ya está activo, también)
        $this->recalcularTotalesPadre($arriendo->fresh());

        return back()->with('success', 'Transporte agregado.');
    }

    public function destroy(ArriendoTransporte $transporte)
    {
        $transporte->load('arriendo');

        $arriendo = $transporte->arriendo;

        if (!$arriendo) {
            return back()->with('success', 'No se encontró el arriendo del transporte.');
        }

        if ((int)($arriendo->cerrado ?? 0) === 1 || $arriendo->estado !== 'activo') {
            return back()->with('success', 'No puedes borrar: el arriendo está cerrado o no está activo.');
        }

        $transporte->delete();

        $this->recalcularTotalesPadre($arriendo->fresh());

        return back()->with('success', 'Transporte eliminado.');
    }

    private function recalcularTotalesPadre(Arriendo $arriendo): void
    {
        $arriendo->load(['items', 'transportes']);

        $totalAlquiler = (float)$arriendo->items->sum('total_alquiler');
        $totalMerma    = (float)$arriendo->items->sum('total_merma');
        $totalPagado   = (float)$arriendo->items->sum('total_pagado');

        $totalTransportes = (float)$arriendo->transportes->sum('valor');

        $subtotal = $totalAlquiler + $totalMerma + $totalTransportes;

        $ivaAplica = (int)($arriendo->iva_aplica ?? 0) === 1;
        $ivaRate   = (float)($arriendo->iva_rate ?? 0.19);
        $ivaValor  = $ivaAplica ? ($subtotal * $ivaRate) : 0;

        $precioTotal = $subtotal + $ivaValor;
        $saldo = max(0, $precioTotal - $totalPagado);

        $arriendo->update([
            'total_alquiler' => $totalAlquiler,
            'total_merma' => $totalMerma,
            'total_pagado' => $totalPagado,
            'precio_total' => $precioTotal,
            'saldo' => $saldo,
        ]);
    }
}
