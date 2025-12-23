@extends('layouts.app')
@section('title','Cliente')
@section('header','Cliente')

@section('content')
<h2>{{ $cliente->nombre }}</h2>

<p><strong>Documento:</strong> {{ $cliente->documento }}</p>
<p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>

<hr>

<div style="display:flex; justify-content:space-between;">
    <h3>Obras</h3>
    <a class="btn" href="{{ route('obras.create', $cliente) }}">+ Nueva obra</a>
</div>

@forelse($cliente->obras as $obra)
    <div class="card">
        <strong>{{ $obra->direccion }}</strong>
        <p>{{ $obra->detalle }}</p>
    </div>
@empty
    <p>No hay obras</p>
@endforelse
@endsection
