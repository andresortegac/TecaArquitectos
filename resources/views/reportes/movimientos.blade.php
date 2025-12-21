@extends('layouts.app')


@section('content')
<div class="container1">
    <h2 class="mb-4">📦 Reporte de Entradas y Salidas</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $mov)
                <tr class="{{ $mov->tipo == 'salida' ? 'table-danger' : 'table-success' }}">
                    <td>{{ $mov->created_at->format('d/m/Y') }}</td>
                    <td>{{ $mov->producto->nombre }}</td>
                    <td>
                        @if($mov->tipo == 'entrada')
                            <span class="badge bg-success">ENTRADA</span>
                        @else
                            <span class="badge bg-danger">SALIDA</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $mov->cantidad }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        No hay movimientos registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
