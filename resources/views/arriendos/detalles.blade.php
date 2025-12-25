@extends('layouts.app')
@section('title','Detalles / Factura de arriendo')
@section('header','Detalles / Factura de arriendo')

@section('content')

<style>
  /* Factura */
  .factura-wrap { background:#fff; padding:16px; border-radius:10px; }
  .factura-top { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px; }
  .factura-top h2 { margin:0; }
  .badge { display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; background:#eee; }
  .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  .box { background:#fff; border:1px solid #eee; border-radius:10px; padding:12px; }
  .muted { color:#666; font-size:12px; }
  .table { width:100%; border-collapse:collapse; }
  .table th, .table td { border-bottom:1px solid #eee; padding:8px; font-size:13px; vertical-align:top; }
  .table th { text-align:left; background:#fafafa; }
  .right { text-align:right; }
  .section-title { margin:16px 0 8px; }
  .btn-print { cursor:pointer; }

  /* ✅ “Factura” por herramienta dentro del espacio izquierdo */
  .item-grid {
    display:grid;
    grid-template-columns: 1fr 320px; /* izquierda grande, derecha resumen */
    gap:12px;
    align-items:start;
  }
  @media (max-width: 900px){
    .item-grid { grid-template-columns:1fr; }
  }
  .mini-factura {
    border:1px dashed #e6e6e6;
    background:#fcfcfc;
    border-radius:10px;
    padding:10px 12px;
    margin-top:10px;
  }
  .mini-head {
    display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;
    margin-bottom:6px;
  }
  .mini-head .title { font-weight:700; font-size:13px; }
  .mini-head .tag {
    font-size:12px; background:#f1f1f1; border-radius:999px; padding:3px 8px;
  }
  .mini-lines { margin-top:6px; }
  .mini-line {
    display:flex; justify-content:space-between; gap:12px;
    padding:6px 0;
    border-bottom:1px dotted #e9e9e9;
    font-size:12.5px;
  }
  .mini-line:last-child { border-bottom:0; }
  .mini-line .lbl { color:#444; }
  .mini-line .val { font-weight:600; white-space:nowrap; }
  .mini-sub {
    margin-top:8px;
    padding-top:8px;
    border-top:1px solid #e9e9e9;
    font-size:12px;
    color:#666;
  }

  .mov-table { width:100%; border-collapse:collapse; margin-top:8px; }
  .mov-table th, .mov-table td {
    border-bottom:1px solid #eee;
    padding:6px 6px;
    font-size:12px;
    white-space:nowrap;
  }
  .mov-table th { background:#fafafa; text-align:left; }
  .mov-right { text-align:right; }
  .mov-muted { color:#666; }

  /* Impresión */
  @media print {
    .no-print { display:none !important; }
    body { background:#fff !important; }
    .factura-wrap { padding:0; border-radius:0; }
    .box { border:0; }
    .table th { background:#f2f2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mini-factura { border:1px solid #ddd; background:#fff; }
    .mov-table th { background:#f2f2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>

<div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <a class="btn-sm" href="{{ route('arriendos.ver', $arriendo->id) }}">Volver</a>
    <button type="button" class="btn-sm btn-print" onclick="window.print()">Imprimir</button>
</div>

<div class="factura-wrap">

  <div class="factura-top">
    <div>
      <h2>Factura / Detalle Arriendo #{{ $arriendo->id }}</h2>
      <div class="muted">
        Fecha emisión: {{ now()->format('d/m/Y H:i') }}
      </div>
    </div>
    <div style="text-align:right;">
      <div class="badge">Estado: {{ ucfirst($arriendo->estado ?? '—') }}</div>
      <div class="muted" style="margin-top:6px;">
        Inicio: {{ $arriendo->fecha_inicio ? \Carbon\Carbon::parse($arriendo->fecha_inicio)->format('d/m/Y') : '—' }}
        @if(!empty($arriendo->fecha_fin))
          &nbsp;|&nbsp; Fin: {{ \Carbon\Carbon::parse($arriendo->fecha_fin)->format('d/m/Y') }}
        @endif
      </div>
    </div>
  </div>

  {{-- Datos cliente / totales --}}
  <div class="grid2">
    <div class="box">
      <div><strong>Cliente:</strong> {{ $arriendo->cliente->nombre ?? '—' }}</div>
      <div class="muted">Arriendo creado: {{ $arriendo->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
      @if(!empty($arriendo->obra_id))
        <div class="muted">Obra ID: {{ $arriendo->obra_id }}</div>
      @endif
    </div>

    <div class="box">
      @php
        $totales = $totales ?? [
          'total_alquiler' => 0,
          'total_merma' => 0,
          'total_pagado' => 0,
          'precio_total' => 0,
          'saldo' => 0,
        ];
      @endphp
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        <div><strong>Total alquiler:</strong></div><div class="right">${{ number_format((float)$totales['total_alquiler'], 2) }}</div>
        <div><strong>Total merma:</strong></div><div class="right">${{ number_format((float)$totales['total_merma'], 2) }}</div>
        <div><strong>Total cobrado:</strong></div><div class="right"><strong>${{ number_format((float)$totales['precio_total'], 2) }}</strong></div>
        <div><strong>Total abonado:</strong></div><div class="right">${{ number_format((float)$totales['total_pagado'], 2) }}</div>
        <div><strong>Saldo:</strong></div><div class="right"><strong>${{ number_format((float)$totales['saldo'], 2) }}</strong></div>
      </div>
    </div>
  </div>

  <h3 class="section-title">Productos alquilados (Items) — Detalle por herramienta</h3>

  @if(empty($arriendo->items) || $arriendo->items->isEmpty())
    <div class="box">No hay items en este arriendo.</div>
  @else

    @foreach($arriendo->items as $it)
      @php
        $tarifa = (float)($it->tarifa_diaria ?? ($it->producto->costo ?? 0));

        // ✅ SOLO usamos devoluciones si ya vienen cargadas (evita consultas que revienten)
        $devs = $it->relationLoaded('devoluciones') ? ($it->devoluciones ?? collect()) : collect();

        // ✅ orden cronológico para sacar "último saldo_resultante"
        $devsAsc = $devs->sortBy('id')->values();
        $devsDesc = $devs->sortByDesc('id')->values();

        $cantidadInicial = (int)($it->cantidad_inicial ?? 0);

        $totalDevuelto = (int)$devs->sum('cantidad_devuelta');
        $totalCobradoDevs = (float)$devs->sum('total_cobrado');
        $totalAbonadoDevs = (float)$devs->sum('pago_recibido');

        $cantidadRestanteCalc = max(0, $cantidadInicial - $totalDevuelto);

        // ✅ saldo real: el último saldo_resultante si existe, si no el saldo del item
        $saldoHist = $devsAsc->count() ? (float)($devsAsc->last()->saldo_resultante ?? 0) : null;
        $saldoMostrado = is_null($saldoHist) ? (float)($it->saldo ?? 0) : $saldoHist;

        // ✅ “último movimiento” para resumen rápido en factura
        $ultimo = $devsDesc->first();

        $ultimoFecha = $ultimo?->fecha_devolucion ? \Carbon\Carbon::parse($ultimo->fecha_devolucion)->format('d/m/Y') : '—';
        $ultimoDev   = $ultimo ? (int)$ultimo->cantidad_devuelta : 0;
        $ultimoAbono = $ultimo ? (float)$ultimo->pago_recibido : 0;
        $ultimoTotal = $ultimo ? (float)$ultimo->total_cobrado : 0;
        $ultimoQuedan= $ultimo ? (int)$ultimo->cantidad_restante : $cantidadRestanteCalc;
      @endphp

      <div class="box" style="margin-bottom:12px;">
        <div class="item-grid">

          {{-- IZQUIERDA: AQUÍ VA LA “FACTURA DETALLADA” (usa el espacio en blanco) --}}
          <div>
            <div style="font-size:15px;"><strong>{{ $it->producto->nombre ?? 'Producto' }}</strong></div>
            <div class="muted">
              Item #{{ $it->id }} |
              Inicio: {{ $it->fecha_inicio_item ? \Carbon\Carbon::parse($it->fecha_inicio_item)->format('d/m/Y H:i') : '—' }}
              @if(!empty($it->fecha_fin_item))
                | Fin: {{ \Carbon\Carbon::parse($it->fecha_fin_item)->format('d/m/Y') }}
              @endif
            </div>
            <div class="muted">Estado item: {{ ucfirst($it->estado ?? '—') }} @if((int)($it->cerrado ?? 0)===1) | Cerrado @endif</div>

            {{-- ✅ FACTURA DETALLE (en el espacio en blanco) --}}
            <div class="mini-factura">
              <div class="mini-head">
                <div class="title">Detalle / Movimientos de esta herramienta</div>
                <div class="tag">{{ $devs->count() }} movimiento(s)</div>
              </div>

              <div class="mini-lines">
                <div class="mini-line">
                  <div class="lbl">Cantidad inicial</div>
                  <div class="val">{{ $cantidadInicial }}</div>
                </div>
                <div class="mini-line">
                  <div class="lbl">Devuelto acumulado</div>
                  <div class="val">{{ $totalDevuelto }}</div>
                </div>
                <div class="mini-line">
                  <div class="lbl">Restante</div>
                  <div class="val">{{ $cantidadRestanteCalc }}</div>
                </div>
                <div class="mini-line">
                  <div class="lbl">Total cobrado por registros</div>
                  <div class="val">${{ number_format($totalCobradoDevs,2) }}</div>
                </div>
                <div class="mini-line">
                  <div class="lbl">Total abonado por registros</div>
                  <div class="val">${{ number_format($totalAbonadoDevs,2) }}</div>
                </div>
                <div class="mini-line">
                  <div class="lbl">Saldo actual de esta herramienta</div>
                  <div class="val">${{ number_format($saldoMostrado,2) }}</div>
                </div>
              </div>

              <div class="mini-sub">
                <div><strong>Último movimiento:</strong> {{ $ultimoFecha }}</div>
                <div class="mov-muted">
                  Devuelto: {{ $ultimoDev }} | Abono: ${{ number_format($ultimoAbono,2) }} | Total: ${{ number_format($ultimoTotal,2) }} | Quedan: {{ $ultimoQuedan }}
                </div>
              </div>

              {{-- ✅ TABLA DE MOVIMIENTOS (tipo factura) --}}
              <div style="margin-top:10px;">
                @if($devsDesc->isEmpty())
                  <div class="muted">No hay movimientos todavía para esta herramienta.</div>
                @else
                  <table class="mov-table">
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th class="mov-right">Dev</th>
                        <th class="mov-right">Días</th>
                        <th class="mov-right">Dom</th>
                        <th class="mov-right">Lluvia</th>
                        <th class="mov-right">Cob</th>
                        <th class="mov-right">Tarifa</th>
                        <th class="mov-right">Alquiler</th>
                        <th class="mov-right">Merma</th>
                        <th class="mov-right">Total</th>
                        <th class="mov-right">Abono</th>
                        <th class="mov-right">Saldo</th>
                        <th class="mov-right">Quedan</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($devsDesc as $d)
                        <tr>
                          <td>{{ \Carbon\Carbon::parse($d->fecha_devolucion)->format('d/m/Y') }}</td>
                          <td class="mov-right">{{ (int)$d->cantidad_devuelta }}</td>
                          <td class="mov-right">{{ (int)$d->dias_transcurridos }}</td>
                          <td class="mov-right">{{ (int)$d->domingos_desc }}</td>
                          <td class="mov-right">{{ (int)$d->dias_lluvia_desc }}</td>
                          <td class="mov-right">{{ (int)$d->dias_cobrables }}</td>
                          <td class="mov-right">${{ number_format((float)$d->tarifa_diaria,2) }}</td>
                          <td class="mov-right">${{ number_format((float)$d->total_alquiler,2) }}</td>
                          <td class="mov-right">${{ number_format((float)$d->total_merma,2) }}</td>
                          <td class="mov-right"><strong>${{ number_format((float)$d->total_cobrado,2) }}</strong></td>
                          <td class="mov-right">${{ number_format((float)$d->pago_recibido,2) }}</td>
                          <td class="mov-right">${{ number_format((float)($d->saldo_resultante ?? 0),2) }}</td>
                          <td class="mov-right">{{ (int)$d->cantidad_restante }}</td>
                        </tr>

                        @if(!empty($d->descripcion_incidencia) || !empty($d->nota))
                          <tr>
                            <td colspan="13" class="mov-muted" style="padding:6px 6px;">
                              @if(!empty($d->descripcion_incidencia))
                                <strong>Incidencia:</strong> {{ $d->descripcion_incidencia }}
                              @endif
                              @if(!empty($d->nota))
                                @if(!empty($d->descripcion_incidencia)) &nbsp;|&nbsp; @endif
                                <strong>Nota:</strong> {{ $d->nota }}
                              @endif
                            </td>
                          </tr>
                        @endif
                      @endforeach
                    </tbody>
                  </table>
                @endif
              </div>
            </div>
          </div>

          {{-- DERECHA: TU RESUMEN (SE QUEDA IGUAL) --}}
          <div style="min-width:280px;">
            <table class="table" style="margin:0;">
              <tbody>
                <tr><td><strong>Cant. inicial</strong></td><td class="right">{{ $cantidadInicial }}</td></tr>
                <tr><td><strong>Total devuelto</strong></td><td class="right">{{ $totalDevuelto }}</td></tr>
                <tr><td><strong>Restante</strong></td><td class="right">{{ (int)$cantidadRestanteCalc }}</td></tr>
                <tr><td><strong>Tarifa/día</strong></td><td class="right">${{ number_format($tarifa,2) }}</td></tr>
                <tr><td><strong>Total cobrado (registros)</strong></td><td class="right">${{ number_format($totalCobradoDevs,2) }}</td></tr>
                <tr><td><strong>Total abonado (registros)</strong></td><td class="right">${{ number_format($totalAbonadoDevs,2) }}</td></tr>
                <tr><td><strong>Saldo item</strong></td><td class="right"><strong>${{ number_format($saldoMostrado,2) }}</strong></td></tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    @endforeach

  @endif

  {{-- Incidencias generales del arriendo --}}
  <h3 class="section-title">Incidencias del arriendo</h3>
  @if(empty($arriendo->incidencias) || $arriendo->incidencias->isEmpty())
    <div class="box">No hay incidencias registradas.</div>
  @else
    <div class="box">
      <table class="table">
        <thead>
          <tr>
            <th>Tipo</th>
            <th class="right">Días</th>
            <th class="right">Costo</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($arriendo->incidencias->sortByDesc('id') as $i)
            <tr>
              <td>{{ $i->tipo }}</td>
              <td class="right">{{ (int)$i->dias }}</td>
              <td class="right">${{ number_format((float)$i->costo,2) }}</td>
              <td>{{ $i->descripcion }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>
@endsection
