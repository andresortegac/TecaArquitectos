@extends('layouts.app')

@section('title', 'Solicitudes por Cliente')
@section('header', 'Detalle de Solicitudes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/detallesolicitudes.css') }}">
@endpush

@section('content')
<div class="ds-wrapper">
    <section class="ds-hero">
        <div>
            <h2>Solicitudes detalladas por cliente</h2>
            <p>Consulta cada solicitud aprobada con su obra, fecha y detalle por producto.</p>
        </div>
        <a href="{{ route('solicitudes.solicitudes') }}" class="ds-btn ds-btn-light">Volver</a>
    </section>

    @forelse($solicitudes as $clienteId => $items)
        @php
            $cliente = $items->first()->cliente;
            $solicitudesCliente = $items->groupBy('solicitud_id');
        @endphp

        <article class="ds-cliente-card">
            <div class="ds-cliente-header">
                <span>Cliente</span>
                <strong>{{ $cliente->nombre ?? 'Cliente eliminado' }}</strong>
            </div>

            <div class="ds-cliente-body">
                @foreach($solicitudesCliente as $solicitudId => $registros)
                    @php
                        $solicitud = $registros->first();
                    @endphp

                    <section class="ds-solicitud-card">
                        <div class="ds-solicitud-info">
                            <div class="ds-info-left">
                                <p><strong>Fecha:</strong> {{ optional($solicitud->created_at)->format('Y-m-d') }}</p>
                                <p><strong>Obra:</strong> {{ $solicitud->obra->direccion ?? 'N/A' }}</p>
                            </div>

                            <span class="ds-estado ds-estado-{{ $solicitud->estado }}">
                                {{ $solicitud->estado === 'aprobado' ? 'Aprobado con exito' : ucfirst($solicitud->estado ?? 'pendiente') }}
                            </span>
                        </div>

                        <div class="ds-detalle">
                            <table class="ds-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Solicitado</th>
                                        <th>Aprobado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registros as $item)
                                        <tr>
                                            <td>{{ $item->producto->nombre ?? 'Producto eliminado' }}</td>
                                            <td>{{ $item->cantidad_solicitada }}</td>
                                            <td>{{ $item->cantidad_aprobada ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="ds-acciones">
                            <a href="{{ route('solicitudes.show', $solicitud->solicitud_id) }}" class="ds-btn ds-btn-primary">
                                Ver solicitud
                            </a>
                        </div>
                    </section>
                @endforeach
            </div>
        </article>
    @empty
        <p class="ds-empty">No hay solicitudes registradas.</p>
    @endforelse
</div>
@endsection
