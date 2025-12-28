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
    // ✅ Usamos la misma tarifa que el backend
    $tarifaVista = (float)($item->tarifa_diaria ?? ($item->producto->costo ?? 0));

    // ✅ Fecha inicio del item (para calcular días en UI)
    $fechaInicioUI = $item->fecha_inicio_item?->toDateString()
        ?? ($item->arriendo->fecha_inicio?->toDateString() ?? date('Y-m-d'));
@endphp

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h2>Devolución - Item #{{ $item->id }} (Arriendo #{{ $item->arriendo_id }})</h2>
    <a class="btn-sm" href="{{ route('arriendos.ver', $item->arriendo_id) }}">Volver</a>
</div>

{{-- ✅ INFO ARRIBA --}}
<div style="background:#fff; padding:14px; border-radius:10px; margin-bottom:12px;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div><strong>Cliente:</strong> {{ $item->arriendo->cliente->nombre ?? '—' }}</div>
        <div><strong>Producto:</strong> {{ $item->producto->nombre ?? '—' }}</div>

        <div><strong>Cantidad inicial:</strong> {{ (int)$item->cantidad_inicial }}</div>
        <div><strong>Cantidad actual:</strong> {{ (int)$item->cantidad_actual }}</div>

        <div><strong>Inicio item:</strong> {{ $item->fecha_inicio_item?->format('d/m/Y H:i') ?? ($item->arriendo->fecha_inicio?->format('d/m/Y H:i') ?? '—') }}</div>
        <div><strong>Estado:</strong> {{ ucfirst($item->estado) }}</div>

        <div><strong>Tarifa diaria:</strong> ${{ number_format($tarifaVista, 2) }}</div>
        <div><strong>Saldo item:</strong> ${{ number_format((float)($item->saldo ?? 0), 2) }}</div>
    </div>
</div>

