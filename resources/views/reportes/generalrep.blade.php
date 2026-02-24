@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reportes-generalrep.css') }}">
@endpush

@section('title', 'Resumen general')
@section('header', 'Reporte general del negocio')

@section('content')
    @php
        $rg = $metricas['resumen_general'] ?? [];
        $rf = $metricas['resumen_financiero'] ?? [];
        $inv = $metricas['inventario'] ?? [];
        $cli = $metricas['clientes'] ?? [];
        $kpis = $metricas['kpis'] ?? [];
    @endphp

    <div class="gr-page">
        <section class="gr-hero">
            <div>
                <h2>Panel ejecutivo de indicadores</h2>
                <p>Resumen unificado de ventas, alquileres, finanzas, inventario, clientes y KPIs operativos.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="gr-btn-back">Volver</a>
        </section>

        <section class="gr-section">
            <h3>1. Resumen General</h3>
            <div class="gr-grid">
                <article class="gr-card"><span>Total de ventas</span><strong>{{ number_format($rg['total_ventas'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Total de alquileres</span><strong>{{ number_format($rg['total_alquileres'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Ingresos totales</span><strong>${{ number_format($rg['ingresos_totales'] ?? 0, 0) }}</strong></article>
                <article class="gr-card"><span>Ganancia estimada</span><strong>${{ number_format($rg['ganancia_estimada'] ?? 0, 0) }}</strong></article>
                <article class="gr-card"><span>Productos vendidos</span><strong>{{ number_format($rg['productos_vendidos'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Productos alquilados</span><strong>{{ number_format($rg['productos_alquilados'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Clientes registrados</span><strong>{{ number_format($rg['clientes_registrados'] ?? 0) }}</strong></article>
                <article class="gr-card">
                    <span>Usuarios activos (admin / vendedor)</span>
                    <strong>{{ number_format($rg['usuarios_activos'] ?? 0) }}</strong>
                    <small>{{ $rg['usuarios_activos_detalle'] ?? 'Admin: 0 / Vendedor: 0' }}</small>
                </article>
            </div>
        </section>

        <section class="gr-section">
            <h3>2. Resumen Financiero</h3>
            <div class="gr-grid">
                <article class="gr-card"><span>Ingresos por ventas</span><strong>${{ number_format($rf['ingresos_ventas'] ?? 0, 0) }}</strong></article>
                <article class="gr-card"><span>Ingresos por alquileres</span><strong>${{ number_format($rf['ingresos_alquileres'] ?? 0, 0) }}</strong></article>
                <article class="gr-card"><span>Abonos pendientes</span><strong>{{ number_format($rf['abonos_pendientes'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Saldo por cobrar</span><strong>${{ number_format($rf['saldo_por_cobrar'] ?? 0, 0) }}</strong></article>
                <article class="gr-card"><span>Multas generadas</span><strong>${{ number_format($rf['multas_generadas'] ?? 0, 0) }}</strong></article>
                <article class="gr-card"><span>Productos más rentables</span><strong>{{ $rf['productos_mas_rentables'] ?? '-' }}</strong></article>
            </div>
        </section>

        <section class="gr-section">
            <h3>3. Inventario</h3>
            <div class="gr-grid">
                <article class="gr-card"><span>Total de productos</span><strong>{{ number_format($inv['total_productos'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Productos disponibles</span><strong>{{ number_format($inv['productos_disponibles'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Productos alquilados actualmente</span><strong>{{ number_format($inv['productos_alquilados_actualmente'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Productos con bajo stock</span><strong>{{ number_format($inv['productos_bajo_stock'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Productos fuera de inventario</span><strong>{{ number_format($inv['productos_fuera_inventario'] ?? 0) }}</strong></article>
            </div>
        </section>

        <section class="gr-section">
            <h3>4. Clientes</h3>
            <div class="gr-grid">
                <article class="gr-card"><span>Total de clientes</span><strong>{{ number_format($cli['total_clientes'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Clientes con deudas</span><strong>{{ number_format($cli['clientes_con_deudas'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Clientes frecuentes</span><strong>{{ number_format($cli['clientes_frecuentes'] ?? 0) }}</strong></article>
                <article class="gr-card"><span>Nuevos clientes del mes</span><strong>{{ number_format($cli['nuevos_clientes_mes'] ?? 0) }}</strong></article>
            </div>
        </section>

        <section class="gr-section">
            <h3>5. Indicadores (KPIs)</h3>
            <div class="gr-grid">
                <article class="gr-card"><span>Producto más vendido</span><strong>{{ $kpis['producto_mas_vendido'] ?? '-' }}</strong></article>
                <article class="gr-card"><span>Producto más alquilado</span><strong>{{ $kpis['producto_mas_alquilado'] ?? '-' }}</strong></article>
                <article class="gr-card"><span>Mes con mayores ingresos</span><strong>{{ $kpis['mes_mayores_ingresos'] ?? '-' }}</strong></article>
                <article class="gr-card"><span>Usuario con más ventas</span><strong>{{ $kpis['usuario_mas_ventas'] ?? '-' }}</strong></article>
                <article class="gr-card"><span>Crecimiento mensual (%)</span><strong>{{ number_format((float) ($kpis['crecimiento_mensual'] ?? 0), 2) }}%</strong></article>
            </div>
        </section>
    </div>
@endsection
