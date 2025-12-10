@extends('layouts.app')
@section('title','Bodega - Productos')
@section('header','Bodega / Inventario')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:12px;">
    <h2>Productos en bodega</h2>
    <a class="btn" href="{{ route('productos.create') }}">+ Ingresar producto</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Cantidad</th>
            <th>Costo</th>
            <th>Ubicación</th>
            <th>Estado</th>
            <th style="width:140px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
    @forelse($productos as $p)
        <tr>
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->categoria ?? '—' }}</td>
            <td>{{ $p->cantidad }}</td>
            <td>${{ number_format($p->costo, 2) }}</td>
            <td>{{ $p->ubicacion ?? '—' }}</td>
            <td>{{ ucfirst($p->estado) }}</td>
            <td>
                <a class="btn-sm" href="{{ route('productos.edit',$p) }}">Editar</a>
                <form action="{{ route('productos.destroy',$p) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn-sm danger" onclick="return confirm('¿Eliminar producto?')">
                        Borrar
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7">No hay productos todavía.</td></tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $productos->links() }}
</div>

@endsection
