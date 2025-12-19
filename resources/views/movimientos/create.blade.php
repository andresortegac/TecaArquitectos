@extends('layouts.app')

@section('title', 'Movimientos')
@section('header', 'Registro de Movimientos')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="mov-card">
    <div class="mov-card-header">
        Nuevo Movimiento
    </div>

    <div class="mov-card-body">
        <form method="POST" action="{{ route('movimientos.store') }}">
            @csrf

            <div class="mov-grid">

                <div class="form-group">
                    <label>Producto *</label>
                    <select name="producto_id" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}">
                                {{ $producto->nombre }} (Stock: {{ $producto->cantidad }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" class="form-control"
                           value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label>Tipo de Movimiento *</label>
                    <select name="tipo" class="form-control" required>
                        <option value="ingreso">Ingreso</option>
                        <option value="salida">Salida</option>
                        <option value="ajuste_positivo">Ajuste Positivo</option>
                        <option value="ajuste_negativo">Ajuste Negativo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" class="form-control" required>
                </div>

                <div class="form-group full">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3"></textarea>
                </div>

            </div>

            <div class="mov-actions">
                <button class="btn btn-success">Guardar Movimiento</button>
                <button type="reset" class="btn btn-secondary">Limpiar</button>
            </div>

        </form>
    </div>
</div>
<hr>

<h4 class="mov-title">Historial de Movimientos</h4>

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
                        <span class="badge {{ $mov->tipo }}">
                            {{ ucfirst(str_replace('_', ' ', $mov->tipo)) }}
                        </span>
                    </td>
                    <td>{{ $mov->cantidad }}</td>
                    <td>{{ $mov->observaciones ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay movimientos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
