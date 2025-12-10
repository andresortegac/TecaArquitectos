@extends('layouts.app')
@section('title','Ingresar producto')
@section('header','Ingresar producto a bodega')

@section('content')
<h2>Nuevo producto</h2>

<form action="{{ route('productos.store') }}" method="POST" class="form">
    @csrf

    <label>Nombre</label>
    <input name="nombre" value="{{ old('nombre') }}" required>

    <label>Categoría</label>
    <input name="categoria" value="{{ old('categoria') }}">

    <label>Cantidad</label>
    <input type="number" name="cantidad" min="0"
           value="{{ old('cantidad',0) }}" required>

    <label>Costo</label>
    <input type="number" step="0.01" name="costo"
           value="{{ old('costo',0) }}" required>

    <label>Ubicación</label>
    <input name="ubicacion" value="{{ old('ubicacion') }}">

    <label>Estado</label>
    <select name="estado" required>
        @foreach(['disponible','dañado','reservado'] as $e)
            <option value="{{ $e }}" @selected(old('estado','disponible')==$e)>
                {{ ucfirst($e) }}
            </option>
        @endforeach
    </select>

    <div class="form-actions">
        <button class="btn">Guardar</button>
        <a class="btn-secondary" href="{{ route('productos.index') }}">Volver</a>
    </div>

    @if($errors->any())
        <div class="alert danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>
@endsection
