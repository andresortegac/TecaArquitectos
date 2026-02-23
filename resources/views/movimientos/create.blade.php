@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/movimiento.css') }}">
@endpush

@section('title', 'Movimientos')
@section('header', 'Registro de Movimientos')

@section('content')
    <div class="mov-page">
        <section class="mov-hero">
            <div>
                <h2>Control de movimientos</h2>
                <p>Registra entradas, salidas y ajustes de inventario con trazabilidad por fecha y producto.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="mov-btn mov-btn-light">Volver</a>
        </section>

        @if(session('success'))
            <div class="mov-alert mov-alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mov-alert mov-alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="mov-card">
            <div class="mov-card-header">Nuevo movimiento</div>
            <div class="mov-card-body">
                <form method="POST" action="{{ route('movimientos.store') }}" class="mov-form">
                    @csrf

                    <div class="mov-grid">
                        <div class="form-group">
                            <label for="producto_id">Producto *</label>
                            <select id="producto_id" name="producto_id" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" @selected(old('producto_id') == $producto->id)>
                                        {{ $producto->nombre }} (Stock: {{ $producto->cantidad }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha *</label>
                            <input id="fecha" type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo de movimiento *</label>
                            <select id="tipo" name="tipo" class="form-control" required>
                                <option value="ingreso" @selected(old('tipo') === 'ingreso')>Ingreso</option>
                                <option value="salida" @selected(old('tipo') === 'salida')>Salida</option>
                                <option value="ajuste_positivo" @selected(old('tipo') === 'ajuste_positivo')>Ajuste positivo</option>
                                <option value="ajuste_negativo" @selected(old('tipo') === 'ajuste_negativo')>Ajuste negativo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cantidad">Cantidad *</label>
                            <input id="cantidad" type="number" name="cantidad" min="1" value="{{ old('cantidad') }}" class="form-control" required>
                        </div>

                        <div class="form-group full">
                            <label for="observaciones">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" rows="3">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>

                    <div class="mov-actions">
                        <button type="submit" class="mov-btn mov-btn-primary">Guardar movimiento</button>
                        <button type="reset" class="mov-btn mov-btn-light">Limpiar</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="mov-card">
            <div class="mov-card-header">Historial de movimientos</div>
            <div class="mov-table-wrapper">
                <table class="mov-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $mov)
                            <tr>
                                <td>{{ $mov->fecha }}</td>
                                <td>{{ $mov->producto->nombre }}</td>
                                <td>
                                    <span class="movi-badge {{ $mov->tipo }}">
                                        {{ ucfirst(str_replace('_', ' ', $mov->tipo)) }}
                                    </span>
                                </td>
                                <td>{{ $mov->cantidad }}</td>
                                <td>{{ $mov->observaciones ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay movimientos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
