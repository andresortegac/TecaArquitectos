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

<table class="table table-shadow">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clientes as $cliente)
            <tr>
                <td>{{ $cliente->nombre }}</td>
                <td>{{ $cliente->documento }}</td>
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
                <td colspan="3">No hay clientes</td>
            </tr>
        @endforelse
    </tbody>
</table>



{{ $clientes->links() }}
@endsection
