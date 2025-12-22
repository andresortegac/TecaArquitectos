@extends('layouts.app')

@section('title','Productos')
@section('header','Inventario de productos')

{{-- CSS --}}
@section('styles')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endsection

@section('content')

{{-- FORMULARIO IMPORTAR EXCEL --}}
<div class="container import-wrapper">
    <form action="{{ route('productos.import') }}"
          method="POST"
          enctype="multipart/form-data"
          class="mb-6 import-form">
        @csrf

        <input type="file" name="archivo" required>

        <button class="btn">
            Importar Excel
        </button>
    </form>
</div>

<div class="container mx-auto px-4">

    {{-- Controles --}}
    <div class="flex flex-col md:flex-row gap-4 mb-6">

        {{-- Buscador --}}
        <input
            type="text"
            id="search"
            placeholder="Buscar producto..."
        >

        {{-- Filtro --}}
        <select id="filterCategoria">
            <option value="">Todas las categorías</option>
            <option value="estructura y soportes">Estructura y soportes</option>
            <option value="encofrado">Encofrado</option>
            <option value="accesorio de seguridad">Accesorios de Seguridad</option>
            <option value="herramientas y equipos">Herramientas y Equipos</option>
            <option value="electricidad">Electricidad</option>
            <option value="Expecial">Expecial</option>
        </select>


    </div>

    {{-- Tarjetas --}}
    <div id="cards">

        @foreach($productos as $producto)
      <div
            class="producto-card"
            data-nombre="{{ strtolower($producto->nombre) }}"
            data-categoria="{{ strtolower($producto->categorias ?? '') }}"
        >



            {{-- Imagen --}}
            <img 
            src="{{ asset($producto->imagen) }}" 
            alt="{{ $producto->nombre }}" 
            class="img-fluid"
            style="height:180px; object-fit:contain;">


            {{-- Nombre --}}
            <h3>{{ $producto->nombre }}</h3>

            {{-- Categoría --}}
            <p>{{ $producto->categorias ?? 'Sin descripción' }}</p>

            {{-- Info --}}
            <p><strong>Cantidad:</strong> {{ $producto->cantidad }}</p>
            <p><strong>Ubicación:</strong> {{ $producto->ubicacion }}</p>
            <p><strong>Costo:</strong> ${{ number_format($producto->costo, 2) }}</p>

            {{-- Estado --}}
            <div class="text-center">
                <span class="estado {{ $producto->estado }}">
                    {{ ucfirst($producto->estado) }}
                </span>
            </div>

            {{-- Acciones --}}
            <div class="producto-acciones">
                <a href="{{ route('productos.edit', $producto) }}">
                    Editar
                </a>

                <form action="{{ route('productos.destroy', $producto) }}"
                      method="POST"
                      onsubmit="return confirm('¿Eliminar este producto?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit">
                        Eliminar
                    </button>
                </form>
            </div>

        </div>
        @endforeach

    </div>

    {{-- Vacío --}}
    @if($productos->isEmpty())
        <p class="text-center text-gray-500 mt-10">
            No hay productos registrados.
        </p>
    @endif
</div>
@php
    $totalProductos = $productos->count();
    $totalStock = $productos->sum('cantidad');
@endphp

    <div class="producstock-wrapper">
        <div class="flex gap-4 my-6">
            <div class="producstock-card bg-blue">
                <h2>{{ $totalProductos }}</h2>
                <p>Productos</p>
            </div>

            <div class="producstock-card bg-green">
                <h2>{{ $totalStock }}</h2>
                <p>Unidades en stock</p>
            </div>
        </div>
    </div>
{{-- JS --}}
<script>
    const search = document.getElementById('search');
    const filterCategoria = document.getElementById('filterCategoria');
    const cards  = document.querySelectorAll('.producto-card');

    function normalizar(texto) {
        return (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function filtrar() {
        const texto = normalizar(search.value);
        const categoria = normalizar(filterCategoria.value);

        cards.forEach(card => {
            const nombre = normalizar(card.dataset.nombre);
            const categoriaCard = normalizar(card.dataset.categoria);

            const matchNombre = nombre.includes(texto);
            const matchCategoria = !categoria || categoriaCard === categoria;

            card.style.display = (matchNombre && matchCategoria)
                ? 'block'
                : 'none';
        });
    }

    search.addEventListener('input', filtrar);
    filterCategoria.addEventListener('change', filtrar);
</script>


@endsection