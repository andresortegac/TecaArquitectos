@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/clientes-obras.css') }}">
@endpush

@section('title','Clientes')
@section('header','Clientes')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between;">
    <h2>Lista de clientes</h2>
    <a class="btn" href="{{ route('clientes.create') }}">+ Nuevo</a>
</div>

<form method="GET" class="clientes-filters" data-live-filter>
    <label for="q">Buscar cliente</label>
    <input
        id="q"
        type="search"
        name="q"
        value="{{ $filters['q'] ?? '' }}"
        placeholder="Nombre, documento, celular o correo"
        autocomplete="off">
    <a href="{{ route('clientes.index') }}">Limpiar</a>
</form>

<table class="table table-shadow">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Celular</th>
            <th>Correo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clientes as $cliente)
            <tr>
                <td>{{ $cliente->nombre }}</td>
                <td>{{ $cliente->documento }}</td>
                <td>{{ $cliente->telefono }}</td>
                <td>{{ $cliente->email }}</td>
                <td>
                    <div class="acciones-left">
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn-action btn-ver">
                            Ver
                        </a>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn-action btn-editar">
                            Editar
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No hay clientes para la búsqueda ingresada.</td>
            </tr>
        @endforelse
    </tbody>
</table>



{{ $clientes->links() }}
@endsection
