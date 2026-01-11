@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/form-crear-obra.css') }}">
@endpush

@section('title','Nueva obra')
@section('header','Nueva obra')

@section('content')

<h2>Nueva obra para {{ $cliente->nombre }}</h2>

<form method="POST" action="{{ route('obras.store', $cliente) }}">
    @csrf

    <label>Dirección</label>
    <input type="text" name="direccion" required>

    <label>Detalle</label>
    <textarea name="detalle"></textarea>

    <div class="form-actions">
        <button class="btn">Guardar</button>
        <a class="btn-secondary" href="{{ route('clientes.show', $cliente) }}">
            Volver
        </a>

    </div>
</form>

@endsection
