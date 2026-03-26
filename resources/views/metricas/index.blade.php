@extends('layouts.app')

@section('title', 'Métricas')
@section('header', 'REPORTES DE MÉTRICAS')

@section('content')
@php
  $currentMonth = (int)now()->month;
  $currentWeek = (int)now()->isoWeek;
  $today = now()->toDateString();

  $monthNames = [];
  for ($i = 1; $i <= 12; $i++) {
    $monthNames[$i] = ucfirst(\Carbon\Carbon::createFromDate((int)$year, $i, 1)->locale('es')->translatedFormat('F'));
  }
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');
  .mx3d{--text:#0a1428;--muted:#57657e;--line:rgba(117,136,169,.28);--line2:rgba(93,119,162,.42);--card:rgba(255,255,255,.88);--surface:linear-gradient(160deg, rgba(255,255,255,.95), rgba(236,244,255,.89));--brand:#1f67f3;--ok:#0f9b6e;--shadow-lg:0 26px 58px rgba(7,21,47,.20);--shadow-md:0 14px 30px rgba(7,21,47,.14);font-family:"Manrope","Space Grotesk","Segoe UI",sans-serif;color:var(--text);position:relative;isolation:isolate}
  .mx3d *{box-sizing:border-box}.mx3d::before,.mx3d::after{content:"";position:absolute;border-radius:999px;filter:blur(28px);z-index:-1}.mx3d::before{width:340px;height:340px;top:-80px;left:-70px;background:radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0))}.mx3d::after{width:260px;height:260px;top:-30px;right:-30px;background:radial-gradient(circle at 50% 50%, rgba(0,165,180,.22), rgba(0,165,180,0))}
  .mx3d-shell{background:radial-gradient(900px 420px at 15% 0%, rgba(31,103,243,.14), transparent 58%),radial-gradient(760px 420px at 95% 0%, rgba(0,165,180,.10), transparent 58%),linear-gradient(180deg,#f7faff 0%,#edf3fb 100%);border:1px solid var(--line);border-radius:24px;padding:16px;box-shadow:var(--shadow-lg), inset 0 1px 0 rgba(255,255,255,.66)}
  .mx3d-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;border:1px solid var(--line2);border-radius:20px;background:var(--surface);padding:16px;box-shadow:var(--shadow-md);margin-bottom:12px}
  .mx3d-title{margin:0;font-size:22px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}.mx3d-sub{margin:6px 0 0;color:var(--muted);font-size:13px}
  .mx3d-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 14px;border-radius:12px;border:1px solid var(--line2);text-decoration:none;color:var(--text);font-size:13px;font-weight:800;background:linear-gradient(180deg,#fff,#eef5ff);box-shadow:0 10px 20px rgba(7,21,47,.12);transition:transform .16s ease, box-shadow .2s ease}
  .mx3d-btn:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(7,21,47,.16)}
  .mx3d-grid{display:grid;grid-template-columns:repeat(4,minmax(180px,1fr));gap:10px;margin-bottom:12px}
  @media(max-width:1100px){.mx3d-grid{grid-template-columns:1fr 1fr}}
  @media(max-width:640px){.mx3d-grid{grid-template-columns:1fr}}
  .mx3d-card{border:1px solid var(--line);border-radius:16px;background:var(--card);padding:12px;box-shadow:var(--shadow-md)}
  .mx3d-k{font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);font-weight:800}.mx3d-v{margin-top:6px;font-size:24px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}
  .mx3d-nav{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:10px;margin-bottom:12px}
  @media(max-width:1100px){.mx3d-nav{grid-template-columns:1fr 1fr}}
  @media(max-width:640px){.mx3d-nav{grid-template-columns:1fr}}
  .mx3d-input,.mx3d-select{width:100%;min-height:42px;border-radius:12px;border:1px solid var(--line2);background:linear-gradient(180deg,#fff,#f4f9ff);padding:8px 12px}
  .mx3d-label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);font-weight:800;margin-bottom:6px}
  .mx3d-action{margin-top:8px;width:100%}
  .mx3d-chart{border:1px solid var(--line);border-radius:20px;background:var(--card);box-shadow:var(--shadow-md);padding:14px}
</style>

