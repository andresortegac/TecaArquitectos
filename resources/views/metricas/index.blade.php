@extends('layouts.app')

@section('title', 'Metricas')
@section('header', 'REPORTES DE METRICAS')

@section('content')
<div class="container">

    <h2 class="mb-4">📊 Métricas de Ventas y Alquiler</h2>

    {{-- 1️⃣ Totales --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6>Total Ventas</h6>
                    <h3>${{ number_format($totalVentas) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h6>Total Arriendos</h6>
                    <h3>${{ number_format($totalArriendos) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 2️⃣ Promedios --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6>Ticket Promedio Venta</h6>
                    <h4>${{ number_format($avgVenta, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6>Ticket Promedio Arriendo</h6>
                    <h4>${{ number_format($avgArriendo, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- 3️⃣ Gráfica --}}
    <div class="card">
        <div class="card-body">
            <canvas id="metricasChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ventas = @json(array_values($ventasMensuales->toArray()));
    const arriendos = @json(array_values($arriendosMensuales->toArray()));
    const labels = @json(array_keys($ventasMensuales->toArray()));

    new Chart(document.getElementById('metricasChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Ventas', data: ventas },
                { label: 'Arriendos', data: arriendos }
            ]
        }
    });
</script>
@endsection
