@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('title', 'Productos')
@section('header', 'Inventario de productos')

@section('content')
    <div class="pro-page">
        <section class="pro-hero">
            <div>
                <h2>Inventario general</h2>
                <p>Consulta, filtra y administra los productos registrados en bodega.</p>
            </div>
            <div class="pro-hero-actions">
                @role('admin')
                    <a href="{{ route('productos.create') }}" class="pro-btn pro-btn-primary">Nuevo producto</a>
                @endrole
            </div>
        </section>

        @role('admin')
            <section class="pro-card">
                <form action="{{ route('productos.import') }}" method="POST" enctype="multipart/form-data" class="pro-import-form">
                    @csrf
                    <label for="archivo" class="pro-upload-box">
                        <span class="pro-upload-title">Subir archivo de inventario</span>
                        <span class="pro-upload-subtitle">Formatos permitidos: .xlsx, .csv</span>
                        <span id="pro-upload-name" class="pro-upload-name">Ningún archivo seleccionado</span>
                    </label>
                    <input id="archivo" type="file" name="archivo" accept=".xlsx,.csv" required>
                    <button class="pro-btn pro-btn-secondary">Importar archivo</button>
                </form>
            </section>
        @endrole

        <section class="pro-kpis">
            <article><span>Productos</span><strong>{{ number_format($resumen['total_productos'] ?? 0) }}</strong></article>
            <article><span>Unidades en stock</span><strong>{{ number_format($resumen['total_stock'] ?? 0) }}</strong></article>
        </section>

        <section class="pro-card">
            <form method="GET" class="pro-filters">
                <div class="field field-grow">
                    <label for="q">Buscar producto</label>
                    <input id="q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nombre de producto">
                </div>
                <div class="field">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria }}" {{ ($filters['categoria'] ?? '') === $categoria ? 'selected' : '' }}>
                                {{ $categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="actions">
                    <button type="submit">Filtrar</button>
                    <a href="{{ route('productos.index') }}">Limpiar</a>
                </div>
            </form>

            <div class="pro-grid">
                @forelse($productos as $producto)
                    <article class="producto-card">
                        <img
                            src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('img/product-icon.svg') }}"
                            alt="{{ $producto->nombre }}">

                        <h3>{{ $producto->nombre }}</h3>
                        <p>{{ $producto->categorias ?? 'Sin categoría' }}</p>
                        <p><strong>Cantidad:</strong> {{ $producto->cantidad }}</p>
                        <p><strong>Ubicación:</strong> {{ $producto->ubicacion ?: '-' }}</p>
                        <p><strong>Costo:</strong> ${{ number_format((float) $producto->costo, 2) }}</p>

                        <div class="text-center">
                            <span class="estado {{ $producto->estado }}">
                                {{ strtoupper($producto->estado) }}
                            </span>
                        </div>

                        @role('admin')
                            <div class="producto-acciones">
                                <a href="{{ route('productos.edit', $producto) }}">Editar</a>
                                <form
                                    action="{{ route('productos.destroy', $producto) }}"
                                    method="POST"
                                    class="delete-product-form"
                                    data-product-name="{{ $producto->nombre }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="open-delete-modal">Eliminar</button>
                                </form>
                            </div>
                        @endrole
                    </article>
                @empty
                    <p class="pro-empty">No hay productos registrados para los filtros seleccionados.</p>
                @endforelse
            </div>

            @if($productos->hasPages())
                <div class="pro-pagination">
                    @if($productos->onFirstPage())
                        <span class="page-btn page-disabled">Anterior</span>
                    @else
                        <a class="page-btn" href="{{ $productos->previousPageUrl() }}">Anterior</a>
                    @endif
                    <span class="page-text">Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
                    @if($productos->hasMorePages())
                        <a class="page-btn" href="{{ $productos->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="page-btn page-disabled">Siguiente</span>
                    @endif
                </div>
            @endif
        </section>
    </div>

    @role('admin')
        <div id="delete-modal" class="pro-modal" aria-hidden="true">
            <div class="pro-modal-backdrop"></div>
            <div class="pro-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
                <h3 id="delete-modal-title">Confirmar eliminación</h3>
                <p>
                    ¿Seguro que deseas eliminar el producto
                    <strong id="delete-modal-product-name"></strong>?
                </p>
                <div class="pro-modal-actions">
                    <button type="button" class="pro-btn pro-btn-light" id="delete-modal-cancel">Cancelar</button>
                    <button type="button" class="pro-btn pro-btn-danger" id="delete-modal-confirm">Eliminar</button>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const input = document.getElementById('archivo');
                const name = document.getElementById('pro-upload-name');
                if (!input || !name) return;
                input.addEventListener('change', function () {
                    const file = input.files && input.files[0];
                    name.textContent = file ? file.name : 'Ningún archivo seleccionado';
                });
            })();

            (function () {
                const modal = document.getElementById('delete-modal');
                const productNameEl = document.getElementById('delete-modal-product-name');
                const cancelBtn = document.getElementById('delete-modal-cancel');
                const confirmBtn = document.getElementById('delete-modal-confirm');
                const openButtons = document.querySelectorAll('.open-delete-modal');
                let activeForm = null;

                if (!modal || !productNameEl || !cancelBtn || !confirmBtn || !openButtons.length) return;

                const closeModal = () => {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    activeForm = null;
                };

                const openModal = (form) => {
                    activeForm = form;
                    productNameEl.textContent = form.dataset.productName || 'seleccionado';
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                };

                openButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const form = button.closest('.delete-product-form');
                        if (!form) return;
                        openModal(form);
                    });
                });

                cancelBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', (event) => {
                    if (event.target.classList.contains('pro-modal-backdrop')) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                        closeModal();
                    }
                });

                confirmBtn.addEventListener('click', () => {
                    if (!activeForm) return;
                    activeForm.submit();
                });
            })();
        </script>
    @endrole
@endsection
