@extends('layouts.app')

@section('title', 'Artículos de Sanción - Taxi Diamantes')
@section('page-title', 'Artículos de Sanción')

@push('styles')
<style>
    /* Estilos KPI Uniformes y Minimalistas */
    .metric-kpi-card {
        background: #ffffff;
        border: 1px solid rgba(186, 230, 253, 0.75);
        border-radius: 16px;
        padding: 16px 18px;
        position: relative;
        box-shadow: 0 4px 20px -2px rgba(2, 132, 199, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }
    .metric-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -4px rgba(2, 132, 199, 0.15);
        border-color: #38bdf8;
    }
    .metric-kpi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .metric-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
    }
    .metric-kpi-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }
    .icon-blue { background: #e0f2fe; color: #0284c7; }
    .icon-green { background: #dcfce7; color: #16a34a; }
    .icon-slate { background: #f1f5f9; color: #64748b; }
    .icon-cyan { background: #cffafe; color: #0891b2; }

    .metric-kpi-body { margin-bottom: 8px; }
    .metric-num {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0a2540;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .metric-kpi-footer {
        display: flex;
        align-items: center;
        gap: 6px;
        min-height: 24px;
    }
    .metric-kpi-pill {
        display: inline-flex;
        align-items: center;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .pill-blue { background: #f0f9ff; color: #0284c7; }
    .pill-green { background: #f0fdf4; color: #16a34a; }
    .pill-slate { background: #f8fafc; color: #64748b; }
    .pill-cyan { background: #ecfeff; color: #0891b2; }

    /* Tarjeta Principal */
    .card-modern {
        background: #ffffff;
        border: 1px solid rgba(186, 230, 253, 0.7);
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(2, 132, 199, 0.08);
        overflow: hidden;
    }
    .card-header-modern {
        background: #ffffff;
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
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
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
    }
    .card-title {
        color: #0a2540;
        font-size: 1.05rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.2px;
    }
    .card-subtitle {
        color: #64748b;
        font-size: 0.78rem;
        margin: 0;
    }

    /* Contenedor de Filtros */
    .contenedor-filtros {
        padding: 14px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Tabla Estilizada */
    .tabla-articulos thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .tabla-articulos tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .tabla-articulos tbody tr:hover {
        background-color: #f0f9ff;
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

    /* Modales */
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

    /* Paginación */
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
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Artículos de Sanción</h4>
        <p class="text-muted small mb-0"><i class="bi bi-journal-text text-primary me-1"></i> Catálogo oficial de faltas, tipificaciones disciplinarias y tiempos reglamentarios</p>
    </div>
</div>

{{-- Métricas Resumen Uniformes y Minimalistas (4 cards) --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mb-4">
    {{-- 1. Total Artículos --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Total Artículos</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-journal-bookmark-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $stats['total'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-card-checklist me-1"></i> Reglamento interno</span>
            </div>
        </div>
    </div>
    {{-- 2. Activos --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Artículos Activos</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-check-circle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-success">{{ $stats['activos'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-check-all me-1"></i> Aplicables en despacho</span>
            </div>
        </div>
    </div>
    {{-- 3. Inactivos --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Inactivos</span>
                <div class="metric-kpi-icon icon-slate"><i class="bi bi-pause-circle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-secondary">{{ $stats['inactivos'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-slate"><i class="bi bi-dash-circle me-1"></i> Deshabilitados</span>
            </div>
        </div>
    </div>
    {{-- 4. Promedio Tiempo --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Tiempo Promedio</span>
                <div class="metric-kpi-icon icon-cyan"><i class="bi bi-stopwatch-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="font-size: 1.45rem;">
                    {{ \App\Models\ArticuloSancion::formatearMinutos($stats['promedio_tiempo']) }}
                </div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-cyan"><i class="bi bi-clock-history me-1"></i> Duración típica</span>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta Principal de Artículos --}}
<div class="card card-modern mb-4">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-journal-text"></i></div>
            <div>
                <h6 class="card-title">Catálogo de Artículos</h6>
                <p class="card-subtitle">Administración de infracciones, tiempos de penalización y estados</p>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#crearArticuloModal">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Artículo
            </button>
        </div>
    </div>

    {{-- Filtros y Paginación --}}
    <div class="contenedor-filtros">
        <form method="GET" action="{{ route('articulos-sancion.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Código o descripción..." value="{{ $buscar }}" style="border-radius: 0 8px 8px 0;">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="estado" class="form-select form-select-sm" style="font-weight: 600;">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ $filtroEstado === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ $filtroEstado === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-auto d-flex align-items-center gap-1">
                <small class="text-muted fw-semibold" style="font-size: 0.78rem;">Mostrar:</small>
                <select name="per_page" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 88px; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 600;" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ ($perPage ?? 10) == 20 ? 'selected' : '' }}>20</option>
                    <option value="30" {{ ($perPage ?? 10) == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="todos" {{ ($perPage ?? 10) === 'todos' || ($perPage ?? 10) == 10000 ? 'selected' : '' }}>Todos</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if($buscar || $filtroEstado)
                    <a href="{{ route('articulos-sancion.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-x-lg me-1"></i> Limpiar
                    </a>
                @endif
            </div>
            @if($buscar || $filtroEstado)
                <div class="col-12 mt-1">
                    <small class="text-muted">
                        Filtro activo: 
                        @if($buscar) texto <strong>"{{ $buscar }}"</strong> @endif
                        @if($filtroEstado) estado <strong>"{{ ucfirst($filtroEstado) }}"</strong> @endif
                        ({{ $articulos->total() }} encontrados)
                    </small>
                </div>
            @endif
        </form>
    </div>

    {{-- Tabla de Artículos --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-articulos mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Descripción</th>
                        <th>Tiempo Reglamentario</th>
                        <th class="text-center">Estado</th>
                        <th class="pe-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articulos as $articulo)
                    <tr>
                        {{-- Código --}}
                        <td class="ps-4">
                            <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-size:0.84rem; font-weight:800; padding: 5px 10px; border-radius: 8px;">
                                <i class="bi bi-hash me-1"></i>{{ $articulo->codigo }}
                            </span>
                        </td>

                        {{-- Descripción --}}
                        <td>
                            <div class="fw-semibold" style="color: #0a2540;">{{ $articulo->descripcion }}</div>
                        </td>

                        {{-- Tiempo de Sanción --}}
                        <td>
                            <div class="d-inline-flex align-items-center gap-1">
                                <span class="badge" style="background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; font-weight: 700; font-size: 0.78rem;">
                                    <i class="bi bi-clock me-1 text-primary"></i>{{ $articulo->tiempo_sancion }} min
                                </span>
                                <small class="text-muted fw-semibold">({{ $articulo->tiempoFormateado() }})</small>
                            </div>
                        </td>

                        {{-- Estado --}}
                        <td class="text-center">
                            @if($articulo->estado === 'activo')
                                <span class="badge rounded-pill" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 0.72rem; font-weight: 700; padding: 5px 12px;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; font-size: 0.72rem; font-weight: 700; padding: 5px 12px;">
                                    <i class="bi bi-pause-circle-fill me-1"></i>Inactivo
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-accion" title="Editar artículo"
                                    onclick="editarArticulo({{ $articulo->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($articulo->estado === 'activo')
                                    <form method="POST" action="{{ route('articulos-sancion.cambiar-estado', $articulo) }}" class="d-inline"
                                        onsubmit="return confirm('¿Desactivar el artículo {{ $articulo->codigo }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="inactivo">
                                        <button type="submit" class="btn btn-sm btn-outline-warning btn-accion" title="Desactivar">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('articulos-sancion.cambiar-estado', $articulo) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="activo">
                                        <button type="submit" class="btn btn-sm btn-outline-success btn-accion" title="Activar">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron artículos de sanción
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginador Moderno --}}
        @if($articulos->hasPages() || $articulos->total() > 0)
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center border-top bg-light">
            <small class="text-muted mb-2 mb-md-0">
                @if(($perPage ?? 10) === 'todos' || ($perPage ?? 10) == 10000)
                    Mostrando <strong>todos los {{ $articulos->total() }}</strong> artículos
                @else
                    Mostrando del <strong>{{ $articulos->firstItem() ?? 0 }}</strong> al <strong>{{ $articulos->lastItem() ?? 0 }}</strong> de <strong>{{ $articulos->total() }}</strong> artículos
                @endif
            </small>
            @if(($perPage ?? 10) !== 'todos' && ($perPage ?? 10) != 10000 && $articulos->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $articulos->links() }}
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Artículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearArticuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" action="{{ route('articulos-sancion.store') }}" novalidate>
                @csrf
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-plus-lg text-info me-2"></i>Nuevo Artículo de Sanción</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="codigo" value="{{ old('codigo') }}" required maxlength="20" placeholder="Ej: ART-01" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Descripción de la Infracción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descripcion" rows="3" required placeholder="Detalle de la falta sancionable..." style="border-radius: 8px;">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-secondary">Tiempo de sanción (minutos) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="tiempo_sancion" value="{{ old('tiempo_sancion', 60) }}" required min="1" style="border-radius: 8px;">
                            <div class="form-text" style="font-size: 0.75rem;">Ej: 60 = 1 hora, 1440 = 1 día, 2880 = 2 días</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-secondary">Estado inicial</label>
                            <select class="form-select" name="estado" style="border-radius: 8px;">
                                <option value="activo" {{ old('estado') !== 'inactivo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-bold"><i class="bi bi-save me-1"></i>Guardar Artículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Editar Artículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarArticuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formEditarArticulo" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header modal-header-modern">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-pencil-square text-info me-2"></i>Editar Artículo de Sanción</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="codigo" id="editar_codigo" required maxlength="20" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Descripción de la Infracción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descripcion" id="editar_descripcion" rows="3" required style="border-radius: 8px;"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-secondary">Tiempo de sanción (minutos) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="tiempo_sancion" id="editar_tiempo" required min="1" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-secondary">Estado</label>
                            <select class="form-select" name="estado" id="editar_estado" style="border-radius: 8px;">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
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

{{-- Errores de Validación --}}
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
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
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
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('erroresModal')).show();
});
@endif

function editarArticulo(id) {
    fetch(`{{ url('articulos-sancion') }}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('formEditarArticulo').action = `{{ url('articulos-sancion') }}/${data.id}`;
        document.getElementById('editar_codigo').value = data.codigo;
        document.getElementById('editar_descripcion').value = data.descripcion;
        document.getElementById('editar_tiempo').value = data.tiempo_sancion;
        document.getElementById('editar_estado').value = data.estado;
        new bootstrap.Modal(document.getElementById('editarArticuloModal')).show();
    })
    .catch(() => alert('Error al cargar datos del artículo'));
}
</script>
@endpush
