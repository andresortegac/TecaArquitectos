@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
@endpush
    @section('title','Rep-principal')
    @section('header','REPORTES PRINCIPAL')
        @section('content')
            <div class="report-container">

                <h2 class="report-title">📊 Módulo de Reportes</h2>

                <div class="report-grid">

                    {{-- Card 1 --}}
                    <div class="report-card">
                        <div class="report-card-body">
                            <h5>📦 Entradas y Salidas</h5>
                            <p>Historial detallado de movimientos</p>

                            <a href="{{ route('reportes.movimientos') }}"
                            class="repor-btn repor-btn-primary">
                                Ver Reporte
                            </a>
                        </div>
                    </div>

                    {{-- Card 2 --}}
                    <div class="report-card">
                        <div class="report-card-body">
                            <h5>📅 Reporte Mensual</h5>
                            <p>Resumen general por mes</p>

                            <a href="{{ route('reportes.mensual') }}"
                            class="repor-btn repor-btn-success">
                                Ver Reporte
                            </a>
                        </div>
                    </div>
                    {{-- Card 3 --}}
                    <div class="report-card">
                        <div class="report-card-body">
                            <h5>CONTROL DE PRODUCTOS</h5>
                            <p>Seguimiento de Producto</p>

                            <a href="{{ route('reportes.controlproducto') }}"
                            class="repor-btn repor-btn-success">
                                Ver Reporte
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        @endsection
