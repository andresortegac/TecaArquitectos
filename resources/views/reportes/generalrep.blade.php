@extends('layouts.app')

@section('title','Dashboard')
@section('header','Panel General')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/res-dashboard.css') }}">
@endpush
 
@section('content')

<div class="dash-wrapper">

    <h2 class="dash-title">📊 Resumen General del Sistema</h2>

    {{-- CARDS --}}
    <div class="dash-cards">

        <div class="dash-card">
            <span class="dash-icon">📦</span>
            <div>
                <h4>{{ $totalProductos }}</h4>
                <small>Total productos</small>
            </div>
        </div>

        <div class="dash-card">
            <span class="dash-icon">🔁</span>
            <div>
                <h4>{{ $totalMovimientos }}</h4>
                <small>Movimientos</small>
            </div>
        </div>

        <div class="dash-card warning">
            <span class="dash-icon">⚠️</span>
            <div>
                <h4>{{ $stockBajo }}</h4>
                <small>Stock bajo</small>
            </div>
        </div>

        <div class="dash-card danger">
            <span class="dash-icon">❌</span>
            <div>
                <h4>{{ $sinStock }}</h4>
                <small>Sin stock</small>
            </div>
        </div>

        @isset($totalSolicitudes)
        <div class="dash-card info">
            <span class="dash-icon">📄</span>
            <div>
                <h4>{{ $totalSolicitudes }}</h4>
                <small>Solicitudes</small>
            </div>
        </div>
        @endisset

    </div>

</div>

@endsection
