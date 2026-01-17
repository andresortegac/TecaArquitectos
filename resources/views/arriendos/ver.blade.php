@extends('layouts.app')
@section('title','Ver arriendo')
@section('header','Ver arriendo')

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

<style>
/* ===========================
   PRO UI (solo esta vista)
   =========================== */
.pro-ui{
  --surface: rgba(255,255,255,.94);
  --card: #ffffff;
  --text: #0f172a;
  --muted: #64748b;
  --line: #e5e7eb;
  --soft: #f8fafc;
  --shadow: 0 18px 40px rgba(15,23,42,.10);
  --shadow2: 0 10px 22px rgba(15,23,42,.08);
  --r: 16px;
  --r2: 12px;

  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Helvetica Neue", sans-serif;
  color: var(--text);
}

.pro-container{
  max-width: 1280px;
  margin: 0 auto;
  padding: 18px 16px 28px 16px;
  background: var(--surface);
  border: 1px solid rgba(226,232,240,.95);
  border-radius: 22px;
  box-shadow: var(--shadow);
  backdrop-filter: blur(10px);
}

/* Header */
.pro-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  flex-wrap:wrap;
  margin-bottom:14px;
}
.pro-title{
  display:flex;
  flex-direction:column;
  gap:6px;
}
.pro-title h2{
  margin:0;
  font-size:22px;
  letter-spacing:.2px;
}
.pro-meta{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  align-items:center;
  font-size:13px;
  color: var(--muted);
}

