@extends('layouts.app')
@section('title','Nueva solicitud arriendo')
@section('header','Nueva solicitud arriendo')

@section('content')

<style>
/* =====================================================
   Encapsulado: .rent-request
   Estilo: Pro SaaS (limpio, moderno, alto contraste)
   ===================================================== */
.rent-request{
  --bg: #f6f8fc;
  --text: #0f172a;
  --muted: #64748b;

  --card: #ffffff;
  --card-2: #f8fafc;

  --border: rgba(15,23,42,.10);
  --shadow: 0 18px 45px rgba(2, 8, 23, .10);
  --shadow-sm: 0 10px 26px rgba(2, 8, 23, .08);

  --primary: #2563eb;
  --primary-700: #1d4ed8;
  --primary-800: #1e40af;
  --primary-bg: rgba(37,99,235,.12);
  --primary-br: rgba(37,99,235,.22);

  --danger-bg: rgba(239,68,68,.10);
  --danger-br: rgba(239,68,68,.22);
  --danger-tx: #7f1d1d;

  --radius: 18px;
  --radius-sm: 14px;

  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
  color: var(--text);
}

.rent-request *{ box-sizing: border-box; }

/* Layout */
.rent-request .wrap{
  min-height: calc(100vh - 180px);
  display:grid;
  place-items:center;
  padding: 28px 16px;
  background:
    radial-gradient(900px 380px at 20% 0%, rgba(37,99,235,.12), transparent 60%),
    radial-gradient(800px 420px at 90% 10%, rgba(99,102,241,.10), transparent 55%),
    linear-gradient(180deg, #f7f9ff 0%, #f6f8fc 55%, #f6f8fc 100%);
  border-radius: 22px;
}

.rent-request .shell{ width: 100%; max-width: 1040px; }

/* Alert */
.rent-request .alert{
  border-radius: 18px;
  padding: 12px 14px;
  border: 1px solid var(--border);
  background: #ffffff;
  box-shadow: var(--shadow-sm);
  margin-bottom: 14px;
}

.rent-request .alert-danger{
  background: var(--danger-bg);
  border-color: var(--danger-br);
  color: var(--danger-tx);
}

.rent-request .alert-title{
  font-weight: 950;
  font-size: 13px;
  letter-spacing: .2px;
  margin-bottom: 8px;
}

.rent-request .alert ul{
  margin: 0;
  padding-left: 18px;
  font-size: 13px;
}

/* Hero */
.rent-request .hero{
  background: linear-gradient(180deg, #ffffff, #fbfcff);
  border: 1px solid var(--border);
  border-radius: 22px;
  box-shadow: var(--shadow-sm);
  padding: 18px 18px;
  margin-bottom: 14px;
  position: relative;
  overflow: hidden;
}

.rent-request .hero::after{
  content:"";
  position:absolute;
  inset:auto -120px -160px auto;
  width: 420px;
  height: 420px;
  background: radial-gradient(circle at 30% 30%, rgba(37,99,235,.18), transparent 60%);
  transform: rotate(12deg);
  pointer-events:none;
}

.rent-request .hero-row{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap: 12px;
  position: relative;
  z-index: 1;
}

.rent-request .hero h2{
  margin:0 0 6px;
  font-size: 20px;
  font-weight: 950;
  letter-spacing: .2px;
}

.rent-request .hero p{
  margin:0;
  font-size: 13px;
  color: var(--muted);
  line-height: 1.55;
  max-width: 70ch;
}

/* Grid */
.rent-request .grid{
  display:grid;
  grid-template-columns: 1.2fr .8fr;
  gap: 14px;
}
@media (max-width: 860px){
  .rent-request .grid{ grid-template-columns: 1fr; }
  .rent-request .hero-row{ flex-direction: column; }
}

/* Card principal */
.rent-request .card{
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 22px;
  box-shadow: var(--shadow);
  overflow:hidden;
}

.rent-request .card-head{
  padding: 14px 18px;
  border-bottom: 1px solid var(--border);
  display:flex;
  align-items:center;
  justify-content:space-between;
  background: linear-gradient(180deg, #ffffff, #fbfcff);
}

.rent-request .pill{
  display:inline-flex;
  align-items:center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 950;
  letter-spacing: .25px;
  color: rgba(37,99,235,.98);
  background: var(--primary-bg);
  border: 1px solid var(--primary-br);
}

.rent-request .card-body{ padding: 18px; }
.rent-request .form{ display:grid; gap: 12px; }

.rent-request .row2{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
@media (max-width: 640px){
  .rent-request .row2{ grid-template-columns: 1fr; }
}

/* Fields */
.rent-request .field label{
  display:flex;
  align-items:center;
  gap: 8px;
  margin-bottom: 6px;
  font-size: 13px;
  font-weight: 950;
  color: rgba(15,23,42,.92);
}

.rent-request .control{
  width: 100%;
  padding: 12px 12px;
  border-radius: 14px;
  border: 1px solid rgba(15,23,42,.12);
  background: #ffffff;
  color: rgba(15,23,42,.92);
  outline: none;
  box-shadow: 0 1px 0 rgba(2,8,23,.03);
  transition: border-color .16s ease, box-shadow .16s ease, transform .05s ease, background .16s ease;
}

.rent-request .control:hover{
  border-color: rgba(37,99,235,.24);
}

.rent-request .control:focus{
  border-color: rgba(37,99,235,.52);
  box-shadow: 0 0 0 4px rgba(37,99,235,.16);
}

.rent-request select.control{
  appearance: none;
  background-image:
    linear-gradient(45deg, transparent 50%, rgba(15,23,42,.55) 50%),
    linear-gradient(135deg, rgba(15,23,42,.55) 50%, transparent 50%),
    linear-gradient(to right, transparent, transparent);
  background-position:
    calc(100% - 18px) 50%,
    calc(100% - 12px) 50%,
    100% 0;
  background-size: 6px 6px, 6px 6px, 2.5em 2.5em;
  background-repeat: no-repeat;
  padding-right: 44px;
}

.rent-request .hint{
  margin-top: 6px;
  font-size: 12px;
  color: var(--muted);
  line-height: 1.4;
}

/* Botones */
.rent-request .btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap: 8px;
  padding: 10px 14px;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 950;
  letter-spacing: .2px;
  border: 1px solid transparent;
  cursor:pointer;
  text-decoration:none;
  transition: transform .06s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease, color .18s ease;
  user-select:none;
  white-space: nowrap;
}

.rent-request .btn:active{ transform: translateY(1px); }

.rent-request .btn-ghost{
  background: #ffffff;
  border-color: rgba(15,23,42,.12);
  color: rgba(15,23,42,.90);
}
.rent-request .btn-ghost:hover{
  box-shadow: 0 12px 26px rgba(2,8,23,.10);
  border-color: rgba(37,99,235,.22);
}

/* ✅ BOTÓN PRINCIPAL (AZUL) */
.rent-request .btn-primary{
  background: linear-gradient(180deg, var(--primary), var(--primary-700));
  border-color: rgba(37,99,235,.35);
  color: #fff;
  box-shadow: 0 14px 34px rgba(37,99,235,.20);
}
.rent-request .btn-primary:hover{
  background: linear-gradient(180deg, var(--primary-700), var(--primary-800));
  box-shadow: 0 18px 44px rgba(37,99,235,.24);
}
.rent-request .btn-primary:focus{
  outline: none;
  box-shadow: 0 0 0 4px rgba(37,99,235,.18), 0 18px 44px rgba(37,99,235,.22);
}

.rent-request .footer{
  display:flex;
  justify-content:flex-end;
  padding-top: 6px;
}

/* Side panel */
.rent-request .side{
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 22px;
  box-shadow: var(--shadow-sm);
  padding: 16px;
  height: fit-content;
  position: sticky;
  top: 16px;
}
@media (max-width: 860px){
  .rent-request .side{ position: static; }
}

.rent-request .side h3{
  margin:0 0 8px;
  font-size: 12px;
  letter-spacing: .28px;
  text-transform: uppercase;
  font-weight: 950;
  color: rgba(15,23,42,.80);
}

.rent-request .side p{
  margin:0 0 10px;
  font-size: 13px;
  line-height: 1.55;
  color: var(--muted);
}

.rent-request .kpi{
  display:grid;
  gap: 10px;
  margin-top: 10px;
}

.rent-request .kpi .box{
  border: 1px solid rgba(15,23,42,.10);
  background: linear-gradient(180deg, #fbfcff, var(--card-2));
  border-radius: 16px;
  padding: 12px;
}

.rent-request .kpi .box strong{
  display:block;
  font-size: 12px;
  font-weight: 950;
  color: rgba(15,23,42,.86);
  margin-bottom: 4px;
}

.rent-request .kpi .box span{
  font-size: 12px;
  color: var(--muted);
}

/* Note */
.rent-request .note-bottom{
  margin-top: 12px;
  text-align:center;
  color: var(--muted);
  font-size: 12.5px;
}

/* Loading */
.rent-request .loading{ opacity: .70; }
</style>

<div class="rent-request">
  <div class="wrap">
    <div class="shell">

      @if($errors->any())
        <div class="alert alert-danger">
          <div class="alert-title">Revisa estos campos antes de continuar</div>
          <ul>
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="hero">
        <div class="hero-row">
          <div>
            <h2>Nueva solicitud de arriendo</h2>
            <p>
              Crea primero el contrato (PADRE). Luego, en el siguiente paso, podrás agregar productos al arriendo.
            </p>
          </div>

          <div class="actions-top">
            <a class="btn btn-ghost" href="{{ route('arriendos.index') }}">Volver</a>
          </div>
        </div>
      </div>

      <div class="grid">
        <div class="card">
          <div class="card-head">
            <span class="pill">PASO 1 · DATOS GENERALES</span>
          </div>

          <div class="card-body">
            <form class="form" method="POST" action="{{ route('arriendos.store') }}">
              @csrf

              <div class="field">
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="control" required>
                  <option value="">Seleccione...</option>
                  @foreach($clientes as $c)
                    <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                      {{ $c->nombre }}
                    </option>
                  @endforeach
                </select>
                <div class="hint">Selecciona el cliente para listar sus obras.</div>
              </div>

              <div class="row2">
                <div class="field">
                  <label for="fecha_inicio">Fecha de inicio</label>
                  <input id="fecha_inicio" class="control" type="datetime-local" name="fecha_inicio" required
                         value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}">
                  <div class="hint">Puedes ajustar la hora si es necesario.</div>
                </div>

                <div class="field">
                  <label for="obra_id">Obra (opcional)</label>
                  <select name="obra_id" id="obra_id" class="control">
                    <option value="">Seleccione cliente primero...</option>
                  </select>
                  <div class="hint">Si no aplica, déjalo vacío.</div>
                </div>
              </div>

              <div class="footer">
                {{-- ✅ Botón azul --}}
                <button type="submit" class="btn btn-primary">Siguiente</button>
              </div>

            </form>
          </div>
        </div>

        <aside class="side">
          <h3>Resumen</h3>
          <p>
            Este paso crea el arriendo <strong>PADRE</strong> (contrato). En el siguiente paso agregas
            los productos (hijos) y continúas el flujo.
          </p>

          <div class="kpi">
            <div class="box">
              <strong>Cliente</strong>
              <span>Obligatorio</span>
            </div>
            <div class="box">
              <strong>Fecha inicio</strong>
              <span>Obligatorio</span>
            </div>
            <div class="box">
              <strong>Obra</strong>
              <span>Opcional</span>
            </div>
          </div>
        </aside>
      </div>

      <div class="note-bottom">
        Nota: solo estás creando el contrato. Luego podrás añadir productos al arriendo.
      </div>

    </div>
  </div>
</div>

<script>
(function () {
  const clienteSelect = document.getElementById('cliente_id');
  const obraSelect = document.getElementById('obra_id');

  function setLoading(isLoading){
    obraSelect.classList.toggle('loading', isLoading);
    obraSelect.disabled = isLoading;
  }

  function setOptions(html){
    obraSelect.innerHTML = html;
  }

  clienteSelect.addEventListener('change', function () {
    const clienteId = this.value;

    if (!clienteId) {
      setLoading(false);
      setOptions('<option value="">Seleccione cliente primero...</option>');
      return;
    }

    setLoading(true);
    setOptions('<option value="">Cargando...</option>');

    fetch(`/clientes/${clienteId}/obras`)
      .then(res => res.json())
      .then(data => {
        setOptions('<option value="">Seleccione...</option>');
        data.forEach(o => {
          obraSelect.innerHTML += `<option value="${o.id}">${o.direccion}</option>`;
        });
      })
      .catch(() => {
        setOptions('<option value="">No se pudo cargar (intente de nuevo)</option>');
      })
      .finally(() => setLoading(false));
  });
})();
</script>

@endsection
