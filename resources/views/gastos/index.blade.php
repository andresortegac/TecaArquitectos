@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gastos.css') }}">
@endpush

@section('title', 'Gastos')
@section('header', 'Control de gastos')

@section('content')
    <div class="gas-page">
        <section class="gas-hero">
            <div>
                <h2>Historial de gastos de la empresa</h2>
                <p>Control financiero de egresos con filtro por fecha y concepto.</p>
            </div>
            <div class="gas-hero-actions">
                <a href="{{ route('gastos.export.excel', request()->query()) }}" class="gas-btn-secondary">Exportar Excel</a>
                <a href="{{ route('gastos.export.pdf', request()->query()) }}" class="gas-btn-secondary">Exportar PDF</a>
                <a href="{{ route('gastos.create') }}" class="gas-btn-primary">Nuevo gasto</a>
            </div>
        </section>

        <section class="gas-kpis">
            <article><span>Registros</span><strong>{{ number_format($resumen['registros'] ?? 0) }}</strong></article>
            <article><span>Total filtrado</span><strong>${{ number_format($resumen['total'] ?? 0, 0) }}</strong></article>
            <article><span>Mes actual</span><strong>${{ number_format($resumen['mes_actual'] ?? 0, 0) }}</strong></article>
        </section>

        <section class="gas-card">
            <form method="GET" class="gas-filters">
                <div class="field field-grow">
                    <label for="q">Buscar</label>
                    <input
                        id="q"
                        type="text"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="Nombre o descripcion">
                </div>
                <div class="field">
                    <label for="fecha_desde">Desde</label>
                    <input id="fecha_desde" type="date" name="fecha_desde" value="{{ $filters['fecha_desde'] ?? '' }}">
                </div>
                <div class="field">
                    <label for="fecha_hasta">Hasta</label>
                    <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ $filters['fecha_hasta'] ?? '' }}">
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('gastos.index') }}">Limpiar</a>
                </div>
            </form>

            <div class="gas-table-wrap">
                <table class="gas-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Gasto</th>
                            <th>Descripcion</th>
                            <th class="right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastos as $gasto)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $gasto->nombre }}</td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td class="right">${{ number_format((float) $gasto->monto, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">No hay gastos para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($gastos->hasPages())
                <div class="gas-pagination">
                    @if($gastos->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $gastos->previousPageUrl() }}">Anterior</a>
                    @endif
                    <span class="page-text">Pagina {{ $gastos->currentPage() }} de {{ $gastos->lastPage() }}</span>
                    @if($gastos->hasMorePages())
                        <a class="page-btn" href="{{ $gastos->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="page-btn page-disabled">Siguiente</span>
                    @endif
                </div>
            @endif
        </section>
    </div>
@endsection
