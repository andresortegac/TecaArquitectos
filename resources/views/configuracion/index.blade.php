@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/configuracion.css') }}">
@endpush

@section('content')
<div class="container config-container">

    <h2 class="mb-4 config-title">⚙️ Configuración del Sistema</h2>

    <ul class="nav nav-tabs config-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#stock">📦 Stock</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#reportes">📊 Reportes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#inventario">🏗️ Inventario</a>
        </li>
    </ul>

    <div class="tab-content mt-4">

        <!-- STOCK -->
        <div class="tab-pane fade show active config-section" id="stock">
            <div class="card config-card">
                <div class="card-body">
                    <h5>Stock mínimo</h5>

                    <div class="mb-3">
                        <label class="form-label">Stock mínimo global</label>
                        <input type="number" class="form-control" value="10">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">
                            Activar alertas de stock bajo
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- REPORTES -->
        <div class="tab-pane fade config-section" id="reportes">
            <div class="card config-card">
                <div class="card-body">
                    <h5>Configuración de Reportes</h5>

                    <div class="mb-3">
                        <label class="form-label">Mes por defecto</label>
                        <select class="form-select">
                            <option>Enero</option>
                            <option>Febrero</option>
                            <option>Marzo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- INVENTARIO -->
        <div class="tab-pane fade config-section" id="inventario">
            <div class="card config-card">
                <div class="card-body">
                    <h5>Opciones de Inventario</h5>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">
                            Bloquear salidas sin stock
                        </label>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
