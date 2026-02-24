@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/solicitud-show.css') }}">
@endpush

@section('title', 'Revisar Solicitud')
@section('header', 'Revisar Solicitud')

@section('content')
<div class="ss-page">
    @if(session('success'))
        <div class="ss-alert ss-alert-success">{{ session('success') }}</div>
    @endif

    <section class="ss-hero">
        <div>
            <h2>Detalle de solicitud</h2>
            <p>Valida productos y cantidades antes de confirmar la solicitud.</p>
        </div>
        <div class="ss-hero-actions">
            <a href="{{ route('solicitudes.solicitudes') }}" class="ss-btn ss-btn-light">Volver</a>
            <a href="{{ route('arriendos.pdf', $arriendo) }}" class="ss-btn ss-btn-danger" target="_blank" rel="noopener">Exportar factura</a>
        </div>
    </section>

    <section class="ss-card">
        <h3>Datos de la solicitud</h3>
        <div class="ss-kv">
            <p><span>Cliente</span><strong>{{ $arriendo->cliente->nombre }}</strong></p>
            <p><span>Obra</span><strong>{{ $arriendo->obra->direccion ?? '-' }}</strong></p>
            <p><span>Fecha solicitud</span><strong>{{ $arriendo->created_at->format('Y-m-d') }}</strong></p>
            <p><span>Estado</span><strong>{{ ucfirst($arriendo->estado) }}</strong></p>
        </div>
    </section>

    <section class="ss-card">
        <form method="POST" action="{{ route('solicitudes.confirmar', $arriendo) }}">
            @csrf

            <div class="ss-table-wrapper">
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad solicitada</th>
                            <th>Aprobar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arriendo->items as $item)
                            @php
                                $yaAprobado = in_array((int) $item->producto_id, $aprobados ?? [], true);
                            @endphp
                            <tr>
                                <td>{{ $item->producto->nombre ?? '-' }}</td>
                                <td>{{ $item->cantidad_inicial }}</td>
                                <td>
                                    @if($yaAprobado)
                                        <span class="ss-approved-text">Aprobado con exito</span>
                                    @else
                                        <label class="ss-check">
                                            <input type="checkbox" name="items[{{ $item->id }}][aprobado]" value="1">
                                            <span>Aprobar</span>
                                        </label>
                                    @endif

                                    <input type="hidden" name="items[{{ $item->id }}][producto_id]" value="{{ $item->producto_id }}">
                                    <input type="hidden" name="items[{{ $item->id }}][cantidad_solicitada]" value="{{ $item->cantidad_inicial }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="ss-empty">No hay productos cargados en esta solicitud.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @php
                $pendientes = $arriendo->items->filter(function ($item) use ($aprobados) {
                    return !in_array((int) $item->producto_id, $aprobados ?? [], true);
                })->count();
            @endphp
            @if($pendientes > 0)
                <div class="ss-actions">
                    <button type="submit" class="ss-btn ss-btn-primary">Confirmar solicitud</button>
                </div>
            @endif
        </form>
    </section>
</div>
@endsection
