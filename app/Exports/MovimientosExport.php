<?php

namespace App\Exports;

use App\Models\Movimiento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MovimientosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    public function collection()
    {
        return Movimiento::with('producto')->get()->map(function ($mov) {
            return [
                $mov->created_at->format('d/m/Y'),
                $mov->producto->nombre,
                strtoupper($mov->tipo),
                $mov->cantidad,
            ];
        });
    }

    // La tabla empieza abajo de los datos de la empresa
    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Producto',
            'Tipo',
            'Cantidad',
        ];
    }

    // 👇 INFO DE LA EMPRESA
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'TECA ARQUITECTOS');
                $sheet->setCellValue('A2', 'NIT: 900123456-7');
                $sheet->setCellValue('A3', 'Reporte de Entradas y Salidas');
                $sheet->setCellValue('A4', 'Fecha: ' . now()->format('d/m/Y'));

                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
            },
        ];
    }
}
