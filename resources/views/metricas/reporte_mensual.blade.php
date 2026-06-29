@extends('layouts.app')

@section('title', 'Reporte mensual')
@section('header', 'REPORTE MENSUAL')

@section('content')
<style>
  /* ====== Corporativo (forzado en esta vista) ====== */
  .report-page{ padding:18px 0 28px; background:#f6f8fb; }
  .report-wrap{ max-width:1100px; margin:0 auto; }

  .surface{
    background:#fff;
    border:1px solid rgba(15,23,42,.08);
    border-radius:14px;
    box-shadow:0 10px 24px rgba(15,23,42,.06);
  }
  .surface-section{ padding:18px; }
  @media(min-width:768px){ .surface-section{ padding:22px 24px; } }
  .divider{ height:1px; background:rgba(15,23,42,.08); border:0; margin:0; opacity:1; }

  .eyebrow{ letter-spacing:.10em; font-weight:800; color:#64748b; text-transform:uppercase; font-size:.78rem; }
  .muted{ color:#64748b; }

  .kpi-label{ font-size:.76rem; letter-spacing:.10em; text-transform:uppercase; color:#64748b; font-weight:800; margin-bottom:6px; }
  .kpi-value{ font-size:2.15rem; font-weight:900; color:#0f172a; line-height:1.05; }

  .chip{
    display:inline-flex; align-items:center;
    padding:.25rem .55rem; border-radius:999px;
    border:1px solid rgba(15,23,42,.14);
    background:#fff; color:#0f172a; font-weight:800; font-size:.82rem;
  }

  .btn-corp{
    background:#0f172a; border-color:#0f172a; color:#fff;
    border-radius:10px; padding:.55rem 1rem; font-weight:800;
  }
  .btn-corp:hover{ background:#111c33; border-color:#111c33; color:#fff; }

  .btn-corp-outline{
    border:1px solid rgba(15,23,42,.22);
    border-radius:10px; padding:.55rem 1rem;
    font-weight:800; color:#0f172a; background:#fff;
  }
  .btn-corp-outline:hover{ background:#f8fafc; }

  .mini-card{
    border:1px solid rgba(15,23,42,.08);
    border-radius:12px;
    padding:14px;
    background:#fff;
    height:100%;
  }

  .table thead th{
    font-size:.76rem; letter-spacing:.10em; text-transform:uppercase;
    color:#64748b; background:#f8fafc !important;
    border-bottom:1px solid rgba(15,23,42,.10);
  }
  .table tbody td{ border-color:rgba(15,23,42,.08); }

  /* ====== FIX: tu template estaba rompiendo el flujo ====== */
  .report-page .btn,
  .report-page .form-select{
    position: static !important;
    float: none !important;
  }
  .report-page .form-select,
  .report-page .btn{ height:42px; }

  /* ====== HEADER: botón a la derecha sí o sí ====== */
  .header-grid{
    display:grid;
    grid-template-columns: 1fr auto;
    align-items:center;
    gap:14px;
  }
  @media (max-width: 767.98px){
    .header-grid{ grid-template-columns: 1fr; }
    .header-actions .btn{ width:100%; }
  }

  /* ====== NAV: ARREGLO DEFINITIVO (no se pisa texto con botón) ====== */
  .nav-form{
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    margin-top: 8px;
  }
  .nav-select, .nav-btn{
    height: 42px;
    border-radius: 10px;
  }
  .nav-action{ margin-top: 2px; }
  .nav-help{ margin-top: 2px; line-height: 1.35; }

  @media (min-width: 992px){
    .nav-form{
      grid-template-columns: 1fr 180px;
      align-items: end;
    }
    .nav-help{
      grid-column: 1 / -1;
      margin-top: 4px;
    }
  }
</style>

@php
  $monthLabel = \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->translatedFormat('F');

  $monthNames = [];
  for ($i=1; $i<=12; $i++) {
    $monthNames[$i] = \Carbon\Carbon::createFromDate((int)$year, $i, 1)->translatedFormat('F');
  }
@endphp

<div class="report-page">
  <div class="container">
    <div class="report-wrap">

      <div class="surface">

        {{-- HEADER --}}
        <div class="surface-section">
          <div class="header-grid">
            <div>
              <div class="eyebrow">Reportes / Métricas</div>
              <h2 class="mb-1 fw-bold text-dark">Resumen {{ ucfirst($monthLabel) }} {{ $year }}</h2>
              <div class="muted">Detalle diario de pagos confirmados</div>
            </div>

            <div class="header-actions">
              <a class="btn btn-corp-outline" href="{{ route('metricas.reporte.anual', $year) }}">
                ← Volver al anual
              </a>
            </div>
          </div>
        </div>

        <hr class="divider">

        {{-- KPI + NAVEGACIÓN --}}
        <div class="surface-section">
          <div class="row g-3">

            {{-- KPI --}}
            <div class="col-12 col-lg-6">
              <div class="mini-card">
                <div class="kpi-label">Total recaudado del mes</div>
                <div class="d-flex align-items-baseline gap-2">
                  <div class="kpi-value">${{ number_format((int)$totalMensual) }}</div>
                  <span class="chip">COP</span>
                </div>
                <div class="muted mt-1" style="font-size:.92rem;">Consolidado del mes seleccionado</div>
              </div>
            </div>

            {{-- NAVEGACIÓN (YA NO SE PISA) --}}
            <div class="col-12 col-lg-6">
              <div class="mini-card">
                <div class="kpi-label">Navegación</div>

                <div class="nav-form">
                  <div class="nav-field">
                    <label class="form-label muted mb-1" style="font-size:.88rem;">Mes</label>
                    <select id="monthPick" class="form-select nav-select">
                      @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" @selected((int)$month === $i)>
                          {{ ucfirst($monthNames[$i]) }}
                        </option>
                      @endfor
                    </select>
                  </div>

                  <div class="nav-action">
                    <a id="btnGoMonth"
                       class="btn btn-corp nav-btn"
                       href="{{ route('metricas.reporte.mensual', ['year'=>$year,'month'=>$month]) }}">
                      Ver reporte
                    </a>
                  </div>

                  <div class="muted nav-help" style="font-size:.90rem;">
                    Selecciona un mes para actualizar el consolidado y el detalle por día.
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>

        <hr class="divider">

        {{-- TABLA --}}
        <div class="surface-section">
          <div class="kpi-label mb-0">Detalle por día</div>
          <div class="muted mb-2" style="font-size:.92rem;">
            Valores en moneda local • Solo pagos confirmados
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th class="text-end">Recaudo</th>
                  <th class="text-end">Arriendos</th>
                  <th class="text-end">Acción</th>
                </tr>
              </thead>
              <tbody>
              @forelse($dias as $d)
                @php
                  $fecha = $d['dia']; // YYYY-MM-DD
                  $recaudo = (int)$d['recaudo'];
                  $arr = (int)$d['arriendos'];
                  $dt = \Carbon\Carbon::parse($fecha);
                @endphp
                <tr>
                  <td>
                    <div class="fw-semibold text-dark">{{ $dt->format('d/m/Y') }}</div>
                    <div class="muted" style="font-size:.86rem;">{{ ucfirst($dt->translatedFormat('l')) }}</div>
                  </td>
                  <td class="text-end fw-semibold text-dark">${{ number_format($recaudo) }}</td>
                  <td class="text-end"><span class="chip">{{ $arr }}</span></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-corp-outline" href="{{ route('metricas.detalle.dia', $fecha) }}">
                      Ver detalle
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center muted py-4">No hay registros para este mes.</td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>

          <div class="muted mt-3" style="font-size:.92rem;">
            Tip: entra a un día para ver pagos con hora exacta y métodos (payment_parts).
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<script>
(function(){
  const m = document.getElementById('monthPick');
  const b = document.getElementById('btnGoMonth');
  if(!m || !b) return;

  function sync(){
    const month = Number(m.value || 1);
    b.href = "{{ url('/metricas/reporte/mensual') }}/{{ (int)$year }}/" + month;
  }

  m.addEventListener('change', sync);
  sync();
})();
</script>
@endsection
