<?php

namespace App\Http\Controllers;

use App\Models\ArriendoItem;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * RF-28
     * Clientes pendientes por cancelar
     */
    public function clientesPendientes()
    {
        // =====================================================
        // 1️⃣ ÍTEMS CON SALDO PENDIENTE
        // =====================================================
        $items = ArriendoItem::with([
                'arriendo.cliente',
                'arriendo.obra'
            ])
            ->where('saldo', '>', 0)
            ->get();

        // =====================================================
        // 2️⃣ AGRUPAR POR CLIENTE
        // =====================================================
        $clientesMorosos = $items
            ->groupBy(fn($item) => $item->arriendo->cliente_id)
            ->map(function ($itemsCliente) {

                $cliente = $itemsCliente->first()->arriendo->cliente;

                // 🏗 Obras
                $obras = $itemsCliente
                    ->pluck('arriendo.obra.nombre')
                    ->filter()
                    ->unique()
                    ->implode(', ');

                // 💰 Total deuda
                $totalDeuda = $itemsCliente->sum('saldo');

                // 📦 # alquileres
                $alquileres = $itemsCliente->count();

                // 🕒 Último cobro
                $ultimoCobro = $itemsCliente
                    ->max('fecha_fin_item');

                // ⏱ Días mora
                $diasMora = $itemsCliente
                    ->map(function ($i) {
                        if (!$i->fecha_fin_item) return 0;
                        return Carbon::parse($i->fecha_fin_item)->diffInDays(now());
                    })
                    ->max();

                // 🚦 Estado
                $estado = $diasMora > 0 ? 'moroso' : 'al_dia';

                // 🚦 Nivel visual
                if ($diasMora >= 10) {
                    $nivel = 'rojo';
                } elseif ($diasMora > 0) {
                    $nivel = 'amarillo';
                } else {
                    $nivel = 'verde';
                }

                return (object)[
                    'nombre'                => $cliente->nombre,
                    'obras'                 => $obras ?: '—',
                    'alquileres_pendientes' => $alquileres,
                    'total_deuda'           => $totalDeuda,
                    'ultimo_cobro'          => $ultimoCobro
                        ? Carbon::parse($ultimoCobro)->format('d/m/Y')
                        : null,
                    'dias_mora'             => $diasMora,
                    'estado'                => $estado,
                    'nivel_mora'            => $nivel,
                ];
            })
            ->values();

        // =====================================================
        // 3️⃣ RESUMEN
        // =====================================================
        $resumen = [
            'clientes'    => $clientesMorosos->count(),
            'alquileres'  => $items->count(),
            'total_deuda' => $items->sum('saldo'),
        ];

        // =====================================================
        // 4️⃣ VISTA
        // =====================================================
        return view('reportes.clientes-pendientes', compact(
            'clientesMorosos',
            'resumen'
        ));
    }
}
