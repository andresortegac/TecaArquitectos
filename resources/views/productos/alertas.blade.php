@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🚨 Alertas de Stock</h2>

    @if($productos->count())
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Herramientas</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    <tr class="{{ $producto->cantidad == 0 ? 'table-danger' : 'table-warning' }}">
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categorias }}</td>
                        <td>{{ $producto->cantidad }}</td>
                        <td>
                            @if($producto->cantidad == 0)
                                <span class="badge bg-danger">Sin Stock</span>
                            @else
                                <span class="badge bg-warning text-dark">Stock Bajo</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-success">
            ✅ No hay alertas de stock
        </div>
    @endif
</div>
@endsection
 