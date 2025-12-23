<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Producto::all()->map(function ($producto) {

            // Estado calculado (igual que en la vista)
            if ($producto->cantidad == 0) {
                $estado = 'Sin Stock';
            } elseif ($producto->cantidad <= 10) {
                $estado = 'Stock Bajo';
            } else {
                $estado = 'Normal';
            }

            return [
                'codigo'        => $producto->id,
                'nombre'        => $producto->nombre,
                'unidad'        => 'Unidades',
                'categoria'     => $producto->categorias,
                'stock_min'     => 10,
                'stock_actual'  => $producto->cantidad,
                'estado'        => $estado,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
            'Unidad',
            'Categoría',
            'Stock Min.',
            'Stock Actual',
            'Estado',
        ];
    }
}
