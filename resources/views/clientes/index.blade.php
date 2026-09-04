@extends('layouts.app')

@section('title', 'Clientes - Taxi Diamantes')
@section('page-title', 'Gestión de Clientes')

@push('styles')
<style>
    /* Tarjetas KPI de Clientes Uniformes y Minimalistas */
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

    /* Buscador Integrado */
    .contenedor-buscador {
        background: #f8fafc;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        padding: 14px 20px;
    }
    .contenedor-buscador .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 0.84rem;
        transition: all 0.2s ease;
    }
    .contenedor-buscador .form-control:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }

    /* Tabla de Clientes */
    .tabla-clientes { font-size: 0.84rem; }
    .tabla-clientes th {
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
    .tabla-clientes td {
        vertical-align: middle;
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .tabla-clientes tr { transition: background 0.15s ease; }
    .tabla-clientes tr:hover { background-color: #f0f9ff !important; }

    .avatar-cliente {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
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

    /* Paginación Moderna */
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    .page-item .page-link {
        border-radius: 8px !important;
        border-color: #e2e8f0;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 6px 12px;
    }
    .page-item.active .page-link {
        background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);
    }
</style>
@endpush

@section('content')
{{-- Encabezado con título moderno --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Gestión de Clientes</h4>
        <p class="text-muted small mb-0"><i class="bi bi-person-lines-fill text-primary me-1"></i> Directorio telefónico, direcciones de recogida y récord de servicios</p>
    </div>
</div>

{{-- Métricas Resumen Uniformes y Minimalistas --}}
<div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
    {{-- Total Clientes Registrados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Total Clientes</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ number_format($clientes->total(), 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-database-check me-1"></i> Base de datos</span>
            </div>
        </div>
    </div>
    {{-- Clientes en Página Actual --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">En Esta Página</span>
                <div class="metric-kpi-icon icon-cyan"><i class="bi bi-file-earmark-text"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $clientes->count() }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-cyan"><i class="bi bi-layers me-1"></i> Pág. {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>
            </div>
        </div>
    </div>
    {{-- Clientes con Direcciones --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Con Direcciones</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-geo-alt-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $clientes->where('direcciones_activas_count', '>', 0)->count() }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-pin-map me-1"></i> En esta página</span>
            </div>
        </div>
    </div>
    {{-- Clientes sin Dirección aún --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Sin Dirección</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-geo"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $clientes->where('direcciones_activas_count', 0)->count() }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-clock-history me-1"></i> Por asociar</span>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta Principal de Clientes --}}
<div class="card card-modern mb-4">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-person-lines-fill"></i></div>
            <div>
                <h6 class="card-title">Lista de Clientes</h6>
                <p class="card-subtitle">Directorio oficial y gestión de direcciones de recogida</p>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#crearClienteModal">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Cliente
            </button>
        </div>
    </div>

    {{-- Buscador Integrado --}}
    <div class="contenedor-buscador">
        <form method="GET" action="{{ route('clientes.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por teléfono o nombre..." value="{{ $filtro }}" style="border-radius: 0 8px 8px 0;">
                </div>
            </div>
            <div class="col-auto d-flex align-items-center gap-1">
                <small class="text-muted fw-semibold" style="font-size: 0.78rem;">Mostrar:</small>
                <select name="per_page" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 88px; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 600;" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ ($perPage ?? 10) == 20 ? 'selected' : '' }}>20</option>
                    <option value="30" {{ ($perPage ?? 10) == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="todos" {{ ($perPage ?? 10) === 'todos' || ($perPage ?? 10) == -1 ? 'selected' : '' }}>Todos</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>
                @if($filtro)
                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-x-lg me-1"></i> Limpiar filtro
                    </a>
                @endif
            </div>
            @if($filtro)
                <div class="col-12 mt-1">
                    <small class="text-muted">
                        Filtrando resultados por: <strong class="text-primary">"{{ $filtro }}"</strong> ({{ $clientes->total() }} encontrados)
                    </small>
                </div>
            @endif
        </form>
    </div>

    {{-- Tabla de Clientes --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-clientes mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Teléfono</th>
                        <th>Nombre del Cliente</th>
                        <th class="text-center">Direcciones</th>
                        <th>Registro</th>
                        <th class="pe-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td class="ps-4"><span class="fw-bold" style="color: #0284c7;">#{{ $cliente->id }}</span></td>
                        <td>
                            <span class="badge" style="background:#f0f9ff;color:#0284c7;border:1px solid #bae6fd;font-size:0.8rem;font-weight:700">
                                <i class="bi bi-telephone-fill me-1"></i>{{ $cliente->telefono }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-cliente">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color: #0a2540;">{{ $cliente->nombre }}</div>
                                    @if($cliente->notas)
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 280px;" title="{{ $cliente->notas }}">
                                            <i class="bi bi-sticky me-1"></i>{{ $cliente->notas }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($cliente->direcciones_activas_count > 0)
                                <span class="badge rounded-pill" style="background:#ecfeff;color:#0891b2;border:1px solid #a5f3fc;font-size:0.75rem;font-weight:700;padding:4px 10px">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $cliente->direcciones_activas_count }} {{ $cliente->direcciones_activas_count == 1 ? 'dirección' : 'direcciones' }}
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background:#f8fafc;color:#94a3b8;border:1px solid #e2e8f0;font-size:0.75rem;font-weight:600;padding:4px 10px">
                                    <i class="bi bi-geo me-1"></i>Sin direcciones
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $cliente->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</span>
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                {{-- Editar --}}
                                <button type="button" class="btn btn-sm btn-outline-primary btn-accion" title="Editar cliente"
                                    onclick="editarCliente({{ $cliente->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Direcciones --}}
                                <button type="button" class="btn btn-sm btn-outline-info btn-accion" title="Gestionar direcciones"
                                    onclick="verDirecciones({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}')">
                                    <i class="bi bi-geo-alt"></i>
                                </button>
                                {{-- Historial --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-accion" title="Historial de carreras"
                                    onclick="verHistorial({{ $cliente->id }})">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-person-x display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron clientes coincidentes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginador Moderno --}}
        @if($clientes->hasPages() || $clientes->total() > 0)
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center border-top bg-light">
            <small class="text-muted mb-2 mb-md-0">
                @if(($perPage ?? 10) === 'todos' || ($perPage ?? 10) == -1)
                    Mostrando <strong>todos los {{ number_format($clientes->total(), 0, ',', '.') }}</strong> clientes
                @else
                    Mostrando del <strong>{{ $clientes->firstItem() ?? 0 }}</strong> al <strong>{{ $clientes->lastItem() ?? 0 }}</strong> de <strong>{{ number_format($clientes->total(), 0, ',', '.') }}</strong> clientes
                @endif
            </small>
            @if(($perPage ?? 10) !== 'todos' && ($perPage ?? 10) != -1 && $clientes->hasPages())
            <div class="d-flex justify-content-center">
                {{ $clientes->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" action="{{ route('clientes.store') }}" novalidate>
                @csrf
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-person-plus-fill text-info me-2"></i>Nuevo Cliente</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}" required maxlength="15" placeholder="Ej: 3125299865" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" maxlength="100" placeholder="Si no se indica, se usará 'Cliente + teléfono'" style="border-radius: 8px;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Notas / Observaciones</label>
                        <textarea class="form-control" name="notas" rows="2" maxlength="1000" placeholder="Observaciones especiales sobre este cliente..." style="border-radius: 8px;">{{ old('notas') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-save me-1"></i>Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Editar Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formEditarCliente" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-pencil-square text-info me-2"></i>Editar Cliente</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Teléfono</label>
                        <input type="text" class="form-control bg-light" id="editar_telefono_display" disabled style="border-radius: 8px; font-weight: 600;">
                        <div class="form-text small">El número telefónico es el identificador principal y no se puede modificar.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre" id="editar_nombre" maxlength="100" style="border-radius: 8px;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Notas / Observaciones</label>
                        <textarea class="form-control" name="notas" id="editar_notas" rows="2" maxlength="1000" style="border-radius: 8px;"></textarea>
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
{{-- MODAL: Direcciones del Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="direccionesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h6 class="modal-title fw-bold text-white mb-0">
                    <i class="bi bi-geo-alt text-info me-2"></i>Direcciones de <span id="dir_nombre_cliente" class="text-white"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Formulario nueva dirección --}}
                <div class="p-3 mb-3 rounded-3" style="background:#f0f9ff; border: 1px solid #bae6fd;">
                    <h6 class="fw-bold mb-2 small text-primary"><i class="bi bi-plus-circle me-1"></i>Asociar Nueva Dirección</h6>
                    <form id="formNuevaDireccion" class="row g-2">
                        <input type="hidden" id="dir_cliente_id">
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-sm" id="nueva_direccion" placeholder="Ej: Calle 10 # 5-20" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" id="nueva_referencia" placeholder="Referencia (opcional)" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm w-100 rounded-3 fw-bold">
                                <i class="bi bi-plus-lg me-1"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>

                <div id="listaDirecciones">
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando direcciones...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Historial del Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="historialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-clock-history text-info me-2"></i>Historial de Servicios</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="historialContenido">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando historial...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Errores de validación --}}
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
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('erroresModal')).show();
});
@endif

// ── Editar Cliente ──
function editarCliente(id) {
    fetch(`{{ url('clientes') }}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('formEditarCliente').action = `{{ url('clientes') }}/${data.id}`;
        document.getElementById('editar_telefono_display').value = data.telefono;
        document.getElementById('editar_nombre').value = data.nombre || '';
        document.getElementById('editar_notas').value = data.notas || '';
        new bootstrap.Modal(document.getElementById('editarClienteModal')).show();
    })
    .catch(() => alert('Error al cargar datos del cliente'));
}

// ── Ver Direcciones ──
function verDirecciones(clienteId, nombre) {
    document.getElementById('dir_cliente_id').value = clienteId;
    document.getElementById('dir_nombre_cliente').textContent = nombre;
    cargarDirecciones(clienteId);
    new bootstrap.Modal(document.getElementById('direccionesModal')).show();
}

function cargarDirecciones(clienteId) {
    const contenedor = document.getElementById('listaDirecciones');
    contenedor.innerHTML = '<div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando...</div>';

    fetch(`{{ url('direcciones') }}?cliente_id=${clienteId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.error || !data.direcciones.length) {
            contenedor.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-geo d-block display-6 opacity-50 mb-2"></i>No hay direcciones registradas</div>';
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0"><thead style="background:#f8fafc;color:#475569;font-size:0.73rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px"><tr>';
        html += '<th class="py-2 ps-3">Dirección</th><th class="py-2">Referencia</th><th class="py-2 text-center">Frecuente</th><th class="py-2 pe-3 text-center">Acciones</th></tr></thead><tbody>';

        data.direcciones.forEach(d => {
            const frecIcon = d.es_frecuente ? 'bi-star-fill text-warning' : 'bi-star text-muted';
            html += `<tr>
                <td class="ps-3 fw-bold" style="color:#0a2540">${d.direccion}</td>
                <td class="text-muted small">${d.referencia || '-'}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-link p-0 text-decoration-none" onclick="toggleFrecuente(${d.id}, ${!d.es_frecuente})" title="${d.es_frecuente ? 'Quitar frecuente' : 'Marcar frecuente'}">
                        <i class="bi ${frecIcon}" style="font-size: 1.1rem;"></i>
                    </button>
                </td>
                <td class="pe-3 text-center">
                    <button class="btn btn-sm btn-outline-danger btn-accion" onclick="eliminarDireccion(${d.id})" title="Eliminar dirección">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        contenedor.innerHTML = html;
    })
    .catch(() => { contenedor.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar direcciones</div>'; });
}

// ── Agregar dirección ──
document.getElementById('formNuevaDireccion').addEventListener('submit', function(e) {
    e.preventDefault();
    const clienteId = document.getElementById('dir_cliente_id').value;
    const direccion = document.getElementById('nueva_direccion').value.trim();
    const referencia = document.getElementById('nueva_referencia').value.trim();

    if (!direccion) return;

    fetch('{{ route("direcciones.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ cliente_id: clienteId, direccion, referencia })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.mensaje); return; }
        document.getElementById('nueva_direccion').value = '';
        document.getElementById('nueva_referencia').value = '';
        cargarDirecciones(clienteId);
    })
    .catch(() => alert('Error al guardar dirección'));
});

// ── Toggle frecuente ──
function toggleFrecuente(id, valor) {
    fetch(`{{ url('direcciones') }}/${id}/frecuente`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ es_frecuente: valor })
    })
    .then(r => r.json())
    .then(() => cargarDirecciones(document.getElementById('dir_cliente_id').value))
    .catch(() => alert('Error al actualizar'));
}

// ── Eliminar dirección ──
function eliminarDireccion(id) {
    if (!confirm('¿Está seguro de eliminar esta dirección?')) return;

    fetch(`{{ url('direcciones') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.mensaje); return; }
        cargarDirecciones(document.getElementById('dir_cliente_id').value);
    })
    .catch(() => alert('Error al eliminar'));
}

// ── Ver Historial ──
function verHistorial(clienteId) {
    const contenedor = document.getElementById('historialContenido');
    contenedor.innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando historial...</div>';
    new bootstrap.Modal(document.getElementById('historialModal')).show();

    fetch(`{{ url('clientes') }}/${clienteId}/historial`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => { contenedor.innerHTML = html; })
    .catch(() => { contenedor.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar historial</div>'; });
}
</script>
@endpush
