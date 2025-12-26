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

{{-- ✅ ESTILOS SOLO PARA ESTA TABLA (NO TOCA LO DEMÁS) --}}
<style>
  .table-wrap { overflow:auto; }
  .items-table { width:100%; border-collapse:separate; border-spacing:0; }
  .items-table th, .items-table td { padding:10px 10px; vertical-align:middle; border-bottom:1px solid #e9e9e9; }
  .items-table thead th { background:#f6f7f9; font-weight:600; font-size:13px; position:sticky; top:0; z-index:1; }
  .td-right { text-align:right; white-space:nowrap; }
  .td-center { text-align:center; white-space:nowrap; }
  .td-producto { min-width:220px; }
  .td-fecha { white-space:nowrap; }
  .td-acciones { min-width:190px; }
  .acciones-box { display:flex; gap:8px; justify-content:flex-end; align-items:center; flex-wrap:wrap; }
  .btn-sm { display:inline-block; padding:6px 10px; border-radius:8px; border:1px solid #ddd; background:#fff; text-decoration:none; cursor:pointer; font-size:13px; }
  .btn-sm.warning { border-color:#f0ad4e; }
  .btn-sm.danger { border-color:#d9534f; color:#d9534f; }
  .btn-sm.danger:hover { background:#d9534f; color:#fff; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h2>Arriendo #{{ $arriendo->id }} (PADRE / Contrato)</h2>
    <a class="btn-sm" href="{{ route('arriendos.index') }}">Volver</a>
</div>

{{-- ✅ INFO GENERAL DEL CONTRATO --}}
<div style="background:#fff; padding:14px; border-radius:10px; margin-bottom:12px;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div><strong>Cliente:</strong> {{ $arriendo->cliente->nombre ?? '—' }}</div>
        <div><strong>Obra: </strong>{{ $arriendo->obra? $arriendo->obra->direccion . ' - ' . $arriendo->obra->detalle
    : '—'}}</div>
        <div><strong>Inicio contrato:</strong> {{ $arriendo->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}</div>
        <div><strong>Estado:</strong> {{ ucfirst($arriendo->estado) }}</div>
    </div>
</div>

{{-- ✅ TOTALES (CONTRATO + HISTÓRICO DEL CLIENTE) --}}
<div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <div style="font-size:12px; color:#666;">Total contrato</div>
        <strong>${{ number_format((float)$totContrato['precio_total'], 2) }}</strong>
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <div style="font-size:12px; color:#666;">Pagado contrato</div>
        <strong>${{ number_format((float)$totContrato['total_pagado'], 2) }}</strong>
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <div style="font-size:12px; color:#666;">Saldo contrato</div>
        <strong>${{ number_format((float)$totContrato['saldo'], 2) }}</strong>
    </div>

    <div style="background:#fff; padding:10px 12px; border-radius:10px; border-left:4px solid #ddd;">
        <div style="font-size:12px; color:#666;">Total histórico cliente</div>
        <strong>${{ number_format((float)$totalHistorico['precio_total'], 2) }}</strong>
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <div style="font-size:12px; color:#666;">Pagado histórico</div>
        <strong>${{ number_format((float)$totalHistorico['total_pagado'], 2) }}</strong>
    </div>
    <div style="background:#fff; padding:10px 12px; border-radius:10px;">
        <div style="font-size:12px; color:#666;">Saldo histórico</div>
        <strong>${{ number_format((float)$totalHistorico['saldo'], 2) }}</strong>
    </div>
</div>


{{-- ✅ BOTÓN AGREGAR PRODUCTO --}}
@if((int)($arriendo->cerrado ?? 0) === 0 && $arriendo->estado === 'activo')
    <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
        <a class="btn-sm" href="{{ route('arriendos.items.create', $arriendo) }}">+ Agregar producto</a>
    </div>
@endif

{{-- ✅ TABLA DE ITEMS --}}
<div style="background:#fff; padding:12px; border-radius:10px;">
    <h3 style="margin-top:0;">Productos alquilados (Items)</h3>

    @if($arriendo->items->isEmpty())
        <div>No hay productos aún. Agrega el primero.</div>
    @else

        <div class="table-wrap">
        <table class="items-table">
            <thead>
                <tr>
                    <th class="td-producto">Producto</th>
                    <th class="td-center">Inicial</th>
                    <th class="td-center">Actual</th>
                    <th class="td-fecha">Inicio item</th>

                    <th class="td-right">Tarifa/día</th>
                    <th class="td-right">Valor día</th>

                    <th class="td-center">Días alquilados</th>
                    <th class="td-center">Días cobrables</th>

                    <th class="td-right">Total</th>
                    <th class="td-right">Pagado</th>
                    <th class="td-right">Saldo</th>

                    <th class="td-center">Estado</th>
                    <th class="td-acciones td-right">Acciones</th>
                </tr>
            </thead>
            <tbody>

            @foreach($arriendo->items as $it)
                @php
                    // ✅ Tarifa real (prioridad: tarifa_diaria guardada, si no: producto->costo)
                    $tarifa = (float)($it->tarifa_diaria ?? ($it->producto->costo ?? 0));

                    // ✅ Valor día: tarifa * cantidad ACTUAL
                    $valorDia = $tarifa * (int)($it->cantidad_actual ?? 0);

                    // ✅ Devoluciones del item (si la relación viene cargada; si no, no revienta)
                    $devs = $it->devoluciones ?? collect();

                    // ✅ DÍAS: nunca negativos
                    if ($devs->count() > 0) {
                        $diasAlquilados = (int)$devs->sum('dias_transcurridos');
                        $diasCobrables  = (int)$devs->sum('dias_cobrables');
                    } else {
                        $inicio = \Carbon\Carbon::parse($it->fecha_inicio_item ?? $arriendo->fecha_inicio)->startOfDay();
                        $hoy    = \Carbon\Carbon::today()->startOfDay();

                        // Si el inicio del item es FUTURO, no hay días aún.
                        if ($inicio->gt($hoy)) {
                            $diasAlquilados = 0;
                            $diasCobrables  = 0;
                        } else {
                            // Regla similar a tu lógica: hoy no se cobra (fin no incluido)
                            $diasTrans = $inicio->diffInDays($hoy); // siempre >= 0
                            if ($diasTrans === 0) $diasTrans = 1;

                            // domingos sin incluir el día fin
                            $domingos = 0;
                            for ($d = $inicio->copy(); $d->lt($hoy); $d->addDay()) {
                                if ($d->isSunday()) $domingos++;
                            }

                            $diasAlquilados = $diasTrans;
                            $diasCobrables  = max(0, $diasTrans - $domingos);
                        }
                    }
                @endphp

                <tr>
                    <td class="td-producto">{{ $it->producto->nombre ?? '—' }}</td>

                    <td class="td-center">{{ (int)$it->cantidad_inicial }}</td>
                    <td class="td-center">{{ (int)$it->cantidad_actual }}</td>
                    <td class="td-fecha">{{ $it->fecha_inicio_item?->format('d/m/Y H:i') ?? '—' }}</td>

                    <td class="td-right">${{ number_format($tarifa, 2) }}</td>
                    <td class="td-right">${{ number_format($valorDia, 2) }}</td>

                    <td class="td-center">{{ $diasAlquilados }}</td>
                    <td class="td-center">{{ $diasCobrables }}</td>

                    <td class="td-right">${{ number_format((float)($it->precio_total ?? 0), 2) }}</td>
                    <td class="td-right">${{ number_format((float)($it->total_pagado ?? 0), 2) }}</td>
                    <td class="td-right">${{ number_format((float)($it->saldo ?? 0), 2) }}</td>

                    <td class="td-center">{{ ucfirst($it->estado) }}</td>

                    <td class="td-right td-acciones">
                        <div class="acciones-box">
                            @if((int)($it->cerrado ?? 0) === 0 && $it->estado === 'activo')
                                <a class="btn-sm warning" href="{{ route('items.devolucion.create', $it) }}">
                                    Devolución
                                </a>
                            @else
                                <span class="btn-sm" style="opacity:.7;">Cerrado</span>
                            @endif
                             <a class="btn-sm" href="{{ route('arriendos.detalles', $arriendo) }}">Detalles</a>


                            {{-- ✅ BORRAR ITEM (ALQUILER) --}}
                            <form action="{{ route('arriendos.items.destroy', $it) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn-sm danger"
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

@endsection