{{-- ✅ FORMULARIO --}}
<form method="POST" action="{{ route('items.devolucion.store', $item) }}"
      style="background:#fff; padding:14px; border-radius:10px;">
    @csrf

    <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Cantidad a devolver</label>
            <input class="input" type="number" min="1" max="{{ (int)$item->cantidad_actual }}"
                   name="cantidad_devuelta" required style="width:100%;"
                   value="{{ old('cantidad_devuelta') }}">
            <small style="color:#666;">Máximo: {{ (int)$item->cantidad_actual }}</small>
        </div>

        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Fecha devolución</label>
            <input class="input" type="date" name="fecha_devolucion" required style="width:100%;"
                   value="{{ old('fecha_devolucion', date('Y-m-d')) }}">
        </div>
    </div>

    <hr style="margin:10px 0;">

    <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Días de lluvia (se descuentan)</label>
            <input class="input" type="number" min="0" name="dias_lluvia"
                   value="{{ old('dias_lluvia', 0) }}" style="width:100%;">
        </div>
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Costo daño/merma</label>
            <input class="input" type="number" min="0" step="0.01" name="costo_merma"
                   value="{{ old('costo_merma', 0) }}" style="width:100%;">
        </div>
    </div>

    <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Pago recibido / Abono (opcional)</label>
            <input class="input" type="number" min="0" step="0.01" name="pago"
                   value="{{ old('pago', 0) }}" style="width:100%;">
            <div style="margin-top:6px;">
                <button type="button" class="btn-sm" id="btn_pagar_completo">Pagar completo</button>
            </div>
        </div>

        {{-- ✅ NUEVO: MÉTODO DE PAGO --}}
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Método de pago</label>
            <select class="input" name="payment_method" style="width:100%;">
                @php $pm = old('payment_method','efectivo'); @endphp
                <option value="efectivo" {{ $pm==='efectivo' ? 'selected' : '' }}>Efectivo</option>
                <option value="nequi" {{ $pm==='nequi' ? 'selected' : '' }}>Nequi</option>
                <option value="daviplata" {{ $pm==='daviplata' ? 'selected' : '' }}>Daviplata</option>
                <option value="transferencia" {{ $pm==='transferencia' ? 'selected' : '' }}>Transferencia</option>
            </select>
            <small style="color:#666;">Si no eliges, queda Efectivo.</small>
        </div>
    </div>

    <div style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
        <div style="flex:1; min-width:220px;">
            <label style="display:block; font-size:13px;">Descripción incidencia (opcional)</label>
            <input class="input" type="text" name="descripcion_incidencia"
                   value="{{ old('descripcion_incidencia') }}"
                   placeholder="Ej: lluvia fuerte / mango roto" style="width:100%;">
        </div>
    </div>

    <div style="margin-bottom:10px;">
        <label style="display:block; font-size:13px;">Nota (opcional)</label>
        <input class="input" type="text" name="nota" style="width:100%;" value="{{ old('nota') }}">
    </div>

    <div style="font-size:12px; color:#666; margin-bottom:10px;">
        Domingos se descuentan automáticamente. No se cobra el día de devolución. Si inicio y devolución son el mismo día, se cobra 1.
    </div>

    {{-- ✅ RESUMEN EN VIVO (VALOR A PAGAR / SALDO) --}}
    <div style="background:#f7f7f7; padding:12px; border-radius:10px; margin:10px 0;">
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px;">
            <div>
                <small>Días cobrables</small>
                <div><strong id="ui_dias_cobrables">0</strong></div>
            </div>
            <div>
                <small>Tarifa diaria</small>
                <div><strong>$<span id="ui_tarifa">{{ number_format($tarifaVista,2) }}</span></strong></div>
            </div>
            <div>
                <small>Subtotal alquiler</small>
                <div><strong>$<span id="ui_subtotal">0.00</span></strong></div>
            </div>
            <div>
                <small>Total a pagar (esta devolución)</small>
                <div><strong>$<span id="ui_total">0.00</span></strong></div>
            </div>
        </div>

        <div style="margin-top:8px; display:flex; gap:16px; flex-wrap:wrap;">
            <div>
                <small>Abono</small>
                <div><strong>$<span id="ui_abono">0.00</span></strong></div>
            </div>
            <div>
                <small>Saldo de esta devolución</small>
                <div><strong>$<span id="ui_saldo">0.00</span></strong></div>
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="submit" class="btn-sm warning">Guardar devolución</button>
    </div>
</form>

{{-- ✅ SCRIPT CÁLCULO UI (NO REEMPLAZA EL BACKEND) --}}
<script>
(function () {
  const tarifa = JSON.parse('{!! json_encode($tarifaVista) !!}');
  const fechaInicio = JSON.parse('{!! json_encode($fechaInicioUI) !!}');

  const $cant = document.querySelector('[name="cantidad_devuelta"]');
  const $fec  = document.querySelector('[name="fecha_devolucion"]');
  const $llu  = document.querySelector('[name="dias_lluvia"]');
  const $mer  = document.querySelector('[name="costo_merma"]');
  const $pago = document.querySelector('[name="pago"]');
  const $btnFull = document.getElementById('btn_pagar_completo');

  const uiDias = document.getElementById('ui_dias_cobrables');
  const uiSub  = document.getElementById('ui_subtotal');
  const uiTot  = document.getElementById('ui_total');
  const uiAbo  = document.getElementById('ui_abono');
  const uiSal  = document.getElementById('ui_saldo');

  function parseNum(v){ v = (v ?? '').toString().trim(); return v === '' ? 0 : Number(v); }
  function money(n){ return (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2); }

  function calcDiasCobrables(inicio, devol){
    const d1 = new Date(inicio + 'T00:00:00');
    const d2 = new Date(devol  + 'T00:00:00');
    if (isNaN(d1) || isNaN(d2)) return 0;

    if (d1.getTime() === d2.getTime()) return 1; // mismo día cobra 1

    let dias = 0;
    let domingos = 0;

    const cur = new Date(d1);
    while (cur < d2) { // excluye el día de devolución
      dias++;
      if (cur.getDay() === 0) domingos++; // domingo
      cur.setDate(cur.getDate() + 1);
    }

    dias = Math.max(0, dias - domingos);
    const lluvia = Math.max(0, parseNum($llu?.value));
    dias = Math.max(0, dias - lluvia);

    return dias;
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

    uiTot.dataset.total = money(total);
  }

  [$cant,$fec,$llu,$mer,$pago].forEach(el => el && el.addEventListener('input', recompute));

  if ($btnFull) {
    $btnFull.addEventListener('click', function(){
      const t = Number(uiTot.dataset.total || 0);
      if ($pago) $pago.value = money(t);
      recompute();
    });
  }

  recompute();
})();
</script>

