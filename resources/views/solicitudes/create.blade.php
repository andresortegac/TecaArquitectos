@extends('layouts.app')

@section('title','Nueva Solicitud')
@section('header','Nueva Solicitud de Alquiler')

@section('content')

<form action="{{ route('solicitudes.store') }}" method="POST" class="form">
@csrf

<label>Nombre del Cliente</label>
<input type="text" name="nombre_cliente" required>

<label>Teléfono del Cliente</label>
<input type="text" name="telefono_cliente" required>

<label>Fecha de Solicitud</label>
<input type="date" name="fecha_solicitud" required>

<h3>Productos solicitados</h3>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Stock</th>
            <th>Cantidad solicitada</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $producto)
        <tr>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->cantidad }}</td>
            <td>
                <input type="number"
                       min="0"
                       max="{{ $producto->cantidad }}"
                       name="productos[{{ $producto->id }}]"
                       value="0">
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<button class="btn btn-primary">Enviar Solicitud a Bodega</button>
</form>


@endsection
