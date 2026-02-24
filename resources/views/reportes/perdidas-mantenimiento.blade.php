@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-perdidas-mantenimiento.css') }}">
@endpush

@section('title', 'Reporte de perdidas y mantenimiento')
@section('header', 'Reporte de perdidas y mantenimiento (RF-25 / RF-26)')

@section('content')
    @php
        $eventoLabel = [
            'daño' => 'Daño',
            'perdida' => 'Perdida',
            'mantenimiento' => 'Mantenimiento',
        ];
    @endphp

    <div class="rpm-page">
        <section class="rpm-hero">
            <div>
                <h2>Reporte de perdidas y mantenimiento</h2>
                <p>Consolidado de costos asociados a herramientas dañadas, en mantenimiento o perdidas por el cliente.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="rpm-btn-back">Volver</a>
        </section>

        <section class="rpm-kpis">
            <article><span>Registros</span><strong>{{ number_format($resumen['registros']) }}</strong></article>
            <article><span>Costo total</span><strong>${{ number_format($resumen['costo_total'], 0) }}</strong></article>
            <article><span>Pendientes de cobro</span><strong>{{ number_format($resumen['pendientes_cobro']) }}</strong></article>
        </section>

        <section class="rpm-card">
            <form method="GET" class="rpm-filters">
                <div class="field">
                    <label for="cliente">Cliente</label>
                    <input id="cliente" type="text" name="cliente" value="{{ $filters['cliente'] }}" placeholder="Buscar cliente">
                </div>
                <div class="field">
                    <label for="evento">Tipo de evento</label>
                    <select id="evento" name="evento">
                        <option value="">Todos</option>
                        <option value="daño" {{ $filters['evento'] === 'daño' ? 'selected' : '' }}>Daño</option>
                        <option value="perdida" {{ $filters['evento'] === 'perdida' ? 'selected' : '' }}>Perdida</option>
                        <option value="mantenimiento" {{ $filters['evento'] === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                </div>
                <div class="field">
                    <label for="estado_cobro">Estado del cobro</label>
                    <select id="estado_cobro" name="estado_cobro">
                        <option value="">Todos</option>
                        <option value="cobrado" {{ $filters['estado_cobro'] === 'cobrado' ? 'selected' : '' }}>Cobrado</option>
                        <option value="pendiente" {{ $filters['estado_cobro'] === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
                <div class="field">
                    <label for="fecha_desde">Desde</label>
                    <input id="fecha_desde" type="date" name="fecha_desde" value="{{ $filters['fecha_desde'] }}">
                </div>
                <div class="field">
                    <label for="fecha_hasta">Hasta</label>
                    <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ $filters['fecha_hasta'] }}">
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('reportes.perdidas-mantenimiento') }}">Limpiar</a>
                </div>
            </form>

            <div class="rpm-table-wrap">
                <table class="rpm-table">
                    <thead>
                        <tr>
                            <th>Herramienta</th>
                            <th>Cliente responsable</th>
                            <th>Tipo de evento</th>
                            <th class="right">Costo aplicado</th>
                            <th>Fecha</th>
                            <th>Estado del cobro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $fila)
                            <tr>
                                <td>{{ $fila->herramienta }}</td>
                                <td>{{ $fila->cliente }}</td>
                                <td>{{ $eventoLabel[$fila->evento] ?? ucfirst($fila->evento) }}</td>
                                <td class="right">${{ number_format($fila->costo, 0) }}</td>
                                <td>{{ $fila->fecha }}</td>
                                <td>
                                    <span class="badge {{ $fila->estado_cobro === 'pendiente' ? 'badge-risk' : 'badge-ok' }}">
                                        {{ strtoupper($fila->estado_cobro) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">No hay registros para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rpm-utility">
            <strong>Utilidad:</strong> Control de costos, evaluación del uso del inventario y recuperación financiera.
        </section>
    </div>
@endsection
