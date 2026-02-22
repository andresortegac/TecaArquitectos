@extends('layouts.app')

@section('title','Arriendos')
@section('header','ALQUILER')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/ui.css') }}">

  {{-- ✅ ESTILOS SOLO PARA ESTA VISTA (ENCAPSULADO) --}}
  <style>
    .pro-ui{
      --surface: rgba(255,255,255,.92);
      --card: #fff;
      --text: #0f172a;
      --muted: #64748b;
      --line: #e5e7eb;
      --soft: #f8fafc;
      --shadow: 0 18px 45px rgba(15,23,42,.12);
      --shadow2: 0 10px 24px rgba(15,23,42,.08);
      --r: 18px;

      width: 100%;
      color: var(--text);
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
    }

    /* ✅ Contenedor: ocupa el ancho del content (no centra raro) */
    .pro-container{
      width: 100%;
      max-width: 100%;
      padding: 16px;
      border-radius: 22px;
      border: 1px solid rgba(226,232,240,.92);
      background: var(--surface);
      backdrop-filter: blur(8px);
      box-shadow: var(--shadow);
      overflow: hidden; /* evita “bordes” raros */
    }

    /* Header superior */
    .pro-topbar{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:12px;
      flex-wrap:wrap;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(226,232,240,.95);
      margin-bottom: 14px;
    }
    .pro-subtitle{
      margin: 6px 0 0;
      color: rgba(71,85,105,.95);
      font-size: 13px;
      line-height: 1.35;
      max-width: 760px;
    }
    .pro-actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
    }

    /* Pulimos botones que ya existen */
    .pro-ui .btn-primary,
    .pro-ui .btn-ghost,
    .pro-ui .btn-sm{
      border-radius: 12px !important;
      font-weight: 700;
      transition: .15s ease;
    }
    .pro-ui .btn-primary:hover,
    .pro-ui .btn-ghost:hover,
    .pro-ui .btn-sm:hover{
      transform: translateY(-1px);
      box-shadow: var(--shadow2);
    }

    /* Cards */
    .pro-ui .card{
      border-radius: var(--r) !important;
      border: 1px solid rgba(226,232,240,.95) !important;
      background: rgba(255,255,255,.98) !important;
      box-shadow: var(--shadow2) !important;
    }
    .pro-ui .card-header{
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(226,232,240,.95);
    }
    .pro-ui .card-title{
      font-size: 14px !important;
      font-weight: 900 !important;
      letter-spacing: .2px;
    }

    /* KPI */
    .pro-ui .kpi-grid{ gap:12px !important; margin-bottom: 12px; }
    .pro-ui .card.kpi{
      min-height: 132px;
      border-radius: 18px !important;
      background: rgba(255,255,255,.96) !important;
    }
    .pro-ui .card.kpi .meta .label{
      font-size: 12px !important;
      color: rgba(100,116,139,.95) !important;
      text-transform: uppercase;
      letter-spacing: .45px;
      font-weight: 800 !important;
    }
    .pro-ui .card.kpi .meta .value{
      font-size: 22px !important;
      font-weight: 900 !important;
      letter-spacing:.2px;
    }
    .pro-ui .card.kpi .meta .hint{
      font-size: 12px !important;
      color: rgba(100,116,139,.95) !important;
    }

    /* Filtros */
    .pro-ui .filters-grid{ gap:12px !important; align-items:center; }
    .pro-ui .input{
      border-radius: 999px !important;
      height: 44px !important;
      border: 1px solid rgba(203,213,225,.95) !important;
      background:#fff !important;
      padding: 0 14px !important;
      outline:none !important;
      transition: .12s ease;
    }
    .pro-ui .input:focus{
      border-color: rgba(59,130,246,.75) !important;
      box-shadow: 0 0 0 5px rgba(59,130,246,.12) !important;
    }

    /* ✅ Tabla pro (sin romper layout). En móvil hace scroll, en desktop no */
    .table-wrap-pro{
      width: 100%;
      overflow-x: auto;
      border-radius: 16px;
      border: 1px solid rgba(226,232,240,.95);
      background: #fff;
      margin-top: 10px;
      position: relative; /* ✅ necesario para layering del dropdown */
    }
    .pro-ui .table-pro{
      width: 100%;
      min-width: 980px; /* móvil/tablet: scroll horizontal controlado */
      border-collapse: separate !important;
      border-spacing: 0 !important;
    }
    .pro-ui .table-pro thead th{
      position: sticky;
      top: 0;
      z-index: 2;
      background: #f8fafc !important;
      color: rgba(71,85,105,.95) !important;
      text-transform: uppercase;
      letter-spacing: .45px;
      font-size: 12px !important;
      font-weight: 900 !important;
      border-bottom: 1px solid rgba(226,232,240,.95) !important;
      padding: 12px !important;
      white-space: nowrap;
    }
    .pro-ui .table-pro tbody td{
      padding: 12px !important;
      border-bottom: 1px solid rgba(226,232,240,.85) !important;
      font-size: 13px !important;
      vertical-align: middle !important;
      color: rgba(15,23,42,.95);
      white-space: nowrap;
    }
    .pro-ui .table-pro tbody tr{
      position: relative; /* ✅ para z-index cuando dropdown abre */
    }

    /* ✅ Hover sin cambiar colores de semáforo */
    .pro-ui .table-pro tbody tr:hover{
      filter: none !important;
      background: inherit !important;
    }

    .td-right{ text-align:right; }
    .small{
      display:block;
      margin-top: 6px;
      color: rgba(100,116,139,.95) !important;
      font-size: 12px !important;
      line-height: 1.2;
      white-space: normal;
    }

    /* Chips más serios (sin cambiar colores de tu ui.css) */
    .pro-ui .chip{
      border-radius: 999px !important;
      padding: 7px 12px !important;
      font-weight: 900 !important;
      letter-spacing: .35px;
      border: 1px solid rgba(226,232,240,.85);
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space: nowrap;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
    }

    /* Dropdown */
    .pro-ui .actions{ display:flex; justify-content:flex-end; }
    .pro-ui .dropdown{ position: relative; z-index: 60; }
    .pro-ui [data-dd].open{ z-index: 60; }
    .pro-ui [data-dd].open .dropdown-menu{ display:block; }

    .pro-ui .btn-kebab{
      width: 40px !important;
      height: 40px !important;
      border-radius: 999px !important;
      border: 1px solid rgba(226,232,240,.95) !important;
      background: #fff !important;
      box-shadow: 0 10px 18px rgba(15,23,42,.08);
      transition: .15s ease;
    }
    .pro-ui .btn-kebab:hover{ transform: translateY(-1px); }
    .pro-ui .btn-kebab:active{ transform: translateY(0); }

    .pro-ui .dropdown-menu{
      display:none;
      position:absolute;
      right:0;
      top: calc(100% + 8px);
      min-width: 220px;
      background:#fff;
      border:1px solid rgba(226,232,240,.95);
      border-radius: 14px;
      box-shadow: 0 18px 40px rgba(15,23,42,.16);
      overflow:hidden;
      z-index: 9999 !important; /* ✅ arriba de todo */
      will-change: transform;   /* ✅ evita flicker */
      transform: translateZ(0); /* ✅ evita flicker */
    }
    .pro-ui .menu-item{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      width:100%;
      padding: 11px 12px;
      border:0;
      background:#fff;
      text-decoration:none;
      color: rgba(15,23,42,.95);
      font-weight: 700;
      cursor:pointer;
      transition:.12s ease;
    }
    .pro-ui .menu-item:hover{ background:#f8fafc; }
    .pro-ui .menu-left{ display:flex; align-items:center; gap:10px; }
    .pro-ui .dot{
      width: 8px; height: 8px;
      border-radius: 999px;
      background: rgba(148,163,184,.9);
    }
    .pro-ui .item-delete .dot{ background: #ef4444; }
    .pro-ui .item-close .dot{ background: #f59e0b; }
    .pro-ui .item-return .dot{ background: #3b82f6; }
    .pro-ui .item-details .dot{ background: #10b981; }

    /* ✅ Fix parpadeo: cuando dropdown esté abierto, la fila sube de nivel */
    .pro-ui .table-pro tbody tr.row-open{ z-index: 60; }
    .pro-ui .table-pro tbody tr.row-open:hover{ filter: none !important; }

    /* Modal (solo estética; si tu ui.css ya lo maneja, no rompe) */
    .pro-ui .modal-backdrop{
      position: fixed !important;
      inset: 0 !important;
      background: rgba(2,6,23,.55) !important;
      backdrop-filter: blur(6px);
      z-index: 80 !important;
      padding: 18px;
    }
    .pro-ui .modal-dialog{
      max-width: 760px;
      margin: auto;
    }
    .pro-ui .modal-grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 12px;
    }
    @media(max-width: 820px){
      .pro-ui .modal-grid{ grid-template-columns:1fr; }
    }
    .pro-ui .modal-actions{
      display:flex;
      justify-content:flex-end;
      gap:10px;
      margin-top: 14px;
    }
    .pro-ui .close-summary{
      margin-top: 12px;
      padding: 12px;
      border-radius: 14px;
      border: 1px solid rgba(59,130,246,.24);
      background: rgba(59,130,246,.06);
    }
    .pro-ui .close-summary-grid{
      display:grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
    }
    @media(max-width: 820px){
      .pro-ui .close-summary-grid{
        grid-template-columns: 1fr 1fr;
      }
    }
    .pro-ui .sum-box{
      background:#fff;
      border: 1px solid rgba(226,232,240,.95);
      border-radius: 12px;
      padding: 9px 10px;
    }
    .pro-ui .sum-k{
      display:block;
      font-size: 11px;
      color: rgba(100,116,139,.95);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .2px;
    }
    .pro-ui .sum-v{
      display:block;
      margin-top: 4px;
      font-size: 15px;
      font-weight: 900;
      color: rgba(15,23,42,.95);
      font-variant-numeric: tabular-nums;
    }
    .pro-ui .sum-v-danger{ color:#b91c1c; }
    .pro-ui .sum-v-ok{ color:#166534; }

    /* ==============================
       ✅ SEMAFORIZACION POR FILA
       AZUL: activo
       VERDE: cerrado/devuelto y saldo=0
       NARANJA: cerrado/devuelto y saldo>0 y dias<=7
       ROJO: cerrado/devuelto y saldo>0 y dias>=8 (hasta que pague)
       ============================== */
    .tr-flag-blue   { background: rgba(59,130,246,.12) !important; }
    .tr-flag-green  { background: rgba(34,197,94,.14) !important; }
    .tr-flag-amber  { background: rgba(245,158,11,.16) !important; }
    .tr-flag-red    { background: rgba(239,68,68,.14) !important; }

    .tr-flag-blue  td:first-child  { box-shadow: inset 4px 0 0 rgba(59,130,246,.55); }
    .tr-flag-green td:first-child  { box-shadow: inset 4px 0 0 rgba(34,197,94,.55); }
    .tr-flag-amber td:first-child  { box-shadow: inset 4px 0 0 rgba(245,158,11,.60); }
    .tr-flag-red   td:first-child  { box-shadow: inset 4px 0 0 rgba(239,68,68,.60); }
  </style>
@endpush

@section('content')

  <div class="principal-page">

    @if(session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif

    @php
      // ✅ KPIs calculados con los datos visibles (paginación)
      $col = $arriendos->getCollection();

      $total = $col->count();
      $activos = $col->where('estado','activo')->count();
      $devueltos = $col->where('estado','devuelto')->count();

      $rojo = $col->where('semaforo_pago','ROJO')->count();
      $amarillo = $col->where('semaforo_pago','AMARILLO')->count();
      $verde = $total - $rojo - $amarillo;

      $saldoTotal = $col->sum(fn($x)=>(float)($x->saldo ?? 0));
      $moraTotal = $col->sum(fn($x)=>(int)($x->dias_mora ?? 0));

      $pctPagos = $total ? round(($verde / $total) * 100) : 0;
      $pctActivos = $total ? round(($activos / $total) * 100) : 0;
      $pctDev = $total ? round(($devueltos / $total) * 100) : 0;
      $pctMora = $total ? round((($rojo + $amarillo) / $total) * 100) : 0;

      $pctRecaudoMes = ((float)($recaudadoMes ?? 0)) > 0 ? 67 : 0;
      $pctRecaudoHoy = ((float)($recaudadoHoy ?? 0)) > 0 ? 85 : 15;
    @endphp

    <div class="pro-ui">
      <div class="pro-container">

        {{-- TOPBAR --}}
        <div class="pro-topbar">
          <p class="pro-subtitle">
            Lista de arriendos (Contratos PADRE), estados de pago y gestión por productos (items).
          </p>

          <div class="pro-actions">
            <a class="btn-ghost" href="{{ route('arriendos.index') }}">Refrescar</a>
            <a class="btn-primary" href="{{ route('arriendos.create') }}">+ Nuevo arriendo</a>
          </div>
        </div>

        {{-- KPIs --}}
        <div class="kpi-grid">

          <div class="card kpi">
            <div class="meta">
              <div class="label">Total</div>
              <div class="value">{{ $total }}</div>
              <div class="hint">En esta página</div>
            </div>
            <div class="ring"
                 style="--p: {{ $pctPagos }}%; --ring: var(--primary);"
                 data-t="{{ $pctPagos }}%">
            </div>
          </div>

          <div class="card kpi">
            <div class="meta">
              <div class="label">Activos</div>
              <div class="value">{{ $activos }}</div>
              <div class="hint">En curso</div>
            </div>
            <div class="ring"
                 style="--p: {{ $pctActivos }}%; --ring: var(--success);"
                 data-t="{{ $pctActivos }}%">
            </div>
          </div>

          <div class="card kpi">
            <div class="meta">
              <div class="label">Devueltos</div>
              <div class="value">{{ $devueltos }}</div>
              <div class="hint">Cerrados</div>
            </div>
            <div class="ring"
                 style="--p: {{ $pctDev }}%; --ring: rgba(100,116,139,.8);"
                 data-t="{{ $pctDev }}%">
            </div>
          </div>

          <div class="card kpi" id="kpiRecaudoMes">
            <div class="meta">
              <div class="label">Recaudo del mes</div>
              <div class="value">${{ number_format((float)($recaudadoMes ?? 0), 0) }}</div>
              <div class="hint">{{ now()->format('m/Y') }} (confirmado)</div>

              @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual') || \Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
                <div style="margin-top:8px;">
                  @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
                    <a class="btn-sm"
                       href="{{ route('metricas.reporte.mensual', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}">
                      Ver detalle del mes
                    </a>
                  @elseif(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual'))
                    <a class="btn-sm"
                       href="{{ route('metricas.reporte.anual', ['year' => request('year', now()->year)]) }}">
                      Ver detalle anual
                    </a>
                  @endif
                </div>
              @endif
            </div>

            <div class="ring"
                 style="--p: {{ $pctRecaudoMes }}%; --ring: var(--primary);"
                 data-t="%">
            </div>
          </div>

          <div class="card kpi" id="kpiRecaudoHoy">
            <div class="meta">
              <div class="label">Recaudado hoy</div>
              <div class="value" id="recaudoHoyValue">
                ${{ number_format((float)($recaudadoHoy ?? 0), 0) }}
              </div>
              <div class="hint">{{ now()->format('d/m/Y') }} (confirmado)</div>

              @if(\Illuminate\Support\Facades\Route::has('metricas.detalle.dia'))
                <div style="margin-top:8px;">
                  <a class="btn-sm"
                     href="{{ route('metricas.detalle.dia', ['date' => now()->toDateString()]) }}">
                    Ver detalle de hoy
                  </a>
                </div>
              @endif
            </div>

            <div class="ring"
                 style="--p: {{ $pctRecaudoHoy }}%; --ring: var(--success);"
                 data-t="$">
            </div>
          </div>

        </div>

        {{-- MINI REPORTES --}}
        <div style="margin:10px 0 0; display:flex; gap:8px; flex-wrap:wrap;">
          @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual'))
            <a class="btn-sm"
               href="{{ route('metricas.reporte.anual', ['year' => request('year', now()->year)]) }}">
              Reporte anual
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
            <a class="btn-sm"
               href="{{ route('metricas.reporte.mensual', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}">
              Reporte mensual
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('metricas.detalle.dia'))
            <a class="btn-sm"
               href="{{ route('metricas.detalle.dia', ['date' => now()->toDateString()]) }}">
              Detalle día (hoy)
            </a>
          @endif
        </div>

        {{-- FILTROS --}}
        <div class="card" style="margin-top:12px;">
          <div class="card-header">
            <h3 class="card-title">Filtros</h3>
            <a class="btn-sm" href="{{ route('arriendos.index') }}">Limpiar</a>
          </div>

          <form id="filtrosForm" method="GET" action="{{ route('arriendos.index') }}">
            <div class="filters-grid">

              <select name="cliente_id" class="input filtro-auto">
                <option value="">Cliente (todos)</option>
                @isset($clientes)
                  @foreach($clientes as $c)
                    <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                      {{ $c->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>

              <select name="producto_id" class="input filtro-auto">
                <option value="">Producto (todos)</option>
                @isset($productos)
                  @foreach($productos as $p)
                    <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                      {{ $p->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>

              <select name="obra_id" class="input filtro-auto">
                <option value="">Obra (todas)</option>
                @isset($obras)
                  @foreach($obras as $obraId)
                    <option value="{{ $obraId }}" {{ (string)request('obra_id') === (string)$obraId ? 'selected' : '' }}>
                      {{ $obraId }}
                    </option>
                  @endforeach
                @endisset
              </select>

            </div>
          </form>
        </div>

        {{-- TABLA --}}
        <div class="card" style="margin-top:12px;">
          <div class="card-header">
            <h3 class="card-title">Lista de arriendos (Contratos PADRE)</h3>
          </div>

          <div class="table-wrap-pro">
            <table class="table-pro">
              <thead>
                <tr>
                  <th>Cliente</th>
                  <th>Items</th>
                  <th>Unidades</th>
                  <th>Inicio</th>
                  <th>Fin</th>
                  <th class="td-right">Total</th>
                  <th class="td-right">Saldo</th>
                  <th>Mora</th>
                  {{-- ✅ OCULTAMOS EL TITULO "SEMAFORO" --}}
                  <th></th>
                  <th>Estado</th>
                  <th style="width:260px;">Acciones</th>
                </tr>
              </thead>

              <tbody>
                @forelse($arriendos as $a)

                  @php
                    $itemsCount = $a->items_count ?? (isset($a->items) ? $a->items->count() : null);
                    $unidades = isset($a->items) ? (int)$a->items->sum('cantidad_actual') : null;

                    // ==============================
                    // ✅ LOGICA SEMAFORIZACION POR FILA (NO MUESTRA TEXTO)
                    // AZUL: activo
                    // VERDE: cerrado/devuelto y saldo=0
                    // NARANJA: cerrado/devuelto y saldo>0 y dias<=7
                    // ROJO: cerrado/devuelto y saldo>0 y dias>=8 (hasta que pague)
                    // ==============================
                    $saldo = (float)($a->saldo ?? 0);

                    $estaActivo  = (strtolower($a->estado ?? '') === 'activo') && ((int)($a->cerrado ?? 0) === 0);
                    $estaCerrado = ((int)($a->cerrado ?? 0) === 1) || (strtolower($a->estado ?? '') === 'devuelto');

                    // Fecha desde que cerró (usa el mejor dato disponible; no rompe si falta)
                    $fechaCierreRaw = $a->fecha_devolucion_real ?? ($a->fecha_fin ?? null) ?? ($a->updated_at ?? null);
                    $fechaCierre = $fechaCierreRaw ? \Carbon\Carbon::parse($fechaCierreRaw)->startOfDay() : null;
                    $diasDesdeCierre = $fechaCierre ? $fechaCierre->diffInDays(\Carbon\Carbon::today()) : 0;

                    if ($estaActivo) {
                      $rowClass = 'tr-flag-blue';
                    } elseif ($estaCerrado && $saldo <= 0) {
                      $rowClass = 'tr-flag-green';
                    } elseif ($estaCerrado && $saldo > 0 && $diasDesdeCierre <= 7) {
                      $rowClass = 'tr-flag-amber';
                    } elseif ($estaCerrado && $saldo > 0) {
                      $rowClass = 'tr-flag-red';
                    } else {
                      $rowClass = '';
                    }

                    $devAlquiler = (float)($a->dev_total_alquiler ?? 0);
                    $devMerma = (float)($a->dev_total_merma ?? 0);
                    $devPagado = (float)($a->dev_total_pagado ?? 0);
                    $devTransporte = (float)($a->dev_total_transporte ?? 0);

                    $itemsAlquiler = (float)(isset($a->items) ? $a->items->sum('total_alquiler') : 0);
                    $itemsMerma = (float)(isset($a->items) ? $a->items->sum('total_merma') : 0);
                    $itemsPagado = (float)(isset($a->items) ? $a->items->sum('total_pagado') : 0);

                    $baseAlquiler = $devAlquiler > 0 ? $devAlquiler : ($itemsAlquiler > 0 ? $itemsAlquiler : (float)($a->total_alquiler ?? 0));
                    $baseMerma = $devMerma > 0 ? $devMerma : ($itemsMerma > 0 ? $itemsMerma : (float)($a->total_merma ?? 0));
                    $basePagado = $devPagado > 0 ? $devPagado : ($itemsPagado > 0 ? $itemsPagado : (float)($a->total_pagado ?? 0));

                    $baseTransportePadre = (float)(isset($a->transportes) ? $a->transportes->sum('valor') : 0);
                    $baseTransporte = $baseTransportePadre + $devTransporte;
                    $baseIvaRate    = (float)($a->iva_rate ?? 0.19);
                    $baseSubtotal   = $baseAlquiler + $baseMerma + $baseTransporte;
                    $baseIvaValor   = (int)($a->iva_aplica ?? 0) === 1 ? ($baseSubtotal * $baseIvaRate) : 0;
                    $baseTotalFinal = $baseSubtotal + $baseIvaValor;
                    $baseSaldoFinal = max(0, $baseTotalFinal - $basePagado);
                  @endphp

                  <tr class="{{ $rowClass }}">
                    <td>
                      {{ $a->cliente->nombre ?? '—' }}

                      @if(!empty($a->obra_id ?? null))
                        <span class="small">Obra: {{ $a->obra_id }}</span>
                      @endif
                    </td>

                    <td>
                      {{ $itemsCount !== null ? $itemsCount : '—' }}
                      <span class="small">productos</span>
                    </td>

                    <td>
                      {{ $unidades !== null ? $unidades : '—' }}
                      <span class="small">unidades</span>
                    </td>

                    <td>{{ $a->fecha_inicio?->format('d/m/Y H:i') }}</td>
                    <td>{{ $a->fecha_fin ?? '—' }}</td>

                    <td class="td-right">${{ number_format((float)($a->precio_total ?? 0), 2) }}</td>
                    <td class="td-right">${{ number_format((float)($a->saldo ?? 0), 2) }}</td>

                    <td>{{ (int)($a->dias_mora ?? 0) }}</td>

                    {{-- ✅ columna vacía (semaforización solo por color de fila) --}}
                    <td></td>

                    <td>
                      @if($a->estado === 'devuelto')
                        <span class="chip gray">Devuelto</span>
                      @else
                        <span class="chip blue">{{ ucfirst($a->estado) }}</span>
                      @endif
                    </td>

                    <td>
                      <div class="actions">
                        <div class="dropdown" data-dd>
                          <button type="button" class="btn-kebab" aria-label="Acciones">⋯</button>

                          <div class="dropdown-menu">

                            <a class="menu-item item-return" href="{{ route('arriendos.ver', $a) }}">
                              <span class="menu-left"><span class="dot"></span>Ver / Gestionar</span>
                              <span>›</span>
                            </a>

                            <a class="menu-item item-edit" href="{{ route('arriendos.edit',$a) }}">
                              <span class="menu-left"><span class="dot"></span>Editar</span>
                              <span>›</span>
                            </a>

                            @if((int)($a->cerrado ?? 0) === 1 || $a->estado === 'devuelto')
                              <a class="menu-item item-details" href="{{ route('arriendos.detalles', $a) }}">
                                <span class="menu-left"><span class="dot"></span>Detalles</span>
                                <span>›</span>
                              </a>
                            @endif

                            @if((int)($a->cerrado ?? 0) === 0)
                              <button type="button"
                                      class="menu-item item-close"
                                      onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='flex'">
                                <span class="menu-left"><span class="dot"></span>Cerrar</span>
                                <span>›</span>
                              </button>
                            @endif

                            <form action="{{ route('arriendos.destroy',$a) }}" method="POST">
                              @csrf
                              @method('DELETE')

                              <button class="menu-item item-delete" onclick="return confirm('¿Eliminar arriendo?')">
                                <span class="menu-left"><span class="dot"></span>Borrar</span>
                                <span>›</span>
                              </button>
                            </form>

                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>

                  {{-- MODAL CERRAR --}}
                  @if((int)($a->cerrado ?? 0) === 0)
                    <div id="modalCerrar{{ $a->id }}" class="modal-backdrop" style="display:none;">
                      <div class="card modal-dialog">
                        <div class="card-header modal-header">
                          <h3 class="card-title">Cerrar arriendo #{{ $a->id }}</h3>
                          <button type="button"
                                  class="btn-ghost"
                                  onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">
                            Cerrar
                          </button>
                        </div>

                        <form method="POST"
                              action="{{ route('arriendos.cerrar', $a) }}"
                              class="js-cerrar-form"
                              data-arriendo-id="{{ $a->id }}"
                              data-base-alquiler="{{ $baseAlquiler }}"
                              data-base-merma="{{ $baseMerma }}"
                              data-base-pagado="{{ $basePagado }}"
                              data-base-transporte="{{ $baseTransporte }}"
                              data-iva-rate="{{ $baseIvaRate }}">
                          @csrf

                          <div class="modal-grid">
                            <div class="modal-field">
                              <label class="small modal-label">Fecha devolución real</label>
                              <input class="input"
                                     type="date"
                                     name="fecha_devolucion_real"
                                     required
                                     value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="modal-field">
                              <label class="small modal-label">Pago recibido (opcional)</label>
                              <input class="input"
                                     type="number"
                                     min="0"
                                     step="0.01"
                                     name="pago"
                                     value="0">
                              <div style="margin-top:8px;">
                                <button type="button" class="btn-sm js-pagar-todo">Pagar saldo completo</button>
                              </div>
                            </div>
                          </div>

                          <div class="modal-grid">
                            <div class="modal-field">
                              <label class="small modal-label">Días de lluvia (se descuentan)</label>
                              <input class="input"
                                     type="number"
                                     min="0"
                                     name="dias_lluvia"
                                     value="0">
                            </div>

                            <div class="modal-field">
                              <label class="small modal-label">Costo daño/merma</label>
                              <input class="input"
                                     type="number"
                                     min="0"
                                     step="0.01"
                                     name="costo_merma"
                                     value="0">
                            </div>
                          </div>

                          <div class="modal-grid">
                            <div class="modal-field">
                              <label class="small modal-label">Factura con IVA</label>
                              <select class="input" name="iva_aplica">
                                <option value="0" {{ (int)($a->iva_aplica ?? 0) === 0 ? 'selected' : '' }}>Sin IVA</option>
                                <option value="1" {{ (int)($a->iva_aplica ?? 0) === 1 ? 'selected' : '' }}>Con IVA (19%)</option>
                              </select>
                              <div class="small modal-help" style="margin-top:6px;">
                                El IVA se calcula sobre (alquiler + merma + transportes).
                              </div>
                            </div>
                            <div class="modal-field"></div>
                          </div>

                          <div class="close-summary">
                            <div class="close-summary-grid">
                              <div class="sum-box">
                                <span class="sum-k">Alquiler generado</span>
                                <span class="sum-v js-sum-alquiler">${{ number_format($baseAlquiler, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Merma total</span>
                                <span class="sum-v js-sum-merma">${{ number_format($baseMerma, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Transportes</span>
                                <span class="sum-v js-sum-transporte">${{ number_format($baseTransporte, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">IVA</span>
                                <span class="sum-v js-sum-iva">${{ number_format($baseIvaValor, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Total generado</span>
                                <span class="sum-v js-sum-total">${{ number_format($baseTotalFinal, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Total pagado</span>
                                <span class="sum-v js-sum-pagado">${{ number_format($basePagado, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Saldo final</span>
                                <span class="sum-v js-sum-saldo {{ $baseSaldoFinal > 0 ? 'sum-v-danger' : 'sum-v-ok' }}">${{ number_format($baseSaldoFinal, 2) }}</span>
                              </div>
                              <div class="sum-box">
                                <span class="sum-k">Estado de cierre</span>
                                <span class="sum-v js-sum-estado {{ $baseSaldoFinal > 0 ? 'sum-v-danger' : 'sum-v-ok' }}">
                                  {{ $baseSaldoFinal > 0 ? 'Queda saldo pendiente' : 'Cierra sin deuda' }}
                                </span>
                              </div>
                            </div>
                          </div>

                          <div class="modal-field">
                            <label class="small modal-label">Descripción (opcional)</label>
                            <input class="input"
                                   type="text"
                                   name="descripcion_incidencia"
                                   placeholder="Ej: lluvia fuerte / mango roto">
                          </div>

                          <div class="small modal-help">
                            Domingos se descuentan automáticamente. Si queda saldo pendiente al cerrar, se activa semáforo (AMARILLO 0–9 / ROJO 10+).
                          </div>

                          <div class="modal-actions">
                            <button type="button"
                                    class="btn-ghost"
                                    onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">
                              Cancelar
                            </button>

                            <button type="submit" class="btn-primary" style="padding:8px 12px;">
                              Cerrar y calcular
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  @endif

                @empty
                  <tr>
                    <td colspan="10">No hay arriendos todavía.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div style="margin-top:12px;">
            {{ $arriendos->links() }}
          </div>
        </div>

        {{-- JS: filtros + dropdown (FIX parpadeo incluido) --}}
        <script>
          (function () {
            const form = document.getElementById('filtrosForm');

            function submitFormLimpio() {
              Array.from(form.elements).forEach(el => {
                if (!el.name) return;
                el.disabled = (el.value === '' || el.value === null);
              });
              form.submit();
            }

            document.querySelectorAll('.filtro-auto').forEach(el => {
              el.addEventListener('change', submitFormLimpio);
            });

            function closeAll() {
              document.querySelectorAll('[data-dd].open').forEach(dd => {
                dd.classList.remove('open');
                const tr = dd.closest('tr');
                if (tr) tr.classList.remove('row-open');
              });
            }

            document.addEventListener('click', function (e) {
              const btn = e.target.closest('.btn-kebab');
              const dd  = e.target.closest('[data-dd]');

              if (btn && dd) {
                e.preventDefault();
                const wasOpen = dd.classList.contains('open');
                closeAll();
                if (!wasOpen) {
                  dd.classList.add('open');
                  const tr = dd.closest('tr');
                  if (tr) tr.classList.add('row-open');
                }
                return;
              }

              if (e.target.closest('.dropdown-menu')) return;
              closeAll();
            });

            document.addEventListener('keydown', function (e) {
              if (e.key === 'Escape') closeAll();
            });

            function parseNum(v) {
              const n = Number(v);
              return Number.isFinite(n) ? n : 0;
            }

            function money(v) {
              return '$' + parseNum(v).toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              });
            }

            function recalcCerrarForm(form) {
              const baseAlquiler = parseNum(form.dataset.baseAlquiler);
              const baseMerma = parseNum(form.dataset.baseMerma);
              const basePagado = parseNum(form.dataset.basePagado);
              const baseTransporte = parseNum(form.dataset.baseTransporte);
              const ivaRate = parseNum(form.dataset.ivaRate || 0.19);

              const pagoInput = form.querySelector('[name="pago"]');
              const mermaInput = form.querySelector('[name="costo_merma"]');
              const ivaInput = form.querySelector('[name="iva_aplica"]');

              const extraMerma = Math.max(0, parseNum(mermaInput?.value));
              const pagoCierre = Math.max(0, parseNum(pagoInput?.value));
              const ivaAplica = String(ivaInput?.value || '0') === '1';

              const totalMerma = baseMerma + extraMerma;
              const subtotal = baseAlquiler + totalMerma + baseTransporte;
              const ivaValor = ivaAplica ? (subtotal * ivaRate) : 0;
              const totalGenerado = subtotal + ivaValor;
              const totalPagado = basePagado + pagoCierre;
              const saldo = Math.max(0, totalGenerado - totalPagado);

              const $alq = form.querySelector('.js-sum-alquiler');
              const $mer = form.querySelector('.js-sum-merma');
              const $trn = form.querySelector('.js-sum-transporte');
              const $iva = form.querySelector('.js-sum-iva');
              const $tot = form.querySelector('.js-sum-total');
              const $pag = form.querySelector('.js-sum-pagado');
              const $sal = form.querySelector('.js-sum-saldo');
              const $est = form.querySelector('.js-sum-estado');

              if ($alq) $alq.textContent = money(baseAlquiler);
              if ($mer) $mer.textContent = money(totalMerma);
              if ($trn) $trn.textContent = money(baseTransporte);
              if ($iva) $iva.textContent = money(ivaValor);
              if ($tot) $tot.textContent = money(totalGenerado);
              if ($pag) $pag.textContent = money(totalPagado);
              if ($sal) {
                $sal.textContent = money(saldo);
                $sal.classList.toggle('sum-v-danger', saldo > 0);
                $sal.classList.toggle('sum-v-ok', saldo <= 0);
              }
              if ($est) {
                $est.textContent = saldo > 0 ? 'Queda saldo pendiente' : 'Cierra sin deuda';
                $est.classList.toggle('sum-v-danger', saldo > 0);
                $est.classList.toggle('sum-v-ok', saldo <= 0);
              }
            }

            document.querySelectorAll('.js-cerrar-form').forEach(form => {
              const pagoInput = form.querySelector('[name="pago"]');
              const mermaInput = form.querySelector('[name="costo_merma"]');
              const ivaInput = form.querySelector('[name="iva_aplica"]');
              const btnPagarTodo = form.querySelector('.js-pagar-todo');

              [pagoInput, mermaInput, ivaInput].forEach(el => {
                if (!el) return;
                el.addEventListener('input', () => recalcCerrarForm(form));
                el.addEventListener('change', () => recalcCerrarForm(form));
              });

              if (btnPagarTodo) {
                btnPagarTodo.addEventListener('click', function () {
                  const baseAlquiler = parseNum(form.dataset.baseAlquiler);
                  const baseMerma = parseNum(form.dataset.baseMerma);
                  const basePagado = parseNum(form.dataset.basePagado);
                  const baseTransporte = parseNum(form.dataset.baseTransporte);
                  const ivaRate = parseNum(form.dataset.ivaRate || 0.19);

                  const extraMerma = Math.max(0, parseNum(mermaInput?.value));
                  const ivaAplica = String(ivaInput?.value || '0') === '1';
                  const subtotal = baseAlquiler + (baseMerma + extraMerma) + baseTransporte;
                  const ivaValor = ivaAplica ? (subtotal * ivaRate) : 0;
                  const totalGenerado = subtotal + ivaValor;
                  const saldoActual = Math.max(0, totalGenerado - basePagado);

                  if (pagoInput) pagoInput.value = saldoActual.toFixed(2);
                  recalcCerrarForm(form);
                });
              }

              recalcCerrarForm(form);
            });
          })();
        </script>

        {{-- Recaudado hoy en tiempo real --}}
        <script>
          (function () {
            const el = document.getElementById('recaudoHoyValue');
            if (!el) return;

            async function refresh() {
              try {
                const url = "{{ \Illuminate\Support\Facades\Route::has('api.recaudado_hoy') ? route('api.recaudado_hoy') : '' }}";
                if (!url) return;

                const res = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
                const data = await res.json();
                const valor = Number(data.total || 0);

                el.textContent = '$' + valor.toLocaleString('es-CO');
              } catch (e) {
                console.error('No se pudo actualizar recaudado hoy', e);
              }
            }

            setInterval(refresh, 10000);
          })();
        </script>

      </div>
    </div>

  </div>

@endsection
