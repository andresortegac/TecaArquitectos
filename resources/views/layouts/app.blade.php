<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TECA ARQUITECTOS')</title>

    {{-- CSS base --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/solicitud.css') }}">
    <link rel="stylesheet" href="{{ asset('css/movimiento.css') }}">

    <script src="{{ asset('js/app.js') }}" defer></script>

    @stack('styles')
</head>
<body>
    <div class="app">

        {{-- Sidebar --}}
        <aside class="sidebar">
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
                        Panel principal
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

                    <a href="{{ route('dashboard') }}" class="nav-item">
                        Vista General del Sistema
                    </a>

                    <div class="nav-section"><b>Control de Alquileres</b></div>
                    <a href="{{ route('arriendos.index') }}" class="nav-item">Listado de Alquileres</a>
                    <a href="{{ route('arriendos.create') }}" class="nav-item">Registro de Alquiler</a>

                    <div class="nav-section"><b>Gestión de Clientes</b></div>
                    <a href="{{ route('clientes.index') }}" class="nav-item">Listado de Clientes</a>
                    <a href="{{ route('clientes.create') }}" class="nav-item">Registro de Cliente</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>

                @endrole

                {{-- ================= BODEGA ================= --}}
                @role('bodega')

                    <a href="{{ route('dashboard') }}" class="nav-item">
                        Vista General del Sistema
                    </a>

                    <div class="nav-section"><b>Sistema de Gestión de Bodega</b></div>
                    <a href="{{ route('productos.create') }}" class="nav-item">Registro de Producto</a>
                    <a href="{{ route('solicitudes.solicitudes') }}" class="nav-item">Solicitudes de Inventario</a>
                    <a href="{{ route('movimientos.create') }}" class="nav-item">Movimientos de Inventario</a>
                    <a href="{{ route('productos.index') }}" class="nav-item">Existencias</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-item nav-logout">
                            CERRAR SESIÓN
                        </button>
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

            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.addEventListener('click', () => {

                    const current = toggle.closest('.nav-dropdown');
                    if (!current) return;

                    document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
                        if (dropdown !== current) {
                            dropdown.classList.remove('open');
                        }
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
