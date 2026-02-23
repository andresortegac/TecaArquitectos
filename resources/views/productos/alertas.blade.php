@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/alerta-producto.css') }}">
@endpush

@section('title', 'Alertas de stock')
@section('header', 'Alertas de stock')

@section('content')
    <div class="aler-page">
        <section class="aler-hero">
            <div>
                <h2>Alertas de stock</h2>
                <p>Productos con cantidad igual o inferior al stock minimo configurado ({{ $stockMinimo }}).</p>
            </div>
            <div class="aler-hero-actions">
                <a href="{{ route('dashboard') }}" class="aler-btn">Volver</a>
                <a href="{{ route('stock.index') }}" class="aler-btn">Ver stock completo</a>
            </div>
        </section>

        <section class="aler-kpis">
            <article><span>Total alertas</span><strong>{{ number_format($resumen['total_alertas'] ?? 0) }}</strong></article>
            <article><span>Sin stock</span><strong>{{ number_format($resumen['sin_stock'] ?? 0) }}</strong></article>
            <article><span>Stock bajo</span><strong>{{ number_format($resumen['stock_bajo'] ?? 0) }}</strong></article>
        </section>

        <section class="aler-card">
            <form method="GET" class="aler-filters">
                <div class="field field-grow">
                    <label for="q">Buscar</label>
                    <input id="q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nombre o categoria">
                </div>
                <div class="field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="sin_stock" {{ ($filters['estado'] ?? '') === 'sin_stock' ? 'selected' : '' }}>Sin stock</option>
                        <option value="stock_bajo" {{ ($filters['estado'] ?? '') === 'stock_bajo' ? 'selected' : '' }}>Stock bajo</option>
                    </select>
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('productos.alertas') }}">Limpiar</a>
                </div>
            </form>

            @if($productos->isNotEmpty())
                <div class="aler-table-wrap">
                    <table class="aler-table">
                        <thead>
                            <tr>
                                <th>Herramienta</th>
                                <th>Categoria</th>
                                <th class="center">Cantidad</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                @php
                                    $isSinStock = (int) $producto->cantidad === 0;
                                @endphp
                                <tr class="{{ $isSinStock ? 'fila-danger' : 'fila-warning' }}">
                                    <td data-label="Herramienta">{{ $producto->nombre }}</td>
                                    <td data-label="Categoria">{{ $producto->categorias ?: '-' }}</td>
                                    <td data-label="Cantidad" class="center">{{ $producto->cantidad }}</td>
                                    <td data-label="Estado">
                                        <span class="aler-badge {{ $isSinStock ? 'bodega-danger' : 'bodega-warning' }}">
                                            {{ $isSinStock ? 'SIN STOCK' : 'STOCK BAJO' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="aler-alert aler-alert-success">
                    No hay alertas de stock para los filtros seleccionados.
                </div>
            @endif
        </section>
    </div>
@endsection
