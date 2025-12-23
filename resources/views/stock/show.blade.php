@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
    <div class="stock-page">   

        <div class="container">

            <h3 class="mb-4">🔍 Detalle del Producto</h3>

            <div class="card shadow">
                <div class="card-body">

                    <div class="row">

                        <!-- IMAGEN -->
                        <div class="col-md-4 text-center">
                            @if ($producto->imagen)
                                <img 
                                    src="{{ asset($producto->imagen) }}" 
                                    alt="{{ $producto->nombre }}"
                                    class="img-fluid rounded shadow-sm"
                                    style="max-height: 320px; object-fit: cover;"
                                >

                            @else
                                <div class="text-muted">
                                    <i class="bi bi-image" style="font-size: 60px;"></i>
                                    <p>Sin imagen</p>
                                </div>
                            @endif
                        </div>

                        <!-- INFORMACIÓN -->
                        <div class="col-md-8">
                            <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
                            <p><strong>Categoría:</strong> {{ $producto->categorias }}</p>
                            <p><strong>Stock actual:</strong> {{ $producto->cantidad }}</p>
                            <p><strong>Ubicación:</strong> {{ $producto->ubicacion }}</p>

                            <p><strong>Estado:</strong>
                                @if ($producto->cantidad == 0)
                                    <span class="badge bg-danger">Sin Stock</span>
                                @elseif ($producto->cantidad <= 10)
                                    <span class="badge bg-warning text-dark">Stock Bajo</span>
                                @else
                                    <span class="badge bg-success">Normal</span>
                                @endif
                            </p>

                            <br>
                            <br>
                            <a href="{{ route('stock.index') }}" class="btn btn-secondary mt-3">
                                ⬅ Volver
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
        
    </div>
@endsection
