@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/configuracion.css') }}">
@endpush

@section('title', 'Configuracion')
@section('header', 'VER CONFIGURACION')

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

            {{-- CONTENIDO --}}
            <div class="conf-content">

                {{-- STOCK --}}
                <div class="conf-pane active" id="stock">
                    <div class="conf-card">
                        <h5>Stock mínimo</h5>

                        <div class="conf-group">
                            <label class="conf-label">Stock mínimo global</label>
                            <input type="number" class="conf-input" value="10">
                        </div>

                        <div class="conf-check">
                            <input type="checkbox" checked>
                            <label>Activar alertas de stock bajo</label>
                        </div>
                    </div>
                </div>

                {{-- REPORTES --}}
                <div class="conf-pane" id="reportes">
                    <div class="conf-card">
                        <h5>Configuración de Reportes</h5>

                        <div class="conf-group">
                            <label class="conf-label">Mes por defecto</label>
                            <select class="conf-select">
                                <option>Enero</option>
                                <option>Febrero</option>
                                <option>Marzo</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- INVENTARIO --}}
                <div class="conf-pane" id="inventario">
                    <div class="conf-card">
                        <h5>Opciones de Inventario</h5>

                        <div class="conf-check">
                            <input type="checkbox" checked>
                            <label>Bloquear salidas sin stock</label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
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
