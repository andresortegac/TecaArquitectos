@extends('layouts.app')
@section('title','Nuevo Cliente')
@section('header','Nuevo Cliente')

@section('content')
<form action="{{ route('clientes.store') }}" method="POST" class="form">
    @csrf

    <label>Nombre</label>
    <input name="nombre" required>

    <label>Celular</label>
    <input name="telefono">

    <label>Email</label>
    <input name="email">

    <label>Documento</label>
    <input name="documento">

    <br>

    <button class="btn">Guardar</button>
</form>
@endsection