{{-- ✅ HISTORIAL DEL ITEM --}}
<hr style="margin:18px 0;">

<h3 style="margin:0 0 10px;">Historial de devoluciones (este producto)</h3>

@php
    $resumen = $resumen ?? [
        'total_devoluciones' => 0,
        'total_devuelto' => 0,
        'total_abonado' => 0,
        'total_cobrado' => 0,
    ];
@endphp

<div style="display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap;">
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Devoluciones:</strong> {{ $resumen['total_devoluciones'] }}
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Total devuelto:</strong> {{ $resumen['total_devuelto'] }}
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Total cobrado:</strong> ${{ number_format((float)$resumen['total_cobrado'], 2) }}
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <strong>Total abonado:</strong> ${{ number_format((float)$resumen['total_abonado'], 2) }}
    </div>
</div>

@if(!isset($item->devoluciones) || $item->devoluciones->isEmpty())
    <div style="background:#fff; padding:12px; border-radius:10px;">
        No hay devoluciones registradas todavía para este producto.
    </div>
@else
    <div style="background:#fff; padding:12px; border-radius:10px;">
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Devuelto</th>
                    <th>Días</th>
                    <th>Dom</th>
                    <th>Lluvia</th>
                    <th>Cobrables</th>
                    <th>Tarifa</th>
                    <th>Alquiler</th>
                    <th>Merma</th>
                    <th>Total</th>
                    <th>Abono</th>
                    <th>Saldo devolución</th>
                    <th>Quedan</th>
                    <th>Saldo item</th>
                </tr>
            </thead>
            <tbody>
            @foreach($item->devoluciones->sortByDesc('id') as $d)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($d->fecha_devolucion)->format('d/m/Y') }}</td>
                    <td>{{ (int)$d->cantidad_devuelta }}</td>
                    <td>{{ (int)$d->dias_transcurridos }}</td>
                    <td>{{ (int)$d->domingos_desc }}</td>
                    <td>{{ (int)$d->dias_lluvia_desc }}</td>
                    <td>{{ (int)$d->dias_cobrables }}</td>
                    <td>${{ number_format((float)$d->tarifa_diaria, 2) }}</td>
                    <td>${{ number_format((float)$d->total_alquiler, 2) }}</td>
                    <td>${{ number_format((float)$d->total_merma, 2) }}</td>
                    <td><strong>${{ number_format((float)$d->total_cobrado, 2) }}</strong></td>
                    <td>${{ number_format((float)$d->pago_recibido, 2) }}</td>

                    {{-- ✅ si ya existe saldo_devolucion en BD lo muestra, si no, no rompe --}}
                    <td>
                        @if(isset($d->saldo_devolucion))
                            ${{ number_format((float)$d->saldo_devolucion, 2) }}
                        @else
                            —
                        @endif
                    </td>

                    <td>{{ (int)$d->cantidad_restante }}</td>
                    <td>${{ number_format((float)$d->saldo_resultante, 2) }}</td>
                </tr>

                @if(!empty($d->descripcion_incidencia) || !empty($d->nota))
                    <tr>
                        <td colspan="14" style="font-size:12px; color:#666;">
                            @if(!empty($d->descripcion_incidencia))
                                <strong>Incidencia:</strong> {{ $d->descripcion_incidencia }}
                            @endif

                            @if(!empty($d->nota))
                                @if(!empty($d->descripcion_incidencia)) &nbsp;|&nbsp; @endif
                                <strong>Nota:</strong> {{ $d->nota }}
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
