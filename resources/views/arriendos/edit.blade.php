@extends('layouts.app')
@section('title','Editar arriendo')
@section('header','Editar arriendo')

@section('content')
<h2>Editar arriendo</h2>

<form action="{{ route('arriendos.update',$arriendo) }}" method="POST" class="form">
    @csrf @method('PUT')

    <label>Cliente</label>
    <select name="cliente_id" required>
        @foreach($clientes as $c)
            <option value="{{ $c->id }}"
                @selected(old('cliente_id',$arriendo->cliente_id)==$c->id)>
                {{ $c->nombre }}
            </option>
        @endforeach
    </select>

    <label>Producto</label>
    <select name="producto_id" required>
        @foreach($productos as $p)
            <option value="{{ $p->id }}"
                @selected(old('producto_id',$arriendo->producto_id)==$p->id)>
                {{ $p->nombre }}
            </option>
        @endforeach
    </select>

    <label>Cantidad</label>
    <input type="number" name="cantidad" min="1"
           value="{{ old('cantidad',$arriendo->cantidad) }}" required>

    <label>Fecha inicio</label>
    <input type="date" name="fecha_inicio"
           value="{{ old('fecha_inicio',$arriendo->fecha_inicio) }}" required>

    <label>Fecha fin</label>
    <input type="date" name="fecha_fin"
           value="{{ old('fecha_fin',$arriendo->fecha_fin) }}">

    <label>Precio total</label>
    <input type="number" step="0.01" name="precio_total"
           value="{{ old('precio_total',$arriendo->precio_total) }}" required>

    <label>Estado</label>
    <select name="estado" required>
        @foreach(['activo','devuelto','vencido'] as $e)
            <option value="{{ $e }}"
                @selected(old('estado',$arriendo->estado)==$e)>
                {{ ucfirst($e) }}
            </option>
        @endforeach
    </select>

    <div class="form-actions">
        <button class="btn">Actualizar</button>
        <a class="btn-secondary" href="{{ route('arriendos.index') }}">Volver</a>
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
