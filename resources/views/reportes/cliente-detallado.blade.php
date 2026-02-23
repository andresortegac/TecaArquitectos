@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-cliente-detallado.css') }}">
@endpush

@section('title', 'Reporte detallado por cliente')
@section('header', 'Reporte detallado por cliente (RF-31)')

@section('content')
    <div class="rcd-page">
        <section class="rcd-hero">
            <div>
                <h2>Reporte detallado por cliente</h2>
                <p>Desglose por periodo de alquileres, incidencias, costos, pagos y saldo final.</p>
            </div>
            <div class="rcd-hero-actions">
                <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
                <button type="button" class="btn-secondary" onclick="window.print()">Imprimir</button>
                <a
                    href="{{ route('reportes.cliente-detallado.pdf', request()->query()) }}"
                    class="btn-primary">
                    Exportar PDF
                </a>
            </div>
        </section>

        <section class="rcd-card">
            <form method="GET" class="rcd-filters">
                <div class="field">
                    <label for="cliente_id">Cliente</label>
                    <select id="cliente_id" name="cliente_id">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ (string) $filters['cliente_id'] === (string) $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
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
                    <button type="submit" class="btn-primary">Filtrar</button>
                    <a href="{{ route('reportes.cliente-detallado') }}" class="btn-secondary">Limpiar</a>
                </div>
            </form>
        </section>

        <section class="rcd-card">
            <h3>Datos del cliente</h3>
            @if($clienteSeleccionado)
                <div class="rcd-client-grid">
                    <div><span>Nombre</span><strong>{{ $clienteSeleccionado->nombre }}</strong></div>
                    <div><span>Documento</span><strong>{{ $clienteSeleccionado->documento ?: '-' }}</strong></div>
                    <div><span>Teléfono</span><strong>{{ $clienteSeleccionado->telefono ?: '-' }}</strong></div>
                    <div><span>Email</span><strong>{{ $clienteSeleccionado->email ?: '-' }}</strong></div>
                </div>
            @else
                <p class="muted">Mostrando todos los clientes para el periodo seleccionado.</p>
            @endif

            <h4>Obras asociadas</h4>
            <p class="muted">
                {{ $obrasAsociadas->isNotEmpty() ? $obrasAsociadas->implode(', ') : '-' }}
            </p>
        </section>

        <section class="rcd-kpis">
            <article><span>Costo alquiler</span><strong>${{ number_format($resumen['total_alquiler'], 0) }}</strong></article>
            <article><span>Costo transporte</span><strong>${{ number_format($resumen['total_transporte'], 0) }}</strong></article>
            <article><span>Descuentos aplicados</span><strong>${{ number_format($resumen['total_descuentos'], 0) }}</strong></article>
            <article><span>Pérdidas/mantenimiento</span><strong>${{ number_format($resumen['total_perdidas'], 0) }}</strong></article>
            <article><span>Pagos y abonos</span><strong>${{ number_format($resumen['total_pagado'], 0) }}</strong></article>
            <article><span>Saldo final</span><strong>${{ number_format($resumen['saldo_final'], 0) }}</strong></article>
        </section>

        <section class="rcd-card">
            <div class="table-wrap">
                <table class="rcd-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Herramienta</th>
                            <th>Fechas alquiler/devolución</th>
                            <th class="center">Días cobrados</th>
                            <th class="center">Días no cobrados</th>
                            <th class="right">Costo alquiler</th>
                            <th class="right">Costo transporte</th>
                            <th class="right">Descuentos</th>
                            <th class="right">Pérdidas / mantenimiento</th>
                            <th class="right">Pagos / abonos</th>
                            <th class="right">Saldo final</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filas as $fila)
                            <tr>
                                <td>{{ $fila->cliente }}</td>
                                <td>{{ $fila->obra }}</td>
                                <td>{{ $fila->herramienta }}</td>
                                <td>{{ $fila->fecha_alquiler }} - {{ $fila->fecha_devolucion }}</td>
                                <td class="center">{{ $fila->dias_cobrados }}</td>
                                <td class="center">{{ $fila->dias_no_cobrados }}</td>
                                <td class="right">${{ number_format($fila->costo_alquiler, 0) }}</td>
                                <td class="right">${{ number_format($fila->costo_transporte, 0) }}</td>
                                <td class="right">${{ number_format($fila->descuentos_aplicados, 0) }}</td>
                                <td class="right">${{ number_format($fila->costos_perdidas_mantenimiento, 0) }}</td>
                                <td class="right">${{ number_format($fila->pagos_abonos, 0) }}</td>
                                <td class="right">${{ number_format($fila->saldo_final, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="empty">No hay registros para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
