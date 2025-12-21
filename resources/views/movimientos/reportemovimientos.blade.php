<table class="table table-bordered">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movimientos as $m)
            <tr class="{{ $m->tipo == 'salida' ? 'table-danger' : 'table-success' }}">
                <td>{{ $m->created_at->format('d/m/Y') }}</td>
                <td>{{ $m->producto->nombre }}</td>
                <td>{{ strtoupper($m->tipo) }}</td>
                <td>{{ $m->cantidad }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Año</th>
            <th>Mes</th>
            <th>Tipo</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reporte as $r)
            <tr>
                <td>{{ $r->anio }}</td>
                <td>{{ \Carbon\Carbon::create()->month($r->mes)->translatedFormat('F') }}</td>
                <td>{{ strtoupper($r->tipo) }}</td>
                <td>{{ $r->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

