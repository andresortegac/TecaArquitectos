@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-movimientos.css') }}">
@endpush

@section('title', 'Reporte de movimientos')
@section('header', 'Reporte de movimientos de inventario')

@section('content')
    @php
        $tipoLabels = [
            'ingreso' => 'Ingreso',
            'salida' => 'Salida',
            'ajuste_positivo' => 'Ajuste positivo',
            'ajuste_negativo' => 'Ajuste negativo',
        ];

        $tipoClasses = [
            'ingreso' => 'tipo tipo-ingreso',
            'salida' => 'tipo tipo-salida',
            'ajuste_positivo' => 'tipo tipo-ajuste-pos',
            'ajuste_negativo' => 'tipo tipo-ajuste-neg',
        ];
    @endphp

    <div class="rep-mov-page">
        <section class="rep-mov-hero">
            <div>
                <h2>Control operativo de entradas y salidas</h2>
                <p>Consulta, filtra y exporta el historial de movimientos con trazabilidad por fecha, tipo y producto.</p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('reportes.index') }}" class="btn-volver">Volver</a>
                <a href="{{ route('movimientos.export') }}" class="btn-exportar">Exportar a Excel</a>
            </div>
        </section>

        <section class="rep-mov-kpis">
            <article class="kpi-card">
                <span class="kpi-label">Registros</span>
                <strong class="kpi-value">{{ number_format($resumen['total_registros']) }}</strong>
            </article>
            <article class="kpi-card">
                <span class="kpi-label">Unidades movidas</span>
                <strong class="kpi-value">{{ number_format($resumen['total_unidades']) }}</strong>
            </article>
            <article class="kpi-card">
                <span class="kpi-label">Entradas y ajustes positivos</span>
                <strong class="kpi-value kpi-ok">{{ number_format($resumen['entradas']) }}</strong>
            </article>
            <article class="kpi-card">
                <span class="kpi-label">Salidas y ajustes negativos</span>
                <strong class="kpi-value kpi-risk">{{ number_format($resumen['salidas']) }}</strong>
            </article>
        </section>

        <section class="rep-mov-panel">
            <form method="GET" class="rep-mov-filters">
                <div class="field">
                    <label for="tipo">Tipo</label>
                    <select id="tipo" name="tipo">
                        <option value="">Todos</option>
                        @foreach($tipoLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('tipo') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="fecha_desde">Desde</label>
                    <input id="fecha_desde" type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                </div>

                <div class="field">
                    <label for="fecha_hasta">Hasta</label>
                    <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="field field-grow">
                    <label for="producto">Producto</label>
                    <input
                        id="producto"
                        type="text"
                        name="producto"
                        value="{{ request('producto') }}"
                        placeholder="Buscar por nombre de producto">
                </div>

                <div class="actions">
                    <button type="submit" class="btn-filtrar">Filtrar</button>
                    <a href="{{ route('reportes.movimientos') }}" class="btn-limpiar">Limpiar</a>
                </div>
            </form>

            @if($errors->any())
                <div class="error-box">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="table-wrap">
                <table class="rep-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th class="right">Cantidad</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($movimiento->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $movimiento->producto->nombre ?? 'Producto no disponible' }}</td>
                                <td>
                                    <span class="{{ $tipoClasses[$movimiento->tipo] ?? 'tipo' }}">
                                        {{ $tipoLabels[$movimiento->tipo] ?? ucfirst(str_replace('_', ' ', $movimiento->tipo)) }}
                                    </span>
                                </td>
                                <td class="right">{{ number_format($movimiento->cantidad) }}</td>
                                <td>{{ $movimiento->observaciones ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty">No hay movimientos para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movimientos->hasPages())
                <div class="pagination-row">
                    @if($movimientos->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $movimientos->previousPageUrl() }}">Anterior</a>
                    @endif

                    <span class="page-text">
                        Página {{ $movimientos->currentPage() }} de {{ $movimientos->lastPage() }}
                    </span>

                    @if($movimientos->hasMorePages())
                        <a class="page-btn" href="{{ $movimientos->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="page-btn page-disabled">Siguiente</span>
                    @endif
                </div>
            @endif
        </section>
    </div>
@endsection
