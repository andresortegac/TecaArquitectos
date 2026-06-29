@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-ingresos-diarios.css') }}">
@endpush

@section('title', 'Reporte diario de ingresos')
@section('header', 'Reporte diario de ingresos (RF-29)')

@section('content')
    @php
        $metodoLabel = [
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'nequi' => 'Nequi',
            'daviplata' => 'Daviplata',
        ];
    @endphp

    <div class="rid-page">
        <section class="rid-hero">
            <div>
                <h2>Reporte diario de ingresos</h2>
                <p>Resumen de pagos y abonos registrados en una fecha específica para control de caja y cierre diario.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="rid-btn-back">Volver</a>
        </section>

        <section class="rid-kpis">
            <article class="rid-kpi">
                <span>Fecha del reporte</span>
                <strong>{{ $resumen['fecha'] ?? '-' }}</strong>
            </article>
            <article class="rid-kpi">
                <span>Ingresos del día</span>
                <strong>${{ number_format((float) ($resumen['ingresos_totales'] ?? 0), 0) }}</strong>
            </article>
            <article class="rid-kpi">
                <span>Pagos registrados</span>
                <strong>{{ number_format((int) ($resumen['pagos_registrados'] ?? 0)) }}</strong>
            </article>
            <article class="rid-kpi">
                <span>Abonos parciales</span>
                <strong>{{ number_format((int) ($resumen['abonos_parciales'] ?? 0)) }}</strong>
            </article>
        </section>

        <section class="rid-card">
            <form method="GET" class="rid-filters">
                <div class="field">
                    <label for="fecha">Fecha</label>
                    <input id="fecha" type="date" name="fecha" value="{{ $filters['fecha'] }}">
                </div>
                <div class="field">
                    <label for="tipo_pago">Tipo de pago</label>
                    <select id="tipo_pago" name="tipo_pago">
                        <option value="">Todos</option>
                        <option value="efectivo" {{ $filters['tipo_pago'] === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ $filters['tipo_pago'] === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="nequi" {{ $filters['tipo_pago'] === 'nequi' ? 'selected' : '' }}>Nequi</option>
                        <option value="daviplata" {{ $filters['tipo_pago'] === 'daviplata' ? 'selected' : '' }}>Daviplata</option>
                    </select>
                </div>
                <div class="field field-grow">
                    <label for="cliente">Cliente</label>
                    <input id="cliente" type="text" name="cliente" value="{{ $filters['cliente'] }}" placeholder="Buscar por nombre">
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('reportes.ingresos-diarios') }}">Limpiar</a>
                </div>
            </form>

            @if($errors->any())
                <div class="rid-errors">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="rid-table-wrap">
                <table class="rid-table">
                    <thead>
                        <tr>
                            <th>Fecha del reporte</th>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Tipo de pago</th>
                            <th>Concepto del cobro</th>
                            <th class="right">Valor recibido</th>
                            <th>Usuario que registró el pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                            @php
                                $esAbonoParcial = ((float) $registro->devol_pago_recibido) < ((float) $registro->devol_total_cobrado)
                                    && ((float) $registro->devol_total_cobrado) > 0;
                                $concepto = trim((string) ($registro->note ?? ''));
                                if ($concepto === '') {
                                    if (str_contains((string) $registro->source_type, 'DevolucionArriendo')) {
                                        $concepto = $esAbonoParcial ? 'Abono parcial de devolución' : 'Pago total de devolución';
                                    } else {
                                        $concepto = 'Pago registrado';
                                    }
                                }
                                $obra = $registro->obra_direccion ?: $registro->obra_detalle ?: '-';
                            @endphp
                            <tr>
                                <td>{{ optional(\Illuminate\Support\Carbon::parse($registro->occurred_at))->format('d/m/Y H:i') }}</td>
                                <td>{{ $registro->cliente ?: '-' }}</td>
                                <td>{{ $obra }}</td>
                                <td>{{ $metodoLabel[$registro->tipo_pago] ?? ucfirst($registro->tipo_pago) }}</td>
                                <td>{{ $concepto }}</td>
                                <td class="right">${{ number_format((float) $registro->valor_recibido, 0) }}</td>
                                <td>{{ $registro->usuario ?: 'Usuario no identificado' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="rid-empty">No hay ingresos registrados con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($registros->hasPages())
                <div class="rid-pagination">
                    @if($registros->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $registros->previousPageUrl() }}">Anterior</a>
                    @endif

                    <span class="page-text">
                        Página {{ $registros->currentPage() }} de {{ $registros->lastPage() }}
                    </span>

                    @if($registros->hasMorePages())
                        <a class="page-btn" href="{{ $registros->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="page-btn page-disabled">Siguiente</span>
                    @endif
                </div>
            @endif
        </section>
    </div>
@endsection
