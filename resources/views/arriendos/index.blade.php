@extends('layouts.app')
@section('title','Arriendos')
@section('header','Arriendos')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:12px;">
    <h2>Lista de arriendos</h2>
    <a class="btn" href="{{ route('arriendos.create') }}">+ Nuevo arriendo</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Precio</th>
            <th>Estado</th>
            <th style="width:140px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
    @forelse($arriendos as $a)
        <tr>
            <td>{{ $a->cliente->nombre ?? '—' }}</td>
            <td>{{ $a->producto->nombre ?? '—' }}</td>
            <td>{{ $a->cantidad }}</td>
            <td>{{ $a->fecha_inicio }}</td>
            <td>{{ $a->fecha_fin ?? '—' }}</td>
            <td>${{ number_format($a->precio_total, 2) }}</td>
            <td>{{ ucfirst($a->estado) }}</td>
            <td>
                <a class="btn-sm" href="{{ route('arriendos.edit',$a) }}">Editar</a>
                <form action="{{ route('arriendos.destroy',$a) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn-sm danger" onclick="return confirm('¿Eliminar arriendo?')">
                        Borrar
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="8">No hay arriendos todavía.</td></tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $arriendos->links() }}
</div>

@endsection
