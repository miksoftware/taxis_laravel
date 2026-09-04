@extends('layouts.app')

@section('title', 'Reporte de Servicios - Taxi Diamantes')
@section('page-title', 'Reporte Ejecutivo de Servicios')

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
    .icon-navy { background: #e2e8f0; color: #0a2540; }

    .metric-kpi-body { margin-bottom: 8px; }
    .metric-num {
        font-size: 1.65rem;
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
    .pill-navy { background: #f8fafc; color: #0a2540; }

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
    .preset-btn.active {
        background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    }

    /* Buscador Autocompletar de Vehículo */
    .vehiculo-search-wrap { position: relative; }
    .vehiculo-search-wrap input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 0.85rem;
        width: 100%;
        background: #ffffff;
    }
    .vehiculo-search-wrap input:focus {
        border-color: #0284c7;
        outline: none;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
    .vehiculo-search-wrap .vehiculo-dropdown {
        position: absolute;
        z-index: 1050;
        background: #ffffff;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        max-height: 220px;
        overflow-y: auto;
        width: 100%;
        box-shadow: 0 10px 25px -4px rgba(2, 132, 199, 0.2);
        display: none;
        margin-top: 4px;
    }
    .vehiculo-search-wrap .veh-item {
        padding: 8px 12px;
        cursor: pointer;
        font-size: 0.82rem;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s;
    }
    .vehiculo-search-wrap .veh-item:hover {
        background: #f0f9ff;
        color: #0284c7;
        font-weight: 600;
    }

    /* Tablas */
    .tabla-reporte thead th {
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
    .tabla-reporte tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.84rem;
        vertical-align: middle;
    }
    .tabla-reporte tbody tr:hover {
        background-color: #f0f9ff;
    }

    /* Paginación */
    .pagination { margin-bottom: 0; gap: 4px; }
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
{{-- Encabezado con título moderno y presets de fecha --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Reporte de Servicios</h4>
        <p class="text-muted small mb-0"><i class="bi bi-graph-up text-primary me-1"></i> Análisis estadístico, indicadores de efectividad, tiempos y demanda en tiempo real</p>
    </div>
    {{-- Accesos rápidos de fechas --}}
    <div class="d-flex flex-wrap gap-1 mt-2 mt-md-0">
        <button type="button" class="preset-btn" onclick="aplicarPreset('hoy')">Hoy</button>
        <button type="button" class="preset-btn" onclick="aplicarPreset('ayer')">Ayer</button>
        <button type="button" class="preset-btn" onclick="aplicarPreset('7dias')">Últimos 7 días</button>
        <button type="button" class="preset-btn" onclick="aplicarPreset('mes')">Este Mes</button>
        <button type="button" class="preset-btn" onclick="aplicarPreset('30dias')">Últimos 30 días</button>
    </div>
</div>

{{-- Filtros Avanzados --}}
<div class="card card-modern mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('reportes.servicios') }}" id="formFiltroReporte" class="row g-3 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="filtro_fecha_inicio" class="form-control form-control-sm" value="{{ $filtros['fechaInicio'] }}" style="border-radius: 8px; border-color: #cbd5e1;">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="filtro_fecha_fin" class="form-control form-control-sm" value="{{ $filtros['fechaFin'] }}" style="border-radius: 8px; border-color: #cbd5e1;">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm" style="border-radius: 8px; border-color: #cbd5e1; font-weight: 600;">
                    <option value="">Todos los estados</option>
                    @foreach(['pendiente' => 'Pendiente', 'asignado' => 'Asignado', 'en_camino' => 'En Camino', 'finalizado' => 'Finalizado', 'cancelado' => 'Cancelado'] as $k => $lbl)
                        <option value="{{ $k }}" {{ $filtros['estado'] == $k ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Operador</label>
                <select name="operador_id" class="form-select form-select-sm" style="border-radius: 8px; border-color: #cbd5e1;">
                    <option value="">Todos los operadores</option>
                    @foreach($operadores as $op)
                        <option value="{{ $op->id }}" {{ $filtros['operadorId'] == $op->id ? 'selected' : '' }}>{{ $op->nombre }} {{ $op->apellidos }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small fw-bold text-secondary mb-1">Vehículo</label>
                <select name="vehiculo_id" class="form-select form-select-sm" id="selectVehiculo">
                    <option value="">Todos los vehículos</option>
                    @foreach($vehiculos as $v)
                        <option value="{{ $v->id }}" {{ $filtros['vehiculoId'] == $v->id ? 'selected' : '' }}>Móvil {{ $v->numero_movil }} — {{ $v->placa }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 fw-bold flex-fill shadow-sm">
                    <i class="bi bi-funnel-fill me-1"></i> Filtrar
                </button>
                <a href="{{ route('reportes.exportar-servicios', request()->query()) }}" class="btn btn-success btn-sm rounded-3 px-3 fw-bold shadow-sm" title="Descargar en formato Excel">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                </a>
                @if($filtros['estado'] || $filtros['operadorId'] || $filtros['vehiculoId'] || $filtros['fechaInicio'] != now()->subDays(30)->format('Y-m-d') || $filtros['fechaFin'] != now()->format('Y-m-d'))
                    <a href="{{ route('reportes.servicios') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Restablecer filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Fila de 6 Métricas KPI Uniformes y Minimalistas --}}
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 mb-4">
    {{-- 1. Total Servicios --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Total Solicitudes</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-taxi-front-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ number_format($estadisticas['total'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-journal-check me-1"></i> Demanda total</span>
            </div>
        </div>
    </div>
    {{-- 2. Finalizados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Finalizados</span>
                <div class="metric-kpi-icon icon-green"><i class="bi bi-check-circle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-success">{{ number_format($estadisticas['finalizados'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-green"><i class="bi bi-check-all me-1"></i> Completados</span>
            </div>
        </div>
    </div>
    {{-- 3. Cancelados --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Cancelados</span>
                <div class="metric-kpi-icon icon-red"><i class="bi bi-x-circle-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num text-danger">{{ number_format($estadisticas['cancelados'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-red"><i class="bi bi-slash-circle me-1"></i> Tasa: {{ $estadisticas['tasa_cancelacion'] ?? 0 }}%</span>
            </div>
        </div>
    </div>
    {{-- 4. Efectividad --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Efectividad</span>
                <div class="metric-kpi-icon icon-cyan"><i class="bi bi-bullseye"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #0891b2;">{{ $estadisticas['efectividad'] }}%</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-cyan"><i class="bi bi-pie-chart-fill me-1"></i> Ratio de éxito</span>
            </div>
        </div>
    </div>
    {{-- 5. T. Asignación --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">T. Asignación</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-stopwatch-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #b45309;">{{ $estadisticas['tiempo_asignacion'] }} <small style="font-size: 0.85rem; font-weight: 600;">min</small></div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-hourglass-split me-1"></i> Despacho a móvil</span>
            </div>
        </div>
    </div>
    {{-- 6. T. Carrera --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">T. Prom. Carrera</span>
                <div class="metric-kpi-icon icon-navy"><i class="bi bi-clock-history"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #0a2540;">{{ $estadisticas['tiempo_servicio'] }} <small style="font-size: 0.85rem; font-weight: 600;">min</small></div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-navy"><i class="bi bi-geo-alt me-1"></i> Duración viaje</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SECCIÓN DE GRÁFICOS INTERACTIVOS (4 GRÁFICAS)                           --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

{{-- Fila 1 de Gráficos: Tendencia Temporal (8 cols) + Distribución por Estado (4 cols) --}}
<div class="row g-3 mb-4">
    {{-- Gráfico 1: Tendencia Temporal --}}
    <div class="col-lg-8">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <h6 class="card-title">Evolución de Servicios en el Período</h6>
                        <p class="card-subtitle">Historial de solicitudes, finalizaciones y cancelaciones diarias</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; min-height: 270px; height: 280px;">
                    <canvas id="chartTendencia"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico 2: Distribución por Estados (Doughnut) --}}
    <div class="col-lg-4">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="bi bi-pie-chart-fill"></i></div>
                    <div>
                        <h6 class="card-title">Distribución por Estado</h6>
                        <p class="card-subtitle">Proporción de resultados operativos</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; width: 100%; max-width: 250px; height: 230px;">
                    <canvas id="chartEstados"></canvas>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                    <small class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">● Finalizados</small>
                    <small class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;">● Cancelados</small>
                    <small class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd;">● En Curso</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Fila 2 de Gráficos: Demanda por Franja Horaria (6 cols) + Requerimientos/Condiciones (6 cols) --}}
<div class="row g-3 mb-4">
    {{-- Gráfico 3: Demanda por Horas --}}
    <div class="col-lg-6">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <h6 class="card-title">Demanda por Franja Horaria (Horas Pico)</h6>
                        <p class="card-subtitle">Concentración de solicitudes entre 00:00 y 23:00</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 240px;">
                    <canvas id="chartHoras"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico 4: Requerimientos Especiales / Condiciones --}}
    <div class="col-lg-6">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background: linear-gradient(135deg, #0891b2 0%, #0284c7 100%);"><i class="bi bi-card-checklist"></i></div>
                    <div>
                        <h6 class="card-title">Requerimientos y Condiciones de Servicio</h6>
                        <p class="card-subtitle">Preferencias solicitadas por pasajeros (aire, baúl, pago digital, etc.)</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 240px;">
                    <canvas id="chartCondiciones"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TABLAS DE RANKING: TOP VEHÍCULOS Y TOP OPERADORES                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Top Vehículos --}}
    <div class="col-lg-6">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-truck-front-fill"></i></div>
                    <div>
                        <h6 class="card-title">Top 10 Vehículos con Mayor Actividad</h6>
                        <p class="card-subtitle">Móviles con más carreras atendidas en el período</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle tabla-reporte mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 40px;">#</th>
                                <th>Móvil / Placa</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Completados</th>
                                <th class="pe-3 text-center">Cancelados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topVehiculos as $i => $v)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-weight:800; padding: 4px 8px; border-radius: 6px;">
                                            <i class="bi bi-hash"></i>{{ $v['numero_movil'] }}
                                        </span>
                                        <span class="badge" style="background:#ffffff; color:#0a2540; border:1px solid #cbd5e1; font-weight:700; padding: 4px 6px; border-radius: 6px;">
                                            {{ $v['placa'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center fw-bold" style="color: #0a2540;">{{ $v['total_servicios'] }}</td>
                                <td class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">{{ $v['finalizados'] }}</span></td>
                                <td class="pe-3 text-center"><span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ $v['cancelados'] }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No hay datos de vehículos para el período seleccionado</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Operadores --}}
    <div class="col-lg-6">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <h6 class="card-title">Top 10 Operadores Más Efectivos</h6>
                        <p class="card-subtitle">Desempeño y volumen de servicios despachados</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle tabla-reporte mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 40px;">#</th>
                                <th>Operador</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Completados</th>
                                <th class="pe-3" style="width: 140px;">Efectividad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topOperadores as $i => $op)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">{{ $i + 1 }}</td>
                                <td class="fw-bold" style="color: #0a2540;">{{ $op['nombre'] }}</td>
                                <td class="text-center fw-bold" style="color: #0a2540;">{{ $op['total_servicios'] }}</td>
                                <td class="text-center text-success fw-bold">{{ $op['finalizados'] }}</td>
                                <td class="pe-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 7px; border-radius: 4px; background: #e2e8f0;">
                                            <div class="progress-bar {{ $op['efectividad'] >= 80 ? 'bg-success' : ($op['efectividad'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                 style="width: {{ $op['efectividad'] }}%; border-radius: 4px;"></div>
                                        </div>
                                        <span class="small fw-bold" style="font-size: 0.75rem; min-width: 42px;">{{ $op['efectividad'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No hay datos de operadores para el período seleccionado</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TABLA DE DETALLE DE SERVICIOS CON PAGINACIÓN                            --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="card card-modern">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-list-check"></i></div>
            <div>
                <h6 class="card-title">Detalle Individual de Servicios</h6>
                <p class="card-subtitle">Listado cronológico de carreras registradas según los filtros aplicados</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted fw-semibold" style="font-size: 0.78rem;">Mostrar:</small>
            <select name="per_page" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 88px; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 600;" onchange="cambiarPaginacionServicios(this.value)">
                <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                <option value="todos" {{ ($perPage ?? 25) === 'todos' || ($perPage ?? 25) == 5000 ? 'selected' : '' }}>Todos</option>
            </select>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-reporte mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th class="text-center">Estado</th>
                        <th>Cliente / Teléfono</th>
                        <th>Dirección de Recogida</th>
                        <th>Móvil Asignado</th>
                        <th>Condición</th>
                        <th>Operador</th>
                        <th class="pe-4">Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicios as $s)
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#{{ $s->id }}</td>
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'finalizado' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Finalizado'],
                                    'cancelado'  => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Cancelado'],
                                    'asignado'   => ['bg' => '#f0f9ff', 'color' => '#0284c7', 'border' => '#bae6fd', 'label' => 'Asignado'],
                                    'en_camino'  => ['bg' => '#ecfeff', 'color' => '#0891b2', 'border' => '#a5f3fc', 'label' => 'En Camino'],
                                    'pendiente'  => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'label' => 'Pendiente'],
                                ];
                                $st = $statusMap[$s->estado] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($s->estado)];
                            @endphp
                            <span class="badge rounded-pill" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; border: 1px solid {{ $st['border'] }}; font-size: 0.72rem; font-weight: 700; padding: 4px 10px;">
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #0a2540;">{{ $s->cliente_nombre ?: 'Sin nombre' }}</div>
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $s->telefono ?: 'N/A' }}</small>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 220px;" title="{{ $s->direccion }}">
                                <i class="bi bi-geo-alt text-primary me-1"></i>{{ $s->direccion ?: 'Sin dirección' }}
                            </div>
                        </td>
                        <td>
                            @if($s->numero_movil)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge" style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; font-weight:800; font-size:0.75rem;">
                                        #{{ $s->numero_movil }}
                                    </span>
                                    <small class="text-muted fw-bold">{{ $s->placa }}</small>
                                </div>
                            @else
                                <span class="text-muted small">Sin asignar</span>
                            @endif
                        </td>
                        <td>
                            @if($s->condicion && $s->condicion !== 'ninguno')
                                <span class="badge" style="background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; font-size: 0.72rem;">
                                    {{ ucfirst(str_replace('_', ' ', $s->condicion)) }}
                                </span>
                            @else
                                <span class="text-muted small">Estándar</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-secondary fw-semibold">{{ $s->operador_nombre ?: 'Sistema' }}</small>
                        </td>
                        <td class="pe-4">
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($s->fecha_solicitud)->format('d/m/Y H:i') }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron servicios registrados con los filtros seleccionados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación Moderna --}}
        @if($servicios->hasPages() || $servicios->total() > 0)
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center border-top bg-light">
            <small class="text-muted mb-2 mb-md-0">
                @if(($perPage ?? 25) === 'todos' || ($perPage ?? 25) == 5000)
                    Mostrando <strong>todos los {{ number_format($servicios->total(), 0, ',', '.') }}</strong> registros
                @else
                    Mostrando del <strong>{{ $servicios->firstItem() ?? 0 }}</strong> al <strong>{{ $servicios->lastItem() ?? 0 }}</strong> de <strong>{{ number_format($servicios->total(), 0, ',', '.') }}</strong> servicios
                @endif
            </small>
            @if(($perPage ?? 25) !== 'todos' && ($perPage ?? 25) != 5000 && $servicios->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $servicios->links() }}
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ══════════════════════════════════════════════════════════════
    // CONFIGURACIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS)
    // ══════════════════════════════════════════════════════════════
    
    // Configuración global de fuentes y colores
    Chart.defaults.font.family = 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.color = '#64748b';

    // ── 1. Gráfico de Tendencia Temporal ──
    const ctxTendencia = document.getElementById('chartTendencia').getContext('2d');
    const gradBlue = ctxTendencia.createLinearGradient(0, 0, 0, 260);
    gradBlue.addColorStop(0, 'rgba(2, 132, 199, 0.25)');
    gradBlue.addColorStop(1, 'rgba(2, 132, 199, 0.01)');

    const gradGreen = ctxTendencia.createLinearGradient(0, 0, 0, 260);
    gradGreen.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradGreen.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

    new Chart(ctxTendencia, {
        type: 'line',
        data: {
            labels: @json($tendencia['labels']),
            datasets: [
                {
                    label: 'Total Solicitados',
                    data: @json($tendencia['total']),
                    borderColor: '#0284c7',
                    backgroundColor: gradBlue,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#0284c7'
                },
                {
                    label: 'Finalizados (Éxito)',
                    data: @json($tendencia['finalizados']),
                    borderColor: '#10b981',
                    backgroundColor: gradGreen,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981'
                },
                {
                    label: 'Cancelados',
                    data: @json($tendencia['cancelados']),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.03)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    borderDash: [4, 4],
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ef4444'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { boxWidth: 12, boxHeight: 12, usePointStyle: true, font: { weight: 600, size: 11 } }
                },
                tooltip: {
                    backgroundColor: '#0a2540',
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: { weight: 'bold' }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(226, 232, 240, 0.8)' }
                }
            }
        }
    });

    // ── 2. Gráfico Donut de Distribución por Estados ──
    const ctxEstados = document.getElementById('chartEstados').getContext('2d');
    new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: @json($distribucionEstados['labels']),
            datasets: [{
                data: @json($distribucionEstados['data']),
                backgroundColor: @json($distribucionEstados['colors']),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0a2540',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const val = context.raw || 0;
                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${val} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // ── 3. Gráfico de Demanda por Franja Horaria (Horas Pico) ──
    const ctxHoras = document.getElementById('chartHoras').getContext('2d');
    new Chart(ctxHoras, {
        type: 'bar',
        data: {
            labels: @json($distribucionHoras['labels']),
            datasets: [{
                label: 'Servicios Solicitados',
                data: @json($distribucionHoras['data']),
                backgroundColor: '#0284c7',
                hoverBackgroundColor: '#0052cc',
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0a2540',
                    padding: 8,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12, font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(226, 232, 240, 0.8)' }
                }
            }
        }
    });

    // ── 4. Gráfico de Requerimientos / Condiciones ──
    const ctxCond = document.getElementById('chartCondiciones').getContext('2d');
    new Chart(ctxCond, {
        type: 'bar',
        data: {
            labels: @json($distribucionCondiciones['labels']),
            datasets: [{
                label: 'Carreras',
                data: @json($distribucionCondiciones['data']),
                backgroundColor: [
                    '#38bdf8', '#0284c7', '#06b6d4', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'
                ],
                borderRadius: 5,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
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

    // ══════════════════════════════════════════════════════════════
    // PRESETS RÁPIDOS DE FECHAS (1 CLICK)
    // ══════════════════════════════════════════════════════════════
    function aplicarPreset(tipo) {
        const inputInicio = document.getElementById('filtro_fecha_inicio');
        const inputFin = document.getElementById('filtro_fecha_fin');
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
            // Hoy
            fInicio = hoy;
            fFin = hoy;
        } else if (tipo === 'ayer') {
            // Ayer
            const ayer = new Date();
            ayer.setDate(hoy.getDate() - 1);
            fInicio = ayer;
            fFin = ayer;
        } else if (tipo === '7dias') {
            // Últimos 7 días
            fInicio.setDate(hoy.getDate() - 7);
            fFin = hoy;
        } else if (tipo === 'mes') {
            // Primer día de este mes
            fInicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fFin = hoy;
        } else if (tipo === '30dias') {
            // Últimos 30 días
            fInicio.setDate(hoy.getDate() - 30);
            fFin = hoy;
        }

        inputInicio.value = formatoFecha(fInicio);
        inputFin.value = formatoFecha(fFin);

        document.getElementById('formFiltroReporte').submit();
    }

    // ══════════════════════════════════════════════════════════════
    // CAMBIAR PAGINACIÓN DE SERVICIOS
    // ══════════════════════════════════════════════════════════════
    function cambiarPaginacionServicios(valor) {
        const form = document.getElementById('formFiltroReporte');
        let hidden = form.querySelector('input[name="per_page"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'per_page';
            form.appendChild(hidden);
        }
        hidden.value = valor;
        form.submit();
    }

    // ══════════════════════════════════════════════════════════════
    // SELECT AUTOCOMPLETAR PARA VEHÍCULOS
    // ══════════════════════════════════════════════════════════════
    (function() {
        const select = document.getElementById('selectVehiculo');
        if (!select) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'vehiculo-search-wrap';

        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Buscar móvil o placa...';
        input.autocomplete = 'off';

        const dropdown = document.createElement('div');
        dropdown.className = 'vehiculo-dropdown';

        const opciones = Array.from(select.options).map(o => ({
            value: o.value,
            text: o.textContent,
            selected: o.selected
        }));

        const seleccionado = opciones.find(o => o.selected && o.value);
        if (seleccionado) input.value = seleccionado.text;

        function renderOpciones(filtro) {
            const term = (filtro || '').toLowerCase();
            const filtradas = opciones.filter(o => o.value === '' || o.text.toLowerCase().includes(term));
            dropdown.innerHTML = filtradas.map(o =>
                `<div class="veh-item" data-value="${o.value}">${o.value ? o.text : '<em>Todos los vehículos</em>'}</div>`
            ).join('');
            dropdown.style.display = filtradas.length ? 'block' : 'none';
        }

        input.addEventListener('focus', () => renderOpciones(input.value));
        input.addEventListener('input', () => renderOpciones(input.value));

        dropdown.addEventListener('click', function(e) {
            const item = e.target.closest('.veh-item');
            if (!item) return;
            const val = item.dataset.value;
            select.value = val;
            input.value = val ? item.textContent : '';
            dropdown.style.display = 'none';
        });

        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) dropdown.style.display = 'none';
        });

        select.style.display = 'none';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(input);
        wrapper.appendChild(dropdown);
    })();
</script>
@endpush
