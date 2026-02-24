@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('title', 'Stock')
@section('header', 'Ver stock actual')

@section('content')
    <div class="stk-page">
        <section class="stk-hero">
            <div>
                <h2>Stock actual de inventario</h2>
                <p>Control de disponibilidad por producto con alertas de nivel mínimo.</p>
            </div>
            <div class="stk-hero-actions">
                <a href="{{ route('dashboard') }}" class="stk-btn stk-btn-secondary">Volver</a>
                <a href="{{ route('stock.index') }}" class="stk-btn stk-btn-secondary">Actualizar</a>
                <a href="{{ route('stock.export') }}" class="stk-btn stk-btn-success">Exportar Excel</a>
            </div>
        </section>

        <section class="stk-kpis">
            <article><span>Total productos</span><strong>{{ number_format($resumen['total']) }}</strong></article>
            <article><span>Sin stock</span><strong>{{ number_format($resumen['sin_stock']) }}</strong></article>
            <article><span>Stock bajo</span><strong>{{ number_format($resumen['bajo']) }}</strong></article>
            <article><span>Stock normal</span><strong>{{ number_format($resumen['normal']) }}</strong></article>
        </section>

        <section class="stk-card">
            <form method="GET" class="stk-filters">
                <div class="field field-grow">
                    <label for="q">Buscar</label>
                    <input id="q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nombre o categoría">
                </div>
                <div class="field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="normal" {{ ($filters['estado'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="bajo" {{ ($filters['estado'] ?? '') === 'bajo' ? 'selected' : '' }}>Bajo</option>
                        <option value="sin_stock" {{ ($filters['estado'] ?? '') === 'sin_stock' ? 'selected' : '' }}>Sin stock</option>
                    </select>
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('stock.index') }}">Limpiar</a>
                </div>
            </form>

            <div class="stk-table-wrap">
                <table class="stk-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Unidad</th>
                            <th>Categoría</th>
                            <th class="center">Stock min.</th>
                            <th class="center">Stock total</th>
                            <th>Estado</th>
                            <th class="center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            @php
                                $total = (int) $producto->cantidad;
                                if ($total <= 0) {
                                    $estado = 'Sin stock';
                                    $badge = 'badge-danger';
                                    $rowClass = 'row-danger';
                                } elseif ($total <= $stockMinimo) {
                                    $estado = 'Stock bajo';
                                    $badge = 'badge-warning';
                                    $rowClass = 'row-warning';
                                } else {
                                    $estado = 'Normal';
                                    $badge = 'badge-ok';
                                    $rowClass = '';
                                }
                            @endphp

                            <tr class="{{ $rowClass }}">
                                <td>{{ $producto->id }}</td>
                                <td>{{ $producto->nombre }}</td>
                                <td>Unidades</td>
                                <td>{{ $producto->categorias ?: '-' }}</td>
                                <td class="center">{{ $stockMinimo }}</td>
                                <td class="center">{{ $total }}</td>
                                <td>
                                    <span class="stk-badge {{ $badge }}">{{ strtoupper($estado) }}</span>
                                </td>
                                <td class="center">
                                    <a href="{{ route('stock.show', $producto->id) }}" class="stk-btn-sm">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty">No hay productos para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($productos->hasPages())
                <div class="stk-pagination">
                    @if($productos->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $productos->previousPageUrl() }}">Anterior</a>
                    @endif
                    <span class="page-text">Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
                    @if($productos->hasMorePages())
                        <a class="page-btn" href="{{ $productos->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="page-btn page-disabled">Siguiente</span>
                    @endif
                </div>
            @endif
        </section>
    </div>
@endsection
