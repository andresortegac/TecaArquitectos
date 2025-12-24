@extends('layouts.app')
@section('title','Arriendos')
@section('header','Arriendos')

@section('content')

@if(session('success'))
  <div class="alert success">{{ session('success') }}</div>
@endif

<div class="page-wrap">

  {{-- Header --}}
  <div class="page-header">
    <div>
      <h1 class="page-title">Arriendos</h1>
      <p class="page-subtitle">Lista de arriendos, estados de pago y gestión de devoluciones.</p>
    </div>

    <a class="btn-primary" href="{{ route('arriendos.create') }}">+ Nuevo arriendo</a>
  </div>

  {{-- Filtros --}}
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Filtros</h3>
      <div class="filters-actions">
        <a class="btn-sm" href="{{ route('arriendos.index') }}">Limpiar</a>
      </div>
    </div>

    <form id="filtrosForm" method="GET" action="{{ route('arriendos.index') }}">
      <div class="filters-grid">
        <select name="cliente_id" class="input filtro-auto">
          <option value="">Cliente (todos)</option>
          @isset($clientes)
            @foreach($clientes as $c)
              <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                {{ $c->nombre }}
              </option>
            @endforeach
          @endisset
        </select>

        <select name="producto_id" class="input filtro-auto">
          <option value="">Producto (todos)</option>
          @isset($productos)
            @foreach($productos as $p)
              <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                {{ $p->nombre }}
              </option>
            @endforeach
          @endisset
        </select>

        <select name="obra_id" class="input filtro-auto">
          <option value="">Obra (todas)</option>
          @isset($obras)
            @foreach($obras as $obraId)
              <option value="{{ $obraId }}" {{ (string)request('obra_id') === (string)$obraId ? 'selected' : '' }}>
                {{ $obraId }}
              </option>
            @endforeach
          @endisset
        </select>
      </div>
    </form>
  </div>

  <script>
  (function () {
      const form = document.getElementById('filtrosForm');

      function submitFormLimpio() {
          Array.from(form.elements).forEach(el => {
              if (!el.name) return;
              el.disabled = (el.value === '' || el.value === null);
          });
          form.submit();
      }

      document.querySelectorAll('.filtro-auto').forEach(el => {
          el.addEventListener('change', submitFormLimpio);
      });
  })();
  </script>

  {{-- Tabla --}}
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Lista de arriendos</h3>
    </div>

    <table class="table-pro">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th class="td-right">Precio</th>
          <th class="td-right">Saldo</th>
          <th>Mora</th>
          <th>Semáforo</th>
          <th>Estado</th>
          <th style="width:260px;">Acciones</th>
        </tr>
      </thead>

      <tbody>
      @forelse($arriendos as $a)
        <tr>
          <td>
            {{ $a->cliente->nombre ?? '—' }}
            @if(!empty($a->obra_id ?? null))
              <span class="meta">Obra: {{ $a->obra_id }}</span>
            @endif
          </td>

          <td>{{ $a->producto->nombre ?? '—' }}</td>
          <td>{{ $a->cantidad }}</td>
          <td>{{ $a->fecha_inicio?->format('d/m/Y H:i') }}</td>
          <td>{{ $a->fecha_fin ?? '—' }}</td>

          <td class="td-right">${{ number_format((float)$a->precio_total, 2) }}</td>
          <td class="td-right">${{ number_format((float)($a->saldo ?? 0), 2) }}</td>

          <td>{{ (int)($a->dias_mora ?? 0) }}</td>

          <td>
            @php $sem = ($a->semaforo_pago ?? 'VERDE'); @endphp

            @if($sem === 'ROJO')
              <span class="chip red">ROJO</span>
            @elseif($sem === 'AMARILLO')
              <span class="chip yellow">AMARILLO</span>
            @else
              <span class="chip green">VERDE</span>
            @endif

            @if((int)($a->cerrado ?? 0) === 1 || $a->estado === 'devuelto')
              <div style="margin-top:8px;">
                <a class="btn-sm" href="{{ route('arriendos.detalles', $a) }}">Detalles</a>
              </div>
            @endif
          </td>

          <td>
            @if($a->estado === 'devuelto')
              <span class="chip gray">Devuelto</span>
            @else
              <span class="chip blue">{{ ucfirst($a->estado) }}</span>
            @endif
          </td>

          <td>
            <a class="btn-sm" href="{{ route('arriendos.edit',$a) }}">Editar</a>

            @if((int)($a->cerrado ?? 0) === 0)
              <button type="button" class="btn-sm"
                onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='block'">
                Cerrar
              </button>

              <form action="{{ route('arriendos.devolucion.create', $a) }}" method="GET" style="display:inline;">
                <button type="submit" class="btn-sm warning">Devolución</button>
              </form>
            @endif

            <form action="{{ route('arriendos.destroy',$a) }}" method="POST" style="display:inline;">
              @csrf @method('DELETE')
              <button class="btn-sm danger" onclick="return confirm('¿Eliminar arriendo?')">Borrar</button>
            </form>
          </td>
        </tr>

        {{-- Modal cerrar (misma funcionalidad, mejor look) --}}
        @if((int)($a->cerrado ?? 0) === 0)
          <div id="modalCerrar{{ $a->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999;">
            <div class="card" style="max-width:560px; margin:7% auto; padding:16px;">
              <div class="card-header" style="margin-bottom:10px;">
                <h3 class="card-title">Cerrar arriendo #{{ $a->id }}</h3>
                <button type="button" class="btn-sm danger"
                  onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">X</button>
              </div>

              <form method="POST" action="{{ route('arriendos.cerrar', $a) }}">
                @csrf

                <div style="display:flex; gap:10px; margin-bottom:10px;">
                  <div style="flex:1;">
                    <label class="meta" style="font-size:13px;">Fecha devolución real</label>
                    <input class="input" type="date" name="fecha_devolucion_real" required value="{{ date('Y-m-d') }}">
                  </div>
                  <div style="flex:1;">
                    <label class="meta" style="font-size:13px;">Pago recibido (opcional)</label>
                    <input class="input" type="number" min="0" step="0.01" name="pago" value="0">
                  </div>
                </div>

                <div style="display:flex; gap:10px; margin-bottom:10px;">
                  <div style="flex:1;">
                    <label class="meta" style="font-size:13px;">Días de lluvia (se descuentan)</label>
                    <input class="input" type="number" min="0" name="dias_lluvia" value="0">
                  </div>
                  <div style="flex:1;">
                    <label class="meta" style="font-size:13px;">Costo daño/merma</label>
                    <input class="input" type="number" min="0" step="0.01" name="costo_merma" value="0">
                  </div>
                </div>

                <div style="margin-bottom:10px;">
                  <label class="meta" style="font-size:13px;">Descripción (opcional)</label>
                  <input class="input" type="text" name="descripcion_incidencia" placeholder="Ej: lluvia fuerte / mango roto">
                </div>

                <div class="meta" style="font-size:12px; margin-bottom:12px;">
                  Domingos se descuentan automáticamente. Si queda saldo pendiente al cerrar, se activa semáforo (AMARILLO 0–9 / ROJO 10+).
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px;">
                  <button type="button" class="btn-sm"
                    onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">Cancelar</button>
                  <button type="submit" class="btn-primary" style="padding:8px 12px;">Cerrar y calcular</button>
                </div>
              </form>
            </div>
          </div>
        @endif

      @empty
        <tr><td colspan="11">No hay arriendos todavía.</td></tr>
      @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $arriendos->links() }}
    </div>
  </div>

</div>
@endsection
