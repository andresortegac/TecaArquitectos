@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/form-crear-obra.css') }}">
@endpush

@section('title','Editar obra')
@section('header','Editar obra')

@section('content')

<h2>Editar obra de {{ $cliente->nombre }}</h2>

<form method="POST" action="{{ route('obras.update', [$cliente, $obra]) }}">
    @csrf
    @method('PUT')

    <label>Dirección</label>
    <input type="text" name="direccion"
           value="{{ old('direccion', $obra->direccion) }}"
           required>

    <label>Detalle</label>
    <textarea name="detalle">{{ old('detalle', $obra->detalle) }}</textarea>

    <div class="form-actions">
        <button class="btn">Actualizar</button>

        <a class="btn-secondary"
           href="{{ route('clientes.show', $cliente) }}">
            Cancelar
        </a>
    </div>
</form>

@endsection
