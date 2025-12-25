@extends('layouts.app')
@section('title','Nueva solicitud arriendo')
@section('header','Nueva solicitud arriendo')

@section('content')

@if($errors->any())
    <div class="alert danger">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h2>Nueva solicitud arriendo (PADRE)</h2>
    <a class="btn-sm" href="{{ route('arriendos.index') }}">Volver</a>
</div>

<form method="POST" action="{{ route('arriendos.store') }}"
      style="background:#fff; padding:14px; border-radius:10px; max-width:520px;">
    @csrf

    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:13px;">Cliente</label>
        <select name="cliente_id" class="input" required style="width:100%;">
            <option value="">Seleccione...</option>
            @foreach($clientes as $c)
                <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:13px;">Fecha de inicio</label>
        <input class="input" type="datetime-local" name="fecha_inicio" required style="width:100%;"
               value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}">
    </div>

    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:13px;">Obra (opcional)</label>
        <input class="input" type="number" name="obra_id" style="width:100%;"
               value="{{ old('obra_id') }}">
    </div>

    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:12px;">
        <button type="submit" class="btn-sm warning">Siguiente</button>
    </div>
</form>

<div style="font-size:12px; color:#666; margin-top:10px;">
    Nota: En este paso solo creas el arriendo PADRE (contrato). Luego podrás agregar productos dentro del arriendo.
</div>

@endsection
