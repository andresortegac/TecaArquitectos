<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TECA ARQUITECTOS')</title>

    {{-- CSS base del sistema --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/movimiento.css') }}">
    
   


    {{-- JS base --}}
    
       
    <script src="{{ asset('js/app.js') }}" defer></script>

    {{-- CSS específico por vista --}}
    @stack('styles')
</head>
<body>
    <div class="app">
        {{-- Sidebar --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/img_producto/logo.png') }}" alt="TECA Arquitectos" class="sidebar-logo">
                </a>
            </div>

            <nav class="sidebar-nav">

                @role('admin')
                    <a href="{{ route('dashboard') }}" class="nav-item">Dashboard General</a>

                    <div class="nav-section">Bodega / Solicitud</div>

                    <div class="nav-dropdown">
                        <a href="javascript:void(0)" class="nav-item dropdown-toggle" onclick="toggleMenu()">
                            Solicitud <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu" id="bodegaMenu">
                            <a href="{{ route('productos.create') }}" class="nav-item">Nuevo Producto</a>
                            <a href="{{ route('solicitudes.index') }}" class="nav-item">Solicitud</a>
                            <a href="{{ route('movimientos.create') }}" class="nav-item">Movimientos</a>
                            <a href="{{ route('productos.index') }}" class="nav-item">Inventario</a>
                            <a href="{{ route('reportes.index') }}" class="nav-item">Reportes</a>
                            <a href="{{ route('configuracion.index') }}" class="nav-item">Configuración</a>
                        </div>
                    </div>

                    <div class="nav-section">Alquiler / Bodega</div>
                    <a href="{{ route('arriendos.index') }}" class="nav-item">Generar Alquiler</a>
                    <a href="{{ route('arriendos.create') }}" class="nav-item">Nuevo Alquiler</a>

                    <div class="nav-section">Clientes</div>
                    <a href="{{ route('clientes.index') }}" class="nav-item">Lista de Clientes</a>
                    <a href="{{ route('clientes.create') }}" class="nav-item">Agregar Cliente Nuevo</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" id="logout-btn" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>
                @endrole

                @role('asistente')
                    <a href="{{ route('dashboard') }}" class="nav-item">Dashboard General</a>

                    <div class="nav-section">Alquiler / Bodega</div>
                    <a href="{{ route('arriendos.index') }}" class="nav-item">Generar Alquiler</a>
                    <a href="{{ route('arriendos.create') }}" class="nav-item">Nuevo Alquiler</a>

                    <div class="nav-section">Clientes</div>
                    <a href="{{ route('clientes.index') }}" class="nav-item">Lista de Clientes</a>
                    <a href="{{ route('clientes.create') }}" class="nav-item">Agregar Cliente Nuevo</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" id="logout-btn" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>
                @endrole

                @role('bodega')
                    <a href="{{ route('dashboard') }}" class="nav-item">Dashboard General</a>

                    <div class="nav-section">Bodega / Solicitud</div>

                    <div class="nav-dropdown">
                        <a href="javascript:void(0)" class="nav-item dropdown-toggle" onclick="toggleMenu()">
                            Solicitud <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu" id="bodegaMenu">
                            <a href="{{ route('productos.create') }}" class="nav-item">Nuevo Producto</a>
                            <a href="{{ route('solicitudes.index') }}" class="nav-item">Solicitud</a>
                            <a href="{{ route('movimientos.create') }}" class="nav-item">Movimientos</a>
                            <a href="{{ route('productos.index') }}" class="nav-item">Inventario</a>
                            <a href="{{ route('reportes.index') }}" class="nav-item">Reportes</a>
                            <a href="{{ route('configuracion.index') }}" class="nav-item">Configuración</a>
                        </div>
                    </div>

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
        <main class="principal-content">
            <header class="principal-topbar">
                <h1>@yield('header', 'Panel')</h1>
            </header>

            <section class="principal-page">
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('bodegaMenu');
            if (!menu) return;

            const parent = menu.parentElement;
            const isOpen = menu.style.display === 'flex';

            menu.style.display = isOpen ? 'none' : 'flex';
            parent.classList.toggle('open');
        }
    </script>

    {{-- Scripts específicos por vista --}}
    @yield('scripts')
    @stack('scripts')
</body>
</html>
