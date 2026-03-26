@extends('layouts.app')
@section('title','Nueva solicitud arriendo')
@section('header','Nueva solicitud arriendo')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@500;700;800&display=swap');

/* =====================================================
   Encapsulado: .rent-request
   Estilo: Pro SaaS (limpio, moderno, alto contraste)
   ===================================================== */
.rent-request{
  --bg: #eef3fb;
  --text: #0a1428;
  --muted: #4f607b;

  --card: rgba(255,255,255,.87);
  --card-2: #f1f7ff;

  --border: rgba(103,126,164,.28);
  --border-strong: rgba(80,109,156,.38);
  --shadow: 0 24px 56px rgba(5, 20, 48, .18);
  --shadow-sm: 0 16px 34px rgba(5, 20, 48, .12);
  --glow: inset 0 1px 0 rgba(255,255,255,.84), inset 0 -1px 0 rgba(103,126,164,.14);

  --primary: #1f67f3;
  --primary-700: #144ac1;
  --primary-800: #11388f;
  --accent: #00a9b6;
  --primary-bg: rgba(31,103,243,.10);
  --primary-br: rgba(31,103,243,.30);

  --danger-bg: rgba(239,68,68,.10);
  --danger-br: rgba(239,68,68,.22);
  --danger-tx: #7f1d1d;

  --radius: 20px;
  --radius-sm: 14px;

  font-family: "Manrope", "Space Grotesk", "Segoe UI", sans-serif;
  color: var(--text);
  position: relative;
  isolation: isolate;
}

.rent-request *{ box-sizing: border-box; }

.rent-request::before,
.rent-request::after{
  content: "";
  position: absolute;
  pointer-events: none;
  border-radius: 999px;
  z-index: -1;
  filter: blur(28px);
}
.rent-request::before{
  width: 380px;
  height: 380px;
  top: -80px;
  left: -80px;
  background: radial-gradient(circle at 35% 35%, rgba(31,103,243,.30), rgba(31,103,243,0));
}
.rent-request::after{
  width: 300px;
  height: 300px;
  top: -40px;
  right: -40px;
  background: radial-gradient(circle at 50% 50%, rgba(0,169,182,.22), rgba(0,169,182,0));
}