/* Badges */
.badge{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 10px;
  border-radius:999px;
  border:1px solid var(--line);
  background: var(--soft);
  font-size:12px;
  white-space:nowrap;
}
.badge.ok{ background:#ecfdf5; border-color:#a7f3d0; color:#065f46; }
.badge.warn{ background:#fffbeb; border-color:#fde68a; color:#92400e; }
.badge.off{ background:#f1f5f9; color:#475569; }

/* Cards */
.card{
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--r);
  box-shadow: var(--shadow2);
  padding: 16px;
}
.card + .card{ margin-top: 14px; }
.card h3{
  margin:0 0 8px 0;
  font-size:15px;
}
.hint{
  font-size:12px;
  color: var(--muted);
}

/* ✅ COMPACTO SOLO EN TRANSPORTES */
.card.transport-card{
  padding: 12px;              /* antes 16px (baja más si quieres: 10px) */
}
.card.transport-card .hint{
  margin-top:2px;
}
.card.transport-card .form{
  margin-top:8px;             /* antes 10px */
  padding:10px;               /* antes 12px */
}
.card.transport-card .help{
  margin-top:4px;             /* antes 6px */
}
.card.transport-card .divider{
  margin:8px 0;               /* antes 10px */
}
.card.transport-card .form-grid{
  gap:8px;                    /* antes 10px */
}
.card.transport-card .label{
  margin-bottom:4px;          /* antes 6px */
}
.card.transport-card .input{
  height:34px;                /* antes 40px (baja más si quieres: 32px) */
  border-radius:10px;         /* antes 12px */
  padding:0 10px;
  font-size:13px;
}
.card.transport-card select.input{
  height:34px;
}
.card.transport-card .btn{
  padding:8px 10px;           /* antes 10px 12px */
  border-radius:10px;
  font-size:13px;
}
.card.transport-card .table th,
.card.transport-card .table td{
  padding:10px;               /* antes 12px */
}
.card.transport-card .table thead th{
  font-size:11px;             /* antes 12px */
}

/* Buttons */
.btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  padding:10px 12px;
  border-radius: 12px;
  border:1px solid var(--line);
  background:#fff;
  color: var(--text);
  text-decoration:none;
  cursor:pointer;
  font-size:13px;
  line-height:1;
  transition: .15s ease;
  box-shadow: 0 1px 0 rgba(15,23,42,.04);
}
.btn:hover{ transform: translateY(-1px); box-shadow: var(--shadow2); }
.btn:active{ transform: translateY(0); }
.btn.primary{ background:#eef2ff; border-color:#c7d2fe; }
.btn.warning{ background:#fff7ed; border-color:#fed7aa; }
.btn.danger{ background:#fff1f2; border-color:#fecaca; color:#b91c1c; }
.btn.danger:hover{ background:#fee2e2; }

/* Layout split */
.split{
  display:grid;
  grid-template-columns: 1.2fr 1fr;
  gap:12px;
}
@media(max-width:980px){ .split{ grid-template-columns:1fr; } }

.kv{
  display:grid;
  grid-template-columns: 160px 1fr;
  gap:10px;
  padding:10px 0;
  border-bottom:1px dashed var(--line);
}
.kv:last-child{ border-bottom:none; padding-bottom:0; }
.kv b{ color: var(--muted); font-weight:800; font-size:12px; }
.kv span{ font-size:13px; }

/* KPIs - no se cortan */
.kpis{
  display:grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap:10px;
}
@media(max-width:1100px){ .kpis{ grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media(max-width:640px){ .kpis{ grid-template-columns: 1fr; } }

.kpi{
  border:1px solid var(--line);
  border-radius: var(--r2);
  padding:12px;
  background: #fff;
}
.kpi .label{ font-size:12px; color: var(--muted); }
.kpi .value{ margin-top:6px; font-weight:900; letter-spacing:.2px; }

/* Rows */
.row{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  flex-wrap:wrap;
}
.divider{ height:1px; background:var(--line); margin:10px 0; }

/* Form */
.form{
  margin-top:10px;
  padding:12px;
  border:1px solid var(--line);
  border-radius: var(--r2);
  background: var(--soft);
}
.form-grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:10px;
}
@media(max-width:900px){ .form-grid{ grid-template-columns:1fr; } }

.label{
  display:block;
  font-size:12px;
  color:var(--muted);
  font-weight:800;
  margin-bottom:6px;
}
.help{ margin-top:6px; font-size:12px; color:var(--muted); }

/* Tus inputs existentes (clase .input) */
.pro-ui .input{
  width:100%;
  height:40px;
  border-radius: 12px;
  border:1px solid var(--line);
  padding:0 12px;
  outline:none;
  background:#fff;
}
.pro-ui select.input{ height:40px; }
.pro-ui .input:focus{
  border-color:#93c5fd;
  box-shadow: 0 0 0 4px rgba(59,130,246,.12);
}

/* Tables */
.table-wrap{
  overflow:auto;
  border:1px solid var(--line);
  border-radius: 14px;
  background:#fff;
}
.table{
  width:100%;
  border-collapse:separate;
  border-spacing:0;
}
.table th, .table td{
  padding:12px;
  border-bottom:1px solid var(--line);
  font-size:13px;
  vertical-align:middle;
}
.table thead th{
  position:sticky;
  top:0;
  background:#f8fafc;
  z-index:1;
  text-transform:uppercase;
  letter-spacing:.35px;
  font-size:12px;
  color:#334155;
}
.table tbody tr:hover{ background:#fbfdff; }
.right{ text-align:right; white-space:nowrap; }
.center{ text-align:center; white-space:nowrap; }
.product{ min-width: 240px; }
.actions{ min-width: 220px; }

.actions-box{
  display:flex;
  justify-content:flex-end;
  gap:8px;
  flex-wrap:wrap;
}
</style>

<div class="pro-ui">
  <div class="pro-container">

    {{-- HEADER --}}
    <div class="pro-head">
      <div class="pro-title">
        <h2>
          Arriendo #{{ $arriendo->id }}
          <span class="badge off">PADRE / Contrato</span>
        </h2>

        <div class="pro-meta">
          <span><b>Cliente:</b> {{ $arriendo->cliente->nombre ?? '—' }}</span>
          <span>•</span>
          <span>
            <b>Estado:</b>
            @if(strtolower($arriendo->estado ?? '') === 'activo')
              <span class="badge ok">Activo</span>
            @else
              <span class="badge off">{{ ucfirst($arriendo->estado) }}</span>
            @endif
          </span>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="{{ route('arriendos.index') }}">← Volver</a>
        @if((int)($arriendo->cerrado ?? 0) === 0 && $arriendo->estado === 'activo')
          <a class="btn primary" href="{{ route('arriendos.items.create', $arriendo) }}">+ Agregar producto</a>
        @endif
      </div>
    </div>

    {{-- INFO + KPIS --}}
    <div class="card">
      <div class="split">
        <div>
          <h3>Información del contrato</h3>

          <div class="kv">
            <b>Cliente</b>
            <span>{{ $arriendo->cliente->nombre ?? '—' }}</span>
          </div>

          <div class="kv">
            <b>Obra</b>
            <span>{{ $arriendo->obra? $arriendo->obra->direccion . ' - ' . $arriendo->obra->detalle : '—'}}</span>
          </div>

          <div class="kv">
            <b>Inicio contrato</b>
            <span>{{ $arriendo->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}</span>
          </div>

          <div class="kv">
            <b>Estado</b>
            <span>{{ ucfirst($arriendo->estado) }}</span>
          </div>
        </div>

        <div>
          <h3>Resumen financiero</h3>
          <div class="hint">Totales del contrato e histórico del cliente.</div>
          <div class="divider"></div>

          <div class="kpis">
            <div class="kpi">
              <div class="label">Total contrato</div>
              <div class="value">${{ number_format((float)$totContrato['precio_total'], 2) }}</div>
            </div>
            <div class="kpi">
              <div class="label">Pagado contrato</div>
              <div class="value">${{ number_format((float)$totContrato['total_pagado'], 2) }}</div>
            </div>
            <div class="kpi">
              <div class="label">Saldo contrato</div>
              <div class="value">${{ number_format((float)$totContrato['saldo'], 2) }}</div>
            </div>

            <div class="kpi">
              <div class="label">Total histórico cliente</div>
              <div class="value">${{ number_format((float)$totalHistorico['precio_total'], 2) }}</div>
            </div>
            <div class="kpi">
              <div class="label">Pagado histórico</div>
              <div class="value">${{ number_format((float)$totalHistorico['total_pagado'], 2) }}</div>
            </div>
            <div class="kpi">
              <div class="label">Saldo histórico</div>
              <div class="value">${{ number_format((float)$totalHistorico['saldo'], 2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- TRANSPORTES --}}
    @php
      $transportes = $arriendo->transportes ?? collect();
      $totalTransportes = (float) $transportes->sum('valor');
    @endphp

    {{-- ✅ Solo agregué class="transport-card" para compactar este bloque --}}
    <div class="card transport-card">
      <div class="row">
        <div>
          <h3 style="margin:0;">Transportes / Entregas</h3>
          <div class="hint">Registra entregas o recogidas. Ambas suman al total.</div>
        </div>
        <div>
          <span class="badge warn">Total transportes: <b>${{ number_format($totalTransportes, 2) }}</b></span>
        </div>
      </div>

      @if((int)($arriendo->cerrado ?? 0) === 0 && $arriendo->estado === 'activo')
        <form class="form" method="POST" action="{{ route('arriendos.transportes.store', $arriendo) }}">
          @csrf

          <div style="margin-bottom:8px;">
            <label class="label">Tipo</label>
            <select class="input" name="tipo" required>
              <option value="ENTREGA" {{ old('tipo', 'ENTREGA') === 'ENTREGA' ? 'selected' : '' }}>ENTREGA</option>
              <option value="RECOGIDA" {{ old('tipo') === 'RECOGIDA' ? 'selected' : '' }}>RECOGIDA</option>
            </select>
            <div class="help">Selecciona si es entrega o recogida.</div>
          </div>

          <div class="form-grid">
            <div>
              <label class="label">Fecha</label>
              <input class="input" type="date" name="fecha"
                     value="{{ old('fecha', now()->toDateString()) }}" required>
            </div>

            <div>
              <label class="label">Valor transporte</label>
              <input class="input" type="number" min="0" step="0.01" name="valor"
                     value="{{ old('valor', 0) }}" required>
              <div class="help">Ej: 15000 / 25000 / etc.</div>
            </div>
          </div>

          <div style="margin-top:8px;">
            <label class="label">Nota (opcional)</label>
            <input class="input" type="text" name="nota"
                   value="{{ old('nota') }}" placeholder="Ej: Entrega 1 / retiro / domicilio...">
          </div>

          <div style="display:flex; justify-content:flex-end; margin-top:10px;">
            <button type="submit" class="btn warning">+ Agregar transporte</button>
          </div>
        </form>
      @else
        <div class="hint" style="margin-top:10px;">
          Este arriendo no está activo o ya está cerrado. No se pueden agregar transportes.
        </div>
      @endif

      <div style="margin-top:10px;">
        @if($transportes->isEmpty())
          <div class="hint">No hay transportes registrados aún.</div>
        @else
          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Nota</th>
                  <th class="right">Valor</th>
                  <th class="right" style="width:160px;">Acción</th>
                </tr>
              </thead>
              <tbody>
                @foreach($transportes->sortByDesc('id') as $t)
                  <tr>
                    <td>{{ !empty($t->fecha) ? \Carbon\Carbon::parse($t->fecha)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $t->nota ?? '—' }}</td>
                    <td class="right"><b>${{ number_format((float)$t->valor, 2) }}</b></td>
                    <td class="right">
                      @if((int)($arriendo->cerrado ?? 0) === 0 && $arriendo->estado === 'activo')
                        <form method="POST" action="{{ route('arriendos.transportes.destroy', $t) }}" style="display:inline;">
                          @csrf
                          @method('DELETE')
                          <button class="btn danger"
                                  onclick="return confirm('¿Seguro que deseas borrar este transporte?')">
                            Borrar
                          </button>
                        </form>
                      @else
                        <span class="badge off">—</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

    {{-- ITEMS --}}
    <div class="card">
      <div class="row">
        <div>
          <h3 style="margin:0;">Productos alquilados (Items)</h3>
          <div class="hint">Listado de productos, cantidades, días, totales y acciones.</div>
        </div>
      </div>

      @if($arriendo->items->isEmpty())
        <div class="hint">No hay productos aún. Agrega el primero.</div>
      @else
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th class="product">Producto</th>
                <th class="center">Inicial</th>
                <th class="center">Actual</th>
                <th>Inicio item</th>
                <th class="right">Tarifa/día</th>
                <th class="right">Valor día</th>
                <th class="center">Días alquilados</th>
                <th class="center">Días cobrables</th>
                <th class="right">Total</th>
                <th class="right">Pagado</th>
                <th class="right">Saldo</th>
                <th class="center">Estado</th>
                <th class="right actions">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @foreach($arriendo->items as $it)
                @php
                  $tarifa = (float)($it->tarifa_diaria ?? ($it->producto->costo ?? 0));
                  $valorDia = $tarifa * (int)($it->cantidad_actual ?? 0);
                  $devs = $it->devoluciones ?? collect();

                  if ($devs->count() > 0) {
                    $diasAlquilados = (int)$devs->sum('dias_transcurridos');
                    $diasCobrables  = (int)$devs->sum('dias_cobrables');
                  } else {
                    $inicio = \Carbon\Carbon::parse($it->fecha_inicio_item ?? $arriendo->fecha_inicio)->startOfDay();
                    $hoy    = \Carbon\Carbon::today()->startOfDay();

                    if ($inicio->gt($hoy)) {
                      $diasAlquilados = 0;
                      $diasCobrables  = 0;
                    } else {
                      $diasTrans = $inicio->diffInDays($hoy);
                      if ($diasTrans === 0) $diasTrans = 1;

                      $domingos = 0;
                      for ($d = $inicio->copy(); $d->lt($hoy); $d->addDay()) {
                        if ($d->isSunday()) $domingos++;
                      }

                      $diasAlquilados = $diasTrans;
                      $diasCobrables  = max(0, $diasTrans - $domingos);
                    }
                  }

                  $itEstado = strtolower($it->estado ?? '');
                @endphp

                <tr>
                  <td class="product"><b>{{ $it->producto->nombre ?? '—' }}</b></td>
                  <td class="center">{{ (int)$it->cantidad_inicial }}</td>
                  <td class="center">{{ (int)$it->cantidad_actual }}</td>
                  <td>{{ $it->fecha_inicio_item?->format('d/m/Y H:i') ?? '—' }}</td>

                  <td class="right">${{ number_format($tarifa, 2) }}</td>
                  <td class="right">${{ number_format($valorDia, 2) }}</td>

                  <td class="center">{{ $diasAlquilados }}</td>
                  <td class="center">{{ $diasCobrables }}</td>

                  <td class="right">${{ number_format((float)($it->precio_total ?? 0), 2) }}</td>
                  <td class="right">${{ number_format((float)($it->total_pagado ?? 0), 2) }}</td>
                  <td class="right">${{ number_format((float)($it->saldo ?? 0), 2) }}</td>

                  <td class="center">
                    @if($itEstado === 'activo')
                      <span class="badge ok">Activo</span>
                    @else
                      <span class="badge off">{{ ucfirst($it->estado) }}</span>
                    @endif
                  </td>

                  <td class="right actions">
                    <div class="actions-box">
                      @if((int)($it->cerrado ?? 0) === 0 && $it->estado === 'activo')
                        <a class="btn warning" href="{{ route('items.devolucion.create', $it) }}">Devolución</a>
                      @else
                        <span class="badge off">Cerrado</span>
                      @endif

                      <a class="btn" href="{{ route('arriendos.detalles', $arriendo) }}">Detalles</a>

                      <form action="{{ route('arriendos.items.destroy', $it) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger"
                                onclick="return confirm('¿Seguro que deseas borrar este alquiler (item)?')">
                          Borrar
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>

          </table>
        </div>
      @endif
    </div>

  </div>
</div>

@endsection
