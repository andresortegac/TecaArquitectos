<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TECA ARQUITECTOS')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">

    <script src="{{ asset('js/app.js') }}" defer></script>

</head>
<body>
    <div class="app">
        {{-- Sidebar --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>TECA ARQUITECTOS</h2>
            </div>
            

            <nav class="sidebar-nav">
                @role('admin')
                    <a href="{{ route('dashboard') }}" class="nav-item">
                        BIENVENIDOS
                    </a>
                    
                    <div class="nav-section">Bodega / Solicitud</div>
                    <a href="{{ route('solicitudes.index') }}" class="nav-item">
                        Solicitudes
                    </a>

                    <div class="nav-section">Bodega / Inventario</div>
                    <a href="{{ route('productos.index') }}" class="nav-item">
                        Inventario bodega
                    </a>
                    <a href="{{ route('productos.create') }}" class="nav-item">
                        Ingresar a bodega
                    </a>

                    <div class="nav-section">Alquiler / Bodega</div>
                    <a href="{{ route('arriendos.index') }}" class="nav-item">
                        Generar Alquilar
                    </a>
                    <a href="{{ route('arriendos.create') }}" class="nav-item">
                        Nuevo Alquiler
                    </a>

                    <div class="nav-section">Clientes</div>
                    <a href="{{ route('clientes.index') }}" class="nav-item">
                        Lista de Clientes
                    </a>
                    <a href="{{ route('clientes.create') }}" class="nav-item">
                        Agregar Cliente Nuevo
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" id="logout-btn" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>
                @endrole

                @role('asistente')
                    <a href="{{ route('dashboard') }}" class="nav-item">
                        BIENVENIDOS
                    </a>

                    <div class="nav-section">Bodega / Inventario</div>
                    <a href="{{ route('productos.index') }}" class="nav-item">
                        Inventario bodega
                    </a>
                    <a href="{{ route('productos.create') }}" class="nav-item">
                        Ingresar a bodega
                    </a>

                    <div class="nav-section">Alquiler / Bodega</div>
                    <a href="{{ route('arriendos.index') }}" class="nav-item">
                        Generar Alquilar
                    </a>
                    <a href="{{ route('arriendos.create') }}" class="nav-item">
                        Nuevo Alquiler
                    </a>

                    <div class="nav-section">Clientes</div>
                    <a href="{{ route('clientes.index') }}" class="nav-item">
                        Lista de Clientes
                    </a>
                    <a href="{{ route('clientes.create') }}" class="nav-item">
                        Agregar Cliente Nuevo
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" id="logout-btn" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>
                @endrole

                @role('bodega')

                    <a href="{{ route('dashboard') }}" class="nav-item">
                        BIENVENIDOS
                    </a>

                    <div class="nav-section">Bodega / Solicitud</div>

                    <a href="{{ route('solicitudes.index') }}" class="nav-item">
                        Solicitudes
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" id="logout-btn" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>

                @endrole

            </nav>
        </aside>

        {{-- Contenido --}}
        <main class="content">
            <header class="topbar">
                <h1>@yield('header', 'Panel')</h1>
            </header>

            <section class="page">
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
