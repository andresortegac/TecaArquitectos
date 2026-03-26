@extends('layouts.app')
@section('title', 'Reporte semanal')
@section('header', 'REPORTE SEMANAL')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');
  .w3d{--text:#0a1428;--muted:#57657e;--line:rgba(117,136,169,.28);--line-2:rgba(92,118,161,.40);--card:rgba(255,255,255,.88);--surface:linear-gradient(160deg, rgba(255,255,255,.95), rgba(237,245,255,.89));--brand:#1f67f3;--shadow-lg:0 26px 58px rgba(7,21,47,.20);--shadow-md:0 14px 30px rgba(7,21,47,.14);font-family:"Manrope","Space Grotesk","Segoe UI",sans-serif;color:var(--text);position:relative;isolation:isolate;}
  .w3d *{box-sizing:border-box}.w3d::before,.w3d::after{content:"";position:absolute;border-radius:999px;filter:blur(28px);z-index:-1}.w3d::before{width:340px;height:340px;top:-80px;left:-70px;background:radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0))}.w3d::after{width:260px;height:260px;top:-30px;right:-30px;background:radial-gradient(circle at 50% 50%, rgba(0,165,180,.22), rgba(0,165,180,0))}
  .w3d-shell{background:radial-gradient(900px 420px at 15% 0%, rgba(31,103,243,.14), transparent 58%),radial-gradient(760px 420px at 95% 0%, rgba(0,165,180,.10), transparent 58%),linear-gradient(180deg,#f7faff 0%,#edf3fb 100%);border:1px solid var(--line);border-radius:24px;padding:16px;box-shadow:var(--shadow-lg),inset 0 1px 0 rgba(255,255,255,.66)}
  .w3d-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;border:1px solid var(--line-2);border-radius:20px;background:var(--surface);padding:16px;box-shadow:var(--shadow-md);margin-bottom:12px}
  .w3d-title{margin:0;font-size:22px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}.w3d-sub{margin:6px 0 0;color:var(--muted);font-size:13px}
  .w3d-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 14px;border-radius:12px;border:1px solid var(--line-2);text-decoration:none;color:var(--text);font-size:13px;font-weight:800;background:linear-gradient(180deg,#fff,#eef5ff);box-shadow:0 10px 20px rgba(7,21,47,.12)}
  .w3d-btn:hover{transform:translateY(-2px)}
  .w3d-kpi{display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:10px;margin-bottom:12px}
  @media(max-width:900px){.w3d-kpi{grid-template-columns:1fr}}
  .w3d-box{border:1px solid var(--line);border-radius:16px;background:var(--card);padding:12px;box-shadow:var(--shadow-md)}
  .w3d-k{font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);font-weight:800}.w3d-v{margin-top:6px;font-size:24px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}
  .w3d-card{border:1px solid var(--line);border-radius:20px;background:var(--card);box-shadow:var(--shadow-md);overflow:hidden}
  .w3d-head{padding:14px 16px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#f1f8ff);font-size:12px;letter-spacing:.3px;text-transform:uppercase;color:var(--muted);font-weight:800}
  .w3d-body{padding:12px 14px}.w3d-table-wrap{overflow:auto;border:1px solid var(--line);border-radius:14px}.w3d-table{width:100%;min-width:760px;border-collapse:separate;border-spacing:0}
  .w3d-table thead th{position:sticky;top:0;z-index:1;background:linear-gradient(180deg,#fcfdff,#ecf4ff);font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);padding:11px 10px;border-bottom:1px solid var(--line);text-align:left}
  .w3d-table tbody td{padding:11px 10px;border-bottom:1px solid rgba(130,150,182,.20);font-size:13px}.right{text-align:right}
</style>

<div class="w3d">
  <div class="w3d-shell">
    <div class="w3d-top">
      <div>
        <h2 class="w3d-title">Semana {{ $week }} - {{ $year }}</h2>
        <p class="w3d-sub">Rango: {{ $rangoLabel }}. Resumen diario de recaudo confirmado y contratos.</p>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="w3d-btn" href="{{ route('metricas.reporte.mensual', ['year' => $year, 'month' => $month]) }}">Volver al mes</a>
        <a class="w3d-btn" href="{{ route('metricas.reporte.anual', ['year' => $year]) }}">Volver al anual</a>
      </div>
    </div>

    <div class="w3d-kpi">
      <div class="w3d-box"><div class="w3d-k">Total semanal</div><div class="w3d-v">${{ number_format((float)$totalSemanal, 0, ',', '.') }}</div></div>
      <div class="w3d-box"><div class="w3d-k">Días con recaudo</div><div class="w3d-v">{{ collect($dias)->where('recaudo', '>', 0)->count() }}</div></div>
      <div class="w3d-box"><div class="w3d-k">Arriendos creados</div><div class="w3d-v">{{ collect($dias)->sum('arriendos') }}</div></div>
    </div>

    <div class="w3d-card">
      <div class="w3d-head">Detalle por día</div>
      <div class="w3d-body">
        <div class="w3d-table-wrap">
          <table class="w3d-table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th class="right">Recaudo</th>
                <th class="right">Arriendos</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dias as $d)
                @php
                  $dt = \Carbon\Carbon::parse($d['dia']);
                  $isoYear = $dt->isoWeekYear;
                  $isoWeek = $dt->isoWeek;
                @endphp
                <tr>
                  <td>
                    <strong>{{ $dt->format('d/m/Y') }}</strong>
                    <div style="font-size:12px;color:#5d6c85;">{{ ucfirst($dt->translatedFormat('l')) }}</div>
                  </td>
                  <td class="right">${{ number_format((float)$d['recaudo'], 0, ',', '.') }}</td>
                  <td class="right">{{ (int)$d['arriendos'] }}</td>
                  <td style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a class="w3d-btn" href="{{ route('metricas.detalle.dia', ['date' => $d['dia']]) }}">Ver día</a>
                    <a class="w3d-btn" href="{{ route('metricas.reporte.semanal', ['year' => $isoYear, 'week' => $isoWeek]) }}">Ver semana</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" style="padding:14px;color:#5d6c85;">No hay registros para esta semana.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
