@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
@endpush

@section('title', 'Reportes')
@section('header', 'Modulo de reportes')

@section('content')
    @php
        $cards = [
            [
                'chip' => 'Inventario',
                'title' => 'Entrada y salida de movimientos',
                'description' => 'Historial detallado de entradas, salidas y ajustes de inventario.',
                'route' => 'reportes.movimientos',
                'button' => 'primary',
            ],
            [
                'chip' => 'Inventario',
                'title' => 'Control de productos',
                'description' => 'Seguimiento de productos en bodega y equipos en alquiler.',
                'route' => 'reportes.controlproducto',
                'button' => 'success',
            ],
            [
                'chip' => 'Cartera',
                'title' => 'Clientes pendientes por cancelar',
                'description' => 'Control de deudas, mora y estado de cobro por cliente.',
                'route' => 'reportes.clientes-pendientes',
                'button' => 'warning',
            ],
            [
                'chip' => 'Direccion',
                'title' => 'Resumen general del sistema',
                'description' => 'KPIs de ventas, alquileres, finanzas, clientes e inventario.',
                'route' => 'reportes.generalrep',
                'button' => 'primary',
            ],
            [
                'chip' => 'Alquiler',
                'title' => 'Productos alquilados y detallado',
                'description' => 'Cliente, obra y detalle completo de productos alquilados.',
                'route' => 'reportes.productos-alquilados-detallado',
                'button' => 'success',
            ],
            [
                'chip' => 'Caja',
                'title' => 'Reporte diario de ingresos',
                'description' => 'Resumen diario de pagos por fecha, tipo de pago y cliente.',
                'route' => 'reportes.ingresos-diarios',
                'button' => 'warning',
            ],
            [
                'chip' => 'Cliente',
                'title' => 'Reporte detallado por cliente',
                'description' => 'Desglose completo por cliente con alquileres, cobros, incidencias y saldo final.',
                'route' => 'reportes.cliente-detallado',
                'button' => 'primary',
            ],
            [
                'chip' => 'Incidencias',
                'title' => 'Reporte de incidencias y dias no cobrados',
                'description' => 'Eventos que impactan el cobro: lluvia y otros no laborables con dias descontados.',
                'route' => 'reportes.incidencias-no-cobrados',
                'button' => 'warning',
            ],
            [
                'chip' => 'Costos',
                'title' => 'Reporte de perdidas y mantenimiento',
                'description' => 'Consolida costos por dano, perdida y mantenimiento para control y recuperacion financiera.',
                'route' => 'reportes.perdidas-mantenimiento',
                'button' => 'primary',
            ],
        ];
    @endphp

    <div class="report-container">
        <section class="report-hero">
            <div>
                <p class="report-eyebrow">Panel ejecutivo</p>
                <h2 class="report-title">Centro de reportes</h2>
                <p class="report-subtitle">
                    Accede a reportes operativos, financieros e inventario desde un solo lugar.
                </p>
            </div>
        </section>

        <div class="report-grid">
            @foreach($cards as $card)
                <article class="report-card">
                    <div class="report-card-top">
                        <span class="report-chip">{{ $card['chip'] }}</span>
                        <h5>{{ $card['title'] }}</h5>
                    </div>
                    <p>{{ $card['description'] }}</p>
                    <a href="{{ route($card['route']) }}" class="repor-btn repor-btn-{{ $card['button'] }}">
                        Ver reporte
                    </a>
                </article>
            @endforeach
        </div>
    </div>
@endsection
