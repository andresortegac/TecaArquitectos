@extends('layouts.app')
@section('title','Detalles del arriendo')
@section('header','Detalles del arriendo')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h2>Detalles - Arriendo #{{ $arriendo->id }}</h2>
    <a class="btn-sm" href="{{ route('arriendos.index') }}">Volver</a>
</div>

<div style="background:#fff; padding:14px; border-radius:10px; margin-bottom:12px;">
    <h3 style="margin-top:0;">Información general</h3>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div><strong>Cliente:</strong> {{ $arriendo->cliente->nombre ?? '—' }}</div>
        <div><strong>Producto:</strong> {{ $arriendo->producto->nombre ?? '—' }}</div>
        <div><strong>Cantidad final:</strong> {{ $arriendo->cantidad }}</div>
        <div><strong>Estado:</strong> {{ ucfirst($arriendo->estado) }}</div>
        <div><strong>Inicio:</strong> {{ $arriendo->fecha_inicio?->format('d/m/Y H:i') }}</div>
        <div><strong>Fin:</strong> {{ $arriendo->fecha_fin ?? '—' }}</div>

        <div><strong>Días transcurridos:</strong> {{ (int)($arriendo->dias_transcurridos ?? 0) }}</div>
        <div><strong>Domingos desc.:</strong> {{ (int)($arriendo->domingos_desc ?? 0) }}</div>
        <div><strong>Lluvia desc.:</strong> {{ (int)($arriendo->dias_lluvia_desc ?? 0) }}</div>
        <div><strong>Días cobrables:</strong> {{ (int)($arriendo->dias_cobrables ?? 0) }}</div>

        <div><strong>Total alquiler:</strong> ${{ number_format((float)($arriendo->total_alquiler ?? 0), 2) }}</div>
        <div><strong>Total merma:</strong> ${{ number_format((float)($arriendo->total_merma ?? 0), 2) }}</div>
        <div><strong>Total pagado:</strong> ${{ number_format((float)($arriendo->total_pagado ?? 0), 2) }}</div>
        <div><strong>Precio total:</strong> ${{ number_format((float)($arriendo->precio_total ?? 0), 2) }}</div>
        <div><strong>Saldo:</strong> ${{ number_format((float)($arriendo->saldo ?? 0), 2) }}</div>
    </div>
</div>

<hr style="margin:18px 0;">

<h3 style="margin-bottom:10px;">Devoluciones parciales</h3>

@if(($arriendo->devoluciones ?? collect())->isEmpty())
    <div style="background:#fff; padding:12px; border-radius:10px;">
        No hay devoluciones parciales registradas.
    </div>
@else
    <div style="background:#fff; padding:12px; border-radius:10px;">
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Devuelto</th>
                    <th>Días</th>
                    <th>Cobrables</th>
                    <th>Alquiler</th>
                    <th>Merma</th>
                    <th>Total</th>
                    <th>Abono</th>
                    <th>Quedan</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
            @foreach($arriendo->devoluciones->sortByDesc('id') as $d)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($d->fecha_devolucion)->format('d/m/Y') }}</td>
                    <td>{{ (int)$d->cantidad_devuelta }}</td>
                    <td>{{ (int)$d->dias_transcurridos }}</td>
                    <td>{{ (int)$d->dias_cobrables }}</td>
                    <td>${{ number_format((float)$d->total_alquiler, 2) }}</td>
                    <td>${{ number_format((float)$d->total_merma, 2) }}</td>
                    <td><strong>${{ number_format((float)$d->total_cobrado, 2) }}</strong></td>
                    <td>${{ number_format((float)$d->pago_recibido, 2) }}</td>
                    <td>{{ (int)$d->cantidad_restante }}</td>
                    <td>${{ number_format((float)$d->saldo_resultante, 2) }}</td>
                </tr>
                @if(!empty($d->descripcion_incidencia) || !empty($d->nota))
                    <tr>
                        <td colspan="10" style="font-size:12px; color:#666;">
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

<hr style="margin:18px 0;">

<h3 style="margin-bottom:10px;">Incidencias</h3>

@if(($arriendo->incidencias ?? collect())->isEmpty())
    <div style="background:#fff; padding:12px; border-radius:10px;">
        No hay incidencias registradas.
    </div>
@else
    <div style="background:#fff; padding:12px; border-radius:10px;">
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Días</th>
                    <th>Costo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            @foreach($arriendo->incidencias->sortByDesc('id') as $i)
                <tr>
                    <td>{{ $i->tipo }}</td>
                    <td>{{ (int)($i->dias ?? 0) }}</td>
                    <td>${{ number_format((float)($i->costo ?? 0), 2) }}</td>
                    <td>{{ $i->descripcion ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($i->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
