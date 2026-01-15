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

            <div class="cliente-card-header">
                <div class="cliente-avatar">
                    {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                </div>

                <div class="cliente-header-info">
                    <h2 class="cliente-nombre">{{ $cliente->nombre }}</h2>
                    <span class="cliente-subtitle">Cliente registrado</span>
                </div>
            </div>

            <div class="cliente-card-body">
                <div class="cliente-item">
                    <span class="label">Documento</span>
                    <span class="value">{{ $cliente->documento ?? '—' }}</span>
                </div>

                <div class="cliente-item">
                    <span class="label">Celular</span>
                    <span class="value">{{ $cliente->telefono ?? '—' }}</span>
                </div>

                <div class="cliente-item">
                    <span class="label">Correo</span>
                    <span class="value">{{ $cliente->email ?? '—' }}</span>
                </div>
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

                        {{-- Header --}}
                        <div class="obra-card-header">
                            <div class="obra-icon">🏗️</div>

                            <div class="obra-header-info">
                                <h4 class="obra-title">{{ $obra->direccion }}</h4>
                                <span class="obra-subtitle">Obra asociada al cliente</span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="obra-card-body">
                            <p class="obra-text">
                                {{ $obra->detalle ?? 'Sin descripción' }}
                            </p>
                        </div>

                        {{-- Acciones --}}
                        <div class="obra-card-actions">

                            {{-- Editar --}}
                            <a href="{{ route('obras.edit', [$cliente, $obra]) }}"
                            class="btn-obra btn-editar">
                                Editar
                            </a>

                            {{-- Eliminar (solo admin) --}}
                            @role('admin')
                                <form class="form-eliminar"
                                    action="{{ route('obras.destroy', [$cliente, $obra]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn-obra btn-eliminar"
                                        onclick="return confirm('¿Eliminar esta obra?')">
                                        Eliminar
                                    </button>
                                </form>
                            @endrole

                        </div>

                    </div>

                @endforeach
            </div>
        @else
            <p class="sin-obras">No hay obras registradas</p>
        @endif


    </div>

@endsection
