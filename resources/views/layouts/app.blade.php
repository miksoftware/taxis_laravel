<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taxi Diamantes')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @stack('styles')
    <style>
        .sidebar {
            min-height: 100vh;
            position: relative;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #1a1a2e;
            background: #18dff5;
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }
        .sidebar-brand {
            color: #18dff5;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .main-content {
            background-color: #f4f6f9;
            min-height: 100vh;
        }
        .top-bar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            <nav class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="sidebar-brand d-flex align-items-center">
                    <i class="bi bi-taxi-front me-2"></i> Taxi Diamantes
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" href="{{ route('servicios.index') }}">
                            <i class="bi bi-headset"></i> Recepción
                        </a>
                    </li>
                    @if(auth()->user()->esAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                            <i class="bi bi-person-lines-fill"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}" href="{{ route('vehiculos.index') }}">
                            <i class="bi bi-truck"></i> Vehículos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sanciones.*') ? 'active' : '' }}" href="{{ route('sanciones.index') }}">
                            <i class="bi bi-exclamation-triangle"></i> Sanciones
                        </a>
                    </li>
                    @if(auth()->user()->esAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('articulos-sancion.*') ? 'active' : '' }}" href="{{ route('articulos-sancion.index') }}">
                            <i class="bi bi-journal-text"></i> Art. Sanción
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->rol === 'superadmin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}" href="{{ route('backup.index') }}">
                            <i class="bi bi-database-up"></i> Importar Backup
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="p-3" style="position: absolute; bottom: 0; width: 100%;">
                    <div class="text-white-50 small px-2 mb-2">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user()->nombreCompleto() }}
                        <br>
                        <span class="badge bg-warning text-dark mt-1">{{ ucfirst(auth()->user()->rol) }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100">
                            <i class="bi bi-box-arrow-left me-1"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </nav>

            {{-- Main content --}}
            <main class="col-md-10 ms-sm-auto main-content p-0">
                <div class="top-bar d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                    <span class="text-muted small">{{ now()->format('d/m/Y H:i') }}</span>
                </div>

                <div class="p-4">
                    {{-- Flash messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
