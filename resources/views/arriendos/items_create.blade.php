@extends('layouts.app')
@section('title','Agregar producto')
@section('header','Agregar producto')

@section('content')

@if(session('success'))
  <div class="alert success">{{ session('success') }}</div>
@endif

@if($errors->any())
  <div class="alert danger">
    <ul style="margin:0; padding-left:18px;">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
  <h2>Agregar producto - Arriendo #{{ $arriendo->id }}</h2>
  <a class="btn-sm" href="{{ route('arriendos.ver', $arriendo) }}">Volver</a>
</div>

<div style="background:#fff; padding:14px; border-radius:10px; margin-bottom:12px;">
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
    <div><strong>Cliente:</strong> {{ $arriendo->cliente->nombre ?? '—' }}</div>
    <div><strong>Obra: </strong>{{ $arriendo->obra? $arriendo->obra->direccion . ' - ' . $arriendo->obra->detalle
    : '—'}}</div>
    <div><strong>Inicio contrato:</strong> {{ $arriendo->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}</div>
    <div><strong>Estado:</strong> {{ ucfirst($arriendo->estado) }}</div>
  </div>
</div>

<form method="POST" action="{{ route('arriendos.items.store', $arriendo) }}"
      style="background:#fff; padding:14px; border-radius:10px; max-width:520px;">
  @csrf

  <div style="margin-bottom:10px;">
    <label style="display:block; font-size:13px;">Producto</label>

    <select class="input" name="producto_id" id="producto_id" required style="width:100%;">
      <option value="">Seleccione...</option>

      @foreach($productos as $p)
        <option value="{{ $p->id }}"
                data-cost="{{ (float)($p->costo ?? 0) }}"
                data-stock="{{ (int)($p->cantidad ?? 0) }}"
                {{ old('producto_id') == $p->id ? 'selected' : '' }}>
          {{ $p->nombre }}
          (Stock: {{ (int)($p->cantidad ?? 0) }})
          (Tarifa: ${{ number_format((float)($p->costo ?? 0), 2) }})
        </option>
      @endforeach
    </select>
  </div>

  <div style="margin-bottom:10px;">
    <label style="display:block; font-size:13px;">Cantidad</label>
    <input class="input" type="number" name="cantidad" id="cantidad"
           min="1" required style="width:100%;"
           value="{{ old('cantidad', 1) }}">
    {{-- ✅ mensaje de error stock --}}
    <div id="stockError" style="display:none; margin-top:6px; color:#b00020; font-size:13px;"></div>
  </div>

  {{-- ✅ NUEVO: Cobrar domingos --}}
  <div style="margin-bottom:10px;">
    <label style="display:block; font-size:13px;">¿Cobrar domingos?</label>
    <select class="input" name="cobra_domingo" id="cobra_domingo" required style="width:100%;">
      <option value="1" {{ old('cobra_domingo', '0') == '1' ? 'selected' : '' }}>Sí, cobrar domingos</option>
      <option value="0" {{ old('cobra_domingo', '0') == '0' ? 'selected' : '' }}>No, NO cobrar domingos</option>
    </select>
    <div style="font-size:12px; color:#666; margin-top:4px;">
      Si eliges <strong>No</strong>, los domingos no se contarán como días cobrables para este producto.
    </div>
  </div>

  <div style="margin-bottom:10px; font-size:13px;">
    <div style="display:flex; justify-content:space-between; gap:10px;">
      <div>
        <span style="color:#666;">Tarifa/día:</span>
        <strong id="tarifaText">$0.00</strong>
      </div>
      <div>
        <span style="color:#666;">Total por día:</span>
        <strong id="totalDiaText">$0.00</strong>
      </div>
    </div>
    <div style="font-size:12px; color:#888; margin-top:4px;">
      (Tarifa × Cantidad)
    </div>
  </div>

  <div style="margin-bottom:10px;">
    <label style="display:block; font-size:13px;">Fecha inicio del producto (opcional)</label>
    <input class="input" type="datetime-local" name="fecha_inicio_item" style="width:100%;"
           value="{{ old('fecha_inicio_item') }}">
    <div style="font-size:12px; color:#666; margin-top:4px;">
      Si la dejas vacía, usará la fecha inicio del contrato.
    </div>
  </div>

  <div style="display:flex; justify-content:flex-end;">
    <button type="submit" id="btnGuardar" class="btn-sm warning">Guardar producto</button>
  </div>
</form>

<script>
(function(){
  const sel = document.getElementById('producto_id');
  const qty = document.getElementById('cantidad');
  const tarifaText = document.getElementById('tarifaText');
  const totalDiaText = document.getElementById('totalDiaText');
  const stockError = document.getElementById('stockError');
  const btnGuardar = document.getElementById('btnGuardar');
  const form = sel.closest('form');

  function money(n){
    n = Number(n || 0);
    return '$' + n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
  }

  function getSelectedData(){
    const opt = sel.options[sel.selectedIndex];
    const tarifa = opt ? Number(opt.dataset.cost || opt.getAttribute('data-cost') || 0) : 0;
    const stock  = opt ? parseInt(opt.dataset.stock || opt.getAttribute('data-stock') || '0', 10) : 0;
    return { tarifa, stock };
  }

  function validarStock(){
    if (!sel.value) {
      stockError.style.display = 'none';
      stockError.textContent = '';
      btnGuardar.disabled = false;
      qty.removeAttribute('max');
      return true;
    }

    const { stock } = getSelectedData();
    const cantidad = parseInt(qty.value || '0', 10);

    // ✅ ajusta max del input para ayudar al usuario
    if (!Number.isNaN(stock) && stock >= 0) qty.max = stock;

    if (cantidad > stock) {
      stockError.style.display = 'block';
      stockError.textContent = `No hay suficiente stock. Disponible: ${stock}. Estás intentando alquilar: ${cantidad}.`;
      btnGuardar.disabled = true;
      return false;
    }

    stockError.style.display = 'none';
    stockError.textContent = '';
    btnGuardar.disabled = false;
    return true;
  }

  function calc(){
    const { tarifa } = getSelectedData();
    const cantidad = Number(qty.value || 0);

    tarifaText.textContent = money(tarifa);
    totalDiaText.textContent = money(tarifa * cantidad);

    validarStock();
  }

  sel.addEventListener('change', calc);
  qty.addEventListener('input', calc);

  form.addEventListener('submit', function(e){
    if (!validarStock()) e.preventDefault();
  });

  // ✅ inicial
  calc();
})();
</script>

@endsection
