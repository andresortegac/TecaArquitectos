@extends('layouts.app')

@section('title','Arriendos')
@section('header','ALQUILER')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/ui.css') }}">

  {{-- ✅ ESTILOS SOLO PARA ESTA VISTA (ENCAPSULADO) --}}
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');

    .pro-ui{
      --surface: linear-gradient(150deg, rgba(255,255,255,.94), rgba(241,245,255,.9));
      --card: rgba(255,255,255,.86);
      --text: #081225;
      --muted: #54627a;
      --line: rgba(158,171,196,.34);
      --line-strong: rgba(120,140,170,.44);
      --soft: #f4f8ff;
      --brand: #1f67f3;
      --brand-2: #00a9b6;
      --brand-3: #0e2f77;
      --shadow: 0 24px 60px rgba(8,24,55,.2);
      --shadow2: 0 14px 34px rgba(8,24,55,.14);
      --shadow3: inset 0 1px 0 rgba(255,255,255,.9), inset 0 -1px 0 rgba(116,137,171,.12);
      --r: 20px;

      width: 100%;
      color: var(--text);
      font-family: "Manrope", "Space Grotesk", "Segoe UI", sans-serif;
      position: relative;
      isolation: isolate;
    }

    .pro-ui::before,
    .pro-ui::after{
      content: "";
      position: absolute;
      pointer-events: none;
      border-radius: 999px;
      filter: blur(28px);
      z-index: -1;
    }
    .pro-ui::before{
      width: 360px;
      height: 360px;
      top: -90px;
      left: -70px;
      background: radial-gradient(circle at 30% 35%, rgba(31,103,243,.32), rgba(31,103,243,0));
    }
    .pro-ui::after{
      width: 280px;
      height: 280px;
      top: -50px;
      right: -40px;
      background: radial-gradient(circle at 50% 50%, rgba(0,169,182,.22), rgba(0,169,182,0));
    }

    /* ✅ Contenedor principal */
    .pro-container{
      width: 100%;
      max-width: 100%;
      padding: 18px;
      border-radius: 24px;
      border: 1px solid var(--line);
      background: var(--surface);
      backdrop-filter: blur(14px);
      box-shadow: var(--shadow);
      overflow: visible; /* ✅ IMPORTANTE para dropdown */
      position: relative;
      transform-style: preserve-3d;
    }
    .pro-container::before{
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      border-radius: 24px;
      background: linear-gradient(115deg, rgba(255,255,255,.45), rgba(255,255,255,0) 38%);
      mix-blend-mode: screen;
    }

    /* Topbar */
    .pro-topbar{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:12px;
      flex-wrap:wrap;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--line);
      margin-bottom: 16px;
      position: relative;
    }
    .pro-topbar::after{
      content: "";
      position: absolute;
      left: 0;
      bottom: -1px;
      width: 170px;
      height: 2px;
      background: linear-gradient(90deg, var(--brand), transparent);
    }
    .pro-subtitle{
      margin: 6px 0 0;
      color: var(--muted);
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

    /* Botones */
    .pro-ui .btn-primary,
    .pro-ui .btn-ghost,
    .pro-ui .btn-sm{
      border-radius: 12px !important;
      font-weight: 800;
      transition: transform .18s ease, box-shadow .24s ease, filter .24s ease, border-color .24s ease;
      border: 1px solid rgba(145,167,204,.34);
      box-shadow: 0 8px 18px rgba(10,24,52,.1);
      transform: translateZ(0);
    }
    .pro-ui .btn-primary:hover,
    .pro-ui .btn-ghost:hover,
    .pro-ui .btn-sm:hover{
      transform: translateY(-2px);
      box-shadow: 0 14px 28px rgba(10,24,52,.16);
      filter: saturate(1.04);
    }
    .pro-ui .btn-primary{
      border-color: rgba(82,133,230,.4);
      background: linear-gradient(140deg, #0f3ea8, #1f67f3 60%, #2ca9ff) !important;
      box-shadow: 0 14px 30px rgba(20,72,193,.28), inset 0 1px 0 rgba(255,255,255,.25);
    }
    .pro-ui .btn-ghost,
    .pro-ui .btn-sm{
      background: linear-gradient(170deg, rgba(255,255,255,.95), rgba(241,246,255,.9)) !important;
      color: var(--text) !important;
    }

    /* Cards base (por defecto pueden tener hidden, NO la tabla) */
    .pro-ui .card{
      border-radius: var(--r) !important;
      border: 1px solid var(--line) !important;
      background: var(--card) !important;
      box-shadow: var(--shadow2) !important;
      backdrop-filter: blur(9px);
      transform-style: preserve-3d;
      position: relative;
      overflow: hidden; /* ✅ base */
    }
    .pro-ui .card::before{
      content:"";
      position:absolute;
      inset: 0;
      pointer-events: none;
      background: linear-gradient(130deg, rgba(255,255,255,.5), rgba(255,255,255,0) 45%);
    }
    .pro-ui .card-header{
      padding-bottom: 10px;
      border-bottom: 1px solid var(--line);
    }
    .pro-ui .card-title{
      font-size: 14px !important;
      font-weight: 800 !important;
      letter-spacing: .3px;
      font-family: "Space Grotesk", "Manrope", sans-serif;
    }

    /* ✅ FIX: el card de la tabla NO debe recortar el dropdown */
    .pro-ui .card.card-table{
      overflow: visible !important;
    }

    /* KPI */
    .pro-ui .kpi-grid{ gap:12px !important; margin-bottom: 12px; }
    .pro-ui .card.kpi{
      min-height: 132px;
      border-radius: 18px !important;
      background: linear-gradient(155deg, rgba(255,255,255,.95), rgba(236,244,255,.88)) !important;
      box-shadow: 0 16px 35px rgba(9,32,74,.16), inset 0 1px 0 rgba(255,255,255,.9) !important;
      transition: transform .2s ease, box-shadow .24s ease;
    }
    .pro-ui .card.kpi:hover{
      transform: translateY(-4px) rotateX(.8deg);
      box-shadow: 0 22px 44px rgba(9,32,74,.2), inset 0 1px 0 rgba(255,255,255,.92) !important;
    }
    .pro-ui .card.kpi .meta .label{
      font-size: 12px !important;
      color: var(--muted) !important;
      text-transform: uppercase;
      letter-spacing: .62px;
      font-weight: 800 !important;
    }
    .pro-ui .card.kpi .meta .value{
      font-size: 22px !important;
      font-weight: 800 !important;
      letter-spacing:.2px;
      font-family: "Space Grotesk", "Manrope", sans-serif;
    }
    .pro-ui .card.kpi .meta .hint{
      font-size: 12px !important;
      color: var(--muted) !important;
    }
    .pro-ui .ring{
      width: 62px;
      height: 62px;
      box-shadow: 0 14px 24px rgba(10,41,93,.2), inset 0 1px 0 rgba(255,255,255,.55) !important;
    }

    /* Filtros */
    .pro-ui .filters-grid{ gap:12px !important; align-items:center; }
    .pro-ui .input{
      border-radius: 999px !important;
      height: 44px !important;
      border: 1px solid var(--line-strong) !important;
      background: linear-gradient(180deg, #fff, #f7faff) !important;
      padding: 0 14px !important;
      outline:none !important;
      transition: border-color .16s ease, box-shadow .18s ease, transform .16s ease;
      box-shadow: var(--shadow3);
    }
    .pro-ui .input:focus{
      border-color: rgba(31,103,243,.75) !important;
      box-shadow: 0 0 0 5px rgba(31,103,243,.14), 0 10px 20px rgba(9,32,74,.1) !important;
      transform: translateY(-1px);
    }

    /* Tabla */
    .table-wrap-pro{
      width: 100%;
      overflow: visible !important; /* ✅ FIX: no recortar menú */
      border-radius: 16px;
      border: 1px solid var(--line);
      background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(244,248,255,.92));
      margin-top: 10px;
      position: relative;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.86), 0 12px 26px rgba(10,32,70,.1);
    }
    .pro-ui .table-pro{
      width: 100%;
      min-width: 0;
      table-layout: fixed;
      border-collapse: separate !important;
      border-spacing: 0 !important;
    }
    .pro-ui .table-pro thead th{
      position: sticky;
      top: 0;
      z-index: 2;
      background: linear-gradient(180deg, #fdfefe, #edf4ff) !important;
      color: var(--muted) !important;
      text-transform: uppercase;
      letter-spacing: .45px;
      font-size: 12px !important;
      font-weight: 800 !important;
      border-bottom: 1px solid var(--line) !important;
      padding: 12px !important;
      white-space: normal;
      word-break: break-word;
      backdrop-filter: blur(6px);
      text-align: center !important;
    }
    .pro-ui .table-pro tbody td{
      padding: 12px !important;
      border-bottom: 1px solid rgba(167,181,205,.28) !important;
      font-size: 13px !important;
      vertical-align: middle !important;
      color: rgba(8,18,37,.95);
      white-space: normal;
      word-break: break-word;
      text-align: center !important;
    }
    .pro-ui .table-pro tbody tr{
      position: relative;
      transition: transform .16s ease, box-shadow .18s ease;
    }
    .pro-ui .table-pro tbody tr:hover{
      filter: none !important;
      transform: translateY(-1px);
      box-shadow: 0 8px 18px rgba(8,24,55,.08);
    }

    .td-right{ text-align:center !important; }
    .small{
      display:block;
      margin-top: 6px;
      color: var(--muted) !important;
      font-size: 12px !important;
      line-height: 1.2;
      white-space: normal;
      text-align: center;
    }

    /* Chips */
    .pro-ui .chip{
      border-radius: 999px !important;
      padding: 7px 12px !important;
      font-weight: 800 !important;
      letter-spacing: .35px;
      border: 1px solid rgba(140,160,191,.36);
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space: nowrap;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.82), 0 5px 12px rgba(8,24,55,.08);
    }

    /* Dropdown */
    .pro-ui .actions{ display:flex; justify-content:center; }
    .pro-ui .dropdown{ position: relative; z-index: 60; }
    .pro-ui [data-dd].open{ z-index: 99998; }
    .pro-ui [data-dd].open .dropdown-menu{ display:block; }

    .pro-ui .btn-kebab{
      width: 40px !important;
      height: 40px !important;
      border-radius: 999px !important;
      border: 1px solid rgba(142,165,201,.34) !important;
      background: linear-gradient(180deg, #fff, #eef4ff) !important;
      box-shadow: 0 12px 20px rgba(8,24,55,.12), inset 0 1px 0 rgba(255,255,255,.75);
      transition: transform .16s ease, box-shadow .2s ease;
      font-size: 18px;
      line-height: 1;
      display:flex;
      align-items:center;
      justify-content:center;
      user-select: none;
    }
    .pro-ui .btn-kebab:hover{
      transform: translateY(-2px);
      box-shadow: 0 14px 24px rgba(8,24,55,.16), inset 0 1px 0 rgba(255,255,255,.9);
    }
    .pro-ui .btn-kebab:active{ transform: translateY(0); }

    .pro-ui .dropdown-menu{
      display:none;
      position:absolute;
      right:0;
      top: calc(100% + 8px);
      min-width: 220px;
      background: linear-gradient(180deg, #f9fbff, #f2f6fc);
      border:1px solid rgba(166,183,209,.45);
      border-radius: 14px;
      box-shadow: 0 18px 34px rgba(8,24,55,.18);
      overflow:hidden;
      z-index: 99999 !important;
      will-change: transform;
      transform: translateZ(0);
      padding: 8px 0;
      max-width: calc(100vw - 24px); /* ✅ evita que se salga del viewport */
    }
    .pro-ui .menu-item{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      width:100%;
      padding: 9px 14px;
      border:0;
      background: transparent;
      text-decoration:none;
      color: rgba(8,18,37,.95);
      font-weight: 800;
      cursor:pointer;
      transition:.12s ease;
      font-size: 15px;
    }
    .pro-ui .menu-item:hover{ background: rgba(31,103,243,.08); }
    .pro-ui .menu-left{ display:flex; align-items:center; gap:10px; }
    .pro-ui .dot{
      width: 10px; height: 10px;
      border-radius: 999px;
      background: rgba(148,163,184,.9);
      box-shadow: 0 0 0 3px rgba(110,131,164,.14);
    }
    .pro-ui .menu-arrow{
      font-size: 16px;
      line-height: 1;
      color: rgba(30,41,59,.9);
      font-weight: 800;
    }
    .pro-ui .item-delete .dot{ background: #ef4444; }
    .pro-ui .item-close .dot{ background: #f59e0b; }
    .pro-ui .item-return .dot{ background: #3b82f6; }
    .pro-ui .item-details .dot{ background: #10b981; }

    .pro-ui .table-pro tbody tr.row-open{ z-index: 99998; }
    .pro-ui .table-pro tbody tr.row-open:hover{ filter: none !important; }

    /* Modal */
    .pro-ui .modal-backdrop{
      position: fixed !important;
      inset: 0 !important;
      background: radial-gradient(circle at 15% 0%, rgba(31,103,243,.28), rgba(2,6,23,.64)) !important;
      backdrop-filter: blur(8px);
      z-index: 20000 !important;
      padding: 18px;
      display: none;
      align-items: center;
      justify-content: center;
    }
    .pro-ui .modal-dialog{
      width: 100%;
      max-width: 760px;
      margin: 0 auto;
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
      border: 1px solid rgba(71,122,216,.3);
      background: linear-gradient(170deg, rgba(31,103,243,.1), rgba(255,255,255,.8));
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
      background:linear-gradient(180deg, rgba(255,255,255,.97), rgba(238,245,255,.92));
      border: 1px solid rgba(148,168,201,.3);
      border-radius: 12px;
      padding: 9px 10px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
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

    /* Semaforización filas */
    .tr-flag-blue td{
      background: linear-gradient(180deg, rgba(46,124,244,.28), rgba(46,124,244,.24)) !important;
      color: #072a5f !important;
    }
    .tr-flag-green td{
      background: linear-gradient(180deg, rgba(20,184,96,.30), rgba(20,184,96,.24)) !important;
      color: #0a4a2a !important;
    }
    .tr-flag-amber td{
      background: linear-gradient(180deg, rgba(245,158,11,.34), rgba(245,158,11,.27)) !important;
      color: #6a3a00 !important;
    }
    .tr-flag-red td{
      background: linear-gradient(180deg, rgba(239,68,68,.32), rgba(239,68,68,.26)) !important;
      color: #6b1010 !important;
    }

    .tr-flag-blue  td:first-child  { box-shadow: inset 6px 0 0 #1d66e8, inset 0 0 0 1px rgba(29,102,232,.18); }
    .tr-flag-green td:first-child  { box-shadow: inset 6px 0 0 #0f9a56, inset 0 0 0 1px rgba(15,154,86,.18); }
    .tr-flag-amber td:first-child  { box-shadow: inset 6px 0 0 #e08a00, inset 0 0 0 1px rgba(224,138,0,.20); }
    .tr-flag-red   td:first-child  { box-shadow: inset 6px 0 0 #d62828, inset 0 0 0 1px rgba(214,40,40,.20); }

    @media(max-width: 780px){
      .pro-container{
        padding: 14px;
        border-radius: 18px;
      }
      .pro-ui .table-pro thead th,
      .pro-ui .table-pro tbody td{
        font-size: 12px !important;
        padding: 9px 6px !important;
      }
      .pro-topbar{
        gap: 10px;
      }
      .pro-actions{
        width: 100%;
      }
      .pro-ui .btn-primary,
      .pro-ui .btn-ghost{
        min-height: 42px;
      }
    }
  </style>
@endpush

@section('content')

  <div class="principal-page">

    @if(session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif

    @php
      // ✅ KPIs con datos visibles (paginación)
      $col = $arriendos->getCollection();

      $total = $col->count();
      $activos = $col->where('estado','activo')->count();
      $devueltos = $col->where('estado','devuelto')->count();

      $rojo = $col->where('semaforo_pago','ROJO')->count();
      $amarillo = $col->where('semaforo_pago','AMARILLO')->count();
      $verde = $total - $rojo - $amarillo;

      $pctPagos = $total ? round(($verde / $total) * 100) : 0;
      $pctActivos = $total ? round(($activos / $total) * 100) : 0;
      $pctDev = $total ? round(($devueltos / $total) * 100) : 0;

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
            <div class="ring" style="--p: {{ $pctPagos }}%; --ring: var(--primary);" data-t="{{ $pctPagos }}%"></div>
          </div>

          <div class="card kpi">
            <div class="meta">
              <div class="label">Activos</div>
              <div class="value">{{ $activos }}</div>
              <div class="hint">En curso</div>
            </div>
            <div class="ring" style="--p: {{ $pctActivos }}%; --ring: var(--success);" data-t="{{ $pctActivos }}%"></div>
          </div>

          <div class="card kpi">
            <div class="meta">
              <div class="label">Devueltos</div>
              <div class="value">{{ $devueltos }}</div>
              <div class="hint">Cerrados</div>
            </div>
            <div class="ring" style="--p: {{ $pctDev }}%; --ring: rgba(100,116,139,.8);" data-t="{{ $pctDev }}%"></div>
          </div>

          <div class="card kpi" id="kpiRecaudoMes">
            <div class="meta">
              <div class="label">Recaudo del mes</div>
              <div class="value">${{ number_format((float)($recaudadoMes ?? 0), 0) }}</div>
              <div class="hint">{{ now()->format('m/Y') }} (confirmado)</div>

              @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
                <div style="margin-top:8px;">
                  <a class="btn-sm"
                     href="{{ route('metricas.reporte.mensual', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}">
                    Ver detalle del mes
                  </a>
                </div>
              @elseif(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual'))
                <div style="margin-top:8px;">
                  <a class="btn-sm"
                     href="{{ route('metricas.reporte.anual', ['year' => request('year', now()->year)]) }}">
                    Ver detalle anual
                  </a>
                </div>
              @endif
            </div>

            <div class="ring" style="--p: {{ $pctRecaudoMes }}%; --ring: var(--primary);" data-t="%"></div>
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
                  <a class="btn-sm" href="{{ route('metricas.detalle.dia', ['date' => now()->toDateString()]) }}">
                    Ver detalle de hoy
                  </a>
                </div>
              @endif
            </div>

            <div class="ring" style="--p: {{ $pctRecaudoHoy }}%; --ring: var(--success);" data-t="$"></div>
          </div>

        </div>

        {{-- MINI REPORTES --}}
        <div style="margin:10px 0 0; display:flex; gap:8px; flex-wrap:wrap;">
          @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.anual'))
            <a class="btn-sm" href="{{ route('metricas.reporte.anual', ['year' => request('year', now()->year)]) }}">
              Reporte anual
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('metricas.reporte.mensual'))
            <a class="btn-sm" href="{{ route('metricas.reporte.mensual', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}">
              Reporte mensual
            </a>
          @endif

          @if(\Illuminate\Support\Facades\Route::has('metricas.detalle.dia'))
            <a class="btn-sm" href="{{ route('metricas.detalle.dia', ['date' => now()->toDateString()]) }}">
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
        {{-- ✅ OJO: class="card card-table" para que el dropdown no se recorte --}}
        <div class="card card-table" style="margin-top:12px;">
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
                  <th></th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>

              <tbody>
                @forelse($arriendos as $a)

                  @php
                    $itemsCount = $a->items_count ?? (isset($a->items) ? $a->items->count() : null);
                    $unidades = isset($a->items) ? (int)$a->items->sum('cantidad_actual') : null;

                    // Semaforización por fila
                    $saldo = (float)($a->saldo ?? 0);

                    $estaActivo  = (strtolower($a->estado ?? '') === 'activo') && ((int)($a->cerrado ?? 0) === 0);
                    $estaCerrado = ((int)($a->cerrado ?? 0) === 1) || (strtolower($a->estado ?? '') === 'devuelto');

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

                    // Bases para el modal
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
                    $itemsPendientes = isset($a->items)
                      ? (int)$a->items->filter(fn($it) => ((int)($it->cerrado ?? 0) === 0) && (($it->estado ?? 'activo') === 'activo') && ((int)($it->cantidad_actual ?? 0) > 0))->count()
                      : 0;
                    $unidadesPendientes = isset($a->items)
                      ? (int)$a->items->filter(fn($it) => ((int)($it->cerrado ?? 0) === 0) && (($it->estado ?? 'activo') === 'activo'))->sum('cantidad_actual')
                      : 0;

                    $puedeCerrar = (int)($a->cerrado ?? 0) === 0;
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
                          <button type="button" class="btn-kebab" aria-label="Acciones">⋮</button>

                          <div class="dropdown-menu">
                            <a class="menu-item item-return" href="{{ route('arriendos.ver', $a) }}">
                              <span class="menu-left"><span class="dot"></span>Ver / Gestionar</span>
                              <span class="menu-arrow">›</span>
                            </a>

                            <a class="menu-item item-edit" href="{{ route('arriendos.edit',$a) }}">
                              <span class="menu-left"><span class="dot"></span>Editar</span>
                              <span class="menu-arrow">›</span>
                            </a>

                            @if((int)($a->cerrado ?? 0) === 1 || $a->estado === 'devuelto')
                              <a class="menu-item item-details" href="{{ route('arriendos.detalles', $a) }}">
                                <span class="menu-left"><span class="dot"></span>Detalles</span>
                                <span class="menu-arrow">›</span>
                              </a>
                            @endif

                            @if($puedeCerrar)
                              <button
                                type="button"
                                class="menu-item item-close js-open-cerrar"
                                data-arriendo-id="{{ $a->id }}"
                                data-action="{{ route('arriendos.cerrar', $a) }}"
                                data-base-alquiler="{{ $baseAlquiler }}"
                                data-base-merma="{{ $baseMerma }}"
                                data-base-pagado="{{ $basePagado }}"
                                data-base-transporte="{{ $baseTransporte }}"
                                data-iva-rate="{{ $baseIvaRate }}"
                                data-iva-aplica="{{ (int)($a->iva_aplica ?? 0) }}"
                                data-items-pendientes="{{ $itemsPendientes }}"
                                data-unidades-pendientes="{{ $unidadesPendientes }}"
                                data-fecha-default="{{ now()->format('Y-m-d') }}"
                              >
                                <span class="menu-left"><span class="dot"></span>Cerrar</span>
                                <span class="menu-arrow">›</span>
                              </button>
                            @endif

                            <form action="{{ route('arriendos.destroy',$a) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button class="menu-item item-delete" onclick="return confirm('¿Eliminar arriendo?')">
                                <span class="menu-left"><span class="dot"></span>Borrar</span>
                                <span class="menu-arrow">›</span>
                              </button>
                            </form>
                          </div>

                        </div>
                      </div>
                    </td>
                  </tr>

                @empty
                  <tr>
                    <td colspan="11">No hay arriendos todavía.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div style="margin-top:12px;">
            {{ $arriendos->links() }}
          </div>
        </div>

        {{-- ✅ MODAL ÚNICO (FUERA DE LA TABLA) --}}
        <div id="modalCerrarGlobal" class="modal-backdrop">
          <div class="card modal-dialog">
            <div class="card-header modal-header">
              <h3 class="card-title" id="modalCerrarTitulo">Cerrar arriendo</h3>
              <button type="button" class="btn-ghost" id="btnCerrarModal">Cerrar</button>
            </div>

            <form
              id="cerrarFormGlobal"
              method="POST"
              action="#"
              class="js-cerrar-form"
              data-arriendo-id=""
              data-base-alquiler="0"
              data-base-merma="0"
              data-base-pagado="0"
              data-base-transporte="0"
              data-iva-rate="0.19"
            >
              @csrf

              <div class="modal-grid">
                <div class="modal-field">
                  <label class="small modal-label">Fecha devolución real</label>
                  <input class="input" type="date" name="fecha_devolucion_real" required value="{{ now()->format('Y-m-d') }}">
                  <div class="small modal-help js-cierre-auto-help" style="margin-top:6px;">
                    Los items pendientes se cerrarán automáticamente con esta fecha.
                  </div>
                </div>

                <div class="modal-field">
                  <label class="small modal-label">Pago recibido (opcional)</label>
                  <input class="input" type="number" min="0" step="0.01" name="pago" value="0">
                  <div style="margin-top:8px;">
                    <button type="button" class="btn-sm js-pagar-todo">Pagar saldo completo</button>
                  </div>
                </div>
              </div>

              <div class="modal-grid">
                <div class="modal-field">
                  <label class="small modal-label">Días de lluvia (se descuentan)</label>
                  <input class="input" type="number" min="0" name="dias_lluvia" value="0">
                </div>

                <div class="modal-field">
                  <label class="small modal-label">Costo daño/merma</label>
                  <input class="input" type="number" min="0" step="0.01" name="costo_merma" value="0">
                </div>
              </div>

              <div class="modal-grid">
                <div class="modal-field">
                  <label class="small modal-label">Factura con IVA</label>
                  <select class="input" name="iva_aplica" id="ivaAplicaSelect">
                    <option value="0">Sin IVA</option>
                    <option value="1">Con IVA (19%)</option>
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
                    <span class="sum-k">Items pendientes</span>
                    <span class="sum-v js-sum-items-pendientes">0</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Unidades por devolver</span>
                    <span class="sum-v js-sum-unidades-pendientes">0</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Alquiler generado</span>
                    <span class="sum-v js-sum-alquiler">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Merma total</span>
                    <span class="sum-v js-sum-merma">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Transportes</span>
                    <span class="sum-v js-sum-transporte">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">IVA</span>
                    <span class="sum-v js-sum-iva">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Total generado</span>
                    <span class="sum-v js-sum-total">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Total pagado</span>
                    <span class="sum-v js-sum-pagado">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Saldo final</span>
                    <span class="sum-v js-sum-saldo sum-v-ok">$0.00</span>
                  </div>
                  <div class="sum-box">
                    <span class="sum-k">Estado de cierre</span>
                    <span class="sum-v js-sum-estado sum-v-ok">Cierra sin deuda</span>
                  </div>
                </div>
              </div>

              <div class="modal-field">
                <label class="small modal-label">Descripción (opcional)</label>
                <input class="input" type="text" name="descripcion_incidencia" placeholder="Ej: lluvia fuerte / mango roto">
              </div>

              <div class="small modal-help">
                Domingos se descuentan automáticamente. Si queda saldo pendiente al cerrar, se activa semáforo (AMARILLO / ROJO según tu backend).
              </div>
              <div class="small modal-help" style="margin-top:6px;">
                Si hay herramientas pendientes, el sistema las devuelve y las liquida automáticamente con la fecha elegida al guardar el cierre.
              </div>

              <div class="modal-actions">
                <button type="button" class="btn-ghost" id="btnCancelarModal">Cancelar</button>
                <button type="submit" class="btn-primary" style="padding:8px 12px;">Cerrar y calcular</button>
              </div>
            </form>
          </div>
        </div>

        {{-- JS: filtros + dropdown (no recorta / se ajusta al viewport) + modal + recalculo --}}
        <script>
          (function () {
            // filtros
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

            // dropdown
            function closeAllDropdowns() {
              document.querySelectorAll('[data-dd].open').forEach(dd => {
                dd.classList.remove('open');
                const tr = dd.closest('tr');
                if (tr) tr.classList.remove('row-open');
                const menu = dd.querySelector('.dropdown-menu');
                if (menu){
                  menu.style.left = '';
                  menu.style.right = '0';
                  menu.style.top = 'calc(100% + 8px)';
                  menu.style.bottom = '';
                }
              });
            }

            function fitDropdownIntoViewport(dd){
              const menu = dd.querySelector('.dropdown-menu');
              if (!menu) return;

              // reset base
              menu.style.left = '';
              menu.style.right = '0';
              menu.style.top = 'calc(100% + 8px)';
              menu.style.bottom = '';

              const pad = 10;

              // medir luego del display:block
              const r = menu.getBoundingClientRect();

              // si se sale por derecha -> alinear a la izquierda
              if (r.right > window.innerWidth - pad) {
                menu.style.right = 'auto';
                menu.style.left = '0';
              }

              // si se sale por abajo -> abrir hacia arriba
              const r2 = menu.getBoundingClientRect();
              if (r2.bottom > window.innerHeight - pad) {
                menu.style.top = 'auto';
                menu.style.bottom = 'calc(100% + 8px)';
              }
            }

            document.addEventListener('click', function (e) {
              const btn = e.target.closest('.btn-kebab');
              const dd  = e.target.closest('[data-dd]');

              if (btn && dd) {
                e.preventDefault();
                e.stopPropagation();

                const wasOpen = dd.classList.contains('open');
                closeAllDropdowns();

                if (!wasOpen) {
                  dd.classList.add('open');
                  const tr = dd.closest('tr');
                  if (tr) tr.classList.add('row-open');
                  fitDropdownIntoViewport(dd); // ✅ ajuste
                }
                return;
              }

              if (e.target.closest('.dropdown-menu')) return;

              closeAllDropdowns();
            }, true);

            document.addEventListener('keydown', function (e) {
              if (e.key === 'Escape') closeAllDropdowns();
            });

            window.addEventListener('resize', function(){
              const dd = document.querySelector('[data-dd].open');
              if (dd) fitDropdownIntoViewport(dd);
            });

            // helpers
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

              const $itp = form.querySelector('.js-sum-items-pendientes');
              const $unp = form.querySelector('.js-sum-unidades-pendientes');
              const $alq = form.querySelector('.js-sum-alquiler');
              const $mer = form.querySelector('.js-sum-merma');
              const $trn = form.querySelector('.js-sum-transporte');
              const $iva = form.querySelector('.js-sum-iva');
              const $tot = form.querySelector('.js-sum-total');
              const $pag = form.querySelector('.js-sum-pagado');
              const $sal = form.querySelector('.js-sum-saldo');
              const $est = form.querySelector('.js-sum-estado');

              if ($itp) $itp.textContent = String(parseNum(form.dataset.itemsPendientes));
              if ($unp) $unp.textContent = String(parseNum(form.dataset.unidadesPendientes));
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

            // modal
            const modal = document.getElementById('modalCerrarGlobal');
            const cerrarForm = document.getElementById('cerrarFormGlobal');
            const titulo = document.getElementById('modalCerrarTitulo');

            function openModal(payload) {
              cerrarForm.action = payload.action;
              cerrarForm.dataset.arriendoId = String(payload.id || '');
              cerrarForm.dataset.baseAlquiler = String(payload.baseAlquiler || 0);
              cerrarForm.dataset.baseMerma = String(payload.baseMerma || 0);
              cerrarForm.dataset.basePagado = String(payload.basePagado || 0);
              cerrarForm.dataset.baseTransporte = String(payload.baseTransporte || 0);
              cerrarForm.dataset.ivaRate = String(payload.ivaRate || 0.19);
              cerrarForm.dataset.itemsPendientes = String(payload.itemsPendientes || 0);
              cerrarForm.dataset.unidadesPendientes = String(payload.unidadesPendientes || 0);

              const fecha = cerrarForm.querySelector('[name="fecha_devolucion_real"]');
              const ivaSel = cerrarForm.querySelector('[name="iva_aplica"]');
              const pago = cerrarForm.querySelector('[name="pago"]');
              const merma = cerrarForm.querySelector('[name="costo_merma"]');
              const lluvia = cerrarForm.querySelector('[name="dias_lluvia"]');
              const desc = cerrarForm.querySelector('[name="descripcion_incidencia"]');

              if (fecha) fecha.value = payload.fechaDefault || '';
              if (ivaSel) ivaSel.value = String(payload.ivaAplica || 0);
              if (pago) pago.value = '0';
              if (merma) merma.value = '0';
              if (lluvia) lluvia.value = '0';
              if (desc) desc.value = '';

              if (titulo) titulo.textContent = 'Cerrar arriendo #' + payload.id;

              recalcCerrarForm(cerrarForm);
              modal.style.display = 'flex';
            }

            function closeModal() { modal.style.display = 'none'; }

            document.getElementById('btnCerrarModal')?.addEventListener('click', closeModal);
            document.getElementById('btnCancelarModal')?.addEventListener('click', closeModal);

            modal.addEventListener('click', function (e) {
              if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', function (e) {
              if (e.key === 'Escape') closeModal();
            });

            document.querySelectorAll('.js-open-cerrar').forEach(btn => {
              btn.addEventListener('click', function () {
                closeAllDropdowns();
                openModal({
                  id: btn.dataset.arriendoId,
                  action: btn.dataset.action,
                  baseAlquiler: btn.dataset.baseAlquiler,
                  baseMerma: btn.dataset.baseMerma,
                  basePagado: btn.dataset.basePagado,
                  baseTransporte: btn.dataset.baseTransporte,
                  ivaRate: btn.dataset.ivaRate,
                  ivaAplica: btn.dataset.ivaAplica,
                  itemsPendientes: btn.dataset.itemsPendientes,
                  unidadesPendientes: btn.dataset.unidadesPendientes,
                  fechaDefault: btn.dataset.fechaDefault
                });
              });
            });

            const pagoInput = cerrarForm.querySelector('[name="pago"]');
            const mermaInput = cerrarForm.querySelector('[name="costo_merma"]');
            const ivaInput = cerrarForm.querySelector('[name="iva_aplica"]');
            const btnPagarTodo = cerrarForm.querySelector('.js-pagar-todo');

            [pagoInput, mermaInput, ivaInput].forEach(el => {
              if (!el) return;
              el.addEventListener('input', () => recalcCerrarForm(cerrarForm));
              el.addEventListener('change', () => recalcCerrarForm(cerrarForm));
            });

            if (btnPagarTodo) {
              btnPagarTodo.addEventListener('click', function () {
                const baseAlquiler = parseNum(cerrarForm.dataset.baseAlquiler);
                const baseMerma = parseNum(cerrarForm.dataset.baseMerma);
                const basePagado = parseNum(cerrarForm.dataset.basePagado);
                const baseTransporte = parseNum(cerrarForm.dataset.baseTransporte);
                const ivaRate = parseNum(cerrarForm.dataset.ivaRate || 0.19);

                const extraMerma = Math.max(0, parseNum(mermaInput?.value));
                const ivaAplica = String(ivaInput?.value || '0') === '1';
                const subtotal = baseAlquiler + (baseMerma + extraMerma) + baseTransporte;
                const ivaValor = ivaAplica ? (subtotal * ivaRate) : 0;
                const totalGenerado = subtotal + ivaValor;
                const saldoActual = Math.max(0, totalGenerado - basePagado);

                if (pagoInput) pagoInput.value = saldoActual.toFixed(2);
                recalcCerrarForm(cerrarForm);
              });
            }

            recalcCerrarForm(cerrarForm);
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
