@extends('layouts.app')

@section('title','Solicitudes')
@section('header','Solicitudes de Alquiler')

@section('content')

<h2>Solicitudes recibidas</h2>

<table class="table">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Obra</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($solicitudes as $s)
        <tr>
            <td>{{ $s->cliente_nombre }}</td>
            <td>{{ $s->obra_nombre }}</td>
            <td>{{ ucfirst($s->estado) }}</td>
            <td>
                <a class="btn-sm" href="{{ route('solicitudes.show',$s) }}">
                    Revisar
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
