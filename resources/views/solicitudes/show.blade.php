@extends('layouts.app')

@section('title','Revisión')
@section('header','Revisión de Solicitud')

@section('content')

<h2>{{ $solicitud->cliente_nombre }}</h2>
<p><strong>Obra:</strong> {{ $solicitud->obra_nombre }}</p>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Solicitado</th>
            <th>Aprobar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitud->productos as $sp)
        <tr>
            <td>{{ $sp->producto->nombre }}</td>
            <td>{{ $sp->cantidad_solicitada }}</td>
            <td>
                <input type="number"
                       min="0"
                       max="{{ $sp->producto->cantidad }}"
                       value="{{ $sp->cantidad_solicitada }}">
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
