@extends('layouts.app')

@section('title', 'Sanciones - Taxi Diamantes')
@section('page-title', 'Gestión de Sanciones')

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
    .icon-red { background: #fee2e2; color: #dc2626; }
    .icon-green { background: #dcfce7; color: #16a34a; }
    .icon-amber { background: #fef3c7; color: #b45309; }

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
    .pill-red { background: #fef2f2; color: #dc2626; }
    .pill-green { background: #f0fdf4; color: #16a34a; }
    .pill-amber { background: #fffbeb; color: #b45309; }

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
    .tabla-sanciones thead th {
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
    .tabla-sanciones tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .tabla-sanciones tbody tr:hover {
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
    .modal-header-danger {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        border-bottom: 2px solid #f87171;
        padding: 14px 20px;
        color: #ffffff;
    }
    .modal-header-warning {
        background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
        border-bottom: 2px solid #fbbf24;
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
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Gestión de Sanciones</h4>
        <p class="text-muted small mb-0"><i class="bi bi-shield-exclamation text-primary me-1"></i> Control de penalizaciones, tiempos de inhabilitación y estado de móviles</p>
    </div>
</div>

{{-- Métricas Resumen Uniformes y Minimalistas (4 cards) --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mb-4">
    {{-- 1. Total Sanciones --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Total Sanciones</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-shield-slash"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $stats['total'] ?? ($stats['activa'] + $stats['cumplida'] + $stats['anulada']) }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-journal-text me-1"></i> Histórico acumulado</span>
            </div>
        </div>
    </div>
    {{-- 2. Activas --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Sanciones Activas</span>
                <div class="metric-kpi-icon icon-red"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-danger">{{ $stats['activa'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-red"><i class="bi bi-slash-circle me-1"></i> Móviles fuera de despacho</span>
            </div>
        </div>
    </div>
    {{-- 3. Cumplidas --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Cumplidas</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-check2-circle"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $stats['cumplida'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-check-all me-1"></i> Tiempo finalizado</span>
            </div>
        </div>
    </div>
    {{-- 4. Anuladas --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Anuladas</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-slash-circle"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ $stats['anulada'] }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-arrow-counterclockwise me-1"></i> Revocadas / Perdonadas</span>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta Principal de Sanciones --}}
<div class="card card-modern mb-4">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-shield-exclamation"></i></div>
            <div>
                <h6 class="card-title">Registro de Sanciones</h6>
                <p class="card-subtitle">Listado de infracciones aplicadas y monitoreo de tiempo en vivo</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#aplicarSancionModal">
                <i class="bi bi-plus-lg me-1"></i> Aplicar Sanción
            </button>
            @if(auth()->user()->esAdmin())
            <form method="POST" action="{{ route('sanciones.verificar-vencimientos') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold" title="Verificar sanciones vencidas en segundo plano">
                    <i class="bi bi-arrow-repeat me-1"></i> Verificar Vencimientos
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Filtros y Paginación --}}
    <div class="contenedor-filtros">
        <form method="GET" action="{{ route('sanciones.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="vehiculo" class="form-control border-start-0" placeholder="Placa o número móvil..." value="{{ $filtroVehiculo }}" style="border-radius: 0 8px 8px 0;">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="estado" class="form-select form-select-sm" style="font-weight: 600;">
                    <option value="">Todos los estados</option>
                    <option value="activa" {{ $filtroEstado === 'activa' ? 'selected' : '' }}>Activas</option>
                    <option value="cumplida" {{ $filtroEstado === 'cumplida' ? 'selected' : '' }}>Cumplidas</option>
                    <option value="anulada" {{ $filtroEstado === 'anulada' ? 'selected' : '' }}>Anuladas</option>
                </select>
            </div>
            <div class="col-auto d-flex align-items-center gap-1">
                <small class="text-muted fw-semibold" style="font-size: 0.78rem;">Mostrar:</small>
                <select name="per_page" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 88px; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 600;" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
                    <option value="30" {{ ($perPage ?? 20) == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
                    <option value="todos" {{ ($perPage ?? 20) === 'todos' || ($perPage ?? 20) == 10000 ? 'selected' : '' }}>Todos</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if($filtroEstado || $filtroVehiculo)
                    <a href="{{ route('sanciones.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-x-lg me-1"></i> Limpiar
                    </a>
                @endif
            </div>
            @if($filtroEstado || $filtroVehiculo)
                <div class="col-12 mt-1">
                    <small class="text-muted">
                        Filtro activo: 
                        @if($filtroVehiculo) móvil/placa <strong>"{{ $filtroVehiculo }}"</strong> @endif
                        @if($filtroEstado) estado <strong>"{{ ucfirst($filtroEstado) }}"</strong> @endif
                        ({{ $sanciones->total() }} encontrados)
                    </small>
                </div>
            @endif
        </form>
    </div>

    {{-- Tabla de Sanciones --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-sanciones mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Vehículo</th>
                        <th>Artículo</th>
                        <th>Motivo</th>
                        <th>Inicio</th>
                        <th>Fin Estimado</th>
                        <th class="text-center">Tiempo Restante</th>
                        <th class="text-center">Estado</th>
                        <th class="pe-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sanciones as $sancion)
                    <tr>
                        {{-- Vehículo --}}
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-size:0.82rem; font-weight:800; padding: 4px 8px; border-radius: 6px;">
                                    <i class="bi bi-hash"></i>{{ $sancion->vehiculo->numero_movil }}
                                </span>
                                <span class="badge" style="background:#ffffff; color:#0a2540; border:1px solid #cbd5e1; font-size:0.8rem; font-weight:700; letter-spacing:0.5px; border-radius:6px; padding: 4px 8px;">
                                    {{ $sancion->vehiculo->placa }}
                                </span>
                            </div>
                        </td>

                        {{-- Artículo --}}
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-weight:800; font-size:0.75rem;">
                                    {{ $sancion->articulo->codigo }}
                                </span>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 140px;" title="{{ $sancion->articulo->descripcion }}">
                                    {{ $sancion->articulo->descripcion }}
                                </small>
                            </div>
                        </td>

                        {{-- Motivo --}}
                        <td>
                            <span class="text-secondary text-truncate d-inline-block" style="max-width: 200px;" title="{{ $sancion->motivo }}">
                                {{ Str::limit($sancion->motivo, 40) }}
                            </span>
                        </td>

                        {{-- Fechas --}}
                        <td>
                            <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $sancion->fecha_inicio->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ $sancion->fecha_fin->format('d/m/Y H:i') }}</span>
                        </td>

                        {{-- Tiempo Restante (Countdown) --}}
                        <td class="text-center">
                            @if($sancion->estado === 'activa')
                                <span class="badge countdown rounded-pill" data-fin="{{ $sancion->fecha_fin->toIso8601String() }}" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-weight: 800; font-size: 0.78rem; padding: 4px 10px;">
                                    <i class="bi bi-stopwatch me-1"></i>{{ $sancion->tiempoRestanteFormateado() }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'activa'   => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Activa', 'icon' => 'bi-exclamation-octagon-fill'],
                                    'cumplida' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Cumplida', 'icon' => 'bi-check-circle-fill'],
                                    'anulada'  => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'label' => 'Anulada', 'icon' => 'bi-x-circle-fill'],
                                ];
                                $st = $statusMap[$sancion->estado] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($sancion->estado), 'icon' => 'bi-circle'];
                            @endphp
                            <span class="badge rounded-pill" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; border: 1px solid {{ $st['border'] }}; font-size: 0.72rem; font-weight: 700; padding: 5px 12px;">
                                <i class="bi {{ $st['icon'] }} me-1"></i>{{ $st['label'] }}
                            </span>
                        </td>

                        {{-- Acciones --}}
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-info btn-accion" title="Ver detalle completo"
                                    onclick="verDetalle({{ $sancion->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($sancion->estado === 'activa')
                                <button type="button" class="btn btn-sm btn-outline-warning btn-accion" title="Anular sanción"
                                    onclick="anularSancion({{ $sancion->id }}, '{{ $sancion->vehiculo->placa }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-shield-check display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron sanciones registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginador Moderno --}}
        @if($sanciones->hasPages() || $sanciones->total() > 0)
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center border-top bg-light">
            <small class="text-muted mb-2 mb-md-0">
                @if(($perPage ?? 20) === 'todos' || ($perPage ?? 20) == 10000)
                    Mostrando <strong>todas las {{ $sanciones->total() }}</strong> sanciones
                @else
                    Mostrando de la <strong>{{ $sanciones->firstItem() ?? 0 }}</strong> a la <strong>{{ $sanciones->lastItem() ?? 0 }}</strong> de <strong>{{ $sanciones->total() }}</strong> sanciones
                @endif
            </small>
            @if(($perPage ?? 20) !== 'todos' && ($perPage ?? 20) != 10000 && $sanciones->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $sanciones->links() }}
                </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Aplicar Sanción --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="aplicarSancionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" action="{{ route('sanciones.aplicar') }}" novalidate>
                @csrf
                <div class="modal-header modal-header-danger">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Aplicar Sanción a Vehículo</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Vehículo Móvil <span class="text-danger">*</span></label>
                        <select class="form-select" name="vehiculo_id" id="select_vehiculo" required style="border-radius: 8px;">
                            <option value="">Cargando vehículos disponibles...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Artículo de Infracción <span class="text-danger">*</span></label>
                        <select class="form-select" name="articulo_id" id="select_articulo" required style="border-radius: 8px;">
                            <option value="">Cargando catálogo de artículos...</option>
                        </select>
                        <div class="mt-2" id="info_tiempo"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Motivo de la Sanción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motivo" rows="3" required minlength="5" placeholder="Describa los hechos que causaron la sanción..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-3 fw-bold"><i class="bi bi-shield-x me-1"></i>Aplicar Sanción</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Anular Sanción --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="anularSancionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <form method="POST" id="formAnular" novalidate>
                @csrf
                <div class="modal-header modal-header-warning">
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-x-circle me-2"></i>Anular Sanción</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">Anular la sanción activa del vehículo <strong id="anular_placa" class="text-primary fs-6"></strong>:</p>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-secondary">Motivo o justificación de anulación <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="comentario" rows="3" required minlength="5" placeholder="Indique la justificación para levantar la sanción antes de tiempo..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning btn-sm rounded-3 fw-bold"><i class="bi bi-check-circle me-1"></i>Confirmar Anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle de Sanción --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-eye text-info me-2"></i>Detalle de la Sanción</h6>
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
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let articulosData = [];

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('erroresModal')).show();
});
@endif

// ── Countdown en tiempo real ──
function actualizarCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const fin = new Date(el.dataset.fin);
        const ahora = new Date();
        const diff = fin - ahora;

        if (diff <= 0) {
            el.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Vencida';
            el.style.background = '#f1f5f9';
            el.style.color = '#64748b';
            el.style.borderColor = '#cbd5e1';
            return;
        }

        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        let texto = '';
        if (d > 0) texto += d + 'd ';
        texto += String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        el.innerHTML = '<i class="bi bi-stopwatch me-1"></i>' + texto;
    });
}
setInterval(actualizarCountdowns, 1000);
actualizarCountdowns();

// ── Cargar vehículos disponibles al abrir modal ──
document.getElementById('aplicarSancionModal').addEventListener('show.bs.modal', function() {
    const sel = document.getElementById('select_vehiculo');
    sel.innerHTML = '<option value="">Cargando vehículos disponibles...</option>';

    fetch('{{ route("vehiculos.disponibles") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        sel.innerHTML = '<option value="">Seleccione un vehículo móvil</option>';
        data.vehiculos.forEach(v => {
            sel.innerHTML += `<option value="${v.id}">Móvil ${v.numero_movil} - ${v.placa}</option>`;
        });
    });

    // Cargar artículos
    const selArt = document.getElementById('select_articulo');
    selArt.innerHTML = '<option value="">Cargando catálogo de artículos...</option>';

    fetch('{{ route("articulos-sancion.activos") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        articulosData = data;
        selArt.innerHTML = '<option value="">Seleccione un artículo de infracción</option>';
        data.forEach(a => {
            selArt.innerHTML += `<option value="${a.id}">${a.codigo} - ${a.descripcion}</option>`;
        });
    });
});

// Mostrar tiempo al seleccionar artículo
document.getElementById('select_articulo').addEventListener('change', function() {
    const info = document.getElementById('info_tiempo');
    const art = articulosData.find(a => a.id == this.value);
    if (art) {
        const mins = art.tiempo_sancion;
        let texto = '';
        if (mins >= 1440) texto = Math.floor(mins/1440) + ' día(s)';
        else if (mins >= 60) texto = Math.floor(mins/60) + ' hora(s)';
        else texto = mins + ' minuto(s)';
        info.innerHTML = `<span class="badge rounded-pill" style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; font-weight:700;"><i class="bi bi-clock me-1"></i>Duración estimada: ${mins} min (${texto})</span>`;
    } else {
        info.innerHTML = '';
    }
});

function anularSancion(id, placa) {
    document.getElementById('formAnular').action = `{{ url('sanciones') }}/${id}/anular`;
    document.getElementById('anular_placa').textContent = placa;
    new bootstrap.Modal(document.getElementById('anularSancionModal')).show();
}

function verDetalle(id) {
    const c = document.getElementById('detalleContenido');
    c.innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando...</div>';
    new bootstrap.Modal(document.getElementById('detalleModal')).show();

    fetch(`{{ url('sanciones') }}/${id}/detalle`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text())
    .then(html => { c.innerHTML = html; })
    .catch(() => { c.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar detalle</div>'; });
}
</script>
@endpush
