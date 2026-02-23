@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gastos.css') }}">
@endpush

@section('title', 'Nuevo gasto')
@section('header', 'Registrar gasto')

@section('content')
    <div class="gas-page">
        <section class="gas-hero">
            <div>
                <h2>Registro de gasto</h2>
                <p>Ingresa egresos para mantener el control financiero actualizado.</p>
            </div>
            <a href="{{ route('gastos.index') }}" class="gas-btn-secondary">Volver</a>
        </section>

        <section class="gas-card">
            @if($errors->any())
                <div class="gas-errors">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('gastos.store') }}" class="gas-form">
                @csrf

                <div class="field">
                    <label for="nombre">Nombre del gasto</label>
                    <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" required>
                </div>

                <div class="field">
                    <label for="descripcion">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion') }}</textarea>
                </div>

                <div class="field">
                    <label for="monto">Monto</label>
                    <input id="monto" type="number" step="0.01" min="0" name="monto" value="{{ old('monto') }}" required>
                </div>

                <div class="field">
                    <label for="fecha">Fecha</label>
                    <input id="fecha" type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required>
                </div>

                <div class="actions">
                    <button type="submit">Guardar gasto</button>
                    <a href="{{ route('gastos.index') }}">Cancelar</a>
                </div>
            </form>
        </section>
    </div>
@endsection
