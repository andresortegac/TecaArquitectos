@extends('layouts.app')
@section('title','Editar cliente')
@section('header','Editar cliente')

@section('content')
<h2>Editar cliente</h2>

<form action="{{ route('clientes.update',$cliente) }}" method="POST" class="form">
    @csrf @method('PUT')

    <label>Nombre</label>
    <input name="nombre" value="{{ old('nombre',$cliente->nombre) }}" required>

    <label>Teléfono</label>
    <input name="telefono" value="{{ old('telefono',$cliente->telefono) }}">

    <label>Email</label>
    <input name="email" value="{{ old('email',$cliente->email) }}">

    <label>Documento</label>
    <input name="documento" value="{{ old('documento',$cliente->documento) }}">

    <div class="form-actions">
        <button class="btn">Actualizar</button>
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
