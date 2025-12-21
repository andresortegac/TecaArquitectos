<?php
namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;
use App\Exports\ReporteMensualExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportesStockController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function movimientos()
    {
        $movimientos = Movimiento::with('producto')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes.movimientos', compact('movimientos'));
    }

    public function mensual()
    {
        $reporte = Movimiento::selectRaw('
                YEAR(created_at) as anio,
                MONTH(created_at) as mes,
                tipo,
                SUM(cantidad) as total
            ')
            ->groupBy('anio', 'mes', 'tipo')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('reportes.mensual', compact('reporte'));
    }

        public function exportMensual()
    {
        return Excel::download(new ReporteMensualExport, 'reporte_mensual.xlsx');
    }
}
