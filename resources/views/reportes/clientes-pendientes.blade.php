@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-clientes-pendientes.css') }}">
@endpush

@section('title', 'Clientes pendientes por cancelar')
@section('header', 'Reporte de cartera de clientes')

@section('content')
    @php
        $rol = auth()->user()->rol;
    @endphp

    <div class="rcp-page">
        <section class="rcp-hero">
            <div>
                <h2>Clientes pendientes por cancelar</h2>
                <p>Control consolidado de deuda por cliente, estado de mora y seguimiento de alquileres pendientes.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="rcp-btn-back">Volver</a>
        </section>

        <section class="rcp-kpis">
            <article class="rcp-kpi">
                <span>Clientes con deuda</span>
                <strong>{{ number_format($resumen['clientes'] ?? 0) }}</strong>
            </article>
            <article class="rcp-kpi">
                <span>Alquileres pendientes</span>
                <strong>{{ number_format($resumen['alquileres'] ?? 0) }}</strong>
            </article>
            @if($rol !== 'bodega')
                <article class="rcp-kpi">
                    <span>Total adeudado</span>
                    <strong class="is-risk">${{ number_format($resumen['total_deuda'] ?? 0, 0) }}</strong>
                </article>
            @endif
        </section>

        <section class="rcp-card">
            <form method="GET" class="rcp-filters">
                <div class="field field-grow">
                    <label for="cliente">Cliente</label>
                    <input id="cliente" type="text" name="cliente" value="{{ request('cliente') }}" placeholder="Buscar por nombre">
                </div>
                <div class="field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="al_dia" {{ request('estado') === 'al_dia' ? 'selected' : '' }}>Al día</option>
                        <option value="moroso" {{ request('estado') === 'moroso' ? 'selected' : '' }}>Moroso</option>
                    </select>
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('reportes.clientes-pendientes') }}">Limpiar</a>
                </div>
            </form>

            @if($errors->any())
                <div class="rcp-errors">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="rcp-table-wrap">
                <table class="rcp-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Obras</th>
                            <th class="center">Alquileres</th>
                            <th>Productos alquilados</th>
                            @if($rol !== 'bodega')
                                <th class="right">Valor adeudado</th>
                            @endif
                            <th>Último cobro</th>
                            <th class="center">Días mora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientesMorosos as $cliente)
                            <tr>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->obras }}</td>
                                <td class="center">{{ $cliente->alquileres_pendientes }}</td>
                                <td>{{ $cliente->productos_alquilados }}</td>
                                @if($rol !== 'bodega')
                                    <td class="right">${{ number_format((float) $cliente->total_deuda, 0) }}</td>
                                @endif
                                <td>{{ $cliente->ultimo_cobro }}</td>
                                <td class="center">{{ $cliente->dias_mora }}</td>
                                <td>
                                    <span class="badge {{ $cliente->estado === 'moroso' ? 'badge-risk' : 'badge-ok' }}">
                                        {{ $cliente->estado === 'moroso' ? 'MOROSO' : 'AL DIA' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $rol !== 'bodega' ? 8 : 7 }}" class="rcp-empty">
                                    No hay clientes con saldos pendientes para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rcp-card">
            <div class="rcp-section-head">
                <h3>Reporte de devoluciones y pendientes</h3>
                <p>
                    Identifica herramientas devueltas parcial o totalmente y aquellas que aún se encuentran pendientes.
                </p>
            </div>

            <div class="rcp-table-wrap">
                <table class="rcp-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Herramientas alquiladas</th>
                            <th class="center">Cant. alquilada vs devuelta</th>
                            <th class="center">Diferencias</th>
                            <th>Fecha estimada de devolución</th>
                            <th>Estado de devolución</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reporteDevoluciones as $fila)
                            <tr>
                                <td>{{ $fila->cliente }}</td>
                                <td>{{ $fila->obra }}</td>
                                <td>{{ $fila->herramienta }}</td>
                                <td class="center">{{ $fila->cantidad_alquilada }} / {{ $fila->cantidad_devuelta }}</td>
                                <td class="center">{{ $fila->diferencia }}</td>
                                <td>{{ $fila->fecha_estimada_devolucion }}</td>
                                <td>
                                    <span class="badge {{ $fila->estado_class }}">
                                        {{ strtoupper($fila->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="rcp-empty">
                                    No hay información de devoluciones o pendientes para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rcp-utils">
                <strong>Utilidad:</strong> Control operativo de bodega, seguimiento a clientes y reducción de pérdidas.
            </div>
        </section>
    </div>
@endsection
