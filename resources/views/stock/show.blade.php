@extends('layouts.app')

@section('title','DETALLADO')
@section('header','INFORMACION DE HERRAMIENTAS')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
    <div class="stock-page">
    <div class="container">

        <div class="product-wrapper">

            <div class="product-card">

                <div class="product-header">
                    <h3>DETALLE DEL PRODUCTO</h3>
                    <span class="product-badge">Inventario</span>
                </div>

                <div class="row align-items-center">

                    <!-- IMAGEN -->
                    <div class="col-md-5">
                        <div class="product-image-box">
                            <img 
                                src="{{ $producto->imagen 
                                    ? asset('storage/' . $producto->imagen) 
                                    : asset('img/tool-placeholder.jpeg') }}"
                                alt="{{ $producto->nombre }}"
                            >
                        </div>
                    </div>

                    <!-- INFO -->
                    <div class="col-md-7">
                        <h1 class="product-name">{{ $producto->nombre }}</h1>

                        <div class="product-info">
                            <div><span>Categoría</span>{{ $producto->categorias }}</div>
                            <div><span>Stock</span>{{ $producto->cantidad }}</div>
                            <div><span>Ubicación</span>{{ $producto->ubicacion }}</div>
                        </div>

                        <div class="product-status mt-3">
                            @if ($producto->cantidad == 0)
                                <br>
                                <span class="status danger">Sin stock</span>
                            @elseif ($producto->cantidad <= 10)
                                <br>
                                <span class="status warning">Stock bajo</span>
                            @else
                            <br>
                                <span class="status success">Disponible</span>
                            @endif
                        </div>
                        <br>
                        <br>
                        <a href="{{ route('stock.index') }}" class=" btn-back">
                            Volver al listado
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>


@endsection
