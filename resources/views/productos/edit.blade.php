@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/producto-editar.css') }}">
@endpush

@section('title', 'Editar producto')
@section('header', 'Editar producto')

@section('content')
    <div class="pe-page">
        <section class="pe-hero">
            <div>
                <h2>Editar producto</h2>
                <p>Actualiza la informaciÃ³n del producto y su estado en inventario.</p>
            </div>
            <a href="{{ route('productos.index') }}" class="pe-btn-secondary">Volver</a>
        </section>

        <section class="pe-card">
            @if($errors->any())
                <div class="pe-errors">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data" class="pe-form">
                @csrf
                @method('PUT')

                <div class="field field-grow">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                </div>

                <div class="field">
                    <label for="categorias">CategorÃ­a</label>
                    <input id="categorias" name="categorias" value="{{ old('categorias', $producto->categorias) }}">
                </div>

                <div class="field">
                    <label for="cantidad">Cantidad</label>
                    <input id="cantidad" type="number" name="cantidad" min="0" value="{{ old('cantidad', $producto->cantidad) }}" required>
                </div>

                <div class="field">
                    <label for="costo">Costo</label>
                    <input id="costo" type="number" step="0.01" name="costo" value="{{ old('costo', $producto->costo) }}" required>
                </div>

                <div class="field">
                    <label for="ubicacion">UbicaciÃ³n</label>
                    <input id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $producto->ubicacion) }}">
                </div>

                <div class="field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        @foreach(['disponible', 'daÃ±ado', 'reservado'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $producto->estado) === $estado)>
                                {{ ucfirst($estado) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field field-grow">
                    <label for="imagen">Imagen del producto</label>
                    <div class="pe-image-row">
                        <img
                            id="preview"
                            src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('img/product-icon.svg') }}"
                            alt="{{ $producto->nombre }}"
                            class="pe-image-preview">
                        <label for="imagen" class="pe-upload-box">
                            <span class="pe-upload-title">Subir imagen del producto</span>
                            <span class="pe-upload-subtitle">Formatos permitidos: JPG, PNG, WEBP (mÃ¡x. 5 MB)</span>
                            <span id="pe-upload-name" class="pe-upload-name">{{ $producto->imagen ? basename($producto->imagen) : 'Ningún archivo seleccionado' }}</span>
                        </label>
                        <input id="imagen" type="file" name="imagen" accept="image/*">
                    </div>
                </div>

                <div class="pe-actions">
                    <button type="submit" class="pe-btn-primary">Actualizar producto</button>
                    <a class="pe-btn-secondary" href="{{ route('productos.index') }}">Cancelar</a>
                </div>
            </form>
        </section>
    </div>

    <script>
        (function () {
            const input = document.getElementById('imagen');
            const preview = document.getElementById('preview');
            const fileName = document.getElementById('pe-upload-name');
            if (!input || !preview || !fileName) return;

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) {
                    fileName.textContent = '{{ $producto->imagen ? basename($producto->imagen) : 'Ningún archivo seleccionado' }}';
                    preview.src = '{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('img/product-icon.svg') }}';
                    return;
                }
                preview.src = URL.createObjectURL(file);
                fileName.textContent = file.name;
            });
        })();
    </script>
@endsection

