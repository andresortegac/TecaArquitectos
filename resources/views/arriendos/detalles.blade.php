@extends('layouts.app')
@section('title','Detalles / Factura de arriendo')
@section('header','Detalles / Factura de arriendo')

@section('content')

<style>
  /* ====== Base ====== */
  .factura-wrap { background:#fff; padding:16px; border-radius:12px; }
  .muted { color:#6b7280; font-size:12px; }
  .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; background:#f3f4f6; }
  .btn-print { cursor:pointer; }

  .box { background:#fff; border:1px solid #eee; border-radius:12px; padding:14px; }
  .section-title { margin:18px 0 10px; font-size:16px; }

  .table { width:100%; border-collapse:collapse; }
  .table th, .table td { border-bottom:1px solid #eee; padding:9px; font-size:13px; vertical-align:top; }
  .table th { text-align:left; background:#fafafa; }
  .right { text-align:right; }

  /* ====== Header ====== */
  .topbar {
    display:flex; justify-content:space-between; align-items:flex-start;
    gap:12px; flex-wrap:wrap; margin-bottom:12px;
  }
  .doc-title { margin:0; font-size:20px; font-weight:800; }
  .doc-sub { margin-top:2px; }

  .meta-grid {
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
    margin-top:10px;
  }
  @media (max-width: 900px){
    .meta-grid { grid-template-columns:1fr; }
  }

  /* ====== Executive summary ====== */
  .summary-grid {
    display:grid;
    grid-template-columns: 1.2fr 1fr;
    gap:10px;
    margin-top:10px;
  }
  @media (max-width: 900px){
    .summary-grid { grid-template-columns:1fr; }
  }
  .summary-title { font-weight:800; margin-bottom:8px; font-size:14px; }
  .kpi-grid { display:grid; grid-template-columns: 1fr 1fr; gap:8px; }
  .kpi-grid .label { font-weight:700; }
  .kpi-grid .value { text-align:right; font-variant-numeric: tabular-nums; }

  .total-strong { font-weight:900; }
  .saldo-strong { font-weight:900; }

  /* ====== Item layout ====== */
  .item-card { margin-bottom:12px; }
  .item-head {
    display:flex; justify-content:space-between; align-items:flex-start;
    gap:12px; flex-wrap:wrap; margin-bottom:10px;
  }
  .item-name { font-size:15px; font-weight:900; margin:0; }
  .item-meta { margin-top:4px; }
  .pill {
    font-size:12px; background:#f3f4f6; border-radius:999px; padding:4px 10px;
    display:inline-block; margin-left:6px;
  }

  .item-sections {
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
    margin-top:10px;
  }
  @media (max-width: 900px){
    .item-sections { grid-template-columns:1fr; }
  }

  .mini-box {
    border:1px solid #eee;
    background:#fcfcfc;
    border-radius:12px;
    padding:12px;
  }
  .mini-title { font-weight:900; font-size:13px; margin-bottom:8px; }
  .line {
    display:flex; justify-content:space-between; gap:12px;
    padding:7px 0;
    border-bottom:1px dotted #e9e9e9;
    font-size:12.8px;
  }
  .line:last-child { border-bottom:0; }
  .line .lbl { color:#374151; }
  .line .val { font-weight:800; white-space:nowrap; }

  .last-move {
    margin-top:10px;
    padding-top:10px;
    border-top:1px solid #eee;
    font-size:12.5px;
  }

  /* ====== Movements table ====== */
  .mov-table { width:100%; border-collapse:collapse; margin-top:10px; }
  .mov-table th, .mov-table td {
    border-bottom:1px solid #eee;
    padding:7px 7px;
    font-size:12px;
    white-space:nowrap;
  }
  .mov-table th { background:#fafafa; text-align:left; }
  .mov-right { text-align:right; }
  .mov-muted { color:#6b7280; }

  /* ====== Print ====== */
  @media print {
    .no-print { display:none !important; }
    body { background:#fff !important; }
    .factura-wrap { padding:0; border-radius:0; }
    .box { border:0; }
    .table th { background:#f2f2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .mov-table th { background:#f2f2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>

<div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
  <a class="btn-sm" href="{{ route('arriendos.ver', $arriendo->id) }}">Volver</a>
  <button type="button" class="btn-sm btn-print" onclick="window.print()">Imprimir</button>
</div>

@php
  /**
   * ✅ Totales generales calculados desde items + devoluciones (si vienen cargadas)
   * No cambia tu lógica: respeta relationLoaded('devoluciones').
   */
  $items = $arriendo->items ?? collect();

  $g_items_count = $items->count();
  $g_unidades_inicial = 0;
  $g_unidades_devueltas = 0;
  $g_unidades_restantes = 0;

  $g_total_alquiler = 0.0;
  $g_total_merma   = 0.0;
  $g_total_cobrado = 0.0;
  $g_total_abonado = 0.0;
  $g_saldo         = 0.0;

  $movs = 0;

  foreach($items as $it){
    $cantidadInicial = (int)($it->cantidad_inicial ?? 0);

    $devs = $it->relationLoaded('devoluciones') ? ($it->devoluciones ?? collect()) : collect();
    $devsAsc = $devs->sortBy('id')->values();

    $movs += $devs->count();

    $totalDevuelto = (int)$devs->sum('cantidad_devuelta');
    $restante = max(0, $cantidadInicial - $totalDevuelto);

    $g_unidades_inicial += $cantidadInicial;
    $g_unidades_devueltas += $totalDevuelto;
    $g_unidades_restantes += $restante;

    $g_total_alquiler += (float)$devs->sum('total_alquiler');
    $g_total_merma   += (float)$devs->sum('total_merma');
    $g_total_cobrado += (float)$devs->sum('total_cobrado');
    $g_total_abonado += (float)$devs->sum('pago_recibido');

    $saldoHist = $devsAsc->count() ? (float)($devsAsc->last()->saldo_resultante ?? 0) : null;
    $saldoMostrado = is_null($saldoHist) ? (float)($it->saldo ?? 0) : $saldoHist;
    $g_saldo += $saldoMostrado;
  }

  // Si el controlador manda $totales, los usamos; si no, usamos los calculados.
  $totales = $totales ?? [
    'total_alquiler' => $g_total_alquiler,
    'total_merma' => $g_total_merma,
    'total_pagado' => $g_total_abonado,
    'precio_total' => $g_total_cobrado,
    'saldo' => $g_saldo,
  ];
@endphp

<div class="factura-wrap">

  {{-- ====== IDENTIDAD DEL DOCUMENTO ====== --}}
  <div class="topbar">
    <div>
      <h2 class="doc-title">Factura de Arriendo / Informe de Detalle</h2>
      <div class="muted doc-sub">
        Arriendo #{{ $arriendo->id }} · Emisión: {{ now()->format('d/m/Y H:i') }}
      </div>
    </div>
    <div style="text-align:right;">
      <div class="badge">Estado: {{ strtoupper($arriendo->estado ?? '—') }}</div>
      <div class="muted" style="margin-top:6px;">
        Inicio: {{ $arriendo->fecha_inicio ? \Carbon\Carbon::parse($arriendo->fecha_inicio)->format('d/m/Y') : '—' }}
        @if(!empty($arriendo->fecha_fin))
          &nbsp;|&nbsp; Fin: {{ \Carbon\Carbon::parse($arriendo->fecha_fin)->format('d/m/Y') }}
        @endif
      </div>
    </div>
  </div>

  {{-- ====== METADATOS ====== --}}
  <div class="meta-grid">
    <div class="box">
      <div><strong>Cliente:</strong> {{ $arriendo->cliente->nombre ?? '—' }}</div>
      <div class="muted">Arriendo creado: {{ $arriendo->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
      @if(!empty($arriendo->obra_id))
        <div class="muted">Obra ID: {{ $arriendo->obra_id }}</div>
      @endif
    </div>

    <div class="box">
      <div class="summary-title">Resumen ejecutivo (estado)</div>
      <div class="kpi-grid">
        <div class="label">Herramientas (items)</div><div class="value">{{ $g_items_count }}</div>
        <div class="label">Unidades en obra</div><div class="value total-strong">{{ $g_unidades_restantes }}</div>
        <div class="label">Movimientos</div><div class="value">{{ $movs }}</div>
        <div class="label">Saldo general</div><div class="value saldo-strong">${{ number_format((float)$totales['saldo'], 2) }}</div>
      </div>
      <div class="muted" style="margin-top:8px;">
        *Los totales se calculan desde devoluciones si están cargadas.
      </div>
    </div>
  </div>

  {{-- ====== RESUMEN EJECUTIVO ====== --}}
  <div class="summary-grid">
    <div class="box">
      <div class="summary-title">Resumen general (dinero)</div>
      <div class="kpi-grid">
        <div class="label">Total alquiler</div><div class="value">${{ number_format((float)$totales['total_alquiler'], 2) }}</div>
        <div class="label">Total merma</div><div class="value">${{ number_format((float)$totales['total_merma'], 2) }}</div>
        <div class="label">Total cobrado</div><div class="value total-strong">${{ number_format((float)$totales['precio_total'], 2) }}</div>
        <div class="label">Total abonado</div><div class="value">${{ number_format((float)$totales['total_pagado'], 2) }}</div>
        <div class="label">Saldo pendiente</div><div class="value saldo-strong">${{ number_format((float)$totales['saldo'], 2) }}</div>
      </div>
    </div>

    <div class="box">
      <div class="summary-title">Resumen general (herramientas)</div>
      <div class="kpi-grid">
        <div class="label">Unidades iniciales</div><div class="value">{{ $g_unidades_inicial }}</div>
        <div class="label">Devueltas</div><div class="value">{{ $g_unidades_devueltas }}</div>
        <div class="label">Restantes (en obra)</div><div class="value total-strong">{{ $g_unidades_restantes }}</div>
        <div class="label">Movimientos</div><div class="value">{{ $movs }}</div>
      </div>
      <div class="muted" style="margin-top:8px;">
        Lectura rápida para control de obra / bodega.
      </div>
    </div>
  </div>

  {{-- ====== DETALLE POR HERRAMIENTA ====== --}}
  <h3 class="section-title">Detalle por herramienta</h3>

  @if(empty($arriendo->items) || $arriendo->items->isEmpty())
    <div class="box">No hay items en este arriendo.</div>
  @else

    @foreach($arriendo->items as $it)
      @php
        $tarifa = (float)($it->tarifa_diaria ?? ($it->producto->costo ?? 0));

        $devs = $it->relationLoaded('devoluciones') ? ($it->devoluciones ?? collect()) : collect();
        $devsAsc = $devs->sortBy('id')->values();
        $devsDesc = $devs->sortByDesc('id')->values();

        $cantidadInicial = (int)($it->cantidad_inicial ?? 0);

        $totalDevuelto = (int)$devs->sum('cantidad_devuelta');
        $totalCobradoDevs = (float)$devs->sum('total_cobrado');
        $totalAbonadoDevs = (float)$devs->sum('pago_recibido');

        $cantidadRestanteCalc = max(0, $cantidadInicial - $totalDevuelto);

        $saldoHist = $devsAsc->count() ? (float)($devsAsc->last()->saldo_resultante ?? 0) : null;
        $saldoMostrado = is_null($saldoHist) ? (float)($it->saldo ?? 0) : $saldoHist;

        $ultimo = $devsDesc->first();

        $ultimoFecha = $ultimo?->fecha_devolucion ? \Carbon\Carbon::parse($ultimo->fecha_devolucion)->format('d/m/Y') : '—';
        $ultimoDev   = $ultimo ? (int)$ultimo->cantidad_devuelta : 0;
        $ultimoAbono = $ultimo ? (float)$ultimo->pago_recibido : 0;
        $ultimoTotal = $ultimo ? (float)$ultimo->total_cobrado : 0;
        $ultimoQuedan= $ultimo ? (int)$ultimo->cantidad_restante : $cantidadRestanteCalc;

        $estadoTxt = ucfirst($it->estado ?? '—') . (((int)($it->cerrado ?? 0)===1) ? ' / Cerrado' : '');
      @endphp

      <div class="box item-card">

        <div class="item-head">
          <div>
            <p class="item-name">{{ $it->producto->nombre ?? 'Producto' }}</p>
            <div class="muted item-meta">
              Item #{{ $it->id }} ·
              Inicio: {{ $it->fecha_inicio_item ? \Carbon\Carbon::parse($it->fecha_inicio_item)->format('d/m/Y H:i') : '—' }}
              @if(!empty($it->fecha_fin_item))
                · Fin: {{ \Carbon\Carbon::parse($it->fecha_fin_item)->format('d/m/Y') }}
              @endif
            </div>
            <div class="muted">Estado: {{ $estadoTxt }}</div>
          </div>

          <div style="text-align:right;">
            <span class="pill">{{ $devs->count() }} movimiento(s)</span>
          </div>
        </div>

        <div class="item-sections">

          {{-- Estado de unidades --}}
          <div class="mini-box">
            <div class="mini-title">Estado de unidades</div>
            <div class="line"><div class="lbl">Cantidad inicial</div><div class="val">{{ $cantidadInicial }}</div></div>
            <div class="line"><div class="lbl">Devuelto acumulado</div><div class="val">{{ $totalDevuelto }}</div></div>
            <div class="line"><div class="lbl">Restante (en obra)</div><div class="val">{{ $cantidadRestanteCalc }}</div></div>
          </div>

          {{-- Resumen financiero del item --}}
          <div class="mini-box">
            <div class="mini-title">Resumen financiero del item</div>
            <div class="line"><div class="lbl">Tarifa / día</div><div class="val">${{ number_format($tarifa,2) }}</div></div>
            <div class="line"><div class="lbl">Total cobrado (registros)</div><div class="val">${{ number_format($totalCobradoDevs,2) }}</div></div>
            <div class="line"><div class="lbl">Total abonado (registros)</div><div class="val">${{ number_format($totalAbonadoDevs,2) }}</div></div>
            <div class="line"><div class="lbl">Saldo del item</div><div class="val">${{ number_format($saldoMostrado,2) }}</div></div>

            <div class="last-move">
              <div><strong>Último movimiento:</strong> {{ $ultimoFecha }}</div>
              <div class="mov-muted" style="margin-top:4px;">
                Devuelto: {{ $ultimoDev }} · Abono: ${{ number_format($ultimoAbono,2) }} · Total: ${{ number_format($ultimoTotal,2) }} · Quedan: {{ $ultimoQuedan }}
              </div>
            </div>
          </div>

        </div>

        {{-- Tabla de movimientos --}}
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
                      <td colspan="13" class="mov-muted" style="padding:7px;">
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
    @endforeach

  @endif

  {{-- ====== INCIDENCIAS GENERALES ====== --}}
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
