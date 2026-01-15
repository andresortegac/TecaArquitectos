@extends('layouts.app')

@section('title','STOCK')
@section('header','VER STOCK ACTUAL')

@section('content')
<div class="container">

    <h1 class="mb-4">Stock Actual</h1>       
    <br>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-primary">
            🔄 Actualizar Stock
        </a>
        <a href="{{ route('stock.export') }}" class="btn btn-success">
            📥 Exportar Excel
        </a>        
    </div>
    

    <br>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Categoría</th>
                        <th>Stock Min.</th>
                        <th>Stock Total</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($productos as $producto)

                        @php
                            $stockMin = 10;
                            $total    = $producto->cantidad;

                            if ($total <= 0) {
                                $estado   = 'Sin Stock';
                                $rowClass = 'estado-bajo';
                                $badge    = 'badge-bajo';
                            } elseif ($total <= $stockMin) {
                                $estado   = 'Bajo';
                                $rowClass = 'estado-bajo';
                                $badge    = 'badge-bajo';
                            } else {
                                $estado   = 'Normal';
                                $rowClass = 'estado-normal';
                                $badge    = 'badge-normal';
                            }
                        @endphp



                        <tr class="{{ $rowClass }}">
                            <td>{{ $producto->id }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>Unidades</td>
                            <td>{{ $producto->categorias }}</td>
                            <td>{{ $stockMin }}</td>
                            <td>{{ $total }}</td>
                            <td>
                                <span class="badge {{ $badge }}">
                                    {{ $estado }}
                                </span>

                            </td>
                            <td class="text-center">
                                <a href="{{ route('stock.show', $producto->id) }}" 
                                   class="btn btn-sm btn-info">
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
