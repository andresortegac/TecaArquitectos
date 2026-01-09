@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/controlproducto.css') }}">
@endpush

@section('title','Reportes')
@section('header','CONTROL DE PRODUCTO')

@section('content')

<div class="contr-container"> 

    {{-- ===================== RESUMEN ===================== --}}
    <div class="contr-card">
        <div class="contr-card-body">
            <div class="contr-row contr-text-center">

                <div class="contr-col contr-border-end">
                    <h4 class="contr-text-success contr-fw-bold">
                        {{ $alquilados->count() }}
                    </h4>
                    <small class="contr-text-muted">productos en alquiler</small>
                </div>

                <div class="contr-col contr-border-end">
                    <h4 class="contr-text-danger contr-fw-bold">
                        {{ $alquilados->sum('cantidad_actual') }}
                    </h4>
                    <small class="contr-text-muted">unidades fuera</small>
                </div>

                <div class="contr-col contr-border-end">
                    <h4 class="contr-text-primary contr-fw-bold">
                        {{ $bodega->sum('cantidad') }}
                    </h4>
                    <small class="contr-text-muted">disponibles</small>
                </div>

                <div class="contr-col">
                    <h4 class="contr-fw-bold">
                        {{ $alquilados->sum('cantidad_actual') + $bodega->sum('cantidad') }}
                    </h4>
                    <small class="contr-text-muted">en total</small>
                </div>

            </div>
        </div>
    </div>

    <hr class="contr-divider">

    {{-- ===================== TABLA ALQUILADOS ===================== --}}
    <h2 class="contr-title">📦 Productos Alquilados</h2>

    @if($alquilados->isEmpty())
        <p class="contr-empty">No hay productos alquilados</p>
    @else
        <div class="contr-filter">
            <input type="text" id="filtroAlquilados"
                   class="contr-input"
                   placeholder="🔍 Filtrar producto alquilado...">
        </div>

        <table class="contr-table" id="tablaAlquilados">
            <thead class="contr-table-dark">
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
                    <td class="contr-product-name">{{ $item->producto->nombre }}</td>
                    <td>
                        @if($item->producto->imagen)
                            <img src="{{ asset('storage/'.$item->producto->imagen) }}" class="contr-img">
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

    <hr class="contr-divider">

    {{-- ===================== TABLA BODEGA ===================== --}}
    <h2 class="contr-title">🏠 Productos en Bodega</h2>

    @if($bodega->isEmpty())
        <p class="contr-empty">No hay productos en bodega</p>
    @else
        <div class="contr-filter">
            <input type="text" id="filtroBodega"
                   class="contr-input"
                   placeholder="🔍 Filtrar producto en bodega...">
        </div>

        <table class="contr-table contr-table-hover" id="tablaBodega">
            <thead class="contr-table-light">
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
                    <td class="contr-product-name">{{ $producto->nombre }}</td>
                    <td>
                        @if($producto->imagen)
                            <img src="{{ asset('storage/'.$producto->imagen) }}" class="contr-img">
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

</div>

<script>
function filtrarTabla(inputId, tablaId) {
    const filtro = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll(`#${tablaId} tbody tr`).forEach(fila => {
        const texto = fila.querySelector('.contr-product-name').textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
}

document.getElementById('filtroAlquilados')?.addEventListener('keyup',
    () => filtrarTabla('filtroAlquilados', 'tablaAlquilados'));

document.getElementById('filtroBodega')?.addEventListener('keyup',
    () => filtrarTabla('filtroBodega', 'tablaBodega'));
</script>

@endsection
