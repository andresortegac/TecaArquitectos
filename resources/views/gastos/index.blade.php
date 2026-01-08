@extends('layouts.app')

@section('title','Gastos')
@section('header','Listado de Gastos')

@section('content')
        <div class="mb-3" >          
            <span><h2>Historial de Gastos de la Empresa</h2></span>
        </div>
        
        <div class="mb-3" style="text-align:right;">          
            <a href="{{ route('gastos.create') }}" class="btn btn-success">
                ➕ Nuevo gasto
            </a>
        </div>
        <br>
        <table class="table">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Monto</th>
           
        </tr>
        </thead>
        <tbody>
        @foreach($gastos as $g)
        <tr>
            <td>{{ $g->fecha }}</td>
            <td>{{ ucfirst($g->tipo) }}</td>
            <td>{{ $g->descripcion }}</td>
            <td>${{ number_format($g->monto,2) }}</td>         
        </tr>
    @endforeach
    </tbody>
    </table>

@endsection
