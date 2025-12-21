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
                @foreach($solicitudes as $s)
                <tr>
                    <td>{{ $s->nombre_cliente }}</td>
                    <td>{{ $s->fecha_solicitud }}</td>
                    <td>{{ ucfirst(str_replace('_',' ',$s->estado)) }}</td>
                    <td>
                        <a class="btn btn-warning" href="{{ route('solicitudes.show', $s->id) }}">
                            Revisar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
