<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class GastoController extends Controller
{
    public function create()
    {
        return view('gastos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'monto'       => 'required|numeric|min:0',
            'fecha'       => 'required|date',
        ]);

        Gasto::create($data);

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente');
    }

    public function index()
    {
        $filters = $this->validatedFilters(request());
        $query = $this->filteredQuery($filters);

        $gastos = (clone $query)->paginate(20)->withQueryString();

        $resumen = [
            'registros' => (clone $query)->count(),
            'total' => (float) (clone $query)->sum('monto'),
            'mes_actual' => (float) (clone $query)
                ->whereBetween('fecha', [now()->startOfMonth()->toDateString(), now()->toDateString()])
                ->sum('monto'),
        ];

        return view('gastos.index', compact('gastos', 'resumen', 'filters'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $gastos = $this->filteredQuery($filters)->get();

        $filename = 'gastos_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($gastos) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Fecha', 'Gasto', 'Descripcion', 'Monto']);

            foreach ($gastos as $gasto) {
                fputcsv($output, [
                    \Illuminate\Support\Carbon::parse($gasto->fecha)->format('Y-m-d'),
                    $gasto->nombre,
                    $gasto->descripcion,
                    number_format((float) $gasto->monto, 2, '.', ''),
                ]);
            }

            fclose($output);
        }, $filename, $headers);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $gastos = $this->filteredQuery($filters)->get();

        $resumen = [
            'registros' => $gastos->count(),
            'total' => (float) $gastos->sum('monto'),
        ];

        $pdf = Pdf::loadView('gastos.pdf', compact('gastos', 'resumen', 'filters'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('gastos_' . now()->format('Ymd_His') . '.pdf');
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'q' => 'nullable|string|max:120',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        ]);
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = Gasto::query()
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('descripcion', 'like', '%' . $filters['q'] . '%');
            });
        }

        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha', '<=', $filters['fecha_hasta']);
        }

        return $query;
    }
}
