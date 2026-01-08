@extends('layouts.app')

@section('title','Nuevo Gasto')
@section('header','Registrar Gasto')

@section('content')

<form method="POST" action="{{ route('gastos.store') }}" class="form">
@csrf

<label>Nombre del gasto</label>
<input type="text" name="nombre" required>

<label>Descripción</label>
<textarea name="descripcion" required></textarea>

<label>Monto</label>
<input type="number" step="0.01" name="monto" required>

<label>Fecha</label>
<input type="date" name="fecha" required>

<button class="btn btn-success">Guardar</button>

</form>

@endsection
