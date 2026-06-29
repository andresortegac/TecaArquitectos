@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/controlproducto.css') }}">
@endpush

@section('title', 'Control de producto')
@section('header', 'Control de producto y disponibilidad')

@section('content')
    <div class="cp-page">
        <section class="cp-hero">
            <div>
                <h2>Visibilidad consolidada de inventario</h2>
                <p>Seguimiento de equipos alquilados y stock disponible en bodega para control operativo diario.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="cp-btn-back">Volver</a>
        </section>

        <section class="cp-kpis">
            <article class="cp-kpi">
                <span>Productos en alquiler</span>
                <strong>{{ number_format($resumen['productos_alquilados']) }}</strong>
            </article>
            <article class="cp-kpi">
                <span>Unidades fuera</span>
                <strong class="is-danger">{{ number_format($resumen['unidades_fuera']) }}</strong>
            </article>
            <article class="cp-kpi">
                <span>Unidades en bodega</span>
                <strong class="is-ok">{{ number_format($resumen['unidades_bodega']) }}</strong>
            </article>
            <article class="cp-kpi">
                <span>Total de unidades</span>
                <strong>{{ number_format($resumen['total_unidades']) }}</strong>
            </article>
        </section>

        @if($errors->any())
            <div class="cp-errors">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="cp-card">
            <div class="cp-card-head">
                <h3>Productos alquilados</h3>
                <form method="GET" class="cp-filter">
                    <input
                        id="filtroAlquilados"
                        type="text"
                        name="alquilados"
                        value="{{ request('alquilados') }}"
                        placeholder="Filtrar por nombre">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('reportes.controlproducto', ['bodega' => request('bodega')]) }}">Limpiar</a>
                </form>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table">
                    <thead>
                        <tr>
                            <th>Nombre del producto</th>
                            <th>Imagen</th>
                            <th class="right">Cantidad total</th>
                            <th class="right">Cantidad stock</th>
                            <th class="right">Cantidad alquilado</th>
                            <th>Fecha del alquiler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alquilados as $item)
                            <tr>
                                <td class="cp-product-name">{{ $item->nombre }}</td>
                                <td>
                                    @if(!empty($item->imagen))
                                        <img src="{{ asset('storage/' . $item->imagen) }}" class="cp-img" alt="Producto">
                                    @else
                                        <span class="cp-muted">Sin imagen</span>
                                    @endif
                                </td>
                                <td class="right">{{ number_format((int) $item->cantidad_total) }}</td>
                                <td class="right">{{ number_format((int) $item->cantidad_stock) }}</td>
                                <td class="right">{{ number_format((int) $item->cantidad_alquilada) }}</td>
                                <td>
                                    {{ optional($item->fecha_alquiler)->format('d/m/Y') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="cp-empty">No hay productos alquilados para el filtro seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="cp-card">
            <div class="cp-card-head">
                <h3>Productos en bodega</h3>
                <form method="GET" class="cp-filter">
                    <input
                        id="filtroBodega"
                        type="text"
                        name="bodega"
                        value="{{ request('bodega') }}"
                        placeholder="Filtrar por nombre">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('reportes.controlproducto', ['alquilados' => request('alquilados')]) }}">Limpiar</a>
                </form>
            </div>

            <div class="cp-table-wrap">
                <table class="cp-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Imagen</th>
                            <th class="right">Cantidad</th>
                            <th class="right">Costo</th>
                            <th>Ubicación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bodega as $producto)
                            <tr>
                                <td class="cp-product-name">{{ $producto->nombre }}</td>
                                <td>
                                    @if(!empty($producto->imagen))
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" class="cp-img" alt="Producto">
                                    @else
                                        <span class="cp-muted">Sin imagen</span>
                                    @endif
                                </td>
                                <td class="right">{{ number_format((int) $producto->cantidad) }}</td>
                                <td class="right">${{ number_format((float) $producto->costo, 0) }}</td>
                                <td>{{ $producto->ubicacion ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="cp-empty">No hay productos en bodega para el filtro seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
