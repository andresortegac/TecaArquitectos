@extends('layouts.app')
@section('title','Devolución de producto')
@section('header','Devolución de producto')

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

@php
    $tarifaVista = (float)($item->tarifa_diaria ?? ($item->producto->costo ?? 0));

    $fechaInicioUI = $item->fecha_inicio_item?->toDateString()
        ?? ($item->arriendo->fecha_inicio?->toDateString() ?? date('Y-m-d'));

    $resumen = $resumen ?? [
        'total_devoluciones' => 0,
        'total_devuelto' => 0,
        'total_abonado' => 0,
        'total_cobrado' => 0,
    ];

    // ✅ Último saldo devolución guardado (para pintar en la tarjeta)
    $ultimoSaldoDevolucion = optional($item->devoluciones?->sortByDesc('id')->first())->saldo_devolucion;
@endphp

<div class="return-page-pro">
    <style>
        /* =====================================
           PRO UI - Scoped to .return-page-pro
           ===================================== */
        .return-page-pro{
            --surface2: rgba(255,255,255,.98);
            --border: rgba(15,23,42,.10);
            --text: #0f172a;
            --muted: #64748b;
            --primary: #1d4ed8;
            --primary2: #2563eb;
            --shadow: 0 16px 40px rgba(2,6,23,.12);
            --shadow2: 0 8px 18px rgba(2,6,23,.10);

            /* ✅ Colores vivos calendario */
            --cal-danger: #ef4444;
            --cal-info:   #3b82f6;
            --cal-success:#22c55e;

            padding: 14px;
            color: var(--text);
        }
        .return-page-pro *{ box-sizing: border-box; }

        .return-page-pro .rp-shell{
            background: linear-gradient(180deg, rgba(255,255,255,.62), rgba(255,255,255,.50));
            border: 1px solid rgba(255,255,255,.55);
            border-radius: 18px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            overflow:hidden;
        }

        .return-page-pro .rp-head{
            padding: 16px 16px 12px 16px;
            border-bottom: 1px solid rgba(15,23,42,.08);
            background: linear-gradient(180deg, rgba(255,255,255,.75), rgba(255,255,255,.45));
        }
        .return-page-pro .rp-head-top{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;
            flex-wrap:wrap;
        }
        .return-page-pro .rp-title{
            margin:0;
            font-size: 20px;
            letter-spacing: .2px;
            line-height: 1.2;
        }
        .return-page-pro .rp-subtitle{
            margin-top:6px;
            color: var(--muted);
            font-size: 12.5px;
        }
        .return-page-pro .rp-actions{
            display:flex;
            gap:10px;
            align-items:center;
            flex-wrap:wrap;
        }
        .return-page-pro .rp-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface2);
            color: var(--text);
            text-decoration:none;
            cursor:pointer;
            transition: transform .05s ease, box-shadow .2s ease, border-color .2s ease;
            box-shadow: 0 2px 10px rgba(2,6,23,.06);
            font-weight: 950;
            font-size: 13px;
            letter-spacing: .2px;
        }
        .return-page-pro .rp-btn:hover{
            border-color: rgba(37,99,235,.32);
            box-shadow: var(--shadow2);
        }
        .return-page-pro .rp-btn:active{ transform: translateY(1px); }
        .return-page-pro .rp-btn-primary{
            background: linear-gradient(180deg, var(--primary2), var(--primary));
            border-color: rgba(29,78,216,.45);
            color:#fff;
        }

        .return-page-pro .rp-body{
            padding: 14px 16px 16px 16px;
        }

        .return-page-pro .rp-row2{
            margin-top: 14px;
            display:grid;
            grid-template-columns: 1fr 360px;
            gap: 14px;
            align-items:start;
        }
        @media (max-width: 980px){
            .return-page-pro .rp-row2{ grid-template-columns: 1fr; }
        }
        .return-page-pro .rp-sticky{
            position: sticky;
            top: 14px;
        }

        .return-page-pro .rp-card{
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(2,6,23,.08);
            padding: 14px;
        }
        .return-page-pro .rp-card-title{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10px;
            margin:0 0 10px 0;
        }
        .return-page-pro .rp-card-title h3{
            margin:0;
            font-size: 14px;
            letter-spacing: .2px;
        }
        .return-page-pro .rp-chip{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(2,6,23,.03);
            border: 1px solid rgba(15,23,42,.10);
            font-size: 12px;
            color: var(--muted);
            font-weight: 900;
        }

        .return-page-pro .rp-kv-grid{
            display:grid;
            grid-template-columns: repeat(4,1fr);
            gap: 10px;
        }
        @media (max-width: 1100px){
            .return-page-pro .rp-kv-grid{ grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 640px){
            .return-page-pro .rp-kv-grid{ grid-template-columns: 1fr; }
        }

        .return-page-pro .rp-kv{
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid rgba(15,23,42,.08);
            background: rgba(2,6,23,.01);
            min-height: 72px;
        }
        .return-page-pro .rp-kv .k{ color: var(--muted); font-size: 12px; font-weight: 900; }
        .return-page-pro .rp-kv .v{ color: var(--text); font-size: 13.5px; font-weight: 1000; margin-top: 4px; }

        .return-page-pro .rp-form .row{
            display:flex;
            gap: 12px;
            flex-wrap:wrap;
            margin-bottom: 10px;
        }
        .return-page-pro .rp-form .field{
            flex:1;
            min-width: 240px;
        }
        .return-page-pro .rp-label{
            display:block;
            font-size: 12px;
            color: var(--muted);
            margin-bottom:6px;
            font-weight: 1000;
            letter-spacing: .2px;
        }
        .return-page-pro .rp-input{
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(15,23,42,.10);
            padding: 10px 12px;
            background: #fff;
            color: var(--text);
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
            font-weight: 750;
        }
        .return-page-pro .rp-input:focus{
            border-color: rgba(37,99,235,.45);
            box-shadow: 0 0 0 4px rgba(37,99,235,.12);
        }
        .return-page-pro .rp-help{
            margin-top:6px;
            color: var(--muted);
            font-size: 12px;
        }
        .return-page-pro .rp-divider{
            border: 0;
            border-top: 1px solid rgba(15,23,42,.08);
            margin: 12px 0;
        }

        .return-page-pro .rp-summary{
            background: linear-gradient(180deg, rgba(37,99,235,.06), rgba(2,6,23,.02));
            border: 1px solid rgba(37,99,235,.14);
            border-radius: 16px;
            padding: 12px;
            margin-top: 12px;
        }
        .return-page-pro .rp-summary-grid{
            display:grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        @media (max-width: 980px){
            .return-page-pro .rp-summary-grid{ grid-template-columns: 1fr 1fr; }
        }
        .return-page-pro .metric{
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,.85);
            border: 1px solid rgba(15,23,42,.08);
        }
        .return-page-pro .metric small{
            display:block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .22px;
            color: var(--muted);
            font-weight: 1000;
        }
        .return-page-pro .metric strong{
            display:block;
            margin-top: 5px;
            font-size: 16px;
            letter-spacing: .2px;
            font-weight: 1100;
            color: var(--text);
        }

        .return-page-pro .rp-note{
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid rgba(15,23,42,.10);
            background: rgba(2,6,23,.01);
            color: var(--muted);
            font-size: 12px;
            line-height: 1.35;
            margin-top: 10px;
        }
        .return-page-pro .rp-footer-actions{
            display:flex;
            justify-content:flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        /* Calendar */
        .return-page-pro .cal{
            background:#fff;
            border: 1px solid rgba(15,23,42,.10);
            border-radius: 16px;
            overflow:hidden;
        }
        .return-page-pro .cal-head{
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding: 12px 12px;
            background: linear-gradient(180deg, rgba(2,6,23,.03), rgba(2,6,23,.00));
            border-bottom: 1px solid rgba(15,23,42,.08);
        }
        .return-page-pro .cal-title{
            font-weight: 1100;
            letter-spacing: .2px;
            font-size: 13px;
            color: var(--text);
            text-transform: capitalize;
        }
        .return-page-pro .cal-nav{ display:flex; gap:8px; }
        .return-page-pro .cal-nav button{
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,.10);
            background:#fff;
            padding: 8px 10px;
            cursor:pointer;
            font-weight: 1100;
            color: var(--text);
        }
        .return-page-pro .cal-nav button:hover{ border-color: rgba(37,99,235,.30); }
        .return-page-pro .cal-grid{
            display:grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 7px;
            padding: 12px;
        }
        .return-page-pro .cal-dow{
            font-size: 11px;
            font-weight: 1100;
            color: var(--muted);
            text-align:center;
            padding: 6px 0;
            letter-spacing: .2px;
        }
        .return-page-pro .cal-cell{
            position: relative;
            height: 46px;
            border-radius: 14px;
            border: 1px solid rgba(15,23,42,.08);
            background: rgba(2,6,23,.01);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight: 1100;
            color: var(--text);
            user-select:none;
            transition: transform .06s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .return-page-pro .cal-cell:hover{
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(2,6,23,.10);
            border-color: rgba(37,99,235,.18);
        }
        .return-page-pro .cal-cell.is-out{ opacity:.35; background: transparent; }
        .return-page-pro .cal-cell.is-sunday{ border-style: dashed; opacity: .65; }

        .return-page-pro .cal-cell.is-charge{
            background: rgba(239,68,68,.18);
            border-color: rgba(239,68,68,.70);
            color: #7f1d1d;
            box-shadow: 0 6px 16px rgba(239,68,68,.18);
        }
        .return-page-pro .cal-cell.is-charge::after{
            content: "✕";
            position:absolute;
            top: 6px;
            right: 8px;
            font-size: 15px;
            font-weight: 1200;
            color: rgba(239,68,68,1);
        }
        .return-page-pro .cal-cell.is-start{
            outline: 3px solid rgba(59,130,246,.35);
            border-color: rgba(59,130,246,.85);
            background: rgba(59,130,246,.14);
            color: #0f172a;
            box-shadow: 0 6px 16px rgba(59,130,246,.16);
        }
        .return-page-pro .cal-cell.is-return{
            outline: 3px solid rgba(34,197,94,.30);
            border-color: rgba(34,197,94,.85);
            background: rgba(34,197,94,.16);
            color: #14532d;
            box-shadow: 0 6px 16px rgba(34,197,94,.16);
        }

        .return-page-pro .cal-legend{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            padding: 12px;
            border-top: 1px solid rgba(15,23,42,.08);
            background: rgba(2,6,23,.01);
        }
        .return-page-pro .pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 12px;
            border: 1px solid rgba(15,23,42,.10);
            background:#fff;
            color: var(--text);
            font-weight: 1000;
        }
        .return-page-pro .dot{
            width:10px; height:10px; border-radius:999px; display:inline-block;
            background: rgba(100,116,139,.55);
        }
        .return-page-pro .dot.red{ background: var(--cal-danger); }
        .return-page-pro .dot.green{ background: var(--cal-success); }
        .return-page-pro .dot.blue{ background: var(--cal-info); }

        /* HISTORY */
        .return-page-pro .rp-section{ margin-top: 14px; }
        .return-page-pro .rp-kpis{
            display:grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 12px;
        }
        @media (max-width: 980px){
            .return-page-pro .rp-kpis{ grid-template-columns: 1fr 1fr; }
        }
        .return-page-pro .kpi{
            padding: 12px;
            border-radius: 16px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 8px 18px rgba(2,6,23,.06);
        }
        .return-page-pro .kpi .k{
            font-size: 11px;
            color: var(--muted);
            font-weight: 1100;
            text-transform: uppercase;
            letter-spacing: .22px;
        }
        .return-page-pro .kpi .v{
            margin-top: 6px;
            font-size: 18px;
            font-weight: 1200;
            letter-spacing: .2px;
            color: var(--text);
        }
        .return-page-pro .table-wrap{
            border-radius: 16px;
            overflow:hidden;
            border: 1px solid rgba(15,23,42,.10);
            background: rgba(255,255,255,.92);
        }
        .return-page-pro table.rp-table{
            width:100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1120px;
        }
        .return-page-pro .rp-table thead th{
            position: sticky;
            top: 0;
            z-index: 2;
            background: linear-gradient(180deg, rgba(248,250,252,1), rgba(241,245,249,1));
            color: #334155;
            font-size: 11px;
            letter-spacing: .24px;
            text-transform: uppercase;
            font-weight: 1100;
            padding: 12px 12px;
            border-bottom: 1px solid rgba(15,23,42,.10);
            white-space: nowrap;
        }
        .return-page-pro .rp-table tbody td{
            padding: 12px 12px;
            border-bottom: 1px solid rgba(15,23,42,.06);
            font-size: 13px;
            color: var(--text);
            font-weight: 800;
            white-space: nowrap;
        }
        .return-page-pro .rp-table tbody tr:nth-child(odd){ background: rgba(2,6,23,.01); }
        .return-page-pro .rp-table tbody tr:hover{ background: rgba(37,99,235,.06); }

        .return-page-pro .num{ text-align:right; font-variant-numeric: tabular-nums; }
        .return-page-pro .center{ text-align:center; }
        .return-page-pro .money{ font-variant-numeric: tabular-nums; font-weight: 1100; color: rgba(29,78,216,1); }
        .return-page-pro .row-note td{
            background: rgba(2,6,23,.01);
            color: var(--muted);
            font-size: 12px !important;
            font-weight: 650 !important;
        }
    </style>

    <div class="rp-shell">
        <div class="rp-head">
            <div class="rp-head-top">
                <div>
                    <h2 class="rp-title">Devolución · Item #{{ $item->id }} <span style="color:var(--muted); font-weight:900;">(Arriendo #{{ $item->arriendo_id }})</span></h2>
                    <div class="rp-subtitle">Contexto arriba, acción abajo. Diseño limpio para operación diaria.</div>
                </div>
                <div class="rp-actions">
                    <a class="rp-btn" href="{{ route('arriendos.ver', $item->arriendo_id) }}">Volver</a>
                    <button type="button" class="rp-btn rp-btn-primary" onclick="document.querySelector('.return-page-pro form').scrollIntoView({behavior:'smooth'})">Registrar devolución</button>
                </div>
            </div>
        </div>

        <div class="rp-body">
            {{-- ✅ ROW 1: INFO FULL WIDTH --}}
            <div class="rp-card">
                <div class="rp-card-title">
                    <h3>Información del arriendo</h3>
                    <span class="rp-chip">Cobro automático</span>
                </div>

                <div class="rp-kv-grid">
                    <div class="rp-kv">
                        <div class="k">Cliente</div>
                        <div class="v">{{ $item->arriendo->cliente->nombre ?? '—' }}</div>
                    </div>
                    <div class="rp-kv">
                        <div class="k">Producto</div>
                        <div class="v">{{ $item->producto->nombre ?? '—' }}</div>
                    </div>
                    <div class="rp-kv">
                        <div class="k">Cantidad inicial</div>
                        <div class="v">{{ (int)$item->cantidad_inicial }}</div>
                    </div>
                    <div class="rp-kv">
                        <div class="k">Cantidad actual</div>
                        <div class="v">{{ (int)$item->cantidad_actual }}</div>
                    </div>
                    <div class="rp-kv">
                        <div class="k">Inicio item</div>
                        <div class="v">{{ $item->fecha_inicio_item?->format('d/m/Y H:i') ?? ($item->arriendo->fecha_inicio?->format('d/m/Y H:i') ?? '—') }}</div>
                    </div>
                    <div class="rp-kv">
                        <div class="k">Tarifa diaria</div>
                        <div class="v">${{ number_format($tarifaVista, 2) }}</div>
                    </div>
                    <div class="rp-kv">
                        <div class="k">Saldo del item</div>
                        <div class="v">${{ number_format((float)($item->saldo ?? 0), 2) }}</div>
                    </div>

                    {{-- ✅ AQUÍ va tu tarjeta de SALDO DEVOLUCIÓN (para que deje de salir "—") --}}
                    <div class="rp-kv">
                        <div class="k">Saldo devolución</div>
                        <div class="v money">
                            $<span id="ui_saldo_card">{{ number_format((float)($ultimoSaldoDevolucion ?? 0), 2) }}</span>
                        </div>
                    </div>

                    <div class="rp-kv">
                        <div class="k">Regla de cobro</div>
                        <div class="v" style="font-size:12.5px; font-weight:900; color:var(--muted);">
                            Se cobra desde inicio hasta el día anterior a devolución. Domingos no se cobran.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ ROW 2: FORM (left) + CALENDAR (right) --}}
            <div class="rp-row2">
                <form method="POST" action="{{ route('items.devolucion.store', $item) }}" class="rp-card rp-form">
                    @csrf

                    <div class="rp-card-title">
                        <h3>Registrar devolución</h3>
                        <span class="rp-chip">Campos controlados</span>
                    </div>

                    <div class="row">
                        <div class="field">
                            <label class="rp-label">Cantidad a devolver</label>
                            <input class="rp-input" type="number" min="1" max="{{ (int)$item->cantidad_actual }}"
                                   name="cantidad_devuelta" required value="{{ old('cantidad_devuelta') }}">
                            <div class="rp-help">Máximo permitido: <strong>{{ (int)$item->cantidad_actual }}</strong></div>
                        </div>

                        <div class="field">
                            <label class="rp-label">Fecha devolución</label>
                            <input class="rp-input" type="date" name="fecha_devolucion" required
                                   value="{{ old('fecha_devolucion', date('Y-m-d')) }}">
                            <div class="rp-help">No se cobra el día de devolución.</div>
                        </div>
                    </div>

                    <hr class="rp-divider">

                    <div class="row">
                        <div class="field">
                            <label class="rp-label">Días de lluvia (se descuentan)</label>
                            <input class="rp-input" type="number" min="0" name="dias_lluvia"
                                   value="{{ old('dias_lluvia', 0) }}">
                        </div>

                        <div class="field">
                            {{-- ✅ CAMBIO DE NOMBRE VISIBLE --}}
                            <label class="rp-label">Pérdida o daño de herramienta</label>
                            <input class="rp-input" type="number" min="0" step="0.01" name="costo_merma"
                                   value="{{ old('costo_merma', 0) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="field">
                            <label class="rp-label">Pago recibido / Abono (opcional)</label>
                            <input class="rp-input" type="number" min="0" step="0.01" name="pago"
                                   value="{{ old('pago', 0) }}">
                            <div class="rp-footer-actions" style="justify-content:flex-start; margin-top:10px;">
                                <button type="button" class="rp-btn" id="btn_pagar_completo">Pagar completo</button>
                            </div>
                        </div>

                        <div class="field">
                            <label class="rp-label">Método de pago</label>
                            <select class="rp-input" name="payment_method">
                                @php $pm = old('payment_method','efectivo'); @endphp
                                <option value="efectivo" {{ $pm==='efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="nequi" {{ $pm==='nequi' ? 'selected' : '' }}>Nequi</option>
                                <option value="daviplata" {{ $pm==='daviplata' ? 'selected' : '' }}>Daviplata</option>
                                <option value="transferencia" {{ $pm==='transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            <div class="rp-help">Si no eliges uno, se registra como <strong>Efectivo</strong>.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="field">
                            <label class="rp-label">Descripción incidencia (opcional)</label>
                            <input class="rp-input" type="text" name="descripcion_incidencia"
                                   value="{{ old('descripcion_incidencia') }}"
                                   placeholder="Ej: lluvia fuerte / mango roto">
                        </div>
                        {{-- ✅ ELIMINADO: Nota (opcional) --}}
                    </div>

                    <div class="rp-note">
                        Domingos se descuentan automáticamente. No se cobra el día de devolución. Si inicio y devolución son el mismo día, se cobra 1.
                        <br><strong>Nota:</strong> “Días de lluvia” es un número de descuento (no se puede pintar por fecha exacta).
                    </div>

                    <div class="rp-summary">
                        <div class="rp-summary-grid">
                            <div class="metric">
                                <small>Días cobrables</small>
                                <strong id="ui_dias_cobrables">0</strong>
                            </div>
                            <div class="metric">
                                <small>Tarifa diaria</small>
                                <strong>$<span id="ui_tarifa">{{ number_format($tarifaVista,2) }}</span></strong>
                            </div>
                            <div class="metric">
                                <small>Subtotal alquiler</small>
                                <strong>$<span id="ui_subtotal">0.00</span></strong>
                            </div>
                            <div class="metric">
                                <small>Total a pagar</small>
                                <strong>$<span id="ui_total">0.00</span></strong>
                            </div>
                        </div>

                        <div class="rp-summary-grid" style="grid-template-columns: 1fr 1fr; margin-top:10px;">
                            <div class="metric">
                                <small>Abono</small>
                                <strong>$<span id="ui_abono">0.00</span></strong>
                            </div>
                            <div class="metric">
                                <small>Saldo devolución</small>
                                <strong>$<span id="ui_saldo">0.00</span></strong>
                            </div>
                        </div>
                    </div>

                    <div class="rp-footer-actions">
                        <button type="submit" class="rp-btn rp-btn-primary" style="background:linear-gradient(180deg,#fbbf24,#f59e0b); border-color: rgba(245,158,11,.55); color:#111827;">
                            Guardar devolución
                        </button>
                    </div>
                </form>

                <div class="rp-sticky">
                    <div class="rp-card">
                        <div class="rp-card-title">
                            <h3>Calendario de días cobrados</h3>
                            <span class="rp-chip">Marcación automática</span>
                        </div>

                        <div class="cal" id="rp_calendar">
                            <div class="cal-head">
                                <div class="cal-title" id="rp_cal_title">—</div>
                                <div class="cal-nav">
                                    <button type="button" id="rp_cal_prev" aria-label="Mes anterior">‹</button>
                                    <button type="button" id="rp_cal_today" aria-label="Ir a hoy">Hoy</button>
                                    <button type="button" id="rp_cal_next" aria-label="Mes siguiente">›</button>
                                </div>
                            </div>

                            <div class="cal-grid" id="rp_cal_dow">
                                <div class="cal-dow">DO</div>
                                <div class="cal-dow">LU</div>
                                <div class="cal-dow">MA</div>
                                <div class="cal-dow">MI</div>
                                <div class="cal-dow">JU</div>
                                <div class="cal-dow">VI</div>
                                <div class="cal-dow">SA</div>
                            </div>

                            <div class="cal-grid" id="rp_cal_grid"></div>

                            <div class="cal-legend">
                                <span class="pill"><span class="dot red"></span> Cobrado (X)</span>
                                <span class="pill"><span class="dot blue"></span> Inicio</span>
                                <span class="pill"><span class="dot green"></span> Devolución</span>
                                <span class="pill"><span class="dot"></span> Domingo</span>
                            </div>
                        </div>

                        <div class="rp-help" style="margin-top:10px;">
                            Tip: haz clic en un día del calendario para ponerlo como <strong>fecha de devolución</strong>.
                        </div>
                    </div>
                </div>
            </div>

            {{-- HISTORIAL --}}
            <div class="rp-section">
                <div class="rp-card" style="margin-top:14px;">
                    <div class="rp-card-title">
                        <h3>Historial de devoluciones (este producto)</h3>
                        <span class="rp-chip">Consolidado</span>
                    </div>

                    <div class="rp-kpis">
                        <div class="kpi">
                            <div class="k">Devoluciones</div>
                            <div class="v">{{ $resumen['total_devoluciones'] }}</div>
                        </div>
                        <div class="kpi">
                            <div class="k">Total devuelto</div>
                            <div class="v">{{ $resumen['total_devuelto'] }}</div>
                        </div>
                        <div class="kpi">
                            <div class="k">Total cobrado</div>
                            <div class="v">${{ number_format((float)$resumen['total_cobrado'], 2) }}</div>
                        </div>
                        <div class="kpi">
                            <div class="k">Total abonado</div>
                            <div class="v">${{ number_format((float)$resumen['total_abonado'], 2) }}</div>
                        </div>
                    </div>

                    @if(!isset($item->devoluciones) || $item->devoluciones->isEmpty())
                        <div class="rp-note">No hay devoluciones registradas todavía para este producto.</div>
                    @else
                        <div class="table-wrap">
                            <div style="overflow:auto; max-height: 420px;">
                                <table class="rp-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th class="center">Devuelto</th>
                                            <th class="center">Días</th>
                                            <th class="center">Dom</th>
                                            <th class="center">Lluvia</th>
                                            <th class="center">Cobrables</th>
                                            <th class="num">Tarifa</th>
                                            <th class="num">Alquiler</th>
                                            <th class="num">Merma</th>
                                            <th class="num">Total</th>
                                            <th class="num">Abono</th>
                                            <th class="num">Saldo devolución</th>
                                            <th class="center">Quedan</th>
                                            <th class="num">Saldo item</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($item->devoluciones->sortByDesc('id') as $d)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($d->fecha_devolucion)->format('d/m/Y') }}</td>
                                            <td class="center">{{ (int)$d->cantidad_devuelta }}</td>
                                            <td class="center">{{ (int)$d->dias_transcurridos }}</td>
                                            <td class="center">{{ (int)$d->domingos_desc }}</td>
                                            <td class="center">{{ (int)$d->dias_lluvia_desc }}</td>
                                            <td class="center">{{ (int)$d->dias_cobrables }}</td>
                                            <td class="num">${{ number_format((float)$d->tarifa_diaria, 2) }}</td>
                                            <td class="num money">${{ number_format((float)$d->total_alquiler, 2) }}</td>
                                            <td class="num">${{ number_format((float)$d->total_merma, 2) }}</td>
                                            <td class="num money">${{ number_format((float)$d->total_cobrado, 2) }}</td>
                                            <td class="num">${{ number_format((float)$d->pago_recibido, 2) }}</td>
                                            <td class="num">
                                                @if(isset($d->saldo_devolucion))
                                                    ${{ number_format((float)$d->saldo_devolucion, 2) }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                            <td class="center">{{ (int)$d->cantidad_restante }}</td>
                                            <td class="num">${{ number_format((float)$d->saldo_resultante, 2) }}</td>
                                        </tr>

                                        @if(!empty($d->descripcion_incidencia))
                                            <tr class="row-note">
                                                <td colspan="14">
                                                    <strong>Incidencia:</strong> {{ $d->descripcion_incidencia }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SCRIPT (calc + calendar) --}}
            <script>
            (function () {
              const tarifa = JSON.parse('{!! json_encode($tarifaVista) !!}');
              const fechaInicio = JSON.parse('{!! json_encode($fechaInicioUI) !!}');

              const $root = document.querySelector('.return-page-pro');
              const $cant = $root.querySelector('[name="cantidad_devuelta"]');
              const $fec  = $root.querySelector('[name="fecha_devolucion"]');
              const $llu  = $root.querySelector('[name="dias_lluvia"]');
              const $mer  = $root.querySelector('[name="costo_merma"]');
              const $pago = $root.querySelector('[name="pago"]');
              const $btnFull = $root.querySelector('#btn_pagar_completo');

              const uiDias = $root.querySelector('#ui_dias_cobrables');
              const uiSub  = $root.querySelector('#ui_subtotal');
              const uiTot  = $root.querySelector('#ui_total');
              const uiAbo  = $root.querySelector('#ui_abono');
              const uiSal  = $root.querySelector('#ui_saldo');

              // ✅ Tarjeta superior (Saldo devolución)
              const uiSaldoCard = $root.querySelector('#ui_saldo_card');

              const calTitle = $root.querySelector('#rp_cal_title');
              const calGrid  = $root.querySelector('#rp_cal_grid');
              const btnPrev  = $root.querySelector('#rp_cal_prev');
              const btnNext  = $root.querySelector('#rp_cal_next');
              const btnToday = $root.querySelector('#rp_cal_today');

              function pad2(n){ return String(n).padStart(2,'0'); }
              function toYMD(d){ return d.getFullYear() + '-' + pad2(d.getMonth()+1) + '-' + pad2(d.getDate()); }
              function parseYMD(s){
                const [y,m,dd] = (s || '').split('-').map(Number);
                if (!y || !m || !dd) return null;
                const d = new Date(y, m-1, dd);
                return isNaN(d) ? null : d;
              }
              function parseNum(v){ v = (v ?? '').toString().trim(); return v === '' ? 0 : Number(v); }
              function money(n){ return (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2); }

              function isChargeDay(dateObj, inicioObj, devolObj){
                if (!dateObj || !inicioObj || !devolObj) return false;

                if (inicioObj.getTime() === devolObj.getTime()){
                  return (dateObj.getTime() === inicioObj.getTime()) && (dateObj.getDay() !== 0);
                }
                if (dateObj < inicioObj) return false;
                if (dateObj >= devolObj) return false;
                if (dateObj.getDay() === 0) return false;
                return true;
              }

              function calcDiasCobrables(inicio, devol){
                const d1 = parseYMD(inicio);
                const d2 = parseYMD(devol);
                if (!d1 || !d2) return 0;

                if (d1.getTime() === d2.getTime()){
                  return (d1.getDay() === 0) ? 0 : 1;
                }

                let dias = 0;
                let domingos = 0;

                const cur = new Date(d1);
                while (cur < d2){
                  dias++;
                  if (cur.getDay() === 0) domingos++;
                  cur.setDate(cur.getDate() + 1);
                }

                dias = Math.max(0, dias - domingos);
                const lluvia = Math.max(0, parseNum($llu?.value));
                dias = Math.max(0, dias - lluvia);
                return dias;
              }

              let viewDate = (function initView(){
                const d = parseYMD($fec?.value) || new Date();
                return new Date(d.getFullYear(), d.getMonth(), 1);
              })();

              const monthsEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

              function renderCalendar(){
                const inicioObj = parseYMD(fechaInicio);
                const devolObj  = parseYMD($fec?.value || toYMD(new Date()));
                if (!calGrid) return;

                calGrid.innerHTML = '';

                const y = viewDate.getFullYear();
                const m = viewDate.getMonth();
                calTitle.textContent = monthsEs[m] + ' de ' + y;

                const firstOfMonth = new Date(y, m, 1);
                const start = new Date(firstOfMonth);
                start.setDate(firstOfMonth.getDate() - firstOfMonth.getDay());

                for (let i = 0; i < 42; i++){
                  const d = new Date(start);
                  d.setDate(start.getDate() + i);

                  const cell = document.createElement('div');
                  cell.className = 'cal-cell';
                  cell.textContent = String(d.getDate());

                  const ymd = toYMD(d);
                  const isOut = d.getMonth() !== m;
                  if (isOut) cell.classList.add('is-out');
                  if (d.getDay() === 0) cell.classList.add('is-sunday');

                  if (inicioObj && ymd === toYMD(inicioObj)) cell.classList.add('is-start');
                  if (devolObj && ymd === toYMD(devolObj)) cell.classList.add('is-return');
                  if (isChargeDay(d, inicioObj, devolObj)) cell.classList.add('is-charge');

                  cell.style.cursor = 'pointer';
                  cell.title = 'Seleccionar como fecha de devolución: ' + ymd;
                  cell.addEventListener('click', function(){
                    if ($fec){
                      $fec.value = ymd;
                      viewDate = new Date(d.getFullYear(), d.getMonth(), 1);
                      recompute();
                    }
                  });

                  calGrid.appendChild(cell);
                }
              }

              function recompute(){
                const cantidad = Math.max(0, parseNum($cant?.value));
                const fdev = ($fec?.value || new Date().toISOString().slice(0,10));

                const diasCobrables = calcDiasCobrables(fechaInicio, fdev);
                const subtotal = diasCobrables * tarifa * cantidad;

                const merma = Math.max(0, parseNum($mer?.value));
                const total = subtotal + merma;

                const abono = Math.max(0, parseNum($pago?.value));
                const saldo = Math.max(0, total - abono);

                uiDias.textContent = diasCobrables;
                uiSub.textContent  = money(subtotal);
                uiTot.textContent  = money(total);
                uiAbo.textContent  = money(abono);
                uiSal.textContent  = money(saldo);

                // ✅ actualizar la tarjeta superior también
                if (uiSaldoCard) uiSaldoCard.textContent = money(saldo);

                uiTot.dataset.total = money(total);
                renderCalendar();
              }

              [$cant,$fec,$llu,$mer,$pago].forEach(el => el && el.addEventListener('input', recompute));
              $fec && $fec.addEventListener('change', function(){
                const d = parseYMD($fec.value);
                if (d) viewDate = new Date(d.getFullYear(), d.getMonth(), 1);
                recompute();
              });

              if ($btnFull) {
                $btnFull.addEventListener('click', function(){
                  const t = Number(uiTot.dataset.total || 0);
                  if ($pago) $pago.value = money(t);
                  recompute();
                });
              }

              btnPrev && btnPrev.addEventListener('click', function(){
                viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() - 1, 1);
                renderCalendar();
              });
              btnNext && btnNext.addEventListener('click', function(){
                viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 1);
                renderCalendar();
              });
              btnToday && btnToday.addEventListener('click', function(){
                const d = new Date();
                viewDate = new Date(d.getFullYear(), d.getMonth(), 1);
                renderCalendar();
              });

              recompute();
            })();
            </script>

        </div>
    </div>
</div>

@endsection
