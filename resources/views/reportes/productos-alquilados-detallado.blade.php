@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-productos-alquilados.css') }}">
@endpush

@section('title', 'Reporte detallado de productos alquilados')
@section('header', 'Reporte de productos alquilados y detallado')

@section('content')
    <div class="rpa-page">
        <section class="rpa-hero">
            <div>
                <h2>Productos alquilados por cliente y obra</h2>
                <p>Detalle operativo con cliente, obra, producto, cantidad alquilada y fecha registrada.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="rpa-btn-back">Volver</a>
        </section>

        <section class="rpa-card">
            <form method="GET" class="rpa-filter">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por cliente, obra o producto">
                <button type="submit">Filtrar</button>
                <a href="{{ route('reportes.productos-alquilados-detallado') }}">Limpiar</a>
            </form>

            @if($errors->any())
                <div class="rpa-errors">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="rpa-table-wrap">
                <table class="rpa-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Obra</th>
                            <th>Nombre de producto</th>
                            <th>Imagen</th>
                            <th class="right">Cantidad alquilada</th>
                            <th>Fecha registrada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $item)
                            @php
                                $obra = $item->arriendo?->obra;
                            @endphp
                            <tr>
                                <td>{{ $item->arriendo?->cliente?->nombre ?? '-' }}</td>
                                <td>
                                    {{ $obra->direccion ?? $obra->detalle ?? '-' }}
                                </td>
                                <td>{{ $item->producto?->nombre ?? 'Producto no disponible' }}</td>
                                <td>
                                    @if(!empty($item->producto?->imagen))
                                        <img src="{{ asset('storage/' . $item->producto->imagen) }}" class="rpa-img" alt="Producto">
                                    @else
                                        <span class="rpa-muted">Sin imagen</span>
                                    @endif
                                </td>
                                <td class="right">{{ number_format((int) $item->cantidad_actual) }}</td>
                                <td>
                                    {{ optional($item->fecha_inicio_item)->format('d/m/Y H:i') ?? optional($item->arriendo?->fecha_inicio)->format('d/m/Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="rpa-empty">No hay registros para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($registros->hasPages())
                <div class="rpa-pagination">
                    @if($registros->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $registros->previousPageUrl() }}">Anterior</a>
                    @endif

                    <span class="page-text">Página {{ $registros->currentPage() }} de {{ $registros->lastPage() }}</span>

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
