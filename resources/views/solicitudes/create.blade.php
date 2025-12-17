@extends('layouts.app')

@section('title','Nueva Solicitud')
@section('header','Nueva Solicitud de Alquiler')

@section('content')

<form action="{{ route('solicitudes.store') }}" method="POST" class="form">
@csrf

<label>Cliente</label>
<input name="cliente_nombre" required>

<label>Obra</label>
<input name="obra_nombre" required>

<label>Dirección de la obra</label>
<input name="obra_direccion" required>

<label>
    <input type="checkbox" name="usa_transporte">
    Usa transporte
</label>

<h3>Productos</h3>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Stock</th>
            <th>Cantidad</th>
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

<button class="btn">Enviar a bodega</button>
</form>

@endsection
