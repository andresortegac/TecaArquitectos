<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { margin: 0 0 6px; font-size: 18px; }
        .meta { margin-bottom: 10px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f3f4f6; font-size: 10px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Reporte de Gastos</h1>
    <div class="meta">
        Filtros:
        Buscar: {{ $filters['q'] ?? 'Todos' }} |
        Desde: {{ $filters['fecha_desde'] ?? '-' }} |
        Hasta: {{ $filters['fecha_hasta'] ?? '-' }}<br>
        Registros: {{ $resumen['registros'] }} |
        Total: ${{ number_format($resumen['total'], 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Gasto</th>
                <th>Descripcion</th>
                <th class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastos as $gasto)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($gasto->fecha)->format('Y-m-d') }}</td>
                    <td>{{ $gasto->nombre }}</td>
                    <td>{{ $gasto->descripcion }}</td>
                    <td class="right">${{ number_format((float) $gasto->monto, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">No hay registros</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
