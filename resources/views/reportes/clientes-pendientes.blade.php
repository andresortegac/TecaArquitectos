@extends('layouts.app')

@section('title','Reportes')
@section('header','REPORTES FINANCIEROS')

@section('content')

@php
    $rol = auth()->user()->rol;
@endphp

<div class="contr-container">

    {{-- =========================
        RF-28 | CLIENTES PENDIENTES
    ========================= --}}

    {{-- 🔹 CABECERA DEL REPORTE --}}
    <div class="contr-report-header">
        <h2 class="contr-title">💰 Clientes pendientes por cancelar</h2>
        <p class="contr-subtitle">
            Clientes con alquileres activos o finalizados y saldo pendiente.
        </p>
    </div>

    {{-- 🔹 RESUMEN GENERAL --}}
    <div class="contr-card contr-mb">
        <div class="contr-card-body contr-row contr-text-center">

            <div class="contr-col">
                <h4 class="contr-fw-bold">
                    {{ $resumen['clientes'] ?? 0 }}
                </h4>
                <small class="contr-text-muted">Clientes con deuda</small>
            </div>

            <div class="contr-col">
                <h4 class="contr-fw-bold">
                    {{ $resumen['alquileres'] ?? 0 }}
                </h4>
                <small class="contr-text-muted">Alquileres pendientes</small>
            </div>

            @if($rol !== 'bodega')
            <div class="contr-col">
                <h4 class="contr-fw-bold contr-text-danger">
                    ${{ number_format($resumen['total_deuda'] ?? 0,0) }}
                </h4>
                <small class="contr-text-muted">Total adeudado</small>
            </div>
            @endif

        </div>
    </div>

    {{-- 🔹 FILTROS --}}
    <div class="contr-filters contr-mb">
        <input type="text"
               id="filtroCliente"
               class="contr-input"
               placeholder="🔍 Buscar cliente...">

        <select id="filtroEstado" class="contr-select">
            <option value="">Todos</option>
            <option value="al_dia">Al día</option>
            <option value="moroso">Morosos</option>
        </select>
    </div>

    {{-- 🔹 TABLA PRINCIPAL --}}
    <div class="contr-card">
        <div class="contr-card-body">

            <table class="contr-table contr-table-hover" id="tablaClientes">
                <thead class="contr-table-dark">
                    <tr>
                        <th>Cliente</th>
                        <th>Obras</th>
                        <th># Alquileres</th>

                        @if($rol !== 'bodega')
                            <th>Valor Adeudado</th>
                        @endif

                        <th>Último Cobro</th>
                        <th>Días Mora</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    {{-- 🔴 FILAS DINÁMICAS --}}
                    {{-- Se llenan desde el controlador --}}
                    @forelse($clientesMorosos as $cliente)
                        <tr class="contr-mora-{{ $cliente->nivel_mora }}">

                            <td class="contr-client-name">
                                {{ $cliente->nombre }}
                            </td>

                            <td>
                                {{ $cliente->obras }}
                            </td>

                            <td class="contr-fw-bold text-center">
                                {{ $cliente->alquileres_pendientes }}
                            </td>

                            @if($rol !== 'bodega')
                                <td class="contr-fw-bold">
                                    ${{ number_format($cliente->total_deuda,0) }}
                                </td>
                            @endif

                            <td>
                                {{ $cliente->ultimo_cobro ?? '—' }}
                            </td>

                            <td class="text-center">
                                {{ $cliente->dias_mora }}
                            </td>

                            <td>
                                <span class="contr-badge {{ $cliente->estado }}">
                                    {{ strtoupper($cliente->estado) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="contr-empty">
                                No hay clientes con saldos pendientes
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>
    </div>

</div>

{{-- =========================
   JS BÁSICO DE FILTRO
========================= --}}
<script>
document.getElementById('filtroCliente').addEventListener('keyup', function () {
    const filtro = this.value.toLowerCase();

    document.querySelectorAll('#tablaClientes tbody tr').forEach(fila => {
        const nombre = fila.querySelector('.contr-client-name')?.textContent.toLowerCase();
        fila.style.display = nombre.includes(filtro) ? '' : 'none';
    });
});
</script>

@endsection
