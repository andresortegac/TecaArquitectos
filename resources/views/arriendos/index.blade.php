@extends('layouts.app')
@section('title','Arriendos')
@section('header','Arriendos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ui.css') }}">
@endpush

@section('content')

<div class="principal-page">

@if(session('success'))
  <div class="alert success">{{ session('success') }}</div>
@endif

@php
  // ✅ KPIs calculados con los datos visibles (paginación)
  $col = $arriendos->getCollection();

  $total = $col->count();
  $activos = $col->where('estado','activo')->count();
  $devueltos = $col->where('estado','devuelto')->count();

  $rojo = $col->where('semaforo_pago','ROJO')->count();
  $amarillo = $col->where('semaforo_pago','AMARILLO')->count();
  $verde = $total - $rojo - $amarillo;

  $saldoTotal = $col->sum(fn($x)=>(float)($x->saldo ?? 0));
  $moraTotal = $col->sum(fn($x)=>(int)($x->dias_mora ?? 0));

  $pctPagos = $total ? round(($verde / $total) * 100) : 0;
  $pctActivos = $total ? round(($activos / $total) * 100) : 0;
  $pctDev = $total ? round(($devueltos / $total) * 100) : 0;
  $pctMora = $total ? round((($rojo + $amarillo) / $total) * 100) : 0;
@endphp

