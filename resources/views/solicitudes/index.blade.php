@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">
@endpush

@section('title','Solicitudes')
@section('header','Solicitudes de Alquiler')

@section('content')
<div class="solicitud-page">   

    <h2>Solicitudes recibidas</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Fecha Solicitud</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitudes as $s)
                <tr>
                    <td>{{ $s->cliente->nombre }}</td>

                    <td>{{ $s->created_at->format('Y-m-d') }}</td>

                    <td>
                        <span class="estado pendiente">
                            Pendiente
                        </span>
                    </td>

                    <td>
                        <a class="btn btn-warning" href="{{ route('arriendos.show', $s->id) }}">
                            Revisar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay solicitudes registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
