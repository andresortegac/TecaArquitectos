<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- CSS PERSONALIZADO -->
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">

  <title>LOGIN</title>
</head>

<body>

  <div class="login-card">

    <div class="text-center mb-4 position-relative">
      <div class="brand-badge">👷‍♂️ ALFA DIGITAL SOLUTIONS S.A.S</div>
      <div class="brand-title">Ingresar al Sistema</div>
      <div class="brand-sub">
        Acceso exclusivo para gestión de alquiler e inventario
      </div>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
      <div class="alert alert-danger-custom">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="mt-3">
      @csrf

      <div class="mb-3">
        <label class="form-label">Correo</label>
        <div class="input-wrap">
          <span class="input-icon">📧</span>
          <input type="email" name="email" class="form-control"
                 placeholder="Ej: developer@teca.arquitectos.com"
                 required autocomplete="off"
                 value="{{ old('email') }}">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña / Código</label>
        <div class="input-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" name="password"
                 class="form-control"
                 placeholder="Ingresa tu contraseña"
                 required>
        </div>
      </div>

      <button type="submit" class="btn btn-brand w-100 mt-2">
        Entrar
      </button>

      <a href="{{ url('/') }}" class="btn-soft w-100 mt-3">
        ← Volver
      </a>
    </form>

    <div class="small-note">
      Sistema de gestión de alquiler e inventario 🏗️🧱🔨
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
