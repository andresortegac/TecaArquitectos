@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/configuracion.css') }}">
@endpush

@section('title', 'Configuracion')
@section('header', 'Configuracion del sistema')

@section('content')
<div class="conf-page">
    <section class="conf-hero">
        <div>
            <h2>Configuracion del sistema</h2>
            <p>Administra reglas globales de stock, reportes e inventario desde un solo lugar.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="conf-btn conf-btn-light">Volver</a>
    </section>

    @if(session('success'))
        <div class="conf-alert conf-alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="conf-alert conf-alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="conf-card">
        <ul class="conf-tabs" role="tablist">
            <li>
                <button type="button" class="conf-tab-link active" data-tab="stock">Stock</button>
            </li>
            <li>
                <button type="button" class="conf-tab-link" data-tab="reportes">Reportes</button>
            </li>
            <li>
                <button type="button" class="conf-tab-link" data-tab="inventario">Inventario</button>
            </li>
        </ul>

        <div class="conf-content">
            <div class="conf-pane active" id="stock">
                <form method="POST" action="{{ route('config.stock') }}" class="conf-form">
                    @csrf
                    <h3>Stock minimo</h3>

                    <div class="conf-group">
                        <label for="stock_minimo" class="conf-label">Stock minimo global</label>
                        <input id="stock_minimo" type="number" name="stock_minimo" class="conf-input" min="0" value="{{ old('stock_minimo', $config->stock_minimo) }}" required>
                    </div>

                    <label class="conf-check">
                        <input type="checkbox" name="alerta_stock" value="1" {{ old('alerta_stock', $config->alerta_stock) ? 'checked' : '' }}>
                        <span>Activar alertas de stock bajo</span>
                    </label>

                    <button type="submit" class="conf-btn conf-btn-primary">Guardar cambios</button>
                </form>
            </div>

            <div class="conf-pane" id="reportes">
                <form method="POST" action="{{ route('config.reportes') }}" class="conf-form">
                    @csrf
                    <h3>Configuracion de reportes</h3>

                    <div class="conf-group">
                        <label for="mes_defecto" class="conf-label">Mes por defecto</label>
                        <select id="mes_defecto" name="mes_defecto" class="conf-select">
                            @foreach($meses as $mes)
                                <option value="{{ $mes }}" {{ old('mes_defecto', $config->mes_defecto) === $mes ? 'selected' : '' }}>
                                    {{ $mes }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="conf-btn conf-btn-primary">Guardar cambios</button>
                </form>
            </div>

            <div class="conf-pane" id="inventario">
                <form method="POST" action="{{ route('config.inventario') }}" class="conf-form">
                    @csrf
                    <h3>Opciones de inventario</h3>

                    <label class="conf-check">
                        <input type="checkbox" name="bloquear_sin_stock" value="1" {{ old('bloquear_sin_stock', $config->bloquear_sin_stock) ? 'checked' : '' }}>
                        <span>Bloquear salidas sin stock</span>
                    </label>

                    <button type="submit" class="conf-btn conf-btn-primary">Guardar cambios</button>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
(function () {
    const tabs = document.querySelectorAll('.conf-tab-link');
    const panes = document.querySelectorAll('.conf-pane');
    if (!tabs.length || !panes.length) return;

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((t) => t.classList.remove('active'));
            panes.forEach((p) => p.classList.remove('active'));

            tab.classList.add('active');
            const pane = document.getElementById(tab.dataset.tab);
            if (pane) pane.classList.add('active');
        });
    });
})();
</script>
@endsection
