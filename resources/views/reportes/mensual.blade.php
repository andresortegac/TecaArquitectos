@extends('layouts.app')

@section('title','Reportes')
@section('header','REPORTES MENSUAL')
@section('content')
<div class="container1">
    <h2 class="mb-4">📅 Reporte General Mensual</h2>
        <br>
            <div class="d-flex gap-2 mb-3">                

                <a href="{{ route('reporte.mensual.export') }}" class="btn btn-success">
                    📥 Exportar Excel
                </a>
            </div>
        <br>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Año</th>
                <th>Mes</th>
                <th>Tipo</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reporte as $r)
                <tr>
                    <td>{{ $r->anio }}</td>
                    <td>{{ \Carbon\Carbon::create()->month($r->mes)->translatedFormat('F') }}</td>
                    <td>
                        <span class="badge {{ $r->tipo == 'entrada' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($r->tipo) }}
                        </span>
                    </td>
                    <td class="fw-bold">{{ $r->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        No hay datos para mostrar
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
