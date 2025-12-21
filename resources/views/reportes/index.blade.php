@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
@endpush

@section('content')
<div class="container1">
    <h2 class="mb-4">📊 Módulo de Reportes</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-3">
                <div class="card-body text-center">
                    <h5>📦 Entradas y Salidas</h5>
                    <p>Historial detallado de movimientos</p>
                    <a href="{{ route('reportes.movimientos') }}" class="btn btn-primary">
                        Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-3">
                <div class="card-body text-center">
                    <h5>📅 Reporte Mensual</h5>
                    <p>Resumen general por mes</p>
                    <a href="{{ route('reportes.mensual') }}" class="btn btn-success">
                        Ver Reporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
