@extends('layouts.app')

@section('title','Solicitudes por Cliente')
@section('header','Detalle de Solicitudes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/detallesolicitudes.css') }}">
@endpush


@section('content')

<div class="ds-wrapper">

    <h2 class="ds-title">CONTROL DE SOLICITUD</h2>

    @forelse($solicitudes as $clienteId => $items)

        @php
            $cliente = $items->first()->cliente;
            $solicitudesCliente = $items->groupBy('solicitud_id');
        @endphp

        <div class="ds-cliente-card">

            <div class="ds-cliente-header">
                👤 Cliente:
                <strong>{{ $cliente->nombre ?? 'Cliente eliminado' }}</strong>
            </div>

            <div class="ds-cliente-body">

                @foreach($solicitudesCliente as $solicitudId => $registros)

                    @php
                        $solicitud = $registros->first();
                    @endphp

                    <div class="ds-solicitud-card">

                        <!-- INFO -->
                        <div class="ds-solicitud-info">
                            <div class="ds-info-left">
                                <p><strong>📅 Fecha:</strong> {{ optional($solicitud->created_at)->format('Y-m-d') }}</p>
                                <p><strong>🏗 Obra:</strong> {{ $solicitud->obra->direccion ?? 'N/A' }}</p>
                            </div>

                            <span class="ds-estado ds-estado-{{ $solicitud->estado }}">
                                {{ ucfirst($solicitud->estado ?? 'pendiente') }}
                            </span>
                        </div>

                        <!-- DETALLE -->
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
                            <a href="{{ route('solicitudes.show', $solicitud) }}"
                               class="ds-btn">
                                🔍 Ver solicitud
                            </a>
                        </div>

                    </div>

                @endforeach

            </div>
        </div>

    @empty
        <p class="ds-empty">No hay solicitudes registradas.</p>
    @endforelse

</div>
@endsection
