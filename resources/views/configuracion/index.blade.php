@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/configuracion.css') }}">
@endpush

@section('title', 'Configuración')
@section('header', 'VER CONFIGURACIÓN')

@section('content')
<div class="conf-container">

    <h2 class="conf-title">⚙️ Configuración del Sistema</h2>

    {{-- TABS --}}
    <ul class="conf-tabs">
        <li class="conf-tab-item">
            <a class="conf-tab-link active" data-tab="stock">📦 Stock</a>
        </li>
        <li class="conf-tab-item">
            <a class="conf-tab-link" data-tab="reportes">📊 Reportes</a>
        </li>
        <li class="conf-tab-item">
            <a class="conf-tab-link" data-tab="inventario">🏗️ Inventario</a>
        </li>
    </ul>

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- CONTENIDO --}}
    <div class="conf-content">

        {{-- ================= STOCK ================= --}}
        <div class="conf-pane active" id="stock">
            <form method="POST" action="{{ route('config.stock') }}" class="conf-card">
                @csrf

                <h5>Stock mínimo</h5>

                <div class="conf-group">
                    <label class="conf-label">Stock mínimo global</label>
                    <input
                        type="number"
                        name="stock_minimo"
                        class="conf-input"
                        min="0"
                        value="{{ old('stock_minimo', $config->stock_minimo) }}"
                        required
                    >
                </div>

                <div class="conf-check">
                    <input
                        type="checkbox"
                        name="alerta_stock"
                        {{ old('alerta_stock', $config->alerta_stock) ? 'checked' : '' }}
                    >
                    <label>Activar alertas de stock bajo</label>
                </div>

                <br>
                <button type="submit" class="btn btn-praimary">Guardar</button>
            </form>
        </div>

        {{-- ================= REPORTES ================= --}}
        <div class="conf-pane" id="reportes">
            <form method="POST" action="{{ route('config.reportes') }}" class="conf-card">
                @csrf

                <h5>Configuración de Reportes</h5>

                <div class="conf-group">
                    <label class="conf-label">Mes por defecto</label>
                    <select name="mes_defecto" class="conf-select">
                        @foreach($meses as $mes)
                            <option value="{{ $mes }}"
                                {{ old('mes_defecto', $config->mes_defecto) === $mes ? 'selected' : '' }}>
                                {{ $mes }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <br>
                <button type="submit" class="btn btn-praimary">Guardar</button>
            </form>
        </div>

        {{-- ================= INVENTARIO ================= --}}
        <div class="conf-pane" id="inventario">
            <form method="POST" action="{{ route('config.inventario') }}" class="conf-card">
                @csrf

                <h5>Opciones de Inventario</h5>

                <div class="conf-check">
                    <input
                        type="checkbox"
                        name="bloquear_sin_stock"
                        {{ old('bloquear_sin_stock', $config->bloquear_sin_stock) ? 'checked' : '' }}
                    >
                    <label>Bloquear salidas sin stock</label>
                </div>
                <br>
                <button type="submit" class="btn btn-praimary">Guardar</button>
            </form>
        </div>

    </div>
</div>

{{-- JS TABS --}}
<script>
document.querySelectorAll('.conf-tab-link').forEach(tab => {
    tab.addEventListener('click', () => {

        document.querySelectorAll('.conf-tab-link')
            .forEach(t => t.classList.remove('active'));

        document.querySelectorAll('.conf-pane')
            .forEach(p => p.classList.remove('active'));

        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});
</script>
@endsection
