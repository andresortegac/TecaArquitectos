@extends('layouts.app')
@section('title', 'Detalle del día')
@section('header', 'Detalle del día')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');

  .mday{
    --text:#0a1428;
    --muted:#55627a;
    --line:rgba(116,136,168,.28);
    --line-strong:rgba(95,120,160,.40);
    --card:rgba(255,255,255,.88);
    --surface:linear-gradient(160deg, rgba(255,255,255,.95), rgba(236,244,255,.88));
    --brand:#1f67f3;
    --brand-2:#00a6b5;
    --ok:#0f9b6e;
    --shadow-lg:0 26px 60px rgba(8,22,49,.20);
    --shadow-md:0 14px 32px rgba(8,22,49,.14);
    --radius:22px;
    font-family:"Manrope","Space Grotesk","Segoe UI",sans-serif;
    color:var(--text);
    position:relative;
    isolation:isolate;
  }
  .mday *{box-sizing:border-box;}
  .mday::before,
  .mday::after{
    content:"";
    position:absolute;
    border-radius:999px;
    filter:blur(28px);
    z-index:-1;
    pointer-events:none;
  }
  .mday::before{
    width:360px;height:360px;top:-90px;left:-80px;
    background:radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0));
  }
  .mday::after{
    width:280px;height:280px;top:-40px;right:-40px;
    background:radial-gradient(circle at 50% 50%, rgba(0,166,181,.22), rgba(0,166,181,0));
  }
  .mday-shell{
    background:
      radial-gradient(950px 420px at 15% 0%, rgba(31,103,243,.15), transparent 60%),
      radial-gradient(780px 400px at 95% 0%, rgba(0,166,181,.10), transparent 58%),
      linear-gradient(180deg, #f6f9ff 0%, #edf3fb 100%);
    border:1px solid var(--line);
    border-radius:24px;
    box-shadow:var(--shadow-lg), inset 0 1px 0 rgba(255,255,255,.66);
    padding:16px;
  }
  .mday-hero{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    flex-wrap:wrap;
    border:1px solid var(--line-strong);
    border-radius:var(--radius);
    background:var(--surface);
    box-shadow:var(--shadow-md);
    padding:16px;
    position:relative;
    overflow:hidden;
    margin-bottom:12px;
  }
  .mday-hero::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(128deg, rgba(255,255,255,.45), rgba(255,255,255,0) 38%);
    pointer-events:none;
  }
  .mday-title{margin:0;font-size:22px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif;}
  .mday-sub{margin:6px 0 0;color:var(--muted);font-size:13px;}
  .mday-btn{
    display:inline-flex;align-items:center;justify-content:center;min-height:42px;
    padding:10px 14px;border-radius:12px;border:1px solid var(--line-strong);
    background:linear-gradient(180deg,#fff,#eef5ff);color:var(--text);text-decoration:none;
    font-size:13px;font-weight:800;box-shadow:0 10px 20px rgba(8,22,49,.12), inset 0 1px 0 rgba(255,255,255,.84);
    transition:transform .16s ease, box-shadow .2s ease;
  }
  .mday-btn:hover{transform:translateY(-2px);box-shadow:0 14px 26px rgba(8,22,49,.16), inset 0 1px 0 rgba(255,255,255,.92);}
  .mday-grid-kpi{
    display:grid;grid-template-columns:repeat(4,minmax(180px,1fr));gap:10px;margin-bottom:12px;
  }
  @media (max-width:980px){.mday-grid-kpi{grid-template-columns:repeat(2,minmax(180px,1fr));}}
  @media (max-width:640px){.mday-grid-kpi{grid-template-columns:1fr;}}
  .mday-kpi{
    border:1px solid var(--line);border-radius:16px;background:var(--card);
    box-shadow:var(--shadow-md);padding:12px;position:relative;overflow:hidden;
  }
  .mday-kpi::before{content:"";position:absolute;inset:0;background:linear-gradient(130deg, rgba(255,255,255,.40), rgba(255,255,255,0) 44%);pointer-events:none;}
  .mday-k{font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);font-weight:800;}
  .mday-v{margin-top:6px;font-size:24px;font-weight:800;font-family:"Space Grotesk","Manrope",sans-serif;}
  .mday-sections{display:grid;gap:12px;}
  .mday-card{
    border:1px solid var(--line);border-radius:var(--radius);background:var(--card);
    box-shadow:var(--shadow-md);overflow:hidden;position:relative;
  }
  .mday-card::before{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(132deg, rgba(255,255,255,.42), rgba(255,255,255,0) 40%);}
  .mday-head{padding:14px 16px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#f1f8ff);font-size:12px;letter-spacing:.3px;text-transform:uppercase;color:var(--muted);font-weight:800;}
  .mday-body{padding:12px 14px;}
  .mday-table-wrap{overflow:auto;border:1px solid var(--line);border-radius:14px;background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(242,248,255,.94));}
  .mday-table{width:100%;border-collapse:separate;border-spacing:0;min-width:760px;}
  .mday-table thead th{
    position:sticky;top:0;z-index:1;padding:11px 10px;text-align:left;
    font-size:11px;text-transform:uppercase;letter-spacing:.35px;color:var(--muted);
    border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fcfdff,#ecf4ff);
  }
  .mday-table tbody td{padding:11px 10px;border-bottom:1px solid rgba(130,150,182,.20);font-size:13px;color:var(--text);}
  .mday-right{text-align:right;}
  .mday-pill{
    display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;
    border:1px solid rgba(90,118,160,.36);font-size:11px;font-weight:800;letter-spacing:.25px;
    background:linear-gradient(180deg,#fff,#eef5ff);
  }
  .mday-pill.ok{border-color:rgba(15,155,110,.35);color:#0e7d59;background:linear-gradient(180deg, rgba(15,155,110,.12), rgba(255,255,255,.92));}
  .mday-empty{padding:18px;color:var(--muted);font-size:13px;text-align:center;}
</style>

<div class="mday">
  <div class="mday-shell">
    <div class="mday-hero">
      <div>
        <h2 class="mday-title">Detalle diario {{ $dateLabel ?? $dia }}</h2>
        <p class="mday-sub">Recaudo por hora, pagos confirmados y contratos iniciados en la fecha seleccionada.</p>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="mday-btn" href="{{ route('metricas.reporte.mensual', ['year' => $year, 'month' => $month]) }}">Volver al mes</a>
      </div>
    </div>

    <div class="mday-grid-kpi">
      <div class="mday-kpi">
        <div class="mday-k">Total recaudado</div>
        <div class="mday-v">${{ number_format((float)($totalDia ?? 0), 0, ',', '.') }}</div>
      </div>
      <div class="mday-kpi">
        <div class="mday-k">Pagos confirmados</div>
        <div class="mday-v">{{ (int)($countPayments ?? 0) }}</div>
      </div>
      <div class="mday-kpi">
        <div class="mday-k">Arriendos creados</div>
        <div class="mday-v">{{ (int)($arriendosCreados ?? 0) }}</div>
      </div>
      <div class="mday-kpi">
        <div class="mday-k">Arriendos cerrados/devueltos</div>
        <div class="mday-v">{{ (int)($arriendosDevueltos ?? 0) }}</div>
      </div>
    </div>

    <div class="mday-sections">
      <div class="mday-card">
        <div class="mday-head">Recaudo por Hora</div>
        <div class="mday-body">
          <div class="mday-table-wrap">
            <table class="mday-table">
              <thead>
                <tr>
                  <th>Hora</th>
                  <th class="mday-right">Total</th>
                  <th class="mday-right">Pagos</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($porHora ?? []) as $h)
                  <tr>
                    <td><strong>{{ $h['hour_label'] ?? '-' }}</strong></td>
                    <td class="mday-right">${{ number_format((float)($h['total'] ?? 0), 0, ',', '.') }}</td>
                    <td class="mday-right">{{ (int)($h['count'] ?? 0) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="mday-empty">No hay movimientos para esta fecha.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mday-card">
        <div class="mday-head">Pagos Confirmados</div>
        <div class="mday-body">
          <div class="mday-table-wrap">
            <table class="mday-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Hora</th>
                  <th class="mday-right">Monto</th>
                  <th>Métodos</th>
                  <th>Nota</th>
                  <th>Origen</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($payments ?? []) as $p)
                  <tr>
                    <td><strong>#{{ $p['id'] ?? '-' }}</strong></td>
                    <td>{{ $p['time'] ?? '-' }}</td>
                    <td class="mday-right">${{ number_format((float)($p['amount'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ $p['metodos'] ?? '-' }}</td>
                    <td>{{ $p['note'] ?? '-' }}</td>
                    <td>
                      <span class="mday-pill">{{ $p['source_type'] ?? '-' }} #{{ $p['source_id'] ?? '' }}</span>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="mday-empty">No hay pagos confirmados este día.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mday-card">
        <div class="mday-head">Arriendos del día</div>
        <div class="mday-body">
          <div class="mday-table-wrap">
            <table class="mday-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Cliente</th>
                  <th>Inicio</th>
                  <th>Estado</th>
                  <th class="mday-right">Total</th>
                  <th class="mday-right">Pagado</th>
                  <th class="mday-right">Saldo</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($arriendos ?? []) as $a)
                  <tr>
                    <td><strong>#{{ $a['id'] ?? '-' }}</strong></td>
                    <td>{{ $a['cliente'] ?? '-' }}</td>
                    <td>{{ $a['inicio'] ?? '-' }}</td>
                    <td>
                      @if(($a['estado'] ?? '') === 'devuelto')
                        <span class="mday-pill ok">Devuelto</span>
                      @else
                        <span class="mday-pill">{{ ucfirst($a['estado'] ?? '-') }}</span>
                      @endif
                    </td>
                    <td class="mday-right">${{ number_format((float)($a['total'] ?? 0), 0, ',', '.') }}</td>
                    <td class="mday-right">${{ number_format((float)($a['pagado'] ?? 0), 0, ',', '.') }}</td>
                    <td class="mday-right">${{ number_format((float)($a['saldo'] ?? 0), 0, ',', '.') }}</td>
                    <td>
                      @if(!empty($a['id']))
                        <a class="mday-btn" href="{{ route('arriendos.ver', $a['id']) }}">Ver</a>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="mday-empty">No se registraron arriendos en esta fecha.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
