@extends('layouts.app')

    @section('title','Reportes')
    @section('header','REPORTES DE MOVIMIENTOS')
        @section('content')
            <div class="container1">
                <h2 class="mb-4">📦 Reporte de Entradas y Salidas</h2>

                <br>
                <div class="d-flex gap-2 mb-3">                

                    <a href="{{ route('movimientos.export') }}" class="btn btn-success">
                        📥 Exportar Excel
                    </a>
                </div>
                <br>
                <div style="text-align:right;"> 
                    <form method="GET" class="mb-3 d-flex gap-2">
                        <select name="tipo" class="form-control" style="width:200px">
                            <option value="">-- Todos --</option>
                            <option value="ingreso" {{ request('tipo')=='ingreso'?'selected':'' }}>Entrada</option>
                            <option value="salida" {{ request('tipo')=='salida'?'selected':'' }}>Salida</option>
                            <option value="ajuste_positivo" {{ request('tipo')=='ajuste_positivo'?'selected':'' }}>Ajuste positivo</option>
                            <option value="ajuste_negativo" {{ request('tipo')=='ajuste_negativo'?'selected':'' }}>Ajuste negativo</option>
                        </select>

                        <button class="btn btn-primary">Filtrar</button>
                    </form>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $m)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>

                                <td>{{ $m->producto->nombre }}</td>

                                <td>
                                    <span class="
                                        {{ 
                                            in_array($m->tipo, ['Ingreso', 'Ajuste positivo']) 
                                            ? 'text-success' 
                                            : 'text-danger' 
                                        }}">
                                        {{ $m->tipo }}
                                    </span>
                                </td>

                                <td>{{ $m->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        
            </div>
@endsection
