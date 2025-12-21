@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard General')

@section('content')
<div class="dashboard-cards">
    <div class="card">
        <h2>{{ $totalProductos }}</h2>
        <p>Total Productos</p>
    </div>

     <div class="card">
        <h2>{{ $totalMovimientos }}</h2>
        <p>Total Movimientos</p>
    </div>    

    <div class="card warning">
        <h2>{{ $sinStock }}</h2>
        <p>Sin Stock</p>
    </div>

    <div class="card danger">
        <h2>{{ $stockBajo }}</h2>
        <p>Stock Bajo</p>
    </div>
</div>

<div class="alertas">
    <h3>Alertas de Stock</h3>

    <div class="alertas-botones">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            Actualizar Dashboard
        </a>

        <a href="{{ route('productos.alertas') }}" class="btn btn-warning">
            Ver Alertas de Stock
        </a>

        <a href="{{ route('stock.index') }}" class="btn btn-success">
            Ver Stock Actual
        </a>
    </div>
</div>
@endsection
