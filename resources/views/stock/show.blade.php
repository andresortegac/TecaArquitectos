@extends('layouts.app')

@section('title', 'Detallado')
@section('header', 'Informacion de herramientas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
    @php
        $stockMinimo = (int) ($config?->stock_minimo ?? 10);
        $cantidad = (int) ($producto->cantidad ?? 0);
    @endphp

    <div class="stock-page">
        <div class="container">
            <div class="product-wrapper">
                <div class="product-card">
                    <div class="product-header">
                        <h3>Detalle del producto</h3>
                        <span class="product-badge">Inventario</span>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="product-image-box">
                                <img
                                    src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('img/product-icon.svg') }}"
                                    alt="{{ $producto->nombre }}">
                            </div>
                        </div>

                        <div class="col-md-7">
                            <h1 class="product-name">{{ $producto->nombre }}</h1>

                            <div class="product-info">
                                <div><span>Categoría</span>{{ $producto->categorias ?: '-' }}</div>
                                <div><span>Stock</span>{{ $cantidad }}</div>
                                <div><span>Ubicación</span>{{ $producto->ubicacion ?: '-' }}</div>
                            </div>

                            <div class="product-status mt-3">
                                @if ($cantidad <= 0)
                                    <span class="status danger">Sin stock</span>
                                @elseif ($cantidad <= $stockMinimo)
                                    <span class="status warning">Stock bajo</span>
                                @else
                                    <span class="status success">Disponible</span>
                                @endif
                            </div>

                            <a href="{{ route('stock.index') }}" class="btn-back">
                                Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
