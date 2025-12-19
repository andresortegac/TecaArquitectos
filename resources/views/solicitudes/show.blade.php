@extends('layouts.app')

@section('title','Revisión')
@section('header','Revisión de Solicitud')

@section('content')
 

<h2>Cliente: {{ $solicitud->nombre_cliente }}</h2>
<p><strong>Fecha:</strong> {{ $solicitud->fecha_solicitud }}</p>
<p><strong>Estado:</strong> {{ ucfirst(str_replace('_',' ',$solicitud->estado)) }}</p>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Solicitado</th>
            <th>Aprobar</th>
        </tr>
    </thead>
    <tbody>
      @foreach($solicitud->productos as $producto)
<tr>
    <td>{{ $producto->nombre }}</td>

    <td>{{ $producto->pivot->cantidad_solicitada }}</td>

    <td>
        <input type="number"
               min="0"
               max="{{ $producto->cantidad }}"
               value="{{ $producto->pivot->cantidad_solicitada }}">
    </td>
</tr>
@endforeach


    </tbody>
</table>

@endsection
