@extends('layouts.app')
@section('title', 'Detalle del día')
@section('header', 'Detalle del día')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ui.css') }}">
@endpush

@section('content')
<div class="principal-page">

  <div class="card" style="margin-bottom:12px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <div>
        <h3 class="card-title" style="margin:0;">
          Detalle del día {{ $dateLabel ?? ($date ?? $fecha ?? '') }}
        </h3>
        <div class="subtitle" style="margin-top:6px;">Recaudo por hora + arriendos del día</div>
      </div>

      <div style="display:flex;gap:8px;align-items:center;">
        @if(isset($year, $month))
          <a class="btn-sm" href="{{ route('metricas.reporte.mensual', [$year, $month]) }}">← Volver al mes</a>
        @else
          <a class="btn-sm" href="{{ url()->previous() }}">← Volver</a>
        @endif
      </div>
    </div>

    <div style="padding:14px;">
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        <div class="card" style="padding:12px;">
          <div class="small">Total recaudado</div>
          <div style="font-size:22px;font-weight:700;">
            ${{ number_format((float)($totalDia ?? 0), 0) }}
          </div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="small">Pagos confirmados</div>
          <div style="font-size:22px;font-weight:700;">
            {{ (int)($countPayments ?? 0) }}
          </div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="small">Arriendos creados</div>
          <div style="font-size:22px;font-weight:700;">
            {{ (int)($arriendosCreados ?? 0) }}
          </div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="small">Arriendos cerrados/devueltos</div>
          <div style="font-size:22px;font-weight:700;">
            {{ (int)($arriendosDevueltos ?? 0) }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- RECAUDO POR HORA --}}
  <div class="card" style="margin-bottom:12px;">
    <div class="card-header">
      <h3 class="card-title" style="margin:0;">Recaudo por hora</h3>
    </div>

    <div style="padding:12px;">
      <table class="table-pro">
        <thead>
          <tr>
            <th>Hora</th>
            <th class="td-right">Total</th>
            <th class="td-right">Pagos</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($porHora ?? []) as $h)
            <tr>
              <td><strong>{{ $h['hour_label'] ?? '' }}</strong></td>
              <td class="td-right">${{ number_format((float)($h['total'] ?? 0), 0) }}</td>
              <td class="td-right">{{ (int)($h['count'] ?? 0) }}</td>
            </tr>
          @empty
            <tr><td colspan="3">No hay pagos confirmados este día.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- LISTA DE PAGOS --}}
  <div class="card" style="margin-bottom:12px;">
    <div class="card-header">
      <h3 class="card-title" style="margin:0;">Pagos confirmados (detalle)</h3>
    </div>

    <div style="padding:12px;">
      <table class="table-pro">
        <thead>
          <tr>
            <th>Hora</th>
            <th class="td-right">Monto</th>
            <th>Nota</th>
            <th>Origen</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($payments ?? []) as $p)
            <tr>
              <td>{{ $p['time'] ?? '' }}</td>
              <td class="td-right">${{ number_format((float)($p['amount'] ?? 0), 0) }}</td>
              <td>{{ $p['note'] ?? '—' }}</td>
              <td>
                <span class="small" style="opacity:.8;">
                  {{ $p['source_type'] ?? '—' }} #{{ $p['source_id'] ?? '' }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="4">No hay pagos confirmados en este día.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ARRIENDOS DEL DÍA --}}
  <div class="card">
    <div class="card-header">
      <h3 class="card-title" style="margin:0;">Arriendos del día</h3>
    </div>

    <div style="padding:12px;">
      <table class="table-pro">
        <thead>
          <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Inicio</th>
            <th>Estado</th>
            <th class="td-right">Total</th>
            <th class="td-right">Pagado</th>
            <th class="td-right">Saldo</th>
            <th style="width:140px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          @forelse(($arriendos ?? []) as $a)
            <tr>
              <td><strong>#{{ $a['id'] ?? '' }}</strong></td>
              <td>{{ $a['cliente'] ?? '—' }}</td>
              <td>{{ $a['inicio'] ?? '—' }}</td>
              <td>
                @if(($a['estado'] ?? '') === 'devuelto')
                  <span class="chip gray">Devuelto</span>
                @else
                  <span class="chip blue">{{ ucfirst($a['estado'] ?? '—') }}</span>
                @endif
              </td>
              <td class="td-right">${{ number_format((float)($a['total'] ?? 0), 0) }}</td>
              <td class="td-right">${{ number_format((float)($a['pagado'] ?? 0), 0) }}</td>
              <td class="td-right">${{ number_format((float)($a['saldo'] ?? 0), 0) }}</td>
              <td>
                @if(isset($a['id']))
                  <a class="btn-sm" href="{{ route('arriendos.ver', $a['id']) }}">Ver</a>
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="8">No se registraron arriendos este día.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
