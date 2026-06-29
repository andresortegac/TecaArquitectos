<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'TECA ARQUITECTOS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- CSS base --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}?v={{ filemtime(public_path('css/productos.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}?v={{ filemtime(public_path('css/solicitud.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/movimiento.css') }}?v={{ filemtime(public_path('css/movimiento.css')) }}">

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>

    @stack('styles')
</head>
<body>
    <button type="button" class="mobile-menu-toggle" aria-controls="app-sidebar" aria-expanded="false">
        <span class="mobile-menu-icon" aria-hidden="true"></span>
        Menu
    </button>
    <div class="mobile-menu-backdrop" hidden></div>

    <div class="app">

        {{-- Sidebar --}}
        <aside class="sidebar" id="app-sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/LOGIN/logotipo3.png') }}"
                        alt="TECA Arquitectos"
                        class="sidebar-logo">
                </a>
            </div>

            <nav class="sidebar-nav">

                {{-- ================= ADMIN ================= --}}
                @role('admin')

                    <a href="{{ route('dashboard') }}" class="nav-item">
                        <b>Panel principal</b> 
                    </a>

                    {{-- MENÚ DESPLEGABLE GESTIÓN DE BODEGA --}}
                    <div class="nav-dropdown {{ request()->routeIs('productos.*','solicitudes.*','movimientos.*','configuracion.*') ? 'open' : '' }}">

                        <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                            
                            Sistema de Gestión de Bodega
                            <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu">
                            <a href="{{ route('productos.create') }}"
                            class="nav-item {{ request()->routeIs('productos.create') ? 'active' : '' }}">
                                Registro de Producto
                            </a>

                            <a href="{{ route('productos.create') }}"
                            class="nav-item {{ request()->routeIs('producion.index') ? 'active' : '' }}">
                                Registro de Producción
                            </a>

                            <a href="{{ route('solicitudes.solicitudes') }}"
                            class="nav-item {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">
                                Solicitudes de Inventario
                            </a>

                            <a href="{{ route('movimientos.create') }}"
                            class="nav-item {{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                                Movimientos de Inventario
                            </a>

                            <a href="{{ route('productos.index') }}"
                            class="nav-item {{ request()->routeIs('productos.index') ? 'active' : '' }}">
                                Existencias
                            </a>

                            <a href="{{ route('configuracion.index') }}"
                            class="nav-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                                Configuración de Inventario
                            </a>
                        </div>
                    </div>

                    {{-- ================= ALQUILERES ================= --}}
                    <div class="nav-dropdown {{ request()->routeIs('arriendos.*') ? 'open' : '' }}">

                        <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                            
                            Control de Alquileres
                            <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu">
                            <a href="{{ route('arriendos.index') }}"
                            class="nav-item {{ request()->routeIs('arriendos.index') ? 'active' : '' }}">
                                Listado de Alquileres
                            </a>

                            <a href="{{ route('arriendos.create') }}"
                            class="nav-item {{ request()->routeIs('arriendos.create') ? 'active' : '' }}">
                                Registro de Alquiler
                            </a>
                        </div>
                    </div>

                    {{-- ================= CLIENTES ================= --}}
                    <div class="nav-dropdown {{ request()->routeIs('clientes.*') ? 'open' : '' }}">

                        <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                            
                            Control de Clientes y Obras
                            <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu">
                            <a href="{{ route('clientes.index') }}"
                            class="nav-item {{ request()->routeIs('clientes.index') ? 'active' : '' }}">
                                Listado de Clientes
                            </a>

                            <a href="{{ route('clientes.create') }}"
                            class="nav-item {{ request()->routeIs('clientes.create') ? 'active' : '' }}">
                                Registro de Cliente
                            </a>
                        </div>
                    </div>

                    {{-- ================= REPORTES ================= --}}
                    <div class="nav-dropdown {{ request()->routeIs('reportes.*') ? 'open' : '' }}">

                        <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                            
                            Control de Reportes
                            <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu">
                            <a href="{{ route('reportes.index') }}"
                            class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                                Reportes de Inventarios
                            </a>
                        </div>
                    </div>

                    {{-- ================= GASTOS ================= --}}
                    <div class="nav-dropdown {{ request()->routeIs('gastos.*') ? 'open' : '' }}">

                        <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                            
                            Control de Gastos
                            <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu">
                            <a href="{{ route('gastos.index') }}"
                            class="nav-item {{ request()->routeIs('gastos.*') ? 'active' : '' }}">
                                Gastos Generales
                            </a>
                        </div>
                    </div>

                    {{-- ================= CIERRE DE CAJA ================= --}}
                    <div class="nav-dropdown {{ request()->routeIs('cierrecaja.*') ? 'open' : '' }}">

                        <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                            
                            módulo de Caja y Cierres
                            <span class="arrow">▾</span>
                        </a>

                        <div class="dropdown-menu">
                            <a href="{{ route('cierrecaja.cierrecaja') }}"
                            class="nav-item {{ request()->routeIs('cierrecaja.*') ? 'active' : '' }}">
                                Seleccionar cierre
                            </a>
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-item nav-logout">
                            <b>CERRAR SESIÓN</b>
                        </button>
                    </form>

                @endrole

                {{-- ================= ASISTENTE ================= --}}
                @role('asistente')

                <div class="nav-dropdown {{ request()->routeIs('arriendos.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                        Control de Alquileres
                        <span class="arrow">▾</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{ route('arriendos.index') }}" class="nav-item">Listado</a>
                        <a href="{{ route('arriendos.create') }}" class="nav-item">Registro</a>
                    </div>
                </div>

                <div class="nav-dropdown {{ request()->routeIs('clientes.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                        Clientes y Obras
                        <span class="arrow">▾</span>
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{ route('clientes.index') }}" class="nav-item">Listado</a>
                        <a href="{{ route('clientes.create') }}" class="nav-item">Registro</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-item nav-logout">CERRAR SESIÓN</button>
                </form>

            @endrole

                {{-- ================= BODEGA ================= --}}
                @role('bodega')

                <a href="{{ route('dashboard') }}" class="nav-item">
                    <b>Panel principal</b>
                </a>

                <div class="nav-dropdown {{ request()->routeIs('productos.*','solicitudes.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-item dropdown-toggle">
                        Sistema de Gestión de Bodega
                        <span class="arrow">▾</span>
                    </a>

                    <div class="dropdown-menu">
                        <a href="{{ route('solicitudes.solicitudes') }}" class="nav-item">
                            Solicitudes de Inventario
                        </a>

                        <a href="{{ route('productos.index') }}" class="nav-item">
                            Existencias
                        </a>
                        
                        <a href="{{ route('solicitudes.detalladas') }}" class="nav-item">
                            Control de Historial
                        </a>

                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-item nav-logout">CERRAR SESIÓN</button>
                </form>

            @endrole

            </nav>
        </aside>

        {{-- Contenido principal --}}
        <main class="principal-content">
            <header class="principal-topbar">
                <h1>@yield('header', 'Panel')</h1>
            </header>

            <section class="principal-page">
                @yield('content')
            </section>
        </main>

    </div>

    {{-- JS menú --}}
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('app-sidebar');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const menuBackdrop = document.querySelector('.mobile-menu-backdrop');

    const closeMobileMenu = () => {
        if (!sidebar || !menuToggle || !menuBackdrop) return;

        sidebar.classList.remove('is-open');
        menuBackdrop.hidden = true;
        menuToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
    };

    const openMobileMenu = () => {
        if (!sidebar || !menuToggle || !menuBackdrop) return;

        sidebar.classList.add('is-open');
        menuBackdrop.hidden = false;
        menuToggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('menu-open');
    };

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            if (sidebar?.classList.contains('is-open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    if (menuBackdrop) {
        menuBackdrop.addEventListener('click', closeMobileMenu);
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1100) {
            closeMobileMenu();
        }
    });

    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const current = toggle.closest('.nav-dropdown');
            if (!current) return;

            document.querySelectorAll('.nav-dropdown').forEach(d => {
                if (d !== current) d.classList.remove('open');
            });

            current.classList.toggle('open');
        });
    });
});
</script>


    @yield('scripts')
    @stack('scripts')

</body>
</html>
