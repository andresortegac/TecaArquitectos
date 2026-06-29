@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cierrecaja.css') }}?v={{ filemtime(public_path('css/cierrecaja.css')) }}">
@endpush

@section('title', 'Caja y Cierres')
@section('header', 'Caja y Cierres')

@section('content')
    @php
        $money = fn ($value) => '$ ' . number_format((float) $value, 0, ',', '.');
        $selected = $tipoSeleccionado;
        $monthLabel = $mes->translatedFormat('F Y');
    @endphp

    <div class="caja-page">
        <section class="caja-head">
            <div>
                <p class="caja-eyebrow">Modulo financiero</p>
                <h2>Que operacion desea realizar?</h2>
                <p>Seleccione si necesita revisar la caja del dia o consolidar el cierre del mes.</p>
            </div>
            <div class="caja-date">
                <span>{{ now()->format('d/m/Y') }}</span>
                <strong>{{ now()->translatedFormat('l') }}</strong>
            </div>
        </section>

        <section class="caja-options" aria-label="Tipo de cierre">
            <a href="{{ route('cierrecaja.cierrecaja', ['tipo' => 'parcial', 'fecha' => $fecha->toDateString(), 'mes' => $mes->format('Y-m')]) }}"
                class="caja-option {{ $selected === 'parcial' ? 'is-active' : '' }}">
                <span class="caja-icon caja-icon-green">P</span>
                <h3>Cierre parcial / diario</h3>
                <p>Revise los ingresos, gastos y caja actual de una fecha especifica.</p>
                <strong>Hacer cierre parcial</strong>
            </a>

            <a href="{{ route('cierrecaja.cierrecaja', ['tipo' => 'mensual', 'fecha' => $fecha->toDateString(), 'mes' => $mes->format('Y-m')]) }}"
                class="caja-option {{ $selected === 'mensual' ? 'is-active' : '' }}">
                <span class="caja-icon caja-icon-blue">M</span>
                <h3>Cierre mensual</h3>
                <p>Consolide todos los movimientos del mes para revisar utilidad y pagos.</p>
                <strong>Hacer cierre mensual</strong>
            </a>
        </section>

        <section class="caja-workspace">
            @if(!$selected)
                <div class="caja-prompt">
                    <strong>Seleccione una opcion para continuar</strong>
                    <span>El cierre parcial revisa un dia especifico. El cierre mensual consolida todo el mes.</span>
                </div>
            @endif

            <article class="caja-panel {{ $selected === 'parcial' ? 'is-visible' : '' }}">
                <div class="caja-panel-head">
                    <div>
                        <h3>Cierre parcial del dia</h3>
                        <p>Fecha seleccionada: {{ $fecha->format('d/m/Y') }}</p>
                    </div>
                    <form method="GET" action="{{ route('cierrecaja.cierrecaja') }}" class="caja-filter">
                        <input type="hidden" name="tipo" value="parcial">
                        <input type="hidden" name="mes" value="{{ $mes->format('Y-m') }}">
                        <label for="fecha">Fecha</label>
                        <input id="fecha" type="date" name="fecha" value="{{ $fecha->toDateString() }}">
                        <button type="submit">Consultar</button>
                    </form>
                </div>

                <div class="caja-summary">
                    <div>
                        <span>Ingresos del dia</span>
                        <strong class="text-green">{{ $money($resumenDia['ingresos']) }}</strong>
                    </div>
                    <div>
                        <span>Gastos del dia</span>
                        <strong class="text-red">{{ $money($resumenDia['gastos']) }}</strong>
                    </div>
                    <div>
                        <span>Caja actual</span>
                        <strong>{{ $money($resumenDia['utilidad']) }}</strong>
                    </div>
                    <div>
                        <span>Pagos registrados</span>
                        <strong>{{ $resumenDia['pagos'] }}</strong>
                    </div>
                </div>

                <div class="caja-detail">
                    <h4>Ingresos por metodo de pago</h4>
                    <div class="caja-methods">
                        @foreach(['efectivo', 'transferencia', 'nequi', 'daviplata'] as $method)
                            <div>
                                <span>{{ ucfirst($method) }}</span>
                                <strong>{{ $money($resumenDia['metodos'][$method] ?? 0) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>

            <article class="caja-panel {{ $selected === 'mensual' ? 'is-visible' : '' }}">
                <div class="caja-panel-head">
                    <div>
                        <h3>Cierre mensual</h3>
                        <p>Periodo seleccionado: {{ ucfirst($monthLabel) }}</p>
                    </div>
                    <form method="GET" action="{{ route('cierrecaja.cierrecaja') }}" class="caja-filter">
                        <input type="hidden" name="tipo" value="mensual">
                        <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">
                        <label for="mes">Mes</label>
                        <input id="mes" type="month" name="mes" value="{{ $mes->format('Y-m') }}">
                        <button type="submit">Consultar</button>
                    </form>
                </div>

                <div class="caja-summary">
                    <div>
                        <span>Total ingresos</span>
                        <strong class="text-green">{{ $money($resumenMes['ingresos']) }}</strong>
                    </div>
                    <div>
                        <span>Total gastos</span>
                        <strong class="text-red">{{ $money($resumenMes['gastos']) }}</strong>
                    </div>
                    <div>
                        <span>Utilidad del mes</span>
                        <strong>{{ $money($resumenMes['utilidad']) }}</strong>
                    </div>
                    <div>
                        <span>Pagos registrados</span>
                        <strong>{{ $resumenMes['pagos'] }}</strong>
                    </div>
                </div>

                <div class="caja-detail">
                    <h4>Ingresos mensuales por metodo</h4>
                    <div class="caja-methods">
                        @foreach(['efectivo', 'transferencia', 'nequi', 'daviplata'] as $method)
                            <div>
                                <span>{{ ucfirst($method) }}</span>
                                <strong>{{ $money($resumenMes['metodos'][$method] ?? 0) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        </section>

        <section class="caja-actions">
            <a href="{{ route('reportes.ingresos-diarios', ['fecha' => $fecha->toDateString()]) }}">Ver ingresos diarios</a>
            <a href="{{ route('gastos.index') }}">Ver gastos</a>
            <button type="button" disabled>Confirmar cierre</button>
        </section>
    </div>
@endsection
