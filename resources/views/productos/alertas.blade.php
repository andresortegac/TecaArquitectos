@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alerta-producto.css') }}">
@endpush

@section('title','Alertas de Stock')
@section('header','Alertas de Stock')

@section('content')
<div class="aler-container">

    <div class="alerta-stilo">

        <h2 class="aler-title">🚨 Alertas de Stock</h2>

        @if($productos->count())

            <table class="aler-table">
                <thead>
                    <tr>
                        <th>Herramienta</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($productos as $producto)
                        <tr class="{{ $producto->cantidad == 0 ? 'fila-danger' : 'fila-warning' }}">
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->categorias }}</td>
                            <td>{{ $producto->cantidad }}</td>
                            <td>
                                @if($producto->cantidad == 0)
                                    <span class="aler-badge bodega-danger">
                                        Sin Stock
                                    </span>
                                @else
                                    <span class="aler-badge bodega-warning">
                                        Stock Bajo
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @else

            <div class="aler-alert aler-alert-success">
                ✅ No hay alertas de stock
            </div>

        @endif

    </div>

</div>
@endsection