/* Layout */
.rent-request .wrap{
  min-height: calc(100vh - 180px);
  display:grid;
  place-items:center;
  padding: 22px 14px;
  background:
    radial-gradient(900px 380px at 20% 0%, rgba(31,103,243,.18), transparent 60%),
    radial-gradient(800px 420px at 90% 10%, rgba(0,169,182,.12), transparent 55%),
    linear-gradient(180deg, #f5f9ff 0%, #eef3fb 55%, #eef3fb 100%);
  border-radius: 24px;
  border: 1px solid var(--border);
  box-shadow: 0 22px 52px rgba(5,20,48,.16), inset 0 1px 0 rgba(255,255,255,.65);
}

.rent-request .shell{ width: 100%; max-width: 1040px; }

/* Alert */
.rent-request .alert{
  border-radius: 16px;
  padding: 12px 14px;
  border: 1px solid var(--border-strong);
  background: linear-gradient(180deg, #ffffff, #f6f9ff);
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
  background: linear-gradient(155deg, rgba(255,255,255,.95), rgba(239,247,255,.88));
  border: 1px solid var(--border-strong);
  border-radius: 24px;
  box-shadow: var(--shadow-sm);
  padding: 18px 18px;
  margin-bottom: 14px;
  position: relative;
  overflow: hidden;
  transform-style: preserve-3d;
}

.rent-request .hero::after{
  content:"";
  position:absolute;
  inset:auto -120px -160px auto;
  width: 420px;
  height: 420px;
  background: radial-gradient(circle at 30% 30%, rgba(31,103,243,.22), transparent 60%);
  transform: rotate(12deg);
  pointer-events:none;
}
.rent-request .hero::before{
  content: "";
  position:absolute;
  inset: 0;
  pointer-events:none;
  background: linear-gradient(125deg, rgba(255,255,255,.42), rgba(255,255,255,0) 36%);
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
  font-weight: 800;
  letter-spacing: .2px;
  font-family: "Space Grotesk", "Manrope", sans-serif;
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
  border-radius: 24px;
  box-shadow: var(--shadow);
  overflow:hidden;
  backdrop-filter: blur(11px);
  position: relative;
  transform-style: preserve-3d;
}

.rent-request .card::before{
  content: "";
  position:absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(130deg, rgba(255,255,255,.46), rgba(255,255,255,0) 40%);
}

.rent-request .card-head{
  padding: 14px 18px;
  border-bottom: 1px solid var(--border);
  display:flex;
  align-items:center;
  justify-content:space-between;
  background: linear-gradient(180deg, #ffffff, #f3f8ff);
}

.rent-request .pill{
  display:inline-flex;
  align-items:center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: .25px;
  color: rgba(20,74,193,.98);
  background: var(--primary-bg);
  border: 1px solid var(--primary-br);
  box-shadow: inset 0 1px 0 rgba(255,255,255,.75), 0 8px 16px rgba(20,74,193,.13);
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
  font-weight: 800;
  color: rgba(15,23,42,.92);
}

.rent-request .control{
  width: 100%;
  padding: 12px 12px;
  border-radius: 14px;
  border: 1px solid var(--border-strong);
  background: linear-gradient(180deg, #ffffff, #f8fbff);
  color: rgba(15,23,42,.92);
  outline: none;
  box-shadow: var(--glow);
  transition: border-color .16s ease, box-shadow .16s ease, transform .12s ease, background .16s ease;
}

.rent-request .control:hover{
  border-color: rgba(37,99,235,.24);
}

.rent-request .control:focus{
  border-color: rgba(31,103,243,.62);
  box-shadow: 0 0 0 4px rgba(31,103,243,.16), 0 12px 24px rgba(8,33,79,.12);
  transform: translateY(-1px);
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
  font-weight: 800;
  letter-spacing: .2px;
  border: 1px solid transparent;
  cursor:pointer;
  text-decoration:none;
  transition: transform .12s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease, filter .2s ease;
  user-select:none;
  white-space: nowrap;
}

.rent-request .btn:active{ transform: translateY(1px); }

.rent-request .btn-ghost{
  background: linear-gradient(180deg, #ffffff, #eef5ff);
  border-color: var(--border-strong);
  color: rgba(15,23,42,.90);
  box-shadow: 0 10px 20px rgba(5,20,48,.10), inset 0 1px 0 rgba(255,255,255,.85);
}
.rent-request .btn-ghost:hover{
  transform: translateY(-2px);
  box-shadow: 0 15px 28px rgba(5,20,48,.16), inset 0 1px 0 rgba(255,255,255,.92);
  border-color: rgba(31,103,243,.32);
}

/* ✅ BOTÓN PRINCIPAL (AZUL) */
.rent-request .btn-primary{
  background: linear-gradient(140deg, var(--primary-700), var(--primary) 62%, #2fafff);
  border-color: rgba(31,103,243,.40);
  color: #fff;
  box-shadow: 0 16px 34px rgba(20,74,193,.28), inset 0 1px 0 rgba(255,255,255,.22);
}
.rent-request .btn-primary:hover{
  background: linear-gradient(140deg, var(--primary-800), var(--primary-700) 60%, var(--primary));
  box-shadow: 0 20px 42px rgba(20,74,193,.34), inset 0 1px 0 rgba(255,255,255,.28);
  transform: translateY(-2px);
  filter: saturate(1.06);
}
.rent-request .btn-primary:focus{
  outline: none;
  box-shadow: 0 0 0 4px rgba(31,103,243,.18), 0 20px 42px rgba(20,74,193,.34);
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
  border-radius: 24px;
  box-shadow: var(--shadow-sm);
  padding: 16px;
  height: fit-content;
  position: sticky;
  top: 16px;
  backdrop-filter: blur(11px);
  overflow: hidden;
}
.rent-request .side::before{
  content: "";
  position:absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(135deg, rgba(255,255,255,.42), rgba(255,255,255,0) 44%);
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
  border: 1px solid var(--border);
  background: linear-gradient(165deg, #fdfefe, var(--card-2));
  border-radius: 16px;
  padding: 12px;
  box-shadow: 0 10px 22px rgba(5,20,48,.08), inset 0 1px 0 rgba(255,255,255,.8);
  transition: transform .16s ease, box-shadow .22s ease;
}
.rent-request .kpi .box:hover{
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(5,20,48,.12), inset 0 1px 0 rgba(255,255,255,.86);
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

@media (max-width: 700px){
  .rent-request .wrap{
    padding: 14px 8px;
    border-radius: 16px;
  }
  .rent-request .hero,
  .rent-request .card,
  .rent-request .side{
    border-radius: 18px;
  }
  .rent-request .btn{
    min-height: 42px;
  }
}
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
