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
        <select name="obra_id" id="obra_id" class="input" style="width:100%;">
            <option value="">Seleccione cliente primero...</option>
        </select>
    </div>


    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:12px;">
        <button type="submit" class="btn-sm warning">Siguiente</button>
    </div>
</form>

<div style="font-size:12px; color:#666; margin-top:10px;">
    Nota: En este paso solo creas el arriendo PADRE (contrato). Luego podrás agregar productos dentro del arriendo.
</div>
<script>
document.querySelector('select[name="cliente_id"]').addEventListener('change', function () {
    const clienteId = this.value;
    const obraSelect = document.getElementById('obra_id');

    obraSelect.innerHTML = '<option value="">Cargando...</option>';

    if (!clienteId) {
        obraSelect.innerHTML = '<option value="">Seleccione cliente primero...</option>';
        return;
    }

    fetch(`/clientes/${clienteId}/obras`)
        .then(res => res.json())
        .then(data => {
            obraSelect.innerHTML = '<option value="">Seleccione...</option>';
            data.forEach(o => {
                obraSelect.innerHTML += `<option value="${o.id}">${o.direccion}</option>`;
            });
        });
});
</script>

@endsection
