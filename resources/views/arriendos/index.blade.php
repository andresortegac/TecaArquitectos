@extends('layouts.app')
@section('title','Arriendos')
@section('header','Arriendos')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div style="display:flex; justify-content:space-between; margin-bottom:12px;">
    <h2>Lista de arriendos</h2>

    {{-- ✅ NUEVO: acceso rápido a morosos (si luego creas esa ruta, puedes activarlo) --}}
    {{-- <a class="btn danger" href="{{ route('reportes.morosos') }}">Morosos</a> --}}

    <a class="btn" href="{{ route('arriendos.create') }}">+ Nuevo arriendo</a>
</div>

{{-- ✅ NUEVO: filtros simples (cliente, producto, estado, semáforo, obra) --}}
<form method="GET" action="{{ route('arriendos.index') }}" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
    <select name="cliente_id" class="input" style="min-width:180px;">
        <option value="">Cliente (todos)</option>
        @isset($clientes)
            @foreach($clientes as $c)
                <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre }}
                </option>
            @endforeach
        @endisset
    </select>

    <select name="producto_id" class="input" style="min-width:180px;">
        <option value="">Producto (todos)</option>
        @isset($productos)
            @foreach($productos as $p)
                <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        @endisset
    </select>

    <input class="input" type="text" name="obra_id" value="{{ request('obra_id') }}" placeholder="Obra ID" style="width:120px;">

    <select name="estado" class="input" style="width:140px;">
        <option value="">Estado (todos)</option>
        <option value="activo"   {{ request('estado') == 'activo' ? 'selected' : '' }}>activo</option>
        <option value="devuelto" {{ request('estado') == 'devuelto' ? 'selected' : '' }}>devuelto</option>
        <option value="vencido"  {{ request('estado') == 'vencido' ? 'selected' : '' }}>vencido</option>
    </select>

    <select name="semaforo_pago" class="input" style="width:160px;">
        <option value="">Semáforo (todos)</option>
        <option value="VERDE"    {{ request('semaforo_pago') == 'VERDE' ? 'selected' : '' }}>VERDE</option>
        <option value="AMARILLO" {{ request('semaforo_pago') == 'AMARILLO' ? 'selected' : '' }}>AMARILLO</option>
        <option value="ROJO"     {{ request('semaforo_pago') == 'ROJO' ? 'selected' : '' }}>ROJO</option>
    </select>

    <button class="btn-sm" type="submit">Filtrar</button>
    <a class="btn-sm" href="{{ route('arriendos.index') }}">Limpiar</a>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Precio</th>

            {{-- ✅ NUEVO: saldo, mora y semáforo --}}
            <th>Saldo</th>
            <th>Mora</th>
            <th>Semáforo</th>

            <th>Estado</th>
            <th style="width:240px;">Acciones</th> {{-- ✅ ajustado ancho para nuevo botón --}}
        </tr>
    </thead>
    <tbody>
    @forelse($arriendos as $a)
        <tr>
            <td>{{ $a->cliente->nombre ?? '—' }}</td>
            <td>{{ $a->producto->nombre ?? '—' }}</td>
            <td>{{ $a->cantidad }}</td>
            <td>{{ $a->fecha_inicio?->format('d/m/Y H:i') }}</td>
            <td>{{ $a->fecha_fin ?? '—' }}</td>
            <td>${{ number_format((float)$a->precio_total, 2) }}</td>

            {{-- ✅ NUEVO: saldo y mora --}}
            <td>${{ number_format((float)($a->saldo ?? 0), 2) }}</td>
            <td>{{ (int)($a->dias_mora ?? 0) }}</td>

            {{-- ✅ NUEVO: semáforo pintado --}}
            <td>
                {{-- ✅ NUEVO: semáforo pintado (sin @php para evitar warnings del editor) --}}
@if(($a->semaforo_pago ?? 'VERDE') === 'ROJO')
    <span style="display:inline-block; padding:3px 8px; border-radius:10px; color:#fff; background:#d9534f;">
        ROJO
    </span> 
@elseif(($a->semaforo_pago ?? 'VERDE') === 'AMARILLO')
    <span style="display:inline-block; padding:3px 8px; border-radius:10px; color:#fff; background:#f0ad4e;">
        AMARILLO
    </span>
@else
    <span style="display:inline-block; padding:3px 8px; border-radius:10px; color:#fff; background:#5cb85c;">
        VERDE
    </span>
@endif

            </td>

            <td>{{ ucfirst($a->estado) }}</td>

            <td>
                <a class="btn-sm" href="{{ route('arriendos.edit',$a) }}">Editar</a>

                {{-- ✅ NUEVO: botón "Cerrar/Devolver" (solo si aún no está cerrado) --}}
                @if((int)($a->cerrado ?? 0) === 0)
                    <button
                        type="button"
                        class="btn-sm"
                        onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='block'">
                        Cerrar
                    </button>
                @endif

                <form action="{{ route('arriendos.destroy',$a) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn-sm danger" onclick="return confirm('¿Eliminar arriendo?')">
                        Borrar
                    </button>
                </form>
            </td>
        </tr>

        {{-- ✅ NUEVO: modal simple (sin bootstrap) para cerrar/devolver y capturar incidencias --}}
        @if((int)($a->cerrado ?? 0) === 0)
            <div id="modalCerrar{{ $a->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999;">
                <div style="background:#fff; max-width:520px; margin:7% auto; padding:16px; border-radius:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <strong>Cerrar arriendo #{{ $a->id }}</strong>
                        <button type="button" class="btn-sm danger"
                            onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">
                            X
                        </button>
                    </div>

                    <form method="POST" action="{{ route('arriendos.cerrar', $a) }}">
                        @csrf

                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <div style="flex:1;">
                                <label style="display:block; font-size:13px;">Fecha devolución real</label>
                                <input class="input" type="date" name="fecha_devolucion_real" required value="{{ date('Y-m-d') }}" style="width:100%;">
                            </div>
                            <div style="flex:1;">
                                <label style="display:block; font-size:13px;">Pago recibido (opcional)</label>
                                <input class="input" type="number" min="0" step="0.01" name="pago" value="0" style="width:100%;">
                            </div>
                        </div>

                        <hr style="margin:10px 0;">

                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <div style="flex:1;">
                                <label style="display:block; font-size:13px;">Días de lluvia (se descuentan)</label>
                                <input class="input" type="number" min="0" name="dias_lluvia" value="0" style="width:100%;">
                            </div>
                            <div style="flex:1;">
                                <label style="display:block; font-size:13px;">Costo daño/merma</label>
                                <input class="input" type="number" min="0" step="0.01" name="costo_merma" value="0" style="width:100%;">
                            </div>
                        </div>

                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:13px;">Descripción (opcional)</label>
                            <input class="input" type="text" name="descripcion_incidencia" placeholder="Ej: lluvia fuerte / mango roto" style="width:100%;">
                        </div>

                        <div style="font-size:12px; color:#666; margin-bottom:10px;">
                            Domingos se descuentan automáticamente. Si queda saldo pendiente al cerrar, se activa semáforo (AMARILLO 0–9 / ROJO 10+).
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:8px;">
                            <button type="button" class="btn-sm"
                                onclick="document.getElementById('modalCerrar{{ $a->id }}').style.display='none'">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-sm">Cerrar y calcular</button>
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

@endsection
