@extends('layouts.app')
@section('title', 'Reporte anual')
@section('header', 'REPORTE ANUAL')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');
  .y3d{--text:#0a1428;--muted:#57657e;--line:rgba(117,136,169,.28);--line2:rgba(93,119,162,.42);--card:rgba(255,255,255,.88);--surface:linear-gradient(160deg, rgba(255,255,255,.95), rgba(236,244,255,.89));--brand:#1f67f3;--shadow-lg:0 26px 58px rgba(7,21,47,.20);--shadow-md:0 14px 30px rgba(7,21,47,.14);font-family:"Manrope","Space Grotesk","Segoe UI",sans-serif;color:var(--text);position:relative;isolation:isolate}
  .y3d *{box-sizing:border-box}.y3d::before,.y3d::after{content:"";position:absolute;border-radius:999px;filter:blur(28px);z-index:-1}.y3d::before{width:340px;height:340px;top:-80px;left:-70px;background:radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0))}.y3d::after{width:260px;height:260px;top:-30px;right:-30px;background:radial-gradient(circle at 50% 50%, rgba(0,165,180,.22), rgba(0,165,180,0))}
  .y3d-shell{background:radial-gradient(900px 420px at 15% 0%, rgba(31,103,243,.14), transparent 58%),radial-gradient(760px 420px at 95% 0%, rgba(0,165,180,.10), transparent 58%),linear-gradient(180deg,#f7faff 0%,#edf3fb 100%);border:1px solid var(--line);border-radius:24px;padding:16px;box-shadow:var(--shadow-lg), inset 0 1px 0 rgba(255,255,255,.66)}
  .y3d-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;border:1px solid var(--line2);border-radius:20px;background:var(--surface);padding:16px;box-shadow:var(--shadow-md);margin-bottom:12px}
  .y3d-title{margin:0;font-size:22px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}.y3d-sub{margin:6px 0 0;color:var(--muted);font-size:13px}
  .y3d-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 14px;border-radius:12px;border:1px solid var(--line2);text-decoration:none;color:var(--text);font-size:13px;font-weight:800;background:linear-gradient(180deg,#fff,#eef5ff);box-shadow:0 10px 20px rgba(7,21,47,.12)}
  .y3d-btn:hover{transform:translateY(-2px)}
  .y3d-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:10px;margin-bottom:12px}
  @media(max-width:1100px){.y3d-grid{grid-template-columns:1fr 1fr}}
  @media(max-width:640px){.y3d-grid{grid-template-columns:1fr}}
  .y3d-card{border:1px solid var(--line);border-radius:16px;background:var(--card);padding:12px;box-shadow:var(--shadow-md)}
  .y3d-k{font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);font-weight:800}.y3d-v{margin-top:6px;font-size:24px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}
  .y3d-input{width:100%;min-height:42px;border-radius:12px;border:1px solid var(--line2);background:linear-gradient(180deg,#fff,#f4f9ff);padding:8px 12px}
  .y3d-table-wrap{overflow:auto;border:1px solid var(--line);border-radius:14px;background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(242,248,255,.94))}
  .y3d-table{width:100%;min-width:900px;border-collapse:separate;border-spacing:0}
  .y3d-table thead th{position:sticky;top:0;z-index:1;background:linear-gradient(180deg,#fcfdff,#ecf4ff);font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);padding:11px 10px;border-bottom:1px solid var(--line);text-align:left}
  .y3d-table tbody td{padding:11px 10px;border-bottom:1px solid rgba(130,150,182,.20);font-size:13px}.right{text-align:right}
</style>

@php
  $countMesesConIngreso = collect($meses)->where('recaudo', '>', 0)->count();
  $totalArriendosAnio = collect($meses)->sum('arriendos');
  $promedioMensual = ((float)$totalAnual) / 12;
@endphp

<div class="y3d">
  <div class="y3d-shell">
    <div class="y3d-top">
      <div>
        <h2 class="y3d-title">Resumen Anual {{ $year }}</h2>
        <p class="y3d-sub">Consolidado mensual de pagos confirmados y contratos iniciados.</p>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="y3d-btn" href="{{ route('metricas.index') }}">Volver a métricas</a>
      </div>
    </div>

    <div class="y3d-grid">
      <div class="y3d-card">
        <div class="y3d-k">Año de consulta</div>
        <div style="display:flex;gap:8px;align-items:center;margin-top:8px;">
          <input id="yearPick" type="number" class="y3d-input" min="2000" max="2100" value="{{ (int)$year }}">
          <button id="goYear" type="button" class="y3d-btn">Ir</button>
        </div>
      </div>
      <div class="y3d-card"><div class="y3d-k">Total recaudado</div><div class="y3d-v">${{ number_format((float)$totalAnual, 0, ',', '.') }}</div></div>
      <div class="y3d-card"><div class="y3d-k">Promedio mensual</div><div class="y3d-v">${{ number_format((float)$promedioMensual, 0, ',', '.') }}</div></div>
      <div class="y3d-card"><div class="y3d-k">Arriendos del año</div><div class="y3d-v">{{ (int)$totalArriendosAnio }}</div></div>
    </div>

    <div class="y3d-card" style="padding:14px;">
      <div style="margin-bottom:10px;font-size:12px;color:#5d6c85;font-weight:700;">Meses con ingreso: {{ $countMesesConIngreso }} / 12</div>
      <div class="y3d-table-wrap">
        <table class="y3d-table">
          <thead>
            <tr>
              <th>Mes</th>
              <th class="right">Recaudo</th>
              <th class="right">Arriendos</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($meses as $m)
              @php
                $monthNum = (int)($m['mes'] ?? 0);
                $monthName = ucfirst(\Carbon\Carbon::createFromDate((int)$year, $monthNum, 1)->locale('es')->translatedFormat('F'));
              @endphp
              <tr>
                <td>
                  <strong>{{ $monthName }}</strong>
                  <div style="font-size:12px;color:#5d6c85;">{{ $year }}</div>
                </td>
                <td class="right">${{ number_format((float)($m['recaudo'] ?? 0), 0, ',', '.') }}</td>
                <td class="right">{{ (int)($m['arriendos'] ?? 0) }}</td>
                <td style="display:flex;gap:8px;flex-wrap:wrap;">
                  <a class="y3d-btn" href="{{ route('metricas.reporte.mensual', ['year' => $year, 'month' => $monthNum]) }}">Ver mes</a>
                  <a class="y3d-btn" href="{{ route('metricas.reporte.semanal', ['year' => $year, 'week' => \Carbon\Carbon::createFromDate((int)$year, $monthNum, 1)->isoWeek]) }}">Semana inicial</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" style="padding:14px;color:#5d6c85;">No hay datos para este año.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const yearInput = document.getElementById('yearPick');
  const goBtn = document.getElementById('goYear');
  if (!yearInput || !goBtn) return;

  function goYear(){
    const y = Number(yearInput.value || new Date().getFullYear());
    if (!Number.isFinite(y) || y < 2000 || y > 2100) return;
    window.location.href = "{{ url('/metricas/reporte/anual') }}/" + y;
  }

  goBtn.addEventListener('click', goYear);
  yearInput.addEventListener('keydown', function(e){ if (e.key === 'Enter') goYear(); });
})();
</script>
@endsection
