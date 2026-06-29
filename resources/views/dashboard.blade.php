@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/res-dashboard.css') }}">
@endpush

@section('title', 'Dashboard')
@section('header', 'Dashboard General')

@section('content')
    <div class="dash-page">
        <section class="dash-hero">
            <div>
                <p class="dash-eyebrow">Panel de control</p>
                <h2>Resumen operativo del sistema</h2>
                <p>Monitorea inventario, movimientos, solicitudes y clientes en tiempo real.</p>
            </div>
            <div class="dash-actions">
                <a href="{{ route('dashboard') }}" class="dash-btn dash-btn-secondary">Actualizar</a>
                <a href="{{ route('productos.alertas') }}" class="dash-btn dash-btn-warning">Alertas de stock</a>
                <a href="{{ route('stock.index') }}" class="dash-btn dash-btn-primary">Ver stock</a>
            </div>
        </section>

        <section class="dash-grid">
            <article class="dash-card">
                <span class="dash-label">Total productos</span>
                <strong>{{ number_format($totalProductos) }}</strong>
            </article>

            <article class="dash-card">
                <span class="dash-label">Total movimientos</span>
                <strong>{{ number_format($totalMovimientos) }}</strong>
            </article>

            <article class="dash-card">
                <span class="dash-label">Solicitudes</span>
                <strong>{{ number_format($totalSolicitudes) }}</strong>
            </article>

            <article class="dash-card">
                <span class="dash-label">Clientes registrados</span>
                <strong>{{ number_format($totalClientes) }}</strong>
            </article>

            <article class="dash-card dash-alert">
                <span class="dash-label">Sin stock</span>
                <strong>{{ number_format($sinStock) }}</strong>
            </article>

            <article class="dash-card dash-warn">
                <span class="dash-label">Stock bajo</span>
                <strong>{{ number_format($stockBajo) }}</strong>
            </article>
        </section>
    </div>
@endsection