<div class="alquiler-conte">

  {{-- TOPBAR + KPIs --}}
  <div class="topbar">
    <p class="subtitle">Lista de arriendos (Contratos PADRE), estados de pago y gestión por productos (items).</p>

    <div class="topbar-actions">
      <a class="btn-ghost" href="{{ route('arriendos.index') }}">Refrescar</a>
      <a class="btn-primary" href="{{ route('arriendos.create') }}">+ Nuevo arriendo</a>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="card kpi">
      <div class="meta">
        <div class="label">Total</div>
        <div class="value">{{ $total }}</div>
        <div class="hint">En esta página</div>
      </div>
      <div class="ring" style="--p: {{ $pctPagos }}%; --ring: var(--primary);" data-t="{{ $pctPagos }}%"></div>
    </div>

    <div class="card kpi">
      <div class="meta">
        <div class="label">Activos</div>
        <div class="value">{{ $activos }}</div>
        <div class="hint">En curso</div>
      </div>
      <div class="ring" style="--p: {{ $pctActivos }}%; --ring: var(--success);" data-t="{{ $pctActivos }}%"></div>
    </div>

    <div class="card kpi">
      <div class="meta">
        <div class="label">Devueltos</div>
        <div class="value">{{ $devueltos }}</div>
        <div class="hint">Cerrados</div>
      </div>
      <div class="ring" style="--p: {{ $pctDev }}%; --ring: rgba(100,116,139,.8);" data-t="{{ $pctDev }}%"></div>
    </div>

    <div class="card kpi">
      <div class="meta">
        <div class="label">En Mora</div>
        <div class="value">{{ $rojo + $amarillo }}</div>
        <div class="hint">{{ $moraTotal }} días mora</div>
      </div>
      <div class="ring" style="--p: {{ $pctMora }}%; --ring: var(--warning);" data-t="{{ $pctMora }}%"></div>
    </div>

    <div class="card kpi">
      <div class="meta">
        <div class="label">Saldo</div>
        <div class="value">${{ number_format($saldoTotal, 0) }}</div>
        <div class="hint">Pendiente</div>
      </div>
      <div class="ring" style="--p: {{ $saldoTotal > 0 ? 85 : 15 }}%; --ring: var(--danger);" data-t="$"></div>
    </div>
  </div>

  {{-- FILTROS --}}
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Filtros</h3>
      <a class="btn-sm" href="{{ route('arriendos.index') }}">Limpiar</a>
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

        {{-- ✅ En el sistema PADRE+ITEMS, filtrar por producto del PADRE no es real.
             Lo dejamos por compatibilidad si tu controller lo soporta, pero lo ideal es filtrar por items. --}}
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

  {{-- TABLA --}}
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Lista de arriendos (Contratos PADRE)</h3>
    </div>

    <table class="table-pro">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Items</th>
          <th>Unidades</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th class="td-right">Total</th>
          <th class="td-right">Saldo</th>
          <th>Mora</th>
          <th>Semáforo</th>
          <th>Estado</th>
          <th style="width:260px;">Acciones</th>
        </tr>
      </thead>

      <tbody>
      @forelse($arriendos as $a)

        @php
          // ✅ Si el controller cargó items, esto funciona.
          // Si no, items_count (withCount) puede existir.
          $itemsCount = $a->items_count ?? (isset($a->items) ? $a->items->count() : null);
          $unidades = isset($a->items) ? (int)$a->items->sum('cantidad_actual') : null;

          $sem = ($a->semaforo_pago ?? 'VERDE');
        @endphp

        <tr>
          <td>
            {{ $a->cliente->nombre ?? '—' }}
            @if(!empty($a->obra_id ?? null))
              <span class="small">Obra: {{ $a->obra_id }}</span>
            @endif
          </td>

          <td>
            {{ $itemsCount !== null ? $itemsCount : '—' }}
            <span class="small">productos</span>
          </td>

          <td>
            {{ $unidades !== null ? $unidades : '—' }}
            <span class="small">unidades</span>
          </td>

          <td>{{ $a->fecha_inicio?->format('d/m/Y H:i') }}</td>
          <td>{{ $a->fecha_fin ?? '—' }}</td>

          <td class="td-right">${{ number_format((float)($a->precio_total ?? 0), 2) }}</td>
          <td class="td-right">${{ number_format((float)($a->saldo ?? 0), 2) }}</td>

          <td>{{ (int)($a->dias_mora ?? 0) }}</td>

          <td>
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

          {{-- ACCIONES --}}
          <td>
            <div class="actions">
              <div class="dropdown" data-dd>
                <button type="button" class="btn-kebab" aria-label="Acciones">⋯</button>

                <div class="dropdown-menu">

                  {{-- ✅ Ver / Gestionar (PADRE) --}}
                  <a class="menu-item item-return" href="{{ route('arriendos.ver', $a) }}">
                    <span class="menu-left"><span class="dot"></span>Ver / Gestionar</span>
                    <span>›</span>
                  </a>

                  {{-- Editar (si lo sigues usando) --}}
                  <a class="menu-item item-edit" href="{{ route('arriendos.edit',$a) }}">
                    <span class="menu-left"><span class="dot"></span>Editar</span>
                    <span>›</span>
                  </a>

                  {{-- Detalles solo si está cerrado --}}
                  @if((int)($a->cerrado ?? 0) === 1 || $a->estado === 'devuelto')
                    <a class="menu-item item-details" href="{{ route('arriendos.detalles', $a) }}">
                      <span class="menu-left"><span class="dot"></span>Detalles</span>
                      <span>›</span>
                    </a>
                  @endif

                  {{-- Cerrar manual (solo si sigues usando cierre por padre) --}}
                  @if((int)($a->cerrado ?? 0) === 0)
                    <button type="button" class="menu-item item-close"
                      onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='flex'">
                      <span class="menu-left"><span class="dot"></span>Cerrar</span>
                      <span>›</span>
                    </button>
                  @endif

                  <form action="{{ route('arriendos.destroy',$a) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="menu-item item-delete" onclick="return confirm('¿Eliminar arriendo?')">
                      <span class="menu-left"><span class="dot"></span>Borrar</span>
                      <span>›</span>
                    </button>
                  </form>

                </div>
              </div>
            </div>
          </td>
        </tr>

        {{-- MODAL CERRAR (si aún lo usas) --}}
        @if((int)($a->cerrado ?? 0) === 0)
          <div id="modalCerrar{{ $a->id }}" class="modal-backdrop" style="display:none;">
            <div class="card modal-dialog">
              <div class="card-header modal-header">
                <h3 class="card-title">Cerrar arriendo #{{ $a->id }}</h3>
                <button type="button" class="btn-ghost"
                  onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">Cerrar</button>
              </div>

              <form method="POST" action="{{ route('arriendos.cerrar', $a) }}">
                @csrf

                <div class="modal-grid">
                  <div class="modal-field">
                    <label class="small modal-label">Fecha devolución real</label>
                    <input class="input" type="date" name="fecha_devolucion_real" required value="{{ date('Y-m-d') }}">
                  </div>
                  <div class="modal-field">
                    <label class="small modal-label">Pago recibido (opcional)</label>
                    <input class="input" type="number" min="0" step="0.01" name="pago" value="0">
                  </div>
                </div>

                <div class="modal-grid">
                  <div class="modal-field">
                    <label class="small modal-label">Días de lluvia (se descuentan)</label>
                    <input class="input" type="number" min="0" name="dias_lluvia" value="0">
                  </div>
                  <div class="modal-field">
                    <label class="small modal-label">Costo daño/merma</label>
                    <input class="input" type="number" min="0" step="0.01" name="costo_merma" value="0">
                  </div>
                </div>

                <div class="modal-field">
                  <label class="small modal-label">Descripción (opcional)</label>
                  <input class="input" type="text" name="descripcion_incidencia" placeholder="Ej: lluvia fuerte / mango roto">
                </div>

                <div class="small modal-help">
                  Domingos se descuentan automáticamente. Si queda saldo pendiente al cerrar, se activa semáforo (AMARILLO 0–9 / ROJO 10+).
                </div>

                <div class="modal-actions">
                  <button type="button" class="btn-ghost"
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

  {{-- JS: filtros + dropdown --}}
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

    function closeAll(){
      document.querySelectorAll('[data-dd].open').forEach(dd => dd.classList.remove('open'));
    }

    document.addEventListener('click', function(e){
      const btn = e.target.closest('.btn-kebab');
      const dd  = e.target.closest('[data-dd]');

      if(btn && dd){
        e.preventDefault();
        const wasOpen = dd.classList.contains('open');
        closeAll();
        if(!wasOpen) dd.classList.add('open');
        return;
      }

      if(e.target.closest('.dropdown-menu')) return;
      closeAll();
    });

    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') closeAll();
    });
  })();
  </script>

</div> {{-- /alquiler-conte --}}
</div> {{-- /principal-page --}}

@endsection
