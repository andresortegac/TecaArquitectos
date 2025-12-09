@extends('layouts.app')
@section('title','Clientes')
@section('header','Clientes')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:12px;">
    <h2>Lista de clientes</h2>
    <a class="btn" href="{{ route('clientes.create') }}">+ Nuevo</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Documento</th>
            <th style="width:140px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
    @forelse($clientes as $cliente)
        <tr>
            <td>{{ $cliente->nombre }}</td>
            <td>{{ $cliente->telefono }}</td>
            <td>{{ $cliente->email }}</td>
            <td>{{ $cliente->documento }}</td>
            <td>
                <a class="btn-sm" href="{{ route('clientes.edit',$cliente) }}">Editar</a>
                <form action="{{ route('clientes.destroy',$cliente) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn-sm danger" onclick="return confirm('¿Eliminar?')">
                        Borrar
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5">No hay clientes todavía.</td></tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top:12px;">
    {{ $clientes->links() }}
</div>
@endsection
