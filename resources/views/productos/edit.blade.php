@extends('layouts.app')
@section('title','Editar producto')
@section('header','Editar producto')

@section('content')
<h2>Editar producto</h2>

<form action="{{ route('productos.update',$producto) }}"
      method="POST"
      enctype="multipart/form-data"
      class="form">

    @csrf
    @method('PUT')

    <label>Nombre</label>
    <input name="nombre" value="{{ old('nombre',$producto->nombre) }}" required>

    <label>Categoría</label>
    <input
        name="categorias"
        value="{{ old('categorias', $producto->categorias) }}"
>

    <label>Cantidad</label>
    <input type="number" name="cantidad" min="0"
           value="{{ old('cantidad',$producto->cantidad) }}" required>

    <label>Costo</label>
    <input type="number" step="0.01" name="costo"
           value="{{ old('costo',$producto->costo) }}" required>

    <label>Ubicación</label>
    <input name="ubicacion" value="{{ old('ubicacion',$producto->ubicacion) }}">

    <label>Estado</label>
    <select name="estado" required>
        @foreach(['disponible','dañado','reservado'] as $e)
            <option value="{{ $e }}"
                @selected(old('estado',$producto->estado)==$e)>
                {{ ucfirst($e) }}
            </option>
        @endforeach
    </select>

    {{-- IMAGEN --}}
    <div style="margin-top:20px">
        <label>Imagen del producto</label>

        <div style="display:flex;align-items:center;gap:16px">
            <img
                id="preview"
                src="{{ $producto->imagen
                    ? asset('storage/'.$producto->imagen)
                    : asset('img/tool-placeholder.png') }}"
                style="width:100px;height:100px;object-fit:cover;border-radius:12px;border:1px solid #ddd"
            >

            <input
                type="file"
                name="imagen"
                accept="image/*"
                onchange="previewImage(event)"
            >
        </div>
    </div>

    <div class="form-actions">
        <button class="btn">Actualizar</button>
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

{{-- PREVIEW JS --}}
<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
}
</script>

@endsection
