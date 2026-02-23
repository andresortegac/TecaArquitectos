<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        .meta { margin-bottom: 12px; color: #444; }
        .kpis { width: 100%; margin-bottom: 10px; }
        .kpis td { border: 1px solid #ddd; padding: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; vertical-align: top; }
        th { background: #f3f4f6; font-size: 10px; }
        .right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h1>Reporte Detallado por Cliente (RF-31)</h1>
    <div class="meta">
        Periodo: {{ $filters['fecha_desde'] }} a {{ $filters['fecha_hasta'] }}<br>
        Cliente: {{ $clienteSeleccionado?->nombre ?? 'Todos' }}
    </div>

    <table class="kpis">
        <tr>
            <td><strong>Costo alquiler:</strong> ${{ number_format($resumen['total_alquiler'], 0) }}</td>
            <td><strong>Costo transporte:</strong> ${{ number_format($resumen['total_transporte'], 0) }}</td>
            <td><strong>Descuentos:</strong> ${{ number_format($resumen['total_descuentos'], 0) }}</td>
        </tr>
        <tr>
            <td><strong>Pérdidas/mantenimiento:</strong> ${{ number_format($resumen['total_perdidas'], 0) }}</td>
            <td><strong>Pagos/abonos:</strong> ${{ number_format($resumen['total_pagado'], 0) }}</td>
            <td><strong>Saldo final:</strong> ${{ number_format($resumen['saldo_final'], 0) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Obra</th>
                <th>Herramienta</th>
                <th>Fechas</th>
                <th class="center">Días cobrados</th>
                <th class="center">Días no cobrados</th>
                <th class="right">Alquiler</th>
                <th class="right">Transporte</th>
                <th class="right">Descuentos</th>
                <th class="right">Pérdidas</th>
                <th class="right">Pagos</th>
                <th class="right">Saldo final</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $fila)
                <tr>
                    <td>{{ $fila->cliente }}</td>
                    <td>{{ $fila->obra }}</td>
                    <td>{{ $fila->herramienta }}</td>
                    <td>{{ $fila->fecha_alquiler }} - {{ $fila->fecha_devolucion }}</td>
                    <td class="center">{{ $fila->dias_cobrados }}</td>
                    <td class="center">{{ $fila->dias_no_cobrados }}</td>
                    <td class="right">${{ number_format($fila->costo_alquiler, 0) }}</td>
                    <td class="right">${{ number_format($fila->costo_transporte, 0) }}</td>
                    <td class="right">${{ number_format($fila->descuentos_aplicados, 0) }}</td>
                    <td class="right">${{ number_format($fila->costos_perdidas_mantenimiento, 0) }}</td>
                    <td class="right">${{ number_format($fila->pagos_abonos, 0) }}</td>
                    <td class="right">${{ number_format($fila->saldo_final, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center">No hay registros para el periodo seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
