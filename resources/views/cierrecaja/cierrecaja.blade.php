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
        $cierreSeleccionado = $selected === 'parcial' ? $cierreDia : ($selected === 'mensual' ? $cierreMes : null);
        $todayBogota = now('America/Bogota');
    @endphp

    <div class="caja-page">
        @if($errors->any())
            <div class="caja-alert caja-alert-error">
                <strong>Revise la informacion</strong>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <section class="caja-head">
            <div>
                <p class="caja-eyebrow">Modulo financiero</p>
                <h2>Que operacion desea realizar?</h2>
                <p>Seleccione si necesita revisar la caja del dia o consolidar el cierre del mes.</p>
            </div>
            <div class="caja-head-actions">
                <a href="{{ route('dashboard') }}" class="caja-btn-back">Volver</a>
                <div class="caja-date">
                    <span>{{ $todayBogota->format('d/m/Y') }}</span>
                    <strong>{{ $todayBogota->translatedFormat('l') }}</strong>
                </div>
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

                @if($cierreDia)
                    <div class="caja-alert caja-alert-info">
                        <strong>Este dia ya fue cerrado</strong>
                        <span>Cerrado el {{ $cierreDia->closed_at?->format('d/m/Y H:i') }} por {{ $cierreDia->user?->name ?? 'Sistema' }}.</span>
                    </div>
                @endif

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

                @if($cierreMes)
                    <div class="caja-alert caja-alert-info">
                        <strong>Este mes ya fue cerrado</strong>
                        <span>Cerrado el {{ $cierreMes->closed_at?->format('d/m/Y H:i') }} por {{ $cierreMes->user?->name ?? 'Sistema' }}.</span>
                    </div>
                @endif

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
            @if($selected)
                <form method="POST" action="{{ route('cierrecaja.store') }}" class="js-cierre-form">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ $selected }}">
                    <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">
                    <input type="hidden" name="mes" value="{{ $mes->format('Y-m') }}">
                    <input type="hidden" name="observacion" value="">
                    <button type="submit" {{ $cierreSeleccionado ? 'disabled' : '' }}>
                        {{ $cierreSeleccionado ? 'Cierre guardado' : 'Confirmar cierre' }}
                    </button>
                </form>
            @else
                <button type="button" disabled>Confirmar cierre</button>
            @endif
        </section>

        <section class="caja-history">
            <div class="caja-history-head">
                <h3>Historial reciente</h3>
                <span>Ultimos cierres guardados</span>
            </div>
            <div class="caja-history-list">
                @forelse($historial as $cierre)
                    <article>
                        <div>
                            <strong>{{ $cierre->tipo }}</strong>
                            <span>{{ $cierre->periodo }} - {{ $cierre->usuario }}</span>
                        </div>
                        <div>
                            <span>Utilidad</span>
                            <strong>{{ $money($cierre->utilidad) }}</strong>
                        </div>
                    </article>
                @empty
                    <p>No hay cierres guardados todavia.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Cierre guardado',
                    text: successMessage,
                    confirmButtonColor: '#0f172a'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No se pudo guardar',
                    text: errorMessage,
                    confirmButtonColor: '#0f172a'
                });
            }

            document.querySelectorAll('.js-cierre-form').forEach((form) => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const tipo = form.querySelector('[name="tipo"]').value;
                    const periodo = tipo === 'parcial'
                        ? form.querySelector('[name="fecha"]').value
                        : form.querySelector('[name="mes"]').value;

                    const result = await Swal.fire({
                        icon: 'question',
                        title: 'Confirmar cierre',
                        text: `Se guardara el cierre ${tipo} del periodo ${periodo}. Esta accion no se puede duplicar.`,
                        input: 'textarea',
                        inputLabel: 'Observacion opcional',
                        inputPlaceholder: 'Escriba una nota para este cierre',
                        showCancelButton: true,
                        confirmButtonText: 'Si, guardar cierre',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#64748b'
                    });

                    if (!result.isConfirmed) {
                        return;
                    }

                    form.querySelector('[name="observacion"]').value = result.value || '';
                    form.submit();
                });
            });
        });
    </script>
@endpush
