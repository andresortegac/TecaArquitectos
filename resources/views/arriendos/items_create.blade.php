@extends('layouts.app')
@section('title','Agregar producto')
@section('header','Agregar producto')

@section('content')

<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');

  .items3d{
    --text: #0a1428;
    --muted: #53617b;
    --line: rgba(110,132,166,.30);
    --line-strong: rgba(88,116,161,.42);
    --card: rgba(255,255,255,.88);
    --surface: linear-gradient(155deg, rgba(255,255,255,.94), rgba(238,245,255,.88));
    --brand: #1f67f3;
    --brand-700: #144ac1;
    --danger-bg: rgba(239,68,68,.12);
    --danger-line: rgba(239,68,68,.28);
    --danger-text: #8f1f1f;
    --radius: 22px;
    --shadow-lg: 0 26px 58px rgba(7,22,49,.20);
    --shadow-md: 0 16px 34px rgba(7,22,49,.14);
    --glow: inset 0 1px 0 rgba(255,255,255,.86), inset 0 -1px 0 rgba(88,116,161,.14);
    font-family: "Manrope", "Space Grotesk", "Segoe UI", sans-serif;
    color: var(--text);
    position: relative;
    isolation: isolate;
  }
  .items3d *{ box-sizing:border-box; }
  .items3d::before,
  .items3d::after{
    content: "";
    position: absolute;
    border-radius: 999px;
    filter: blur(28px);
    pointer-events: none;
    z-index: -1;
  }
  .items3d::before{
    width: 360px;
    height: 360px;
    top: -80px;
    left: -80px;
    background: radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0));
  }
  .items3d::after{
    width: 280px;
    height: 280px;
    top: -40px;
    right: -30px;
    background: radial-gradient(circle at 50% 50%, rgba(0,167,179,.22), rgba(0,167,179,0));
  }
  .items3d-shell{
    background:
      radial-gradient(920px 420px at 15% 0%, rgba(31,103,243,.16), transparent 58%),
      radial-gradient(760px 380px at 95% 5%, rgba(0,167,179,.11), transparent 58%),
      linear-gradient(180deg, #f6f9ff 0%, #edf3fb 100%);
    border: 1px solid var(--line);
    border-radius: 24px;
    box-shadow: var(--shadow-lg), inset 0 1px 0 rgba(255,255,255,.68);
    padding: 16px;
  }
  .items3d-alert{
    border: 1px solid var(--line-strong);
    background: linear-gradient(180deg, #fff, #f6f9ff);
    border-radius: 16px;
    padding: 12px 14px;
    margin-bottom: 12px;
    box-shadow: var(--shadow-md);
  }
  .items3d-alert.danger{
    border-color: var(--danger-line);
    background: var(--danger-bg);
    color: var(--danger-text);
  }
  .items3d-alert ul{
    margin: 0;
    padding-left: 18px;
  }
  .items3d-hero{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap: 12px;
    flex-wrap:wrap;
    border: 1px solid var(--line-strong);
    border-radius: var(--radius);
    background: var(--surface);
    box-shadow: var(--shadow-md);
    padding: 16px;
    position: relative;
    overflow: hidden;
    margin-bottom: 12px;
  }
  .items3d-hero::before{
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(128deg, rgba(255,255,255,.46), rgba(255,255,255,0) 38%);
    pointer-events: none;
  }
  .items3d-title{
    margin: 0;
    font-size: 21px;
    font-weight: 800;
    letter-spacing: .2px;
    font-family: "Space Grotesk", "Manrope", sans-serif;
  }
  .items3d-sub{
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 13px;
  }
  .items3d-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height: 42px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid var(--line-strong);
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    color: var(--text);
    background: linear-gradient(180deg, #fff, #eef5ff);
    box-shadow: 0 10px 20px rgba(7,22,49,.12), inset 0 1px 0 rgba(255,255,255,.8);
    transition: transform .16s ease, box-shadow .2s ease, filter .2s ease;
  }
  .items3d-btn:hover{
    transform: translateY(-2px);
    box-shadow: 0 14px 26px rgba(7,22,49,.16), inset 0 1px 0 rgba(255,255,255,.92);
  }
  .items3d-layout{
    display:grid;
    grid-template-columns: 1.2fr .8fr;
    gap: 12px;
  }
  @media (max-width: 960px){
    .items3d-layout{ grid-template-columns: 1fr; }
  }
  .items3d-card{
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--card);
    box-shadow: var(--shadow-md);
    backdrop-filter: blur(12px);
    overflow: hidden;
    position: relative;
  }
  .items3d-card::before{
    content: "";
    position:absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(130deg, rgba(255,255,255,.45), rgba(255,255,255,0) 40%);
  }
  .items3d-head{
    border-bottom: 1px solid var(--line);
    padding: 14px 16px;
    background: linear-gradient(180deg, #fff, #f2f8ff);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .3px;
    text-transform: uppercase;
    color: var(--muted);
  }
  .items3d-body{
    padding: 16px;
  }
  .items3d-meta{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  .items3d-meta-box{
    border: 1px solid var(--line);
    border-radius: 14px;
    background: linear-gradient(180deg, rgba(255,255,255,.97), rgba(238,245,255,.92));
    box-shadow: 0 10px 20px rgba(7,22,49,.08), inset 0 1px 0 rgba(255,255,255,.85);
    padding: 10px;
  }
  .items3d-k{
    display:block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .35px;
    color: var(--muted);
    margin-bottom: 4px;
    font-weight: 800;
  }
  .items3d-v{
    display:block;
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
  }
  .items3d-form{
    display:grid;
    gap: 12px;
  }
  .items3d-row2{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  @media (max-width: 720px){
    .items3d-meta,
    .items3d-row2{
      grid-template-columns: 1fr;
    }
  }
  .items3d-field label{
    display:block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 800;
    color: rgba(10,20,40,.95);
  }
  .items3d-control{
    width: 100%;
    min-height: 44px;
    border-radius: 12px;
    border: 1px solid var(--line-strong);
    background: linear-gradient(180deg, #fff, #f7fbff);
    color: var(--text);
    padding: 10px 12px;
    outline:none;
    box-shadow: var(--glow);
    transition: border-color .18s ease, box-shadow .2s ease, transform .16s ease;
  }
  .items3d-control:focus{
    border-color: rgba(31,103,243,.65);
    box-shadow: 0 0 0 4px rgba(31,103,243,.14), 0 12px 24px rgba(7,22,49,.10);
    transform: translateY(-1px);
  }
  .items3d-hint{
    margin-top: 5px;
    font-size: 12px;
    color: var(--muted);
    line-height: 1.35;
  }
  .items3d-error{
    display:none;
    margin-top:6px;
    color:#b00020;
    font-size:13px;
    font-weight: 700;
  }
  .items3d-totals{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    border: 1px solid var(--line);
    border-radius: 14px;
    background: linear-gradient(160deg, rgba(31,103,243,.10), rgba(255,255,255,.95));
    padding: 12px;
  }
  .items3d-totals span{
    display:block;
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .32px;
    font-weight: 800;
  }
  .items3d-totals strong{
    display:block;
    margin-top: 4px;
    font-size: 16px;
    font-weight: 800;
    color: var(--text);
    font-family: "Space Grotesk", "Manrope", sans-serif;
  }
  .items3d-footer{
    display:flex;
    justify-content:flex-end;
  }
  .items3d-btn-primary{
    border: 1px solid rgba(31,103,243,.44);
    color: #fff;
    background: linear-gradient(145deg, var(--brand-700), var(--brand) 62%, #32b5ff);
    box-shadow: 0 16px 34px rgba(20,74,193,.30), inset 0 1px 0 rgba(255,255,255,.23);
  }
  .items3d-btn-primary:hover{
    transform: translateY(-2px);
    filter: saturate(1.06);
    box-shadow: 0 20px 40px rgba(20,74,193,.36), inset 0 1px 0 rgba(255,255,255,.3);
  }
  .items3d-side p{
    margin: 0;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.5;
  }
</style>

<div class="items3d">
  <div class="items3d-shell">

    @if(session('success'))
      <div class="items3d-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="items3d-alert danger">
        <ul>
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="items3d-hero">
      <div>
        <h2 class="items3d-title">Agregar Producto al Arriendo #{{ $arriendo->id }}</h2>
        <p class="items3d-sub">Registra un ítem con control de stock en tiempo real y cálculo instantáneo de tarifa diaria.</p>
      </div>
      <a class="items3d-btn" href="{{ route('arriendos.ver', $arriendo) }}">Volver</a>
    </div>

    <div class="items3d-layout">
      <div class="items3d-card">
        <div class="items3d-head">Datos del Contrato</div>
        <div class="items3d-body">
          <div class="items3d-meta">
            <div class="items3d-meta-box">
              <span class="items3d-k">Cliente</span>
              <span class="items3d-v">{{ $arriendo->cliente->nombre ?? '-' }}</span>
            </div>
            <div class="items3d-meta-box">
              <span class="items3d-k">Obra</span>
              <span class="items3d-v">{{ $arriendo->obra ? $arriendo->obra->direccion . ' - ' . $arriendo->obra->detalle : '-' }}</span>
            </div>
            <div class="items3d-meta-box">
              <span class="items3d-k">Inicio Contrato</span>
              <span class="items3d-v">{{ $arriendo->fecha_inicio?->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
            <div class="items3d-meta-box">
              <span class="items3d-k">Estado</span>
              <span class="items3d-v">{{ ucfirst($arriendo->estado) }}</span>
            </div>
          </div>
        </div>
      </div>

      <aside class="items3d-card items3d-side">
        <div class="items3d-head">Resumen Operativo</div>
        <div class="items3d-body">
          <p>Este formulario agrega un producto hijo al contrato padre. Si el stock no alcanza, el sistema bloquea el guardado automáticamente.</p>
          <div class="items3d-meta" style="margin-top:10px;">
            <div class="items3d-meta-box">
              <span class="items3d-k">Producto</span>
              <span class="items3d-v">Obligatorio</span>
            </div>
            <div class="items3d-meta-box">
              <span class="items3d-k">Cantidad</span>
              <span class="items3d-v">Obligatorio</span>
            </div>
            <div class="items3d-meta-box">
              <span class="items3d-k">Cobro domingos</span>
              <span class="items3d-v">Configurable</span>
            </div>
            <div class="items3d-meta-box">
              <span class="items3d-k">Fecha inicio item</span>
              <span class="items3d-v">Opcional</span>
            </div>
          </div>
        </div>
      </aside>
    </div>

    <div class="items3d-card" style="margin-top:12px;">
      <div class="items3d-head">Registro del Item</div>
      <div class="items3d-body">
        <form method="POST" action="{{ route('arriendos.items.store', $arriendo) }}" class="items3d-form">
          @csrf

          <div class="items3d-field">
            <label for="producto_id">Producto</label>
            <select class="items3d-control" name="producto_id" id="producto_id" required>
              <option value="">Seleccione...</option>
              @foreach($productos as $p)
                <option value="{{ $p->id }}"
                        data-cost="{{ (float)($p->costo ?? 0) }}"
                        data-stock="{{ (int)($p->cantidad ?? 0) }}"
                        {{ old('producto_id') == $p->id ? 'selected' : '' }}>
                  {{ $p->nombre }} (Stock: {{ (int)($p->cantidad ?? 0) }}) (Tarifa: ${{ number_format((float)($p->costo ?? 0), 2) }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="items3d-row2">
            <div class="items3d-field">
              <label for="cantidad">Cantidad</label>
              <input class="items3d-control" type="number" name="cantidad" id="cantidad" min="1" required value="{{ old('cantidad', 1) }}">
              <div id="stockError" class="items3d-error"></div>
            </div>

            <div class="items3d-field">
              <label for="cobra_domingo">Cobrar domingos</label>
              <select class="items3d-control" name="cobra_domingo" id="cobra_domingo" required>
                <option value="1" {{ old('cobra_domingo', '0') == '1' ? 'selected' : '' }}>Sí, cobrar domingos</option>
                <option value="0" {{ old('cobra_domingo', '0') == '0' ? 'selected' : '' }}>No, no cobrar domingos</option>
              </select>
              <div class="items3d-hint">Si eliges No, los domingos no se contarán como días cobrables para este producto.</div>
            </div>
          </div>

          <div class="items3d-totals">
            <div>
              <span>Tarifa por día</span>
              <strong id="tarifaText">$0.00</strong>
            </div>
            <div>
              <span>Total por día</span>
              <strong id="totalDiaText">$0.00</strong>
            </div>
          </div>

          <div class="items3d-field">
            <label for="fecha_inicio_item">Fecha inicio del producto (opcional)</label>
            <input class="items3d-control" id="fecha_inicio_item" type="datetime-local" name="fecha_inicio_item" value="{{ old('fecha_inicio_item') }}">
            <div class="items3d-hint">Si la dejas vacía, usará la fecha de inicio del contrato.</div>
          </div>

          <div class="items3d-footer">
            <button type="submit" id="btnGuardar" class="items3d-btn items3d-btn-primary">Guardar producto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

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

  calc();
})();
</script>

@endsection
