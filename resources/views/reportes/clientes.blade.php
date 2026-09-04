@extends('layouts.app')

@section('title', 'Reporte de Clientes - Taxi Diamantes')
@section('page-title', 'Reporte de Clientes y Demanda')

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
        font-size: 0.73rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    .icon-red { background: #fee2e2; color: #dc2626; }
    .icon-cyan { background: #cffafe; color: #0891b2; }
    .icon-amber { background: #fef3c7; color: #b45309; }

    .metric-kpi-body { margin-bottom: 8px; }
    .metric-num {
        font-size: 1.7rem;
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
    .pill-red { background: #fef2f2; color: #dc2626; }
    .pill-cyan { background: #ecfeff; color: #0891b2; }
    .pill-amber { background: #fffbeb; color: #b45309; }

    /* Tarjeta Principal y Gráficos */
    .card-modern {
        background: #ffffff;
        border: 1px solid rgba(186, 230, 253, 0.7);
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(2, 132, 199, 0.08);
        overflow: hidden;
    }
    .card-header-modern {
        background: #ffffff;
        padding: 16px 22px;
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
        gap: 10px;
    }
    .card-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
    }
    .card-title {
        color: #0a2540;
        font-size: 0.98rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.2px;
    }
    .card-subtitle {
        color: #64748b;
        font-size: 0.76rem;
        margin: 0;
    }

    /* Preset Badges */
    .preset-btn {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        transition: all 0.15s ease;
        text-decoration: none;
        cursor: pointer;
    }
    .preset-btn:hover {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }

    /* Tabla */
    .tabla-clientes thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 11px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .tabla-clientes tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .tabla-clientes tbody tr:hover {
        background-color: #f0f9ff;
    }

    /* Botones Interactivos de Conteo en la Tabla */
    .badge-interactivo {
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid transparent;
        text-decoration: none !important;
        position: relative;
    }
    .badge-interactivo:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    .badge-int-blue {
        background: #f0f9ff;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .badge-int-blue:hover {
        background: #0284c7;
        color: #ffffff;
    }
    .badge-int-green {
        background: #f0fdf4;
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .badge-int-green:hover {
        background: #16a34a;
        color: #ffffff;
    }
    .badge-int-red {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .badge-int-red:hover {
        background: #dc2626;
        color: #ffffff;
    }
    .badge-int-slate {
        background: #f8fafc;
        color: #475569;
        border-color: #cbd5e1;
    }
    .badge-int-slate:hover {
        background: #475569;
        color: #ffffff;
    }

    /* Pills de Filtrado Rápido dentro del Modal */
    .btn-srv-pill {
        font-size: 0.76rem;
        font-weight: 700;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    /* Modales */
    .modal-content-modern {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(2, 132, 199, 0.25);
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
{{-- Encabezado con título moderno y presets de fecha --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Reporte de Clientes</h4>
        <p class="text-muted small mb-0"><i class="bi bi-person-lines-fill text-primary me-1"></i> Historial de uso, fidelidad de pasajeros y detalle individual interactivo de carreras</p>
    </div>
    {{-- Accesos rápidos de fechas --}}
    <div class="d-flex flex-wrap gap-1 mt-2 mt-md-0">
        <button type="button" class="preset-btn" onclick="aplicarPresetCli('hoy')">Hoy</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetCli('ayer')">Ayer</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetCli('7dias')">Últimos 7 días</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetCli('mes')">Este Mes</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetCli('30dias')">Últimos 30 días</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetCli('ano')">Todo el Año</button>
    </div>
</div>

{{-- Filtros Avanzados --}}
<div class="card card-modern mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('reportes.clientes') }}" id="formFiltroClientes" class="row g-3 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="cli_fecha_inicio" class="form-control form-control-sm" value="{{ $filtros['fechaInicio'] }}" style="border-radius: 8px; border-color: #cbd5e1;">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="cli_fecha_fin" class="form-control form-control-sm" value="{{ $filtros['fechaFin'] }}" style="border-radius: 8px; border-color: #cbd5e1;">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Cliente (teléfono o nombre)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="buscar_cliente" class="form-control border-start-0" placeholder="Buscar por teléfono o nombre..." value="{{ $filtros['buscarCliente'] }}" style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;">
                </div>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 fw-bold flex-fill shadow-sm">
                    <i class="bi bi-funnel-fill me-1"></i> Filtrar
                </button>
                <a href="{{ route('reportes.exportar-clientes', request()->query()) }}" class="btn btn-success btn-sm rounded-3 px-3 fw-bold shadow-sm" title="Descargar en Excel">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
                </a>
                @if($filtros['buscarCliente'] || $filtros['fechaInicio'] != now()->subDays(30)->format('Y-m-d') || $filtros['fechaFin'] != now()->format('Y-m-d'))
                    <a href="{{ route('reportes.clientes') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Restablecer filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Fila de 5 Métricas KPI Uniformes y Minimalistas --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
    {{-- 1. Clientes Activos --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Clientes Activos</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ number_format($totales['clientes_activos'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-person-check me-1"></i> Con solicitudes en rango</span>
            </div>
        </div>
    </div>
    {{-- 2. Total Servicios --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Total Solicitudes</span>
                <div class="metric-kpi-icon icon-cyan"><i class="bi bi-taxi-front-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #0891b2;">{{ number_format($totales['total_servicios'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-cyan"><i class="bi bi-journal-text me-1"></i> Carreras pedidas</span>
            </div>
        </div>
    </div>
    {{-- 3. Finalizados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Finalizados</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-check-circle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-success">{{ number_format($totales['finalizados'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-check-all me-1"></i> Carreras exitosas</span>
            </div>
        </div>
    </div>
    {{-- 4. Cancelados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Cancelados</span>
                <div class="metric-kpi-icon icon-red"><i class="bi bi-x-circle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-danger">{{ number_format($totales['cancelados'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-red"><i class="bi bi-slash-circle me-1"></i> Tasa: {{ $totales['tasa_cancelacion'] ?? 0 }}%</span>
            </div>
        </div>
    </div>
    {{-- 5. Efectividad Global --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Efectividad Global</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-bullseye"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #b45309;">{{ $totales['efectividad'] ?? 0 }}%</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-pie-chart-fill me-1"></i> Carreras completadas</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SECCIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS)                              --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Gráfico 1: Top Clientes por Volumen de Carreras (Bar Horizontal) --}}
    <div class="col-lg-7">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-bar-chart-fill"></i></div>
                    <div>
                        <h6 class="card-title">Top 10 Clientes Más Frecuentes</h6>
                        <p class="card-subtitle">Comparativa entre carreras totales solicitadas y finalizadas</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 280px;">
                    <canvas id="chartTopClientes"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico 2: Cuota de Demanda / Concentración (Doughnut) --}}
    <div class="col-lg-5">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="bi bi-pie-chart-fill"></i></div>
                    <div>
                        <h6 class="card-title">Concentración de Demanda</h6>
                        <p class="card-subtitle">Distribución porcentual de carreras por cliente</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; width: 100%; max-width: 250px; height: 240px;">
                    <canvas id="chartCuotaClientes"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TABLA DETALLADA: TOP CLIENTES Y BOTONES DE DETALLE                      --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="card card-modern">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-people-fill"></i></div>
            <div>
                <h6 class="card-title">Ranking de Clientes por Uso</h6>
                <p class="card-subtitle">Presiona cualquier cifra numérica (Servicios, Finalizados, Cancelados o Direcciones) para abrir su historial detallado en ventana modal</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="buscarEnTablaCli" class="form-control border-start-0" placeholder="Filtrar en tabla..." style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;">
            </div>
        </div>
    </div>

    {{-- Banner Informativo para Guiar al Usuario --}}
    <div class="px-3 pt-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between p-2 px-3 rounded-3" style="background: linear-gradient(90deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px; background: #0284c7; color: #ffffff; font-size: 0.85rem; flex-shrink: 0;">
                    <i class="bi bi-cursor-fill"></i>
                </div>
                <div>
                    <span class="fw-bold" style="color: #0369a1; font-size: 0.82rem;">Tabla Interactiva:</span>
                    <span class="text-secondary small ms-1">Presiona sobre cualquier número en <strong>Total Servicios</strong>, <strong>Finalizados</strong>, <strong>Cancelados</strong> o <strong>Direcciones</strong> para ver su desglose en ventana emergente (modal).</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1 mt-1 mt-md-0">
                <span class="badge" style="background: #ffffff; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.72rem; font-weight: 700;">
                    <i class="bi bi-hand-index-thumb me-1"></i>Haz clic en cualquier número
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-clientes mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 45px;">#</th>
                        <th>Teléfono</th>
                        <th>Nombre del Pasajero</th>
                        <th class="text-center">
                            <div>Total Servicios</div>
                            <span class="badge mt-1" style="background: #e0f2fe; color: #0284c7; font-size: 0.65rem; font-weight: 700; text-transform: none;">
                                <i class="bi bi-hand-index me-1"></i>Ver modal
                            </span>
                        </th>
                        <th class="text-center">
                            <div>Finalizados</div>
                            <span class="badge mt-1" style="background: #dcfce7; color: #16a34a; font-size: 0.65rem; font-weight: 700; text-transform: none;">
                                <i class="bi bi-hand-index me-1"></i>Ver modal
                            </span>
                        </th>
                        <th class="text-center">
                            <div>Cancelados</div>
                            <span class="badge mt-1" style="background: #fee2e2; color: #dc2626; font-size: 0.65rem; font-weight: 700; text-transform: none;">
                                <i class="bi bi-hand-index me-1"></i>Ver modal
                            </span>
                        </th>
                        <th class="text-center">
                            <div>Direcciones</div>
                            <span class="badge mt-1" style="background: #f1f5f9; color: #475569; font-size: 0.65rem; font-weight: 700; text-transform: none;">
                                <i class="bi bi-hand-index me-1"></i>Ver modal
                            </span>
                        </th>
                        <th class="pe-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $i => $c)
                    <tr class="fila-cliente">
                        {{-- Posición Podio --}}
                        <td class="ps-3">
                            @if($i === 0 && $c['total_servicios'] > 0)
                                <span class="fs-6" title="Cliente #1 en volumen">🥇</span>
                            @elseif($i === 1 && $c['total_servicios'] > 0)
                                <span class="fs-6" title="Cliente #2">🥈</span>
                            @elseif($i === 2 && $c['total_servicios'] > 0)
                                <span class="fs-6" title="Cliente #3">🥉</span>
                            @else
                                <span class="fw-bold text-muted small">{{ $i + 1 }}</span>
                            @endif
                        </td>

                        {{-- Teléfono --}}
                        <td>
                            <span class="badge col-telefono" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-size:0.85rem; font-weight:800; padding: 5px 10px; border-radius: 6px;">
                                <i class="bi bi-telephone-fill me-1"></i>{{ $c['telefono'] }}
                            </span>
                        </td>

                        {{-- Nombre --}}
                        <td>
                            <div class="fw-bold col-nombre" style="color: #0a2540;">
                                {{ $c['nombre'] ?: 'Cliente no identificado' }}
                            </div>
                        </td>

                        {{-- Total Servicios (Botón Interactivo que abre el modal) --}}
                        <td class="text-center">
                            <button type="button" class="badge-interactivo badge-int-blue" 
                                title="Clic para ver las {{ $c['total_servicios'] }} carreras de este cliente en el modal"
                                onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', '{{ addslashes($c['nombre'] ?? '') }}', '')">
                                <i class="bi bi-taxi-front-fill me-1"></i>
                                <span>{{ $c['total_servicios'] }}</span>
                                <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem; opacity: 0.85;"></i>
                            </button>
                        </td>

                        {{-- Finalizados (Botón Interactivo que abre el modal filtrando finalizados) --}}
                        <td class="text-center">
                            <button type="button" class="badge-interactivo badge-int-green" 
                                title="Clic para ver las {{ $c['finalizados'] }} carreras finalizadas exitosamente en el modal"
                                onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', '{{ addslashes($c['nombre'] ?? '') }}', 'finalizado')">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                <span>{{ $c['finalizados'] }}</span>
                                <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem; opacity: 0.85;"></i>
                            </button>
                        </td>

                        {{-- Cancelados (Botón Interactivo que abre el modal filtrando cancelados) --}}
                        <td class="text-center">
                            @if($c['cancelados'] > 0)
                                <button type="button" class="badge-interactivo badge-int-red" 
                                    title="Clic para ver las {{ $c['cancelados'] }} carreras canceladas en el modal"
                                    onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', '{{ addslashes($c['nombre'] ?? '') }}', 'cancelado')">
                                    <i class="bi bi-x-circle-fill me-1"></i>
                                    <span>{{ $c['cancelados'] }}</span>
                                    <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem; opacity: 0.85;"></i>
                                </button>
                            @else
                                <button type="button" class="badge-interactivo" style="background:#f8fafc; color:#94a3b8; border:1px solid #e2e8f0;" 
                                    title="Clic para ver carreras (0 cancelados)"
                                    onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', '{{ addslashes($c['nombre'] ?? '') }}', 'cancelado')">
                                    <span>0</span>
                                    <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem; opacity: 0.5;"></i>
                                </button>
                            @endif
                        </td>

                        {{-- Direcciones (Botón Interactivo que abre el modal de direcciones) --}}
                        <td class="text-center">
                            <button type="button" class="badge-interactivo badge-int-slate" 
                                title="Clic para ver las {{ $c['total_direcciones'] }} direcciones registradas de este cliente en el modal"
                                onclick="verDirecciones({{ $c['id'] }}, '{{ $c['telefono'] }}', '{{ addslashes($c['nombre'] ?? '') }}')">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <span>{{ $c['total_direcciones'] }}</span>
                                <i class="bi bi-box-arrow-up-right ms-1 text-secondary" style="font-size: 0.65rem; opacity: 0.85;"></i>
                            </button>
                        </td>

                        {{-- Acción Directa --}}
                        <td class="pe-4 text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 py-1 fw-bold" 
                                title="Abrir modal con todo el historial de carreras"
                                onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', '{{ addslashes($c['nombre'] ?? '') }}', '')">
                                <i class="bi bi-window-stack me-1"></i>Detalle
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron clientes con solicitudes en el período seleccionado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando los <strong>{{ count($clientes) }} clientes</strong> con mayor actividad en el rango seleccionado
            </small>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: SERVICIOS DEL CLIENTE                                            --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalServicios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7; font-size: 1.1rem;">
                        <i class="bi bi-taxi-front-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Historial Detallado de Carreras del Pasajero</h6>
                        <small class="text-info opacity-90" id="modalServiciosSubtitulo">Cargando datos del cliente...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                {{-- Barra Superior del Modal: Filtro de Estado Rápido y Total --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                    <div class="d-flex flex-wrap gap-1 align-items-center">
                        <span class="small fw-bold text-muted me-1"><i class="bi bi-funnel me-1"></i>Filtrar por estado:</span>
                        <button type="button" class="btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-primary text-white active" id="btnPillTodos" onclick="filtrarEstadoModal('')">
                            Todos
                        </button>
                        <button type="button" class="btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-outline-secondary" id="btnPillFinalizados" onclick="filtrarEstadoModal('finalizado')">
                            <i class="bi bi-check-circle-fill text-success me-1"></i>Finalizados
                        </button>
                        <button type="button" class="btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-outline-secondary" id="btnPillCancelados" onclick="filtrarEstadoModal('cancelado')">
                            <i class="bi bi-x-circle-fill text-danger me-1"></i>Cancelados
                        </button>
                        <button type="button" class="btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-outline-secondary" id="btnPillAsignados" onclick="filtrarEstadoModal('asignado')">
                            <i class="bi bi-person-badge-fill text-primary me-1"></i>Asignados
                        </button>
                        <button type="button" class="btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-outline-secondary" id="btnPillEnCamino" onclick="filtrarEstadoModal('en_camino')">
                            <i class="bi bi-taxi-front text-info me-1"></i>En Camino
                        </button>
                        <button type="button" class="btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-outline-secondary" id="btnPillPendientes" onclick="filtrarEstadoModal('pendiente')">
                            <i class="bi bi-clock-fill text-warning me-1"></i>Pendientes
                        </button>
                    </div>
                    <div>
                        <span class="badge rounded-pill bg-light text-dark border px-3 py-2" id="srvTotal" style="font-size: 0.8rem; font-weight: 700;">
                            Cargando...
                        </span>
                    </div>
                </div>

                {{-- Filtro secundario por Rango de Fechas dentro del modal --}}
                <div class="row g-2 mb-3 align-items-center p-2 px-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="col-6 col-md-4">
                        <label class="form-label small fw-bold text-muted mb-0">Fecha Solicitud Desde</label>
                        <input type="date" class="form-control form-control-sm" id="srvFechaInicio" style="border-radius: 6px;">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small fw-bold text-muted mb-0">Fecha Solicitud Hasta</label>
                        <input type="date" class="form-control form-control-sm" id="srvFechaFin" style="border-radius: 6px;">
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end gap-2 mt-2 mt-md-0">
                        <button class="btn btn-primary btn-sm rounded-3 fw-bold flex-fill" onclick="cargarServicios()">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar Fechas
                        </button>
                        <button class="btn btn-outline-secondary btn-sm rounded-3 fw-bold" title="Limpiar fechas y ver todo el histórico" onclick="limpiarFechasModal()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Todo
                        </button>
                    </div>
                    <input type="hidden" id="srvEstadoFiltro" value="">
                </div>

                {{-- Tabla de carreras --}}
                <div class="table-responsive rounded-3 border" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                        <thead class="sticky-top" style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th class="ps-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem;">ID</th>
                                <th class="py-2 text-center text-uppercase text-muted" style="font-size: 0.7rem;">Estado</th>
                                <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Dirección de Recogida</th>
                                <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Móvil Asignado</th>
                                <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Condición</th>
                                <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Operador Despachador</th>
                                <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Solicitud</th>
                                <th class="pe-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Fin</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyServicios">
                            <tr><td colspan="8" class="text-center text-muted py-4">Cargando registros...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top">
                <button type="button" class="btn btn-secondary btn-sm rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: DIRECCIONES DEL CLIENTE                                          --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDirecciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background: #fee2e2; color: #dc2626; font-size: 1.1rem;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Direcciones Guardadas del Pasajero</h6>
                        <small class="text-info opacity-90" id="modalDireccionesSubtitulo">Cargando...</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="table-responsive rounded-3 border" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.83rem;">
                        <thead class="sticky-top" style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th class="ps-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Dirección</th>
                                <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Referencia</th>
                                <th class="py-2 text-center text-uppercase text-muted" style="font-size: 0.7rem;">Carreras Despachadas</th>
                                <th class="py-2 text-center text-uppercase text-muted" style="font-size: 0.7rem;">Frecuente</th>
                                <th class="py-2 text-center text-uppercase text-muted" style="font-size: 0.7rem;">Estado</th>
                                <th class="pe-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem;">Último Uso</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDirecciones">
                            <tr><td colspan="6" class="text-center text-muted py-4">Cargando direcciones...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top">
                <button type="button" class="btn btn-secondary btn-sm rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ══════════════════════════════════════════════════════════════
    // CONFIGURACIÓN DE GRÁFICOS (CHART.JS)
    // ══════════════════════════════════════════════════════════════
    Chart.defaults.font.family = 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.color = '#64748b';

    // ── 1. Comparativa de Servicios por Cliente (Bar Horizontal) ──
    const ctxTopCli = document.getElementById('chartTopClientes').getContext('2d');
    new Chart(ctxTopCli, {
        type: 'bar',
        data: {
            labels: @json($graficos['nombres']),
            datasets: [
                {
                    label: 'Total Solicitados',
                    data: @json($graficos['servicios']),
                    backgroundColor: '#0284c7',
                    hoverBackgroundColor: '#0052cc',
                    borderRadius: 4,
                    barPercentage: 0.7
                },
                {
                    label: 'Finalizados (Éxito)',
                    data: @json($graficos['finalizados']),
                    backgroundColor: '#10b981',
                    hoverBackgroundColor: '#059669',
                    borderRadius: 4,
                    barPercentage: 0.7
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { boxWidth: 12, boxHeight: 12, usePointStyle: true, font: { weight: 600, size: 11 } }
                },
                tooltip: {
                    backgroundColor: '#0a2540',
                    padding: 8,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(226, 232, 240, 0.8)' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { weight: 600, size: 11 } }
                }
            }
        }
    });

    // ── 2. Cuota de Demanda por Clientes (Doughnut) ──
    const ctxCuotaCli = document.getElementById('chartCuotaClientes').getContext('2d');
    new Chart(ctxCuotaCli, {
        type: 'doughnut',
        data: {
            labels: @json($graficos['share_labels']),
            datasets: [{
                data: @json($graficos['share_data']),
                backgroundColor: @json($graficos['share_colors']),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, font: { size: 10 } }
                },
                tooltip: {
                    backgroundColor: '#0a2540',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const val = context.raw || 0;
                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${val} carreras (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // ══════════════════════════════════════════════════════════════
    // PRESETS RÁPIDOS DE FECHAS (1 CLICK)
    // ══════════════════════════════════════════════════════════════
    function aplicarPresetCli(tipo) {
        const inputInicio = document.getElementById('cli_fecha_inicio');
        const inputFin = document.getElementById('cli_fecha_fin');
        const hoy = new Date();

        function formatoFecha(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        let fInicio = new Date();
        let fFin = new Date();

        if (tipo === 'hoy') {
            fInicio = hoy;
            fFin = hoy;
        } else if (tipo === 'ayer') {
            const ayer = new Date();
            ayer.setDate(hoy.getDate() - 1);
            fInicio = ayer;
            fFin = ayer;
        } else if (tipo === '7dias') {
            fInicio.setDate(hoy.getDate() - 7);
            fFin = hoy;
        } else if (tipo === 'mes') {
            fInicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fFin = hoy;
        } else if (tipo === '30dias') {
            fInicio.setDate(hoy.getDate() - 30);
            fFin = hoy;
        } else if (tipo === 'ano') {
            fInicio = new Date(hoy.getFullYear(), 0, 1);
            fFin = hoy;
        }

        inputInicio.value = formatoFecha(fInicio);
        inputFin.value = formatoFecha(fFin);

        document.getElementById('formFiltroClientes').submit();
    }

    // ══════════════════════════════════════════════════════════════
    // FILTRADO INSTANTÁNEO EN TABLA DE CLIENTES
    // ══════════════════════════════════════════════════════════════
    document.getElementById('buscarEnTablaCli').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        const filas = document.querySelectorAll('.fila-cliente');

        filas.forEach(f => {
            const tel = f.querySelector('.col-telefono')?.textContent.toLowerCase() || '';
            const nombre = f.querySelector('.col-nombre')?.textContent.toLowerCase() || '';
            if (tel.includes(term) || nombre.includes(term)) {
                f.style.display = '';
            } else {
                f.style.display = 'none';
            }
        });
    });

    // ══════════════════════════════════════════════════════════════
    // MODALES INTERACTIVOS: VER SERVICIOS Y DIRECCIONES
    // ══════════════════════════════════════════════════════════════
    let currentClienteId = null;

    function formatFecha(f) {
        if (!f) return '-';
        const d = new Date(f);
        return d.toLocaleDateString('es-CO', { day:'2-digit', month:'2-digit', year:'numeric' }) + ' ' +
               d.toLocaleTimeString('es-CO', { hour:'2-digit', minute:'2-digit' });
    }

    function colorEstado(e) {
        const map = {
            finalizado: { bg: '#f0fdf4', color: '#16a34a', border: '#bbf7d0', label: 'Finalizado' },
            cancelado:  { bg: '#fef2f2', color: '#dc2626', border: '#fecaca', label: 'Cancelado' },
            asignado:   { bg: '#f0f9ff', color: '#0284c7', border: '#bae6fd', label: 'Asignado' },
            en_camino:  { bg: '#ecfeff', color: '#0891b2', border: '#a5f3fc', label: 'En Camino' },
            pendiente:  { bg: '#fffbeb', color: '#b45309', border: '#fde68a', label: 'Pendiente' },
        };
        const s = map[e] || { bg: '#f8fafc', color: '#475569', border: '#e2e8f0', label: e };
        return `<span class="badge rounded-pill" style="background:${s.bg}; color:${s.color}; border:1px solid ${s.border}; font-weight:700; padding:4px 8px;">${s.label}</span>`;
    }

    function verServicios(clienteId, telefono, nombre, estado) {
        currentClienteId = clienteId;
        const nombreDisplay = nombre ? `${nombre} (${telefono})` : telefono;
        
        let estadoTxt = 'Todas las carreras';
        if (estado === 'finalizado') estadoTxt = 'Solo Finalizadas';
        if (estado === 'cancelado') estadoTxt = 'Solo Canceladas';
        if (estado === 'asignado') estadoTxt = 'Solo Asignadas';
        if (estado === 'en_camino') estadoTxt = 'Solo En Camino';
        if (estado === 'pendiente') estadoTxt = 'Solo Pendientes';

        document.getElementById('modalServiciosSubtitulo').innerHTML = 
            `Pasajero: <strong class="text-white">${nombreDisplay}</strong> &bull; <span class="badge" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); font-weight:700;">${estadoTxt}</span>`;
        
        // Cargar por defecto el rango de fechas actual de la búsqueda principal
        document.getElementById('srvFechaInicio').value = document.getElementById('cli_fecha_inicio').value || '';
        document.getElementById('srvFechaFin').value = document.getElementById('cli_fecha_fin').value || '';
        
        actualizarPillEstado(estado || '');
        document.getElementById('tbodyServicios').innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando historial de carreras del cliente...</td></tr>';
        
        new bootstrap.Modal(document.getElementById('modalServicios')).show();
        cargarServicios();
    }

    function filtrarEstadoModal(estado) {
        actualizarPillEstado(estado);
        cargarServicios();
    }

    function actualizarPillEstado(estado) {
        document.getElementById('srvEstadoFiltro').value = estado;
        
        // Resetear estilo de todas las pills a outline
        const pills = [
            { id: 'btnPillTodos', val: '' },
            { id: 'btnPillFinalizados', val: 'finalizado', activeClass: 'btn-success' },
            { id: 'btnPillCancelados', val: 'cancelado', activeClass: 'btn-danger' },
            { id: 'btnPillAsignados', val: 'asignado', activeClass: 'btn-primary' },
            { id: 'btnPillEnCamino', val: 'en_camino', activeClass: 'btn-info' },
            { id: 'btnPillPendientes', val: 'pendiente', activeClass: 'btn-warning' }
        ];

        pills.forEach(p => {
            const el = document.getElementById(p.id);
            if (!el) return;
            el.className = 'btn btn-sm py-1 px-3 rounded-pill btn-srv-pill btn-outline-secondary';
        });

        const activeObj = pills.find(p => p.val === estado) || pills[0];
        const activeEl = document.getElementById(activeObj.id);
        if (activeEl) {
            activeEl.className = `btn btn-sm py-1 px-3 rounded-pill btn-srv-pill ${activeObj.activeClass || 'btn-primary'} text-white active shadow-sm`;
            if (activeObj.val === 'pendiente') {
                activeEl.classList.remove('text-white');
                activeEl.classList.add('text-dark');
            }
        }
    }

    function limpiarFechasModal() {
        document.getElementById('srvFechaInicio').value = '';
        document.getElementById('srvFechaFin').value = '';
        cargarServicios();
    }

    async function cargarServicios() {
        const fi = document.getElementById('srvFechaInicio').value;
        const ff = document.getElementById('srvFechaFin').value;
        const estado = document.getElementById('srvEstadoFiltro').value;

        let url = `/reportes/clientes/${currentClienteId}/servicios?`;
        if (estado) url += `&estado=${encodeURIComponent(estado)}`;
        if (fi) url += `&fecha_inicio=${fi}`;
        if (ff) url += `&fecha_fin=${ff}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            const tbody = document.getElementById('tbodyServicios');
            document.getElementById('srvTotal').textContent = `${data.servicios.length} carrera(s) encontradas`;

            if (data.servicios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>No se encontraron carreras para este filtro</td></tr>';
                return;
            }

            tbody.innerHTML = data.servicios.map(s => `<tr>
                <td class="ps-3 fw-bold text-muted">#${s.id}</td>
                <td class="text-center">${colorEstado(s.estado)}</td>
                <td>
                    <div class="fw-semibold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i>${s.direccion || 'Sin dirección registrada'}</div>
                    ${s.referencia ? `<small class="text-muted d-block ps-3">${s.referencia}</small>` : ''}
                </td>
                <td>
                    ${s.numero_movil ? `
                        <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-weight:800; font-size:0.75rem;">
                            #${s.numero_movil}
                        </span>
                        <small class="text-muted fw-bold ms-1">${s.placa || ''}</small>
                    ` : '<span class="text-muted small fst-italic">Sin asignar</span>'}
                </td>
                <td>
                    ${s.condicion && s.condicion !== 'ninguno' ? `<span class="badge bg-light text-dark border">${s.condicion.replace('_', ' ')}</span>` : '<span class="text-muted small">Estándar</span>'}
                </td>
                <td>
                    <small class="text-secondary fw-semibold">${s.operador_nombre || 'Sistema'}</small>
                </td>
                <td><small class="text-muted"><i class="bi bi-clock me-1"></i>${formatFecha(s.fecha_solicitud)}</small></td>
                <td class="pe-3"><small class="text-muted">${formatFecha(s.fecha_fin)}</small></td>
            </tr>`).join('');
        } catch (e) {
            document.getElementById('tbodyServicios').innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Error al cargar las carreras del cliente</td></tr>';
        }
    }

    function verDirecciones(clienteId, telefono, nombre) {
        const nombreDisplay = nombre ? `${nombre} (${telefono})` : telefono;
        document.getElementById('modalDireccionesSubtitulo').innerHTML = 
            `Pasajero: <strong class="text-white">${nombreDisplay}</strong>`;
        document.getElementById('tbodyDirecciones').innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando direcciones guardadas...</td></tr>';
        
        new bootstrap.Modal(document.getElementById('modalDirecciones')).show();
        cargarDirecciones(clienteId);
    }

    async function cargarDirecciones(clienteId) {
        try {
            const res = await fetch(`/reportes/clientes/${clienteId}/direcciones`);
            const data = await res.json();
            const tbody = document.getElementById('tbodyDirecciones');

            if (data.direcciones.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-geo-alt fs-3 d-block mb-2 text-secondary opacity-50"></i>Este cliente no tiene direcciones registradas</td></tr>';
                return;
            }

            tbody.innerHTML = data.direcciones.map(d => `<tr>
                <td class="ps-3 fw-semibold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i>${d.direccion}</td>
                <td><small class="text-muted">${d.referencia || '-'}</small></td>
                <td class="text-center">
                    <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-weight:700; font-size:0.8rem;">
                        ${d.total_servicios} carrera(s)
                    </span>
                </td>
                <td class="text-center">${d.es_frecuente ? '<span class="badge bg-warning text-dark fw-bold"><i class="bi bi-star-fill text-warning me-1"></i>Frecuente</span>' : '<span class="text-muted small">-</span>'}</td>
                <td class="text-center">
                    ${d.activa ? '<span class="badge rounded-pill" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-weight:700;">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>'}
                </td>
                <td class="pe-3"><small class="text-muted">${d.ultimo_uso ? new Date(d.ultimo_uso).toLocaleDateString('es-CO') : '-'}</small></td>
            </tr>`).join('');
        } catch (e) {
            document.getElementById('tbodyDirecciones').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error al cargar direcciones</td></tr>';
        }
    }
</script>
@endpush
