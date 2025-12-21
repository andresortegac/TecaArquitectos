<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReporteMensualExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    public function collection()
    {
        return DB::table('movimientos')
            ->selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, tipo, SUM(cantidad) as total')
            ->groupBy('anio', 'mes', 'tipo')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get()
            ->map(function ($r) {
                return [
                    $r->anio,
                    Carbon::create()->month($r->mes)->translatedFormat('F'),
                    strtoupper($r->tipo),
                    $r->total,
                ];
            });
    }

    // La tabla empieza después del encabezado de la empresa
    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [
            'Año',
            'Mes',
            'Tipo',
            'Total',
        ];
    }

    // Info de la empresa
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'TECA ARQUITECTOS');
                $sheet->setCellValue('A2', 'NIT: 900123456-7');
                $sheet->setCellValue('A3', 'Reporte General Mensual');
                $sheet->setCellValue('A4', 'Fecha: ' . now()->format('d/m/Y'));

                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
            },
        ];
    }
}
