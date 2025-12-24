@extends('layouts.app')

@section('title','STOCK')
@section('header','VER STOCK ACTUAL')

@section('content')
<div class="container">

    <h1 class="mb-4">Stock Actual</h1>       
    <br>
    <br>
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-primary">🔄 Actualizar Stock</a>
        <a href="{{ route('stock.export') }}" class="btn btn-success">📥 Exportar Excel</a>        
    </div>
    <br>
    <br>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Categoría</th>
                        <th>Stock Min.</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($productos as $producto)

                        @php
                            if ($producto->cantidad == 0) {
                                $estado = 'Sin Stock';
                                $color = 'table-danger';
                            } elseif ($producto->cantidad <= 10) {
                                $estado = 'Stock Bajo';
                                $color = 'table-warning';
                            } else {
                                $estado = 'Normal';
                                $color = 'table-success';
                            }
                        @endphp

                        <tr class="{{ $color }}">
                            <td>{{ $producto->id }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>Unidades</td>
                            <td>{{ $producto->categorias }}</td>
                            <td>10</td>
                            <td>{{ $producto->cantidad }}</td>
                            <td>
                                <span class="badge 
                                    {{ $estado == 'Normal' ? 'bg-success' : ($estado == 'Stock Bajo' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('stock.show', $producto->id) }}" class="btn btn-sm btn-info">
                                    Ver
                                </a>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
