@extends('layouts.app')
@section('title','Editar producto')
@section('header','Editar producto')

@section('content')
<h2>Editar producto</h2>

<form action="{{ route('productos.update',$producto) }}" method="POST" class="form">
    @csrf @method('PUT')

    <label>Nombre</label>
    <input name="nombre" value="{{ old('nombre',$producto->nombre) }}" required>

    <label>Categoría</label>
    <input name="categoria" value="{{ old('categoria',$producto->categoria) }}">

    <label>Cantidad</label>
    <input type="number" name="cantidad" min="0"
           value="{{ old('cantidad',$producto->cantidad) }}" required>

    <label>Costo</label>
    <input type="number" step="0.01" name="costo"
           value="{{ old('costo',$producto->costo) }}" required>

    <label>Ubicación</label>
    <input name="ubicacion" value="{{ old('ubicacion',$producto->ubicacion) }}">

    <label>Estado</label>
    <select name="estado" required>
        @foreach(['disponible','dañado','reservado'] as $e)
            <option value="{{ $e }}"
                @selected(old('estado',$producto->estado)==$e)>
                {{ ucfirst($e) }}
            </option>
        @endforeach
    </select>

    <div class="form-actions">
        <button class="btn">Actualizar</button>
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
