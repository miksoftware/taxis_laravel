@extends('layouts.app')

@section('title', 'Vehículos - Taxi Diamantes')
@section('page-title', 'Gestión de Vehículos')

@push('styles')
<style>
    /* Tarjetas KPI de Vehículos Uniformes y Minimalistas */
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
    .icon-green { background: #dcfce7; color: #10b981; }
    .icon-blue { background: #e0f2fe; color: #0284c7; }
    .icon-red { background: #fee2e2; color: #ef4444; }
    .icon-amber { background: #fffbeb; color: #d97706; }
    .icon-slate { background: #f1f5f9; color: #64748b; }

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
    .pill-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .pill-blue { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
    .pill-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .pill-amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .pill-slate { background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; }

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

    /* Barra de Filtros Integrada */
    .contenedor-filtros {
        background: #f8fafc;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        padding: 14px 20px;
    }
    .contenedor-filtros .form-control,
    .contenedor-filtros .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 0.82rem;
        transition: all 0.2s ease;
    }
    .contenedor-filtros .form-control:focus,
    .contenedor-filtros .form-select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }

    /* Tabla de Vehículos */
    .tabla-vehiculos { font-size: 0.84rem; }
    .tabla-vehiculos th {
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
    .tabla-vehiculos td {
        vertical-align: middle;
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .tabla-vehiculos tr { transition: background 0.15s ease; }
    .tabla-vehiculos tr:hover { background-color: #f0f9ff !important; }

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
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Gestión de Vehículos</h4>
        <p class="text-muted small mb-0"><i class="bi bi-truck text-primary me-1"></i> Control de flota, estados operativos, sanciones y mantenimiento</p>
    </div>
</div>

{{-- Métricas Resumen Uniformes y Minimalistas --}}
<div class="row row-cols-2 row-cols-md-5 g-3 mb-4">
    {{-- 1. Disponibles --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Disponibles</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-check2-circle"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $estadisticas['disponible'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-check-all me-1"></i> Listos para carrera</span>
            </div>
        </div>
    </div>
    {{-- 2. Ocupados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Ocupados</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-arrow-repeat"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $estadisticas['ocupado'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-geo-alt me-1"></i> En servicio activo</span>
            </div>
        </div>
    </div>
    {{-- 3. Sancionados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Sancionados</span>
                <div class="metric-kpi-icon icon-red"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $estadisticas['sancionado'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-red"><i class="bi bi-slash-circle me-1"></i> Fuera de despacho</span>
            </div>
        </div>
    </div>
    {{-- 4. Mantenimiento --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Mantenimiento</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-wrench"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $estadisticas['mantenimiento'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-tools me-1"></i> En revisión taller</span>
            </div>
        </div>
    </div>
    {{-- 5. Inactivos --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Inactivos</span>
                <div class="metric-kpi-icon icon-slate"><i class="bi bi-x-circle"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $estadisticas['inactivo'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-slate"><i class="bi bi-pause-circle me-1"></i> Desactivados</span>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta Principal de Vehículos --}}
<div class="card card-modern mb-4">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-truck-front-fill"></i></div>
            <div>
                <h6 class="card-title">Lista de Vehículos</h6>
                <p class="card-subtitle">Control de flota oficial, estados operativos y registro de móviles</p>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#crearVehiculoModal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Vehículo
            </button>
        </div>
    </div>

    {{-- Filtros y Paginación --}}
    <div class="contenedor-filtros">
        <form method="GET" action="{{ route('vehiculos.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Placa o número móvil..." value="{{ $buscar }}" style="border-radius: 0 8px 8px 0;">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="estado" class="form-select form-select-sm" style="font-weight: 600;">
                    <option value="">Todos los estados</option>
                    <option value="disponible" {{ $filtroEstado === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="ocupado" {{ $filtroEstado === 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                    <option value="sancionado" {{ $filtroEstado === 'sancionado' ? 'selected' : '' }}>Sancionado</option>
                    <option value="mantenimiento" {{ $filtroEstado === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="inactivo" {{ $filtroEstado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
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
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if($buscar || $filtroEstado)
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-x-lg me-1"></i> Limpiar
                    </a>
                @endif
            </div>
            @if($buscar || $filtroEstado)
                <div class="col-12 mt-1">
                    <small class="text-muted">
                        Filtro activo: 
                        @if($buscar) móvil/placa <strong>"{{ $buscar }}"</strong> @endif
                        @if($filtroEstado) estado <strong>"{{ ucfirst($filtroEstado) }}"</strong> @endif
                        ({{ $vehiculos->total() }} encontrados)
                    </small>
                </div>
            @endif
        </form>
    </div>

    {{-- Tabla de Vehículos --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-vehiculos mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Móvil</th>
                        <th>Placa</th>
                        <th>Marca / Modelo</th>
                        <th class="text-center">Estado</th>
                        <th>Registro</th>
                        <th class="pe-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $vehiculo)
                    <tr>
                        <td class="ps-4">
                            <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-size:0.85rem; font-weight:800; padding: 6px 12px; border-radius: 8px;">
                                <i class="bi bi-hash me-1"></i>{{ $vehiculo->numero_movil }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background:#ffffff; color:#0a2540; border:1px solid #cbd5e1; font-size:0.82rem; font-weight:700; letter-spacing:0.5px; border-radius:6px; padding: 5px 10px;">
                                {{ $vehiculo->placa }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e0f2fe; color: #0284c7;">
                                    <i class="bi bi-car-front"></i>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color:#0a2540;">{{ $vehiculo->marca ?? 'Marca no indicada' }}</div>
                                    <small class="text-muted">{{ $vehiculo->modelo ?? 'Modelo no indicado' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'disponible'    => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Disponible', 'icon' => 'bi-check-circle-fill'],
                                    'ocupado'       => ['bg' => '#f0f9ff', 'color' => '#0284c7', 'border' => '#bae6fd', 'label' => 'Ocupado', 'icon' => 'bi-arrow-repeat'],
                                    'sancionado'    => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Sancionado', 'icon' => 'bi-exclamation-triangle-fill'],
                                    'mantenimiento' => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'label' => 'Mantenimiento', 'icon' => 'bi-wrench'],
                                    'inactivo'      => ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#cbd5e1', 'label' => 'Inactivo', 'icon' => 'bi-dash-circle-fill'],
                                ];
                                $st = $statusMap[$vehiculo->estado] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($vehiculo->estado), 'icon' => 'bi-circle'];
                            @endphp
                            <span class="badge rounded-pill" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; border: 1px solid {{ $st['border'] }}; font-size: 0.72rem; font-weight: 700; padding: 5px 12px;">
                                <i class="bi {{ $st['icon'] }} me-1"></i>{{ $st['label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $vehiculo->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</span>
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                {{-- Editar --}}
                                <button type="button" class="btn btn-sm btn-outline-primary btn-accion" title="Editar datos"
                                    onclick="editarVehiculo({{ $vehiculo->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Detalle --}}
                                <button type="button" class="btn btn-sm btn-outline-info btn-accion" title="Ver detalle y sanciones"
                                    onclick="verDetalle({{ $vehiculo->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(!$vehiculo->estaSancionado())
                                    @if($vehiculo->estado !== 'inactivo')
                                        {{-- Cambiar Estado --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning btn-accion" title="Cambiar estado"
                                            onclick="cambiarEstado({{ $vehiculo->id }}, '{{ $vehiculo->placa }}', '{{ $vehiculo->estado }}')">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        {{-- Dar de baja --}}
                                        <form method="POST" action="{{ route('vehiculos.destroy', $vehiculo) }}" class="d-inline"
                                            onsubmit="return confirm('¿Dar de baja al vehículo {{ $vehiculo->placa }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-accion" title="Dar de baja">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Reactivar --}}
                                        <button type="button" class="btn btn-sm btn-outline-success btn-accion" title="Reactivar vehículo"
                                            onclick="reactivar({{ $vehiculo->id }})">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-truck display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron vehículos coincidentes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginador Moderno --}}
        @if($vehiculos->hasPages() || $vehiculos->total() > 0)
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center border-top bg-light">
            <small class="text-muted mb-2 mb-md-0">
                @if(($perPage ?? 10) === 'todos' || ($perPage ?? 10) == -1)
                    Mostrando <strong>todos los {{ $vehiculos->total() }}</strong> vehículos
                @else
                    Mostrando del <strong>{{ $vehiculos->firstItem() ?? 0 }}</strong> al <strong>{{ $vehiculos->lastItem() ?? 0 }}</strong> de <strong>{{ $vehiculos->total() }}</strong> vehículos
                @endif
            </small>
            @if(($perPage ?? 10) !== 'todos' && ($perPage ?? 10) != -1 && $vehiculos->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $vehiculos->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Vehículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearVehiculoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" action="{{ route('vehiculos.store') }}" novalidate>
                @csrf
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-plus-lg text-info me-2"></i>Nuevo Vehículo</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Placa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="placa" value="{{ old('placa') }}" required maxlength="10" placeholder="ABC123" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Nº Móvil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_movil" value="{{ old('numero_movil') }}" required maxlength="20" placeholder="Ej: 12" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Marca</label>
                            <input type="text" class="form-control" name="marca" value="{{ old('marca') }}" maxlength="50" placeholder="Ej: Chevrolet" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Modelo / Año</label>
                            <input type="text" class="form-control" name="modelo" value="{{ old('modelo') }}" maxlength="50" placeholder="Ej: Spark 2022" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Estado inicial</label>
                        <select class="form-select" name="estado" style="border-radius: 8px;">
                            <option value="disponible" {{ old('estado') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="mantenimiento" {{ old('estado') === 'mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
                            <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-save me-1"></i>Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Editar Vehículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarVehiculoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formEditarVehiculo" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-pencil-square text-info me-2"></i>Editar Vehículo</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Placa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="placa" id="editar_placa" required maxlength="10" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Nº Móvil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_movil" id="editar_numero_movil" required maxlength="20" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Marca</label>
                            <input type="text" class="form-control" name="marca" id="editar_marca" maxlength="50" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-secondary">Modelo</label>
                            <input type="text" class="form-control" name="modelo" id="editar_modelo" maxlength="50" style="border-radius: 8px;">
                        </div>
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
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-arrow-repeat text-info me-2"></i>Cambiar Estado Operativo</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">Actualizar condición del vehículo <strong id="estado_placa" class="text-primary fs-6"></strong>:</p>
                    <select class="form-select" name="estado" id="nuevo_estado" style="border-radius: 8px; font-weight: 600;">
                        <option value="disponible">🟢 Disponible para despacho</option>
                        <option value="mantenimiento">🟡 En mantenimiento / taller</option>
                        <option value="inactivo">⚪ Inactivo / Fuera de servicio</option>
                    </select>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-check-circle me-1"></i>Confirmar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle del Vehículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-eye text-info me-2"></i>Detalle del Vehículo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detalleContenido">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando información...
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
    const baseUrl = '{{ url("vehiculos") }}';

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('erroresModal')).show();
        });
    @endif

    function editarVehiculo(id) {
        fetch(`${baseUrl}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('formEditarVehiculo').action = `${baseUrl}/${data.id}`;
            document.getElementById('editar_placa').value = data.placa;
            document.getElementById('editar_numero_movil').value = data.numero_movil;
            document.getElementById('editar_marca').value = data.marca || '';
            document.getElementById('editar_modelo').value = data.modelo || '';

            new bootstrap.Modal(document.getElementById('editarVehiculoModal')).show();
        })
        .catch(() => alert('Error al cargar datos del vehículo'));
    }

    function cambiarEstado(id, placa, estadoActual) {
        document.getElementById('formCambiarEstado').action = `${baseUrl}/${id}/estado`;
        document.getElementById('estado_placa').textContent = placa;
        document.getElementById('nuevo_estado').value = estadoActual;

        new bootstrap.Modal(document.getElementById('cambiarEstadoModal')).show();
    }

    function verDetalle(id) {
        const contenedor = document.getElementById('detalleContenido');
        contenedor.innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando...</div>';
        new bootstrap.Modal(document.getElementById('detalleModal')).show();

        fetch(`${baseUrl}/${id}/detalle`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => { contenedor.innerHTML = html; })
        .catch(() => { contenedor.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar detalle</div>'; });
    }

    function reactivar(id) {
        if (!confirm('¿Reactivar este vehículo? Pasará a estado Disponible.')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${baseUrl}/${id}/reactivar`;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').content;

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PATCH';

        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
