@extends('layouts.app')

@section('title','Reportes')
@section('header','CONTROL DE PRODUCTO')

@section('content')

{{-- ===================== RESUMEN ===================== --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row text-center">

            <div class="col-md-3 border-end">
                <h4 class="text-success fw-bold">
                    {{ $alquilados->count() }}
                </h4>
                <small class="text-muted">productos en alquiler</small>
            </div>

            <div class="col-md-3 border-end">
                <h4 class="text-danger fw-bold">
                    {{ $alquilados->sum('cantidad_actual') }}
                </h4>
                <small class="text-muted">unidades fuera</small>
            </div>

            <div class="col-md-3 border-end">
                <h4 class="text-primary fw-bold">
                    {{ $bodega->sum('cantidad') }}
                </h4>
                <small class="text-muted">disponibles</small>
            </div>

            <div class="col-md-3">
                <h4 class="fw-bold">
                    {{ $alquilados->sum('cantidad_actual') + $bodega->sum('cantidad') }}
                </h4>
                <small class="text-muted">en total</small>
            </div>

        </div>
    </div>
</div>


<hr>

{{-- ===================== TABLA ALQUILADOS ===================== --}}
<h2>📦 Productos Alquilados</h2>

@if($alquilados->isEmpty())
    <p>No hay productos alquilados</p>
@else
<div class="mb-2">
    <input type="text" id="filtroAlquilados" class="form-control"
           placeholder="🔍 Filtrar producto alquilado...">
</div>

<table class="table table-bordered table-striped" id="tablaAlquilados">
    <thead class="table-dark">
        <tr>
            <th>Producto</th>
            <th>Imagen</th>
            <th>Cantidad Inicial</th>
            <th>Cantidad Actual</th>
            <th>Tarifa Diaria</th>
            <th>Fecha Inicio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alquilados as $item)
        <tr>
            <td class="nombre-producto">{{ $item->producto->nombre }}</td>
            <td>
                @if($item->producto->imagen)
                    <img src="{{ asset('storage/'.$item->producto->imagen) }}" width="60">
                @else
                    Sin imagen
                @endif
            </td>
            <td>{{ $item->cantidad_inicial }}</td>
            <td>{{ $item->cantidad_actual }}</td>
            <td>${{ number_format($item->tarifa_diaria, 0) }}</td>
            <td>{{ $item->fecha_inicio_item }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<hr>

{{-- ===================== TABLA BODEGA ===================== --}}
<h2>🏠 Productos en Bodega</h2>

@if($bodega->isEmpty())
    <p>No hay productos en bodega</p>
@else
<div class="mb-2">
    <input type="text" id="filtroBodega" class="form-control"
           placeholder="🔍 Filtrar producto en bodega...">
</div>

<table class="table table-bordered table-hover" id="tablaBodega">
    <thead class="table-secondary">
        <tr>
            <th>Producto</th>
            <th>Imagen</th>
            <th>Cantidad</th>
            <th>Costo</th>
            <th>Ubicación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bodega as $producto)
        <tr>
            <td class="nombre-producto">{{ $producto->nombre }}</td>
            <td>
                @if($producto->imagen)
                    <img src="{{ asset('storage/'.$producto->imagen) }}" width="60">
                @else
                    Sin imagen
                @endif
            </td>
            <td>{{ $producto->cantidad }}</td>
            <td>${{ number_format($producto->costo, 0) }}</td>
            <td>{{ $producto->ubicacion }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<script>
function filtrarTabla(inputId, tablaId) {
    const filtro = document.getElementById(inputId).value.toLowerCase();
    const filas = document.querySelectorAll(`#${tablaId} tbody tr`);

    filas.forEach(fila => {
        const texto = fila.querySelector('.nombre-producto').textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
}

document.getElementById('filtroAlquilados')
    .addEventListener('keyup', () => filtrarTabla('filtroAlquilados', 'tablaAlquilados'));

document.getElementById('filtroBodega')
    .addEventListener('keyup', () => filtrarTabla('filtroBodega', 'tablaBodega'));
</script>

@endsection
