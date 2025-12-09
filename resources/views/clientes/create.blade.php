@extends('layouts.app')
@section('title','Agregar cliente')
@section('header','Agregar cliente')

@section('content')
<h2>Nuevo cliente</h2>

<form action="{{ route('clientes.store') }}" method="POST" class="form">
    @csrf

    <label>Nombre</label>
    <input name="nombre" value="{{ old('nombre') }}" required>

    <label>Teléfono</label>
    <input name="telefono" value="{{ old('telefono') }}">

    <label>Email</label>
    <input name="email" value="{{ old('email') }}">

    <label>Documento</label>
    <input name="documento" value="{{ old('documento') }}">

    <div class="form-actions">
        <button class="btn">Guardar</button>
        <a class="btn-secondary" href="{{ route('clientes.index') }}">Volver</a>
    </div>

    @if($errors->any())
        <div class="alert danger">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>
@endsection
