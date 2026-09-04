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
        :root {
            --brand-navy: #0a2540;
            --brand-navy-dark: #071a33;
            --brand-blue: #0284c7;
            --brand-blue-dark: #0052cc;
            --brand-cyan: #38bdf8;
            --brand-sky-light: #e0f2fe;
            --brand-border: rgba(186, 230, 253, 0.7);
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1e293b;
        }
        .sidebar {
            min-height: 100vh;
            position: relative;
            background: linear-gradient(180deg, #071a33 0%, #0c2b54 50%, #0a2540 100%);
            box-shadow: 4px 0 20px rgba(7, 26, 51, 0.15);
        }
        .sidebar .nav-link {
            color: rgba(224, 242, 254, 0.75);
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            margin: 3px 10px;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(56, 189, 248, 0.14);
            transform: translateX(2px);
        }
        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.38);
            font-weight: 600;
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }
        .sidebar-brand {
            color: #38bdf8;
            font-weight: 700;
            font-size: 1.25rem;
            padding: 1.25rem;
            border-bottom: 1px solid rgba(56, 189, 248, 0.15);
            text-shadow: 0 0 16px rgba(56, 189, 248, 0.35);
            letter-spacing: 0.5px;
        }
        .main-content {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(1000px 600px at top right, rgba(186, 230, 253, 0.45), transparent 70%),
                radial-gradient(800px 500px at bottom left, rgba(224, 242, 254, 0.55), transparent 70%),
                radial-gradient(600px 400px at 50% 50%, rgba(240, 249, 255, 0.35), transparent 70%);
            min-height: 100vh;
        }
        .top-bar {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(186, 230, 253, 0.75);
            padding: 0.85rem 1.75rem;
            box-shadow: 0 2px 10px rgba(2, 132, 199, 0.04);
        }
        .top-bar h5 {
            color: var(--brand-navy);
            font-weight: 700;
        }

        /* Estilos globales de componentes de marca */
        .btn-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.28);
            transition: all 0.2s ease;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #0369a1 0%, #0046b3 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.38);
        }
        .btn-outline-primary {
            color: #0284c7 !important;
            border-color: #38bdf8 !important;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #0284c7, #0052cc) !important;
            border-color: transparent !important;
            color: #ffffff !important;
        }
        .card {
            border: 1px solid rgba(186, 230, 253, 0.65) !important;
            border-radius: 14px;
            box-shadow: 0 8px 25px -5px rgba(2, 132, 199, 0.07);
        }
        .badge.bg-primary {
            background: linear-gradient(135deg, #0284c7, #0052cc) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.22) !important;
        }
        .modal-content {
            border-radius: 16px;
            border: 1px solid rgba(186, 230, 253, 0.7);
            box-shadow: 0 20px 40px -15px rgba(2, 132, 199, 0.18);
            overflow: hidden;
        }
        .page-item.active .page-link {
            background: linear-gradient(135deg, #0284c7, #0052cc) !important;
            border-color: transparent !important;
        }
        .page-link {
            color: #0284c7;
        }
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #bae6fd;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #38bdf8;
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

                <div class="p-3" style="position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(56, 189, 248, 0.12);">
                    <div class="small px-2 mb-2" style="color: rgba(224, 242, 254, 0.85);">
                        <i class="bi bi-person-circle me-1" style="color: #38bdf8;"></i>
                        <span class="fw-semibold">{{ auth()->user()->nombreCompleto() }}</span>
                        <br>
                        <span class="badge mt-1" style="background: rgba(56, 189, 248, 0.18); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.35); font-weight: 500;">
                            {{ ucfirst(auth()->user()->rol) }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100" style="border: 1px solid rgba(56, 189, 248, 0.35); color: #e0f2fe; background: rgba(56, 189, 248, 0.08); transition: all 0.2s;" onmouseover="this.style.background='rgba(56, 189, 248, 0.2)'; this.style.color='#ffffff';" onmouseout="this.style.background='rgba(56, 189, 248, 0.08)'; this.style.color='#e0f2fe';">
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