<div class="mx3d">
  <div class="mx3d-shell">
    <div class="mx3d-top">
      <div>
        <h2 class="mx3d-title">Métricas del Sistema {{ $year }}</h2>
        <p class="mx3d-sub">Vista consolidada de ventas, recaudo confirmado y comportamiento de arriendos por mes.</p>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="mx3d-btn" id="goAnual" href="{{ route('metricas.reporte.anual', ['year' => $year]) }}">Reporte anual</a>
        <a class="mx3d-btn" id="goMensual" href="{{ route('metricas.reporte.mensual', ['year' => $year, 'month' => $currentMonth]) }}">Reporte mensual</a>
        <a class="mx3d-btn" id="goSemanal" href="{{ route('metricas.reporte.semanal', ['year' => $year, 'week' => $currentWeek]) }}">Reporte semanal</a>
        <a class="mx3d-btn" href="{{ route('metricas.detalle.dia', ['date' => $today]) }}">Detalle de hoy</a>
      </div>
    </div>

    <div class="mx3d-grid">
      <div class="mx3d-card"><div class="mx3d-k">Total ventas</div><div class="mx3d-v">${{ number_format((float)$totalVentas, 0, ',', '.') }}</div></div>
      <div class="mx3d-card"><div class="mx3d-k">Total arriendos cobrados</div><div class="mx3d-v">${{ number_format((float)$totalArriendos, 0, ',', '.') }}</div></div>
      <div class="mx3d-card"><div class="mx3d-k">Recaudo confirmado</div><div class="mx3d-v">${{ number_format((float)$totalRecaudoConfirmado, 0, ',', '.') }}</div></div>
      <div class="mx3d-card"><div class="mx3d-k">Pagos confirmados</div><div class="mx3d-v">{{ (int)$pagosConfirmados }}</div></div>
    </div>

    <div class="mx3d-nav">
      <div class="mx3d-card">
        <label class="mx3d-label" for="yearPick">Año</label>
        <input id="yearPick" class="mx3d-input" type="number" min="2000" max="2100" value="{{ (int)$year }}">
        <a class="mx3d-btn mx3d-action" id="goAnualCard" href="{{ route('metricas.reporte.anual', ['year' => $year]) }}">Ver anual</a>
      </div>
      <div class="mx3d-card">
        <label class="mx3d-label" for="monthPick">Mes</label>
        <select id="monthPick" class="mx3d-select">
          @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" @selected($m === $currentMonth)>{{ $monthNames[$m] }}</option>
          @endfor
        </select>
        <a class="mx3d-btn mx3d-action" id="goMensualCard" href="{{ route('metricas.reporte.mensual', ['year' => $year, 'month' => $currentMonth]) }}">Ver mensual</a>
      </div>
      <div class="mx3d-card">
        <label class="mx3d-label" for="weekPick">Semana</label>
        <select id="weekPick" class="mx3d-select">
          @for($w = 1; $w <= 53; $w++)
            <option value="{{ $w }}" @selected($w === $currentWeek)>Semana {{ $w }}</option>
          @endfor
        </select>
        <a class="mx3d-btn mx3d-action" id="goSemanalCard" href="{{ route('metricas.reporte.semanal', ['year' => $year, 'week' => $currentWeek]) }}">Ver semanal</a>
      </div>
    </div>

    <div class="mx3d-chart">
      <canvas id="metricasChart" height="105"></canvas>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const yearInput = document.getElementById('yearPick');
  const monthPick = document.getElementById('monthPick');
  const weekPick = document.getElementById('weekPick');
  const goAnual = document.getElementById('goAnual');
  const goMensual = document.getElementById('goMensual');
  const goSemanal = document.getElementById('goSemanal');
  const goAnualCard = document.getElementById('goAnualCard');
  const goMensualCard = document.getElementById('goMensualCard');
  const goSemanalCard = document.getElementById('goSemanalCard');

  function syncLinks(){
    const y = Number(yearInput.value || new Date().getFullYear());
    const m = Number(monthPick.value || 1);
    const w = Number(weekPick.value || 1);

    const anualUrl = "{{ url('/metricas/reporte/anual') }}/" + y;
    const mensualUrl = "{{ url('/metricas/reporte/mensual') }}/" + y + "/" + m;
    const semanalUrl = "{{ url('/metricas/reporte/semanal') }}/" + y + "/" + w;

    goAnual.href = anualUrl;
    goMensual.href = mensualUrl;
    goSemanal.href = semanalUrl;

    if (goAnualCard) goAnualCard.href = anualUrl;
    if (goMensualCard) goMensualCard.href = mensualUrl;
    if (goSemanalCard) goSemanalCard.href = semanalUrl;
  }

  [yearInput, monthPick, weekPick].forEach(el => el && el.addEventListener('change', syncLinks));
  syncLinks();

  const labels = @json($monthLabels);
  const ventas = @json($ventasSeries);
  const recaudo = @json($recaudoSeries);
  const arriendos = @json($arriendosSeries);

  const ctx = document.getElementById('metricasChart');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Ventas',
          data: ventas,
          borderColor: '#1f67f3',
          backgroundColor: 'rgba(31,103,243,.32)',
          borderWidth: 2,
          borderRadius: 6,
        },
        {
          label: 'Recaudo confirmado',
          data: recaudo,
          borderColor: '#0f9b6e',
          backgroundColor: 'rgba(15,155,110,.30)',
          borderWidth: 2,
          borderRadius: 6,
        },
        {
          type: 'line',
          label: 'Arriendos (cantidad)',
          data: arriendos,
          borderColor: '#f08b25',
          backgroundColor: 'rgba(240,139,37,.15)',
          borderWidth: 3,
          tension: .32,
          pointRadius: 3,
          yAxisID: 'y1'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      interaction: { mode: 'index', intersect: false },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { callback: (v) => '$' + Number(v).toLocaleString('es-CO') }
        },
        y1: {
          beginAtZero: true,
          position: 'right',
          grid: { drawOnChartArea: false }
        }
      },
      plugins: {
        legend: { position: 'top' }
      }
    }
  });
})();
</script>
@endsection
