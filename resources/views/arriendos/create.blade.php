@extends('layouts.app')
@section('title','Nuevo arriendo')
@section('header','Nuevo arriendo')

@section('content')
<h2>Crear arriendo</h2>

<form action="{{ route('arriendos.store') }}" method="POST" class="form">
    @csrf

    <label>Cliente</label>
    <select name="cliente_id" id="cliente_id" required>
        <option value="">-- Seleccione --</option>
        @foreach($clientes as $c)
            <option value="{{ $c->id }}" @selected(old('cliente_id')==$c->id)>
                {{ $c->nombre }}
            </option>
        @endforeach
    </select>

    <label>Producto</label>
    <select name="producto_id" id="producto_id" required>
        <option value="">-- Seleccione --</option>
        @foreach($productos as $p)
            <option value="{{ $p->id }}"
                    data-costo="{{ $p->costo ?? 0 }}"
                    @selected(old('producto_id')==$p->id)>
                {{ $p->nombre }}
            </option>
        @endforeach
    </select>

    <label>Cantidad</label>
    <input type="number" id="cantidad" name="cantidad" min="1" value="{{ old('cantidad',1) }}" required>

    <label>Fecha inicio</label>
    <input type="datetime-local" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required>

    <label>Precio total</label>
    <input type="number" id="precio_total" step="0.01" name="precio_total"
           value="{{ old('precio_total',0) }}" required readonly>

    {{-- ✅ NUEVO: OBRA (se carga según cliente) --}}
    <label>Obra</label>
    <select name="obra_id" id="obra_id" required>
        <option value="">-- Seleccione obra --</option>
    </select>

    <input type="hidden" name="estado" value="activo">

    <div class="form-actions">
        <button class="btn">Guardar</button>
        <a class="btn-secondary" href="{{ route('arriendos.index') }}">Volver</a>
    </div>

    @if($errors->any())
        <div class="alert danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const producto = document.getElementById('producto_id');
    const cantidad = document.getElementById('cantidad');
    const precioTotal = document.getElementById('precio_total');

    const cliente = document.getElementById('cliente_id');
    const obraSelect = document.getElementById('obra_id');

    // ✅ si viene con old('obra_id') (por errores de validación)
    const oldObraId = @json(old('obra_id'));

    function recalcular() {
        const opt = producto.options[producto.selectedIndex];
        const costo = parseFloat(opt?.dataset?.costo || 0);
        const cant = parseFloat(cantidad.value || 0);

        const total = costo * cant;
        precioTotal.value = total.toFixed(2);
    }

    async function cargarObras() {
        obraSelect.innerHTML = `<option value="">-- Seleccione obra --</option>`;

        const clienteId = cliente.value;
        if (!clienteId) return;

        try {
            // ✅ Endpoint que debes crear: /clientes/{id}/obras
            const res = await fetch(`/clientes/${clienteId}/obras`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) throw new Error('No se pudieron cargar las obras');

            const obras = await res.json();

            obras.forEach(o => {
                const op = document.createElement('option');
                op.value = o.id;
                op.textContent = o.direccion;
                if (oldObraId && String(oldObraId) === String(o.id)) {
                    op.selected = true;
                }
                obraSelect.appendChild(op);
            }); 

        } catch (e) {
            console.error(e);
            // Si falla, al menos avisamos visualmente
            obraSelect.innerHTML = `<option value="">(Error cargando obras)</option>`;
        }
    }

    producto.addEventListener('change', recalcular);
    cantidad.addEventListener('input', recalcular);

    cliente.addEventListener('change', () => {
        // al cambiar cliente, recargamos obras y limpiamos selección anterior
        cargarObras();
    });

    recalcular();
    cargarObras();
});
</script>
@endsection
