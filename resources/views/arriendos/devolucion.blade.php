@extends('layouts.app')
@section('title','Devolución de alquiler')
@section('header','Devolución de alquiler')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert danger">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h2>Devolución parcial - Arriendo #{{ $arriendo->id }}</h2>
    <a class="btn-sm" href="{{ route('arriendos.index') }}">Volver</a>
</div>

<div style="background:#fff; padding:14px; border-radius:10px; margin-bottom:12px;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div><strong>Cliente:</strong> {{ $arriendo->cliente->nombre ?? '—' }}</div>
        <div><strong>Producto:</strong> {{ $arriendo->producto->nombre ?? '—' }}</div>
        <div><strong>Cantidad alquilada actual:</strong> {{ $arriendo->cantidad }}</div>
        <div><strong>Inicio:</strong> {{ $arriendo->fecha_inicio?->format('d/m/Y H:i') }}</div>
        <div><strong>Tarifa diaria:</strong> ${{ number_format((float)($arriendo->producto->costo ?? 0), 2) }}</div>
        <div><strong>Estado:</strong> {{ ucfirst($arriendo->estado) }}</div>
    </div>
</div>

<form method="POST" action="{{ route('arriendos.devolucion.store', $arriendo) }}"
      style="background:#fff; padding:14px; border-radius:10px;">
    @csrf

    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <div style="flex:1;">
            <label style="display:block; font-size:13px;">Cantidad a devolver</label>
            <input class="input" type="number" min="1" max="{{ $arriendo->cantidad }}"
                   name="cantidad_devuelta" required style="width:100%;"
                   value="{{ old('cantidad_devuelta') }}">
            <small style="color:#666;">Máximo: {{ $arriendo->cantidad }}</small>
        </div>

        <div style="flex:1;">
            <label style="display:block; font-size:13px;">Fecha devolución</label>
            <input class="input" type="date" name="fecha_devolucion" required style="width:100%;"
                   value="{{ old('fecha_devolucion', date('Y-m-d')) }}">
        </div>
    </div>

    <hr style="margin:10px 0;">

    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <div style="flex:1;">
            <label style="display:block; font-size:13px;">Días de lluvia (se descuentan)</label>
            <input class="input" type="number" min="0" name="dias_lluvia"
                   value="{{ old('dias_lluvia', 0) }}" style="width:100%;">
        </div>
        <div style="flex:1;">
            <label style="display:block; font-size:13px;">Costo daño/merma</label>
            <input class="input" type="number" min="0" step="0.01" name="costo_merma"
                   value="{{ old('costo_merma', 0) }}" style="width:100%;">
        </div>
    </div>

    <div style="display:flex; gap:10px; margin-bottom:10px;">
        <div style="flex:1;">
            <label style="display:block; font-size:13px;">Pago recibido (opcional)</label>
            <input class="input" type="number" min="0" step="0.01" name="pago"
                   value="{{ old('pago', 0) }}" style="width:100%;">
        </div>
        <div style="flex:1;">
            <label style="display:block; font-size:13px;">Descripción incidencia (opcional)</label>
            <input class="input" type="text" name="descripcion_incidencia"
                   value="{{ old('descripcion_incidencia') }}"
                   placeholder="Ej: lluvia fuerte / mango roto" style="width:100%;">
        </div>
    </div>

    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:13px;">Nota (opcional)</label>
        <input class="input" type="text" name="nota" style="width:100%;" value="{{ old('nota') }}">
    </div>

    <div style="font-size:12px; color:#666; margin-bottom:10px;">
        Domingos se descuentan automáticamente. No se cobra el día de devolución. Si entrega y devolución son el mismo día, se cobra 1.
    </div>

    <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="submit" class="btn-sm warning">Guardar devolución</button>
    </div>
</form>

{{-- =======================
     ✅ HISTORIAL DE DEVOLUCIONES
     ======================= --}}

<hr style="margin:18px 0;">

<h3 style="margin:0 0 10px;">Historial de devoluciones</h3>

@php
    // Por si un día entras aquí sin $resumen (evita errores)
    $resumen = $resumen ?? [
        'total_devoluciones' => 0,
        'total_devuelto' => 0,
        'total_abonado' => 0,
        'total_cobrado' => 0,
    ];
@endphp

<div style="display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap;">
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Devoluciones:</strong> {{ $resumen['total_devoluciones'] }}
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Total devuelto:</strong> {{ $resumen['total_devuelto'] }}
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Total cobrado:</strong> ${{ number_format((float)$resumen['total_cobrado'], 2) }}
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Total abonado:</strong> ${{ number_format((float)$resumen['total_abonado'], 2) }}
    </div>
</div>

@if(!isset($arriendo->devoluciones) || $arriendo->devoluciones->isEmpty())
    <div style="background:#fff; padding:12px; border-radius:10px;">
        No hay devoluciones registradas todavía.
    </div>
@else
    <div style="background:#fff; padding:12px; border-radius:10px;">
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Devuelto</th>
                    <th>Días</th>
                    <th>Dom</th>
                    <th>Lluvia</th>
                    <th>Cobrables</th>
                    <th>Tarifa</th>
                    <th>Alquiler</th>
                    <th>Merma</th>
                    <th>Total</th>
                    <th>Abono</th>
                    <th>Quedan</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
            @foreach($arriendo->devoluciones->sortByDesc('id') as $d)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($d->fecha_devolucion)->format('d/m/Y') }}
                    </td>
                    <td>{{ (int)$d->cantidad_devuelta }}</td>
                    <td>{{ (int)$d->dias_transcurridos }}</td>
                    <td>{{ (int)$d->domingos_desc }}</td>
                    <td>{{ (int)$d->dias_lluvia_desc }}</td>
                    <td>{{ (int)$d->dias_cobrables }}</td>
                    <td>${{ number_format((float)$d->tarifa_diaria, 2) }}</td>
                    <td>${{ number_format((float)$d->total_alquiler, 2) }}</td>
                    <td>${{ number_format((float)$d->total_merma, 2) }}</td>
                    <td><strong>${{ number_format((float)$d->total_cobrado, 2) }}</strong></td>
                    <td>${{ number_format((float)$d->pago_recibido, 2) }}</td>
                    <td>{{ (int)$d->cantidad_restante }}</td>
                    <td>${{ number_format((float)$d->saldo_resultante, 2) }}</td>
                </tr>

                @if(!empty($d->descripcion_incidencia) || !empty($d->nota))
                    <tr>
                        <td colspan="13" style="font-size:12px; color:#666;">
                            @if(!empty($d->descripcion_incidencia))
                                <strong>Incidencia:</strong> {{ $d->descripcion_incidencia }}
                            @endif

                            @if(!empty($d->nota))
                                @if(!empty($d->descripcion_incidencia))
                                    &nbsp; | &nbsp;
                                @endif
                                <strong>Nota:</strong> {{ $d->nota }}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
