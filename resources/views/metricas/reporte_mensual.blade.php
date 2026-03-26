@extends('layouts.app')
@section('title', 'Reporte mensual')
@section('header', 'REPORTE MENSUAL')

@section('content')
@php
  $monthLabel = \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->locale('es')->translatedFormat('F');
  $monthNames = [];
  for ($i=1; $i<=12; $i++) {
    $monthNames[$i] = \Carbon\Carbon::createFromDate((int)$year, $i, 1)->locale('es')->translatedFormat('F');
  }
  $weekNow = \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->isoWeek;
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');
  .m3d{--text:#0a1428;--muted:#57657e;--line:rgba(117,136,169,.28);--line2:rgba(93,119,162,.42);--card:rgba(255,255,255,.88);--surface:linear-gradient(160deg, rgba(255,255,255,.95), rgba(236,244,255,.89));--brand:#1f67f3;--shadow-lg:0 26px 58px rgba(7,21,47,.20);--shadow-md:0 14px 30px rgba(7,21,47,.14);font-family:"Manrope","Space Grotesk","Segoe UI",sans-serif;color:var(--text);position:relative;isolation:isolate}
  .m3d *{box-sizing:border-box}.m3d::before,.m3d::after{content:"";position:absolute;border-radius:999px;filter:blur(28px);z-index:-1}.m3d::before{width:340px;height:340px;top:-80px;left:-70px;background:radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0))}.m3d::after{width:260px;height:260px;top:-30px;right:-30px;background:radial-gradient(circle at 50% 50%, rgba(0,165,180,.22), rgba(0,165,180,0))}
  .m3d-shell{background:radial-gradient(900px 420px at 15% 0%, rgba(31,103,243,.14), transparent 58%),radial-gradient(760px 420px at 95% 0%, rgba(0,165,180,.10), transparent 58%),linear-gradient(180deg,#f7faff 0%,#edf3fb 100%);border:1px solid var(--line);border-radius:24px;padding:16px;box-shadow:var(--shadow-lg), inset 0 1px 0 rgba(255,255,255,.66)}
  .m3d-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;border:1px solid var(--line2);border-radius:20px;background:var(--surface);padding:16px;box-shadow:var(--shadow-md);margin-bottom:12px}
  .m3d-title{margin:0;font-size:22px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}.m3d-sub{margin:6px 0 0;color:var(--muted);font-size:13px}
  .m3d-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:10px 14px;border-radius:12px;border:1px solid var(--line2);text-decoration:none;color:var(--text);font-size:13px;font-weight:800;background:linear-gradient(180deg,#fff,#eef5ff);box-shadow:0 10px 20px rgba(7,21,47,.12)}
  .m3d-btn:hover{transform:translateY(-2px)}
  .m3d-nav{display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:12px}
  @media(max-width:980px){.m3d-nav{grid-template-columns:1fr}}
  .m3d-card{border:1px solid var(--line);border-radius:16px;background:var(--card);padding:12px;box-shadow:var(--shadow-md)}
  .m3d-label{font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);font-weight:800}.m3d-val{margin-top:6px;font-size:24px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif}
  .m3d-select{width:100%;min-height:42px;border-radius:12px;border:1px solid var(--line2);background:linear-gradient(180deg,#fff,#f4f9ff);padding:8px 12px}
  .m3d-table-wrap{overflow:auto;border:1px solid var(--line);border-radius:14px;background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(242,248,255,.94))}
  .m3d-table{width:100%;min-width:820px;border-collapse:separate;border-spacing:0}
  .m3d-table thead th{position:sticky;top:0;z-index:1;background:linear-gradient(180deg,#fcfdff,#ecf4ff);font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);padding:11px 10px;border-bottom:1px solid var(--line);text-align:left}
  .m3d-table tbody td{padding:11px 10px;border-bottom:1px solid rgba(130,150,182,.20);font-size:13px}.right{text-align:right}
</style>

<div class="m3d">
  <div class="m3d-shell">
    <div class="m3d-top">
      <div>
        <h2 class="m3d-title">Resumen {{ ucfirst($monthLabel) }} {{ $year }}</h2>
        <p class="m3d-sub">Detalle diario con navegación a día y semana, sobre pagos confirmados.</p>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="m3d-btn" href="{{ route('metricas.reporte.anual', ['year' => $year]) }}">Volver al anual</a>
      </div>
    </div>

    <div class="m3d-nav">
      <div class="m3d-card">
        <div class="m3d-label">Total mensual</div>
        <div class="m3d-val">${{ number_format((float)$totalMensual, 0, ',', '.') }}</div>
      </div>
      <div class="m3d-card">
        <div class="m3d-label">Mes</div>
        <select id="monthPick" class="m3d-select">
          @for($i=1; $i<=12; $i++)
            <option value="{{ $i }}" @selected((int)$month === $i)>{{ ucfirst($monthNames[$i]) }}</option>
          @endfor
        </select>
      </div>
      <div class="m3d-card">
        <div class="m3d-label">Semana</div>
        <select id="weekPick" class="m3d-select">
          @for($w=1; $w<=53; $w++)
            <option value="{{ $w }}" @selected((int)$weekNow === $w)>Semana {{ $w }}</option>
          @endfor
        </select>
      </div>
    </div>

    <div class="m3d-card" style="padding:14px;">
      <div class="m3d-table-wrap">
        <table class="m3d-table">
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
                  <a class="m3d-btn" href="{{ route('metricas.detalle.dia', ['date' => $d['dia']]) }}">Ver día</a>
                  <a class="m3d-btn" href="{{ route('metricas.reporte.semanal', ['year' => $isoYear, 'week' => $isoWeek]) }}">Ver semana</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" style="padding:14px;color:#5d6c85;">No hay registros para este mes.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const monthPick = document.getElementById('monthPick');
  const weekPick = document.getElementById('weekPick');

  if (monthPick) {
    monthPick.addEventListener('change', function(){
      const m = Number(monthPick.value || 1);
      window.location.href = "{{ url('/metricas/reporte/mensual') }}/{{ (int)$year }}/" + m;
    });
  }

  if (weekPick) {
    weekPick.addEventListener('change', function(){
      const w = Number(weekPick.value || 1);
      window.location.href = "{{ url('/metricas/reporte/semanal') }}/{{ (int)$year }}/" + w;
    });
  }
})();
</script>
@endsection
