@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/crear-obras.css') }}">
@endpush

@section('title','Cliente')
@section('header','Cliente')

@section('content')

<div class="cliente-container">

    {{-- Información del cliente --}}
    <div class="cliente-card">
        <h2 class="cliente-nombre">{{ $cliente->nombre }}</h2>

        <div class="cliente-info">
            <p><strong>Documento:</strong> {{ $cliente->documento }}</p>
            <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
        </div>
    </div>

    {{-- Header obras --}}
    <div class="obras-header">
        <h3>Obras</h3>
        <a class="btn-nueva-obra" href="{{ route('obras.create', $cliente) }}">
            + Nueva obra
        </a>
    </div>

    {{-- Grid de obras --}}
    @if($cliente->obras->count())
        <div class="obras-grid">
            @foreach($cliente->obras as $obra)
                <div class="obra-card">
                    <div class="obra-card-body">
                        <h4 class="obra-title">{{ $obra->direccion }}</h4>
                        <p class="obra-text">{{ $obra->detalle }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="sin-obras">No hay obras registradas</p>
    @endif

</div>

@endsection
