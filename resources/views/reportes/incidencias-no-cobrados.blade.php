@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-incidencias.css') }}?v={{ filemtime(public_path('css/reportes-incidencias.css')) }}">
@endpush

@section('title', 'Reporte de incidencias y dias no cobrados')
@section('header', 'Reporte de incidencias y dias no cobrados (RF-24)')

@section('content')
    <div class="rin-page">
        <section class="rin-hero">
            <div>
                <h2>Reporte de incidencias y dias no cobrados</h2>
                <p>Lista incidencias registradas que afectan el cobro, como dias de lluvia y otros eventos no laborables.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="rin-btn-back">Volver</a>
        </section>

        <section class="rin-kpis">
            <article><span>Total incidencias</span><strong>{{ number_format($resumen['total_incidencias']) }}</strong></article>
            <article><span>Dias descontados</span><strong>{{ number_format($resumen['dias_descontados']) }}</strong></article>
            <article><span>Clientes afectados</span><strong>{{ number_format($resumen['clientes_afectados']) }}</strong></article>
        </section>

        <section class="rin-card">
            <form method="GET" class="rin-filters">
                <div class="field">
                    <label for="cliente">Cliente</label>
                    <input id="cliente" name="cliente" type="text" value="{{ $filters['cliente'] }}" placeholder="Buscar cliente">
                </div>
                <div class="field">
                    <label for="tipo">Tipo de incidencia</label>
                    <input id="tipo" name="tipo" type="text" value="{{ $filters['tipo'] }}" placeholder="Ej: LLUVIA">
                </div>
                <div class="field">
                    <label for="fecha_desde">Desde</label>
                    <input id="fecha_desde" name="fecha_desde" type="date" value="{{ $filters['fecha_desde'] }}">
                </div>
                <div class="field">
                    <label for="fecha_hasta">Hasta</label>
                    <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ $filters['fecha_hasta'] }}">
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('reportes.incidencias-no-cobrados') }}">Limpiar</a>
                </div>
            </form>

            <div class="rin-table-wrap">
                <table class="rin-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Fecha de la incidencia</th>
                            <th>Tipo de incidencia</th>
                            <th class="center">Dias descontados</th>
                            <th>Herramienta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                            @php
                                $obra = $registro->arriendo?->obra;
                                $tipos = collect([
                                    ((int) ($registro->dias_lluvia_desc ?? 0) > 0) ? 'LLUVIA' : null,
                                    ((int) ($registro->domingos_desc ?? 0) > 0) ? 'DOMINGO / NO LABORABLE' : null,
                                ])->filter()->implode(', ');
                                $diasDescontados = (int) ($registro->dias_lluvia_desc ?? 0) + (int) ($registro->domingos_desc ?? 0);
                            @endphp
                            <tr>
                                <td data-label="Cliente">{{ $registro->arriendo?->cliente?->nombre ?? '-' }}</td>
                                <td data-label="Obra">{{ $obra?->direccion ?: ($obra?->detalle ?: '-') }}</td>
                                <td data-label="Fecha">{{ optional($registro->fecha_devolucion)->format('d/m/Y') ?? '-' }}</td>
                                <td data-label="Tipo">{{ $tipos ?: '-' }}</td>
                                <td data-label="Dias descontados" class="center">{{ $diasDescontados }}</td>
                                <td data-label="Herramienta">{{ $registro->arriendoItem?->producto?->nombre ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">No hay incidencias para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($registros->hasPages())
                <div class="rin-pagination">
                    @if($registros->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $registros->previousPageUrl() }}">Anterior</a>
                    @endif
                    <span class="page-text">Pagina {{ $registros->currentPage() }} de {{ $registros->lastPage() }}</span>
                    @if($registros->hasMorePages())
                        <a class="page-btn" href="{{ $registros->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="page-btn page-disabled">Siguiente</span>
                    @endif
                </div>
            @endif
        </section>

        <section class="rin-utility">
            <strong>Utilidad:</strong> Soporte para descuentos, transparencia en cobros y control administrativo.
        </section>
    </div>
@endsection
