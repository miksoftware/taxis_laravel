@extends('layouts.app')

@section('title', 'Usuarios - Taxi Diamantes')
@section('page-title', 'Gestión de Usuarios')

@push('styles')
<style>
    /* Tarjetas KPI de Usuarios Uniformes y Minimalistas */
    .metric-kpi-card {
        background: #ffffff;
        border: 1px solid rgba(186, 230, 253, 0.75);
        border-radius: 16px;
        padding: 16px 18px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 16px -2px rgba(2, 132, 199, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 125px;
    }
    .metric-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -4px rgba(2, 132, 199, 0.12);
        border-color: #38bdf8 !important;
    }
    .metric-kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .metric-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin: 0;
    }
    .metric-kpi-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .icon-blue { background: #e0f2fe; color: #0284c7; }
    .icon-cyan { background: #ecfeff; color: #0891b2; }
    .icon-green { background: #dcfce7; color: #10b981; }
    .icon-amber { background: #fffbeb; color: #d97706; }

    .metric-kpi-body {
        margin: 6px 0;
    }
    .metric-num {
        font-size: 2.1rem;
        font-weight: 800;
        color: #0a2540;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .metric-kpi-footer {
        display: flex;
        align-items: center;
        height: 24px;
    }
    .metric-kpi-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.72rem;
    }
    .pill-blue { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
    .pill-cyan { background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
    .pill-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .pill-amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

    /* Tarjeta Contenedora Moderna */
    .card-modern {
        background: #ffffff;
        border: 1px solid rgba(186, 230, 253, 0.75) !important;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(2, 132, 199, 0.05);
        overflow: hidden;
    }
    .card-header-modern {
        background: #ffffff;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .card-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .card-icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0a2540;
        margin: 0;
    }
    .card-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }

    /* Tabla de Usuarios */
    .tabla-usuarios { font-size: 0.84rem; }
    .tabla-usuarios th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.73rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        white-space: nowrap;
        border-bottom: 1px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 12px 14px;
    }
    .tabla-usuarios td {
        vertical-align: middle;
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .tabla-usuarios tr { transition: background 0.15s ease; }
    .tabla-usuarios tr:hover { background-color: #f0f9ff !important; }

    .avatar-usuario {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.82rem;
        flex-shrink: 0;
        border: 1px solid #bae6fd;
    }

    .btn-accion {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.84rem;
        border-radius: 8px;
        transition: all 0.18s ease;
    }
    .btn-accion:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2);
    }

    /* Modales Estilizados */
    .modal-content-modern {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(2, 132, 199, 0.2);
    }
    .modal-header-modern {
        background: linear-gradient(135deg, #071a33 0%, #0a2540 100%);
        border-bottom: 2px solid #38bdf8;
        padding: 14px 20px;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
{{-- Encabezado con título moderno --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Gestión de Usuarios</h4>
        <p class="text-muted small mb-0"><i class="bi bi-shield-lock text-primary me-1"></i> Control de acceso, cuentas y roles del personal</p>
    </div>
</div>

{{-- Métricas Resumen Uniformes y Minimalistas --}}
<div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
    {{-- Total Cuentas --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Total Cuentas</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-people"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $metricas['total'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-person-lines-fill me-1"></i> Registrados</span>
            </div>
        </div>
    </div>
    {{-- Administradores --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Administradores</span>
                <div class="metric-kpi-icon icon-cyan"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $metricas['admins'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-cyan"><i class="bi bi-key me-1"></i> Control Total</span>
            </div>
        </div>
    </div>
    {{-- Operadores --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Operadores</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-headset"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $metricas['operadores'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-telephone-inbound me-1"></i> Cabina / Turno</span>
            </div>
        </div>
    </div>
    {{-- Cuentas Activas --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Cuentas Activas</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-check2-circle"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $metricas['activos'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-circle-fill me-1" style="font-size: 0.55rem; color: #10b981;"></i> En servicio</span>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de Usuarios Moderna --}}
<div class="card card-modern mb-4">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-people-fill"></i></div>
            <div>
                <h6 class="card-title">Lista de Usuarios</h6>
                <p class="card-subtitle">Directorio de cuentas activas y permisos asignados</p>
            </div>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            {{-- Selector de paginación --}}
            <div class="d-flex align-items-center gap-1">
                <small class="text-muted fw-semibold" style="font-size: 0.78rem;">Mostrar:</small>
                <select id="selectorPaginacionUsuarios" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 88px; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 600;" onchange="cambiarPaginacion(this.value)">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="todos" {{ $perPage === 'todos' || $perPage == -1 ? 'selected' : '' }}>Todos</option>
                </select>
            </div>

            {{-- Buscador instantáneo --}}
            <div class="position-relative">
                <input type="text" id="filtroBuscar" class="form-control form-control-sm rounded-pill ps-4" placeholder="Buscar nombre, usuario..." value="{{ $buscar ?? '' }}" style="width: 200px; font-size: 0.8rem; border-color: #cbd5e1;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted small"></i>
            </div>
            {{-- Filtro por rol --}}
            <select id="filtroRol" class="form-select form-select-sm rounded-pill" style="width: auto; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 600;">
                <option value="">Todos los roles</option>
                <option value="superadmin" {{ ($filtroRol ?? '') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                <option value="administrador" {{ ($filtroRol ?? '') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="operador" {{ ($filtroRol ?? '') === 'operador' ? 'selected' : '' }}>Operador</option>
            </select>
            {{-- Botón Nuevo Usuario --}}
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-usuarios mb-0" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nombre y Apellidos</th>
                        <th>Usuario</th>
                        <th>Email y Contacto</th>
                        <th>Rol</th>
                        <th class="text-center">Estado</th>
                        <th>Registro</th>
                        <th class="pe-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr data-rol="{{ strtolower($usuario->rol) }}" data-texto="{{ strtolower($usuario->nombreCompleto() . ' ' . $usuario->username . ' ' . $usuario->email) }}">
                        <td class="ps-4"><span class="fw-bold" style="color: #0284c7;">#{{ $usuario->id }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-usuario">
                                    {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1) . substr($usuario->apellidos ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold" style="color: #0a2540;">{{ $usuario->nombreCompleto() }}</div>
                                    @if($usuario->telefono)
                                        <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $usuario->telefono }}</small>
                                    @else
                                        <small class="text-muted">Sin teléfono</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:0.75rem;font-weight:600">
                                @ {{ $usuario->username }}
                            </span>
                        </td>
                        <td>
                            <span class="text-secondary small"><i class="bi bi-envelope me-1 text-primary"></i>{{ $usuario->email }}</span>
                        </td>
                        <td>
                            @if($usuario->rol === 'superadmin')
                                <span class="badge" style="background:#fdf2f8;color:#be185d;border:1px solid #fbcfe8;font-size:0.73rem;font-weight:700;border-radius:20px;padding:5px 11px">
                                    <i class="bi bi-shield-lock-fill me-1"></i>Super Admin
                                </span>
                            @elseif($usuario->rol === 'administrador')
                                <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:0.73rem;font-weight:700;border-radius:20px;padding:5px 11px">
                                    <i class="bi bi-shield-check me-1"></i>Administrador
                                </span>
                            @else
                                <span class="badge" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;font-size:0.73rem;font-weight:700;border-radius:20px;padding:5px 11px">
                                    <i class="bi bi-headset me-1"></i>Operador
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($usuario->estado === 'activo')
                                <span class="badge rounded-pill" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:0.72rem;font-weight:700;padding:5px 11px">
                                    <i class="bi bi-check-circle-fill me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:0.72rem;font-weight:700;padding:5px 11px">
                                    <i class="bi bi-dash-circle-fill me-1"></i>Inactivo
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $usuario->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                {{-- Editar --}}
                                <button type="button" class="btn btn-sm btn-outline-primary btn-accion" title="Editar"
                                    onclick="editarUsuario({{ $usuario->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                @if($usuario->id !== auth()->id() && !$usuario->es_protegido)
                                    {{-- Cambiar estado --}}
                                    @if($usuario->estado === 'activo')
                                        <button type="button" class="btn btn-sm btn-outline-warning btn-accion" title="Desactivar cuenta"
                                            onclick="cambiarEstado({{ $usuario->id }}, 'inactivo', '{{ $usuario->username }}')">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success btn-accion" title="Activar cuenta"
                                            onclick="cambiarEstado({{ $usuario->id }}, 'activo', '{{ $usuario->username }}')">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    @endif

                                    {{-- Reset password --}}
                                    <button type="button" class="btn btn-sm btn-outline-info btn-accion" title="Restablecer contraseña"
                                        onclick="resetPassword({{ $usuario->id }}, '{{ $usuario->username }}')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No hay usuarios registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginador Moderno --}}
        @if($usuarios->hasPages() || $usuarios->total() > 0)
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center border-top bg-light">
            <small class="text-muted mb-2 mb-md-0">
                @if($perPage === 'todos' || $perPage == -1)
                    Mostrando <strong>todos los {{ $usuarios->total() }}</strong> usuarios
                @else
                    Mostrando del <strong>{{ $usuarios->firstItem() ?? 0 }}</strong> al <strong>{{ $usuarios->lastItem() ?? 0 }}</strong> de <strong>{{ $usuarios->total() }}</strong> usuarios
                @endif
            </small>
            @if($perPage !== 'todos' && $perPage != -1 && $usuarios->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $usuarios->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Usuario --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" action="{{ route('usuarios.store') }}" id="formCrear" novalidate>
                @csrf
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-person-plus-fill text-info me-2"></i>Nuevo Usuario</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold small text-secondary">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label fw-bold small text-secondary">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" value="{{ old('apellidos') }}" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required style="border-radius: 8px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required minlength="8" style="border-radius: 8px;">
                            <div class="form-text small">Mínimo 8 caracteres</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Confirmar <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" name="rol" required style="border-radius: 8px;">
                            <option value="">Seleccione un rol</option>
                            <option value="administrador" {{ old('rol') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="operador" {{ old('rol') === 'operador' ? 'selected' : '' }}>Operador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-save me-1"></i>Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Editar Usuario --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formEditar" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-pencil-square text-info me-2"></i>Editar Usuario</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="editar_nombre" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" id="editar_apellidos" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="editar_email" required style="border-radius: 8px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" id="editar_username" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="editar_telefono" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" name="rol" id="editar_rol" required style="border-radius: 8px;">
                            <option value="superadmin">Super Admin</option>
                            <option value="administrador">Administrador</option>
                            <option value="operador">Operador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Cambiar Estado --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formCambiarEstado">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" id="estado_valor">
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0" id="titulo_estado"><i class="bi bi-toggle2-off text-info me-2"></i>Cambiar Estado</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="fs-6 mb-3">¿Está seguro que desea <span id="accion_estado" class="fw-bold"></span> al usuario <span id="nombre_usuario" class="fw-bold text-primary"></span>?</p>
                    <div id="advertencia_desactivar" class="alert alert-warning mb-0 border-0 shadow-sm" style="display:none; border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        El usuario no podrá iniciar sesión mientras esté desactivado.
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-check-circle me-1"></i>Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Restablecer Contraseña --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formResetPassword" novalidate>
                @csrf
                @method('PATCH')
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-key text-info me-2"></i>Restablecer Contraseña</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">Ingrese la nueva contraseña para el usuario <strong id="reset_username" class="text-primary"></strong>:</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required minlength="8" style="border-radius: 8px;">
                        <div class="form-text small">Mínimo 8 caracteres</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-check-circle me-1"></i>Restablecer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Errores de validación (abrir modal correspondiente) --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@if($errors->any())
<div class="modal fade" id="erroresModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Errores de validación</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="mb-0 text-danger ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top">
                <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    const baseUrl = '{{ url("usuarios") }}';

    // Mostrar errores de validación al cargar
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('erroresModal')).show();
        });
    @endif

    function editarUsuario(id) {
        fetch(`${baseUrl}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('formEditar').action = `${baseUrl}/${data.id}`;
            document.getElementById('editar_nombre').value = data.nombre;
            document.getElementById('editar_apellidos').value = data.apellidos;
            document.getElementById('editar_email').value = data.email;
            document.getElementById('editar_username').value = data.username;
            document.getElementById('editar_telefono').value = data.telefono || '';
            document.getElementById('editar_rol').value = data.rol;

            new bootstrap.Modal(document.getElementById('editarUsuarioModal')).show();
        })
        .catch(() => alert('Error al cargar datos del usuario'));
    }

    function cambiarEstado(id, estado, username) {
        document.getElementById('formCambiarEstado').action = `${baseUrl}/${id}/estado`;
        document.getElementById('estado_valor').value = estado;
        document.getElementById('nombre_usuario').textContent = username;

        if (estado === 'activo') {
            document.getElementById('titulo_estado').innerHTML = '<i class="bi bi-person-check text-info me-2"></i>Activar Usuario';
            document.getElementById('accion_estado').textContent = 'activar';
            document.getElementById('advertencia_desactivar').style.display = 'none';
        } else {
            document.getElementById('titulo_estado').innerHTML = '<i class="bi bi-person-x text-info me-2"></i>Desactivar Usuario';
            document.getElementById('accion_estado').textContent = 'desactivar';
            document.getElementById('advertencia_desactivar').style.display = 'block';
        }

        new bootstrap.Modal(document.getElementById('cambiarEstadoModal')).show();
    }

    function resetPassword(id, username) {
        document.getElementById('formResetPassword').action = `${baseUrl}/${id}/reset-password`;
        document.getElementById('reset_username').textContent = username;

        // Limpiar campos
        document.querySelectorAll('#formResetPassword input[type="password"]').forEach(i => i.value = '');

        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }

    // Cambiar registros por página
    window.cambiarPaginacion = function(valor) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', valor);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    };

    // Filtrado interactivo en tiempo real y con Enter al servidor
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('filtroBuscar');
        const selectRol = document.getElementById('filtroRol');
        const filas = document.querySelectorAll('#tablaUsuarios tbody tr[data-rol]');

        function filtrarLocal() {
            const query = inputBuscar ? inputBuscar.value.toLowerCase().trim() : '';
            const rol = selectRol ? selectRol.value.toLowerCase().trim() : '';

            filas.forEach(fila => {
                const texto = fila.getAttribute('data-texto') || '';
                const filaRol = fila.getAttribute('data-rol') || '';

                const coincideTexto = !query || texto.includes(query);
                const coincideRol = !rol || filaRol === rol;

                if (coincideTexto && coincideRol) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        if (inputBuscar) {
            inputBuscar.addEventListener('input', filtrarLocal);
            inputBuscar.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const url = new URL(window.location.href);
                    if (this.value.trim()) url.searchParams.set('buscar', this.value.trim());
                    else url.searchParams.delete('buscar');
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                }
            });
        }

        if (selectRol) {
            selectRol.addEventListener('change', function() {
                const url = new URL(window.location.href);
                if (this.value) url.searchParams.set('rol', this.value);
                else url.searchParams.delete('rol');
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });
        }
    });
</script>
@endpush
