@extends('layouts.app')

@section('title', 'Reporte mensual')
@section('header', 'REPORTE MENSUAL')

@section('content')
<div class="container">

    @php
        // ✅ Nombre del mes SIN necesitar $monthLabel desde el controller
        $monthLabel = \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->translatedFormat('F');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Resumen {{ ucfirst($monthLabel) }} / {{ $year }}</h3>
            <div class="text-muted" style="margin-top:6px;">Detalle por días (payments confirmados)</div>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-secondary" href="{{ route('metricas.reporte.anual', $year) }}">← Volver</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="mb-1">Total recaudado del mes</h6>
                <h3 class="mb-0">${{ number_format((int)$totalMensual) }}</h3>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0">Ir a mes</label>
                <input type="number" id="monthPick" class="form-control" style="width:120px;"
                       value="{{ (int)$month }}" min="1" max="12">
                <a id="btnGoMonth" class="btn btn-dark"
                   href="{{ route('metricas.reporte.mensual', ['year'=>$year,'month'=>$month]) }}">
                    Ver
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th class="text-end">Recaudo</th>
                        <th class="text-end"># Arriendos</th>
                        <th class="text-end">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($dias as $d)
                    @php
                        $fecha = $d['dia']; // YYYY-MM-DD
                        $recaudo = (int)$d['recaudo'];
                        $arr = (int)$d['arriendos'];
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                        <td class="text-end">${{ number_format($recaudo) }}</td>
                        <td class="text-end">{{ $arr }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('metricas.detalle.dia', $fecha) }}">
                                Ver día →
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="text-muted">
                Tip: entra a un día para ver pagos con hora exacta y métodos (payment_parts).
            </div>
        </div>
    </div>

</div>

<script>
(function(){
  const m = document.getElementById('monthPick');
  const b = document.getElementById('btnGoMonth');
  if(!m || !b) return;

  function sync(){
    const month = Number(m.value || 1);
    b.href = "{{ url('/metricas/reporte/mensual') }}/{{ (int)$year }}/" + month;
  }

  m.addEventListener('input', sync);
  sync();
})();
</script>
@endsection
