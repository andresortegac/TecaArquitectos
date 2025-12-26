@extends('layouts.app')

@section('title','Revisar Solicitud')
@section('header','Revisar Solicitud')

@section('content')

<div class="card mb-4">
    <h4>Datos de la solicitud</h4>
    <p><strong>Cliente:</strong> {{ $arriendo->cliente->nombre }}</p>
    <p><strong>Obra:</strong> {{ $arriendo->obra->direccion }}</p>
    <p><strong>Fecha solicitud:</strong> {{ $arriendo->created_at->format('Y-m-d') }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($arriendo->estado) }}</p>
</div>

<form method="POST" action="{{ route('solicitudes.confirmar', $arriendo) }}">
@csrf

<table class="table table-shadow">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Aprobar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($arriendo->items as $item)
        <tr>
            <td>{{ $item->producto->nombre }}</td>
            <td>{{ $item->cantidad_inicial }}</td>
            <td>
                <input type="checkbox" name="items[{{ $item->id }}][aprobado]" value="1">

                <input type="hidden" name="items[{{ $item->id }}][producto_id]"
                       value="{{ $item->producto_id }}">

                <input type="hidden" name="items[{{ $item->id }}][cantidad_solicitada]"
                       value="{{ $item->cantidad_inicial }}">
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="text-align:right;">
    <button class="btn btn-success">
        Confirmar
    </button>
</div>

</form>

@endsection
