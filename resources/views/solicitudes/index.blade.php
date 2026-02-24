@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">
@endpush

@section('title', 'Solicitudes')
@section('header', 'Solicitudes de Alquiler')

@section('content')
<div class="solicitud-page">
    @if(session('success'))
        <div class="sol-alert sol-alert-success">{{ session('success') }}</div>
    @endif

    <section class="sol-hero">
        <div>
            <h2>Solicitudes recibidas</h2>
            <p>Consulta el estado de cada solicitud y revisa los detalles antes de aprobar.</p>
        </div>
        <div class="sol-hero-actions">
            <a href="{{ route('dashboard') }}" class="sol-btn sol-btn-light">Volver</a>
        </div>
    </section>

    <section class="sol-card">
        <div class="sol-table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Fecha solicitud</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $s)
                        @php
                            $estaAprobada = (($aprobadasPorArriendo[$s->id] ?? 0) > 0);
                        @endphp
                        <tr>
                            <td>{{ $s->cliente->nombre }}</td>
                            <td>{{ $s->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="estado {{ $estaAprobada ? 'aprobado' : 'pendiente' }}">
                                    {{ $estaAprobada ? 'Aprobado con exito' : 'Pendiente' }}
                                </span>
                            </td>
                            <td>
                                <a class="sol-btn sol-btn-warning" href="{{ route('solicitudes.show', $s->id) }}">Revisar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="sol-empty">No hay solicitudes registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
