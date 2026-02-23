@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/producto-crea.css') }}">
@endpush

@section('title', 'Ingresar producto')
@section('header', 'Ingresar producto a bodega')

@section('content')
    <div class="pc-page">
        <section class="pc-hero">
            <div>
                <h2>Nuevo producto</h2>
                <p>Registra herramientas para mantener el inventario actualizado y trazable.</p>
            </div>
            <a href="{{ route('productos.index') }}" class="pc-btn-secondary">Volver</a>
        </section>

        <section class="pc-card">
            @if($errors->any())
                <div class="pc-errors">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="pc-form">
                @csrf

                <div class="field field-grow">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>

                <div class="field">
                    <label for="categorias">Categoría</label>
                    <input id="categorias" name="categorias" value="{{ old('categorias') }}">
                </div>

                <div class="field">
                    <label for="cantidad">Cantidad</label>
                    <input id="cantidad" type="number" name="cantidad" min="0" value="{{ old('cantidad', 0) }}" required>
                </div>

                <div class="field">
                    <label for="costo">Costo</label>
                    <input id="costo" type="number" step="0.01" name="costo" value="{{ old('costo', 0) }}" required>
                </div>

                <div class="field">
                    <label for="ubicacion">Ubicación</label>
                    <input id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}">
                </div>

                <div class="field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        @foreach(['disponible', 'dañado', 'reservado'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', 'disponible') === $estado)>
                                {{ ucfirst($estado) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field field-grow">
                    <label for="imagen">Imagen del producto</label>
                    <div class="pc-image-row">
                        <img id="preview" src="{{ asset('img/product-icon.svg') }}" alt="Vista previa" class="pc-image-preview">
                        <label for="imagen" class="pc-upload-box">
                            <span class="pc-upload-title">Subir imagen del producto</span>
                            <span class="pc-upload-subtitle">Formatos permitidos: JPG, PNG, WEBP (máx. 5 MB)</span>
                            <span id="pc-upload-name" class="pc-upload-name">Ningún archivo seleccionado</span>
                        </label>
                        <input id="imagen" type="file" name="imagen" accept="image/*">
                    </div>
                </div>

                <div class="pc-actions">
                    <button type="submit" class="pc-btn-primary">Guardar producto</button>
                    <a class="pc-btn-secondary" href="{{ route('productos.index') }}">Cancelar</a>
                </div>
            </form>
        </section>
    </div>

    <script>
        (function () {
            const input = document.getElementById('imagen');
            const preview = document.getElementById('preview');
            const fileName = document.getElementById('pc-upload-name');
            if (!input || !preview || !fileName) return;

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) {
                    fileName.textContent = 'Ningún archivo seleccionado';
                    preview.src = '{{ asset('img/product-icon.svg') }}';
                    return;
                }
                preview.src = URL.createObjectURL(file);
                fileName.textContent = file.name;
            });
        })();
    </script>
@endsection
