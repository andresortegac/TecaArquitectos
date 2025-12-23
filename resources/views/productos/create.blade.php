@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/producto-crea.css') }}">
@endpush

@section('title','Ingresar producto')
@section('header','Ingresar producto a bodega')

@section('content')
    <div class="crea_producto-page">
    

        <h2>Nuevo producto</h2>

        <form action="{{ route('productos.store') }}"  method="POST" enctype="multipart/form-data" class="form">

            @csrf

            <label>Nombre</label>
            <input name="nombre" value="{{ old('nombre') }}" required>

            <label>Categoría</label>
            <input name="categoria" value="{{ old('categoria') }}">

            <label>Cantidad</label>
            <input type="number" name="cantidad" min="0"
                value="{{ old('cantidad',0) }}" required>

            <label>Costo</label>
            <input type="number" step="0.01" name="costo"
                value="{{ old('costo',0) }}" required>

            <label>Ubicación</label>
            <input name="ubicacion" value="{{ old('ubicacion') }}">

            <label>Estado</label>
            <select name="estado" required>
                @foreach(['disponible','dañado','reservado'] as $e)
                    <option value="{{ $e }}" @selected(old('estado','disponible')==$e)>
                        {{ ucfirst($e) }}
                    </option>
                @endforeach
            </select>

            {{-- Imagen --}}
                    <div class="mt-6">
                        <label class="block text-sm text-gray-600 mb-2">Ingresar Imagen</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('img/tool-placeholder.jpg') }}"
                                class="w-20 h-20 rounded-lg border object-cover">
                            <input type="file" name="imagen"
                                class="block w-full text-sm text-gray-500">
                        </div>
                    </div>

            <div class="form-actions">
                <button class="btn">Guardar</button>
                <a class="btn-secondary" href="{{ route('productos.index') }}">Volver</a>
            </div>   

            @if($errors->any())
                <div class="alert danger">
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>

@endsection
