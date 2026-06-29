@extends('layouts.app')
@section('title', 'Reporte anual')
@section('header', 'Reporte anual')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ui.css') }}">
@endpush

@section('content')
<div class="principal-page">

  @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
  @endif

  <div class="card" style="margin-bottom:12px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <div>
        <h3 class="card-title" style="margin:0;">Resumen año {{ $year }}</h3>
        <div class="subtitle" style="margin-top:6px;">
          Totales confirmados (payments.status=confirmed)
        </div>
      </div>

      <div style="display:flex;gap:8px;align-items:center;">
        <a class="btn-sm" href="{{ route('metricas.index') }}">Volver a métricas</a>

        <form method="GET" action="{{ route('metricas.reporte.anual', $year) }}" style="display:flex;gap:8px;">
          <input class="input" type="number" name="year" min="2000" max="2100" value="{{ $year }}" style="width:120px;">
          <button class="btn-primary" type="submit" style="padding:8px 12px;">Ir</button>
        </form>
      </div>
    </div>

    <div style="padding:14px;">
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
        <div class="card" style="padding:12px;">
          <div class="small">Total recaudado año</div>
          <div style="font-size:22px;font-weight:700;">
            ${{ number_format((float)($totalAnual ?? 0), 0) }}
          </div>
          <div class="small" style="opacity:.75;">Año {{ $year }}</div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="small">Pagos confirmados</div>
          <div style="font-size:22px;font-weight:700;">
            {{ (int)($countPayments ?? 0) }}
          </div>
          <div class="small" style="opacity:.75;">Registros en payments</div>
        </div>

        <div class="card" style="padding:12px;">
          <div class="small">Promedio mensual</div>
          <div style="font-size:22px;font-weight:700;">
            ${{ number_format((float)($avgMensual ?? 0), 0) }}
          </div>
          <div class="small" style="opacity:.75;">Total/12</div>
        </div>
      </div>
    </div>
  </div>

  @php
    // Indexamos $meses por número de mes para cruzarlo con 1..12
    $mesesIndex = [];
    foreach(($meses ?? []) as $row){
      $mn = (int)($row['month'] ?? 0);
      if($mn >= 1 && $mn <= 12){
        $mesesIndex[$mn] = $row;
      }
    }

    // Lista fija 1..12
    $months = range(1, 12);
  @endphp

  <div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <h3 class="card-title" style="margin:0;">Meses del año {{ $year }}</h3>
      <div class="small" style="opacity:.75;">Click en “Ver mes” para detalle por días</div>
    </div>

    <div style="padding:12px;">
      <table class="table-pro">
        <thead>
          <tr>
            <th>Mes</th>
            <th class="td-right">Recaudo</th>
            <th class="td-right">Pagos</th>
            <th style="width:160px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($months as $monthNum)
            @php
              // Nombre del mes en español
              $monthName = \Carbon\Carbon::createFromDate((int)$year, (int)$monthNum, 1)
                            ->locale('es')
                            ->translatedFormat('F');
              $monthName = ucfirst($monthName);

              // Datos reales si existen, si no, 0
              $data = $mesesIndex[$monthNum] ?? [];
              $total = (float)($data['total'] ?? 0);
              $count = (int)($data['count'] ?? 0);
            @endphp

            <tr>
              <td>
                <strong>{{ $monthName }}</strong>
                <div class="small" style="opacity:.75;">{{ $year }}</div>
              </td>

              <td class="td-right">
                ${{ number_format($total, 0) }}
              </td>

              <td class="td-right">
                {{ $count }}
              </td>

              <td>
                <a class="btn-sm" href="{{ route('metricas.reporte.mensual', [$year, $monthNum]) }}">
                  Ver mes
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
