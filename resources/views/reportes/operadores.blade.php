@extends('layouts.app')

@section('title', 'Reporte de Operadores - Taxi Diamantes')
@section('page-title', 'Reporte de Rendimiento por Operador')

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
    .tabla-operadores thead th {
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
    .tabla-operadores tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .tabla-operadores tbody tr:hover {
        background-color: #f0f9ff;
    }

    /* Avatar squircle */
    .avatar-op {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.78rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
{{-- Encabezado con título moderno y presets de fecha --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Reporte de Operadores</h4>
        <p class="text-muted small mb-0"><i class="bi bi-people text-primary me-1"></i> Análisis de productividad, carreras gestionadas, efectividad y tiempos de respuesta</p>
    </div>
    {{-- Accesos rápidos de fechas --}}
    <div class="d-flex flex-wrap gap-1 mt-2 mt-md-0">
        <button type="button" class="preset-btn" onclick="aplicarPresetOp('hoy')">Hoy</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetOp('ayer')">Ayer</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetOp('7dias')">Últimos 7 días</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetOp('mes')">Este Mes</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetOp('30dias')">Últimos 30 días</button>
        <button type="button" class="preset-btn" onclick="aplicarPresetOp('ano')">Todo el Año</button>
    </div>
</div>

{{-- Filtros Avanzados --}}
<div class="card card-modern mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('reportes.operadores') }}" id="formFiltroOperadores" class="row g-3 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="op_fecha_inicio" class="form-control form-control-sm" value="{{ $filtros['fechaInicio'] }}" style="border-radius: 8px; border-color: #cbd5e1;">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="op_fecha_fin" class="form-control form-control-sm" value="{{ $filtros['fechaFin'] }}" style="border-radius: 8px; border-color: #cbd5e1;">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Rol de Usuario</label>
                <select name="rol" class="form-select form-select-sm" style="border-radius: 8px; border-color: #cbd5e1; font-weight: 600;">
                    <option value="">Todos los roles</option>
                    <option value="operador" {{ ($filtros['rol'] ?? '') === 'operador' ? 'selected' : '' }}>Operadores</option>
                    <option value="administrador" {{ ($filtros['rol'] ?? '') === 'administrador' ? 'selected' : '' }}>Administradores</option>
                    <option value="superadmin" {{ ($filtros['rol'] ?? '') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 fw-bold flex-fill shadow-sm">
                    <i class="bi bi-funnel-fill me-1"></i> Filtrar
                </button>
                <a href="{{ route('reportes.exportar-operadores', request()->query()) }}" class="btn btn-success btn-sm rounded-3 px-3 fw-bold shadow-sm" title="Descargar en Excel">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
                </a>
                @if(($filtros['rol'] ?? '') || $filtros['fechaInicio'] != now()->subDays(30)->format('Y-m-d') || $filtros['fechaFin'] != now()->format('Y-m-d'))
                    <a href="{{ route('reportes.operadores') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Restablecer filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Fila de 5 Métricas KPI Uniformes y Minimalistas --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
    {{-- 1. Total Servicios --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Carreras Despachadas</span>
                <div class="metric-kpi-icon icon-blue"><i class="bi bi-headset"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num">{{ number_format($totales['total_servicios'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-blue"><i class="bi bi-activity me-1"></i> Volumen en el período</span>
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
                <div class="metric-num text-success">{{ number_format($totales['finalizados'], 0, ',', '.') }}</div>
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
                <div class="metric-num text-danger">{{ number_format($totales['cancelados'], 0, ',', '.') }}</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-red"><i class="bi bi-slash-circle me-1"></i> Tasa: {{ $totales['tasa_cancelacion'] ?? 0 }}%</span>
            </div>
        </div>
    </div>
    {{-- 4. Efectividad Global --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Efectividad Global</span>
                <div class="metric-kpi-icon icon-cyan"><i class="bi bi-bullseye"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #0891b2;">{{ $totales['efectividad'] }}%</div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-cyan"><i class="bi bi-pie-chart-fill me-1"></i> Ratio de éxito</span>
            </div>
        </div>
    </div>
    {{-- 5. Personal con Actividad --}}
    <div class="col">
        <div class="metric-kpi-card">
            <div class="metric-kpi-header">
                <span class="metric-kpi-label">Personal Activo</span>
                <div class="metric-kpi-icon icon-amber"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="metric-kpi-body">
                <div class="metric-num" style="color: #b45309;">{{ $totales['activos_count'] }} <small style="font-size: 0.85rem; font-weight: 600; color: #64748b;">/ {{ $totales['total_operadores'] }}</small></div>
            </div>
            <div class="metric-kpi-footer">
                <span class="metric-kpi-pill pill-amber"><i class="bi bi-person-check me-1"></i> Con carreras asignadas</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SECCIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS)                              --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Gráfico 1: Comparativa de Servicios por Operador (Bar Horizontal) --}}
    <div class="col-lg-7">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-bar-chart-fill"></i></div>
                    <div>
                        <h6 class="card-title">Volumen de Servicios por Operador</h6>
                        <p class="card-subtitle">Comparativa entre servicios totales y finalizados por operador</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 280px;">
                    <canvas id="chartComparativaOperadores"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico 2: Cuota de Participación en el Despacho (Doughnut) --}}
    <div class="col-lg-5">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="bi bi-pie-chart-fill"></i></div>
                    <div>
                        <h6 class="card-title">Cuota de Participación</h6>
                        <p class="card-subtitle">Porcentaje de carreras atendidas por cada operador</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; width: 100%; max-width: 250px; height: 240px;">
                    <canvas id="chartCuotaOperadores"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- TABLA DETALLADA: RENDIMIENTO POR OPERADOR                               --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="card card-modern">
    <div class="card-header-modern">
        <div class="card-title-wrap">
            <div class="card-icon-circle"><i class="bi bi-table"></i></div>
            <div>
                <h6 class="card-title">Tabla de Rendimiento por Operador</h6>
                <p class="card-subtitle">Detalle individual de carreras, efectividad y tiempos de despacho</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: 230px;">
                <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="buscarEnTabla" class="form-control border-start-0" placeholder="Buscar operador..." style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;">
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle tabla-operadores mb-0" id="tablaOperadoresBody">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 45px;">#</th>
                        <th>Operador / Personal</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Total Servicios</th>
                        <th class="text-center">Finalizados</th>
                        <th class="text-center">Cancelados</th>
                        <th style="min-width: 150px;">Efectividad</th>
                        <th class="pe-3 text-center">T. Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operadores as $i => $op)
                    <tr class="fila-operador">
                        {{-- Posición Podio --}}
                        <td class="ps-3">
                            @if($i === 0 && $op['total_servicios'] > 0)
                                <span class="fs-6" title="1º Lugar en Despacho">🥇</span>
                            @elseif($i === 1 && $op['total_servicios'] > 0)
                                <span class="fs-6" title="2º Lugar">🥈</span>
                            @elseif($i === 2 && $op['total_servicios'] > 0)
                                <span class="fs-6" title="3º Lugar">🥉</span>
                            @else
                                <span class="fw-bold text-muted small">{{ $i + 1 }}</span>
                            @endif
                        </td>

                        {{-- Nombre con Avatar --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $partes = explode(' ', trim($op['nombre']));
                                    $iniciales = mb_substr($partes[0] ?? '', 0, 1) . (isset($partes[1]) ? mb_substr($partes[1], 0, 1) : '');
                                @endphp
                                <div class="avatar-op">{{ strtoupper($iniciales) }}</div>
                                <div>
                                    <div class="fw-bold col-nombre" style="color: #0a2540;">{{ $op['nombre'] }}</div>
                                    <small class="text-muted col-username"><i class="bi bi-at"></i>{{ $op['username'] }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Usuario --}}
                        <td>
                            <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; font-weight:600;">
                                {{ $op['username'] }}
                            </span>
                        </td>

                        {{-- Rol --}}
                        <td>
                            @php
                                $rolMap = [
                                    'superadmin'    => ['bg' => '#fdf2f8', 'color' => '#db2777', 'border' => '#fbcfe8', 'label' => 'Super Admin'],
                                    'administrador' => ['bg' => '#eff6ff', 'color' => '#0284c7', 'border' => '#bfdbfe', 'label' => 'Administrador'],
                                    'operador'      => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Operador'],
                                ];
                                $rInfo = $rolMap[$op['rol']] ?? ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0', 'label' => ucfirst($op['rol'])];
                            @endphp
                            <span class="badge rounded-pill" style="background: {{ $rInfo['bg'] }}; color: {{ $rInfo['color'] }}; border: 1px solid {{ $rInfo['border'] }}; font-weight: 700; font-size: 0.72rem; padding: 4px 10px;">
                                {{ $rInfo['label'] }}
                            </span>
                        </td>

                        {{-- Estado --}}
                        <td>
                            @if($op['estado'] === 'activo')
                                <span class="badge rounded-pill" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 0.72rem; font-weight: 700; padding: 4px 8px;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; font-size: 0.72rem; font-weight: 700; padding: 4px 8px;">
                                    <i class="bi bi-dash-circle-fill me-1"></i>Inactivo
                                </span>
                            @endif
                        </td>

                        {{-- Servicios --}}
                        <td class="text-center fw-bold fs-6" style="color: #0a2540;">
                            {{ $op['total_servicios'] }}
                        </td>

                        {{-- Finalizados --}}
                        <td class="text-center">
                            <span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; font-weight:700; font-size:0.8rem; padding:4px 8px;">
                                {{ $op['finalizados'] }}
                            </span>
                        </td>

                        {{-- Cancelados --}}
                        <td class="text-center">
                            @if($op['cancelados'] > 0)
                                <span class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-weight:700; font-size:0.8rem; padding:4px 8px;">
                                    {{ $op['cancelados'] }}
                                </span>
                            @else
                                <span class="text-muted small">0</span>
                            @endif
                        </td>

                        {{-- Efectividad con barra --}}
                        <td>
                            @if($op['total_servicios'] > 0)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
                                        @php
                                            $barColor = $op['efectividad'] >= 80 ? 'background: #10b981;' : ($op['efectividad'] >= 50 ? 'background: #f59e0b;' : 'background: #ef4444;');
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $op['efectividad'] }}%; border-radius: 4px; {{ $barColor }}"></div>
                                    </div>
                                    <span class="small fw-bold" style="font-size: 0.78rem; min-width: 44px; color: #0a2540;">{{ $op['efectividad'] }}%</span>
                                </div>
                            @else
                                <span class="text-muted small">Sin servicios</span>
                            @endif
                        </td>

                        {{-- Tiempo Promedio --}}
                        <td class="pe-3 text-center">
                            @if($op['tiempo_promedio'])
                                <span class="badge" style="background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; font-size: 0.78rem;">
                                    <i class="bi bi-stopwatch text-primary me-1"></i>{{ $op['tiempo_promedio'] }} min
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron operadores para los filtros seleccionados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Total de personal evaluado: <strong>{{ count($operadores) }} operadores</strong> ({{ $totales['activos_count'] }} con actividad registrada)
            </small>
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

    // ── 1. Comparativa de Servicios por Operador (Bar Horizontal) ──
    const ctxComp = document.getElementById('chartComparativaOperadores').getContext('2d');
    new Chart(ctxComp, {
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
                    label: 'Finalizados con Éxito',
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

    // ── 2. Cuota de Participación en el Despacho (Doughnut) ──
    const ctxCuota = document.getElementById('chartCuotaOperadores').getContext('2d');
    new Chart(ctxCuota, {
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
    function aplicarPresetOp(tipo) {
        const inputInicio = document.getElementById('op_fecha_inicio');
        const inputFin = document.getElementById('op_fecha_fin');
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

        document.getElementById('formFiltroOperadores').submit();
    }

    // ══════════════════════════════════════════════════════════════
    // FILTRADO INSTANTÁNEO EN TABLA DE OPERADORES
    // ══════════════════════════════════════════════════════════════
    document.getElementById('buscarEnTabla').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        const filas = document.querySelectorAll('.fila-operador');

        filas.forEach(f => {
            const nombre = f.querySelector('.col-nombre')?.textContent.toLowerCase() || '';
            const user = f.querySelector('.col-username')?.textContent.toLowerCase() || '';
            if (nombre.includes(term) || user.includes(term)) {
                f.style.display = '';
            } else {
                f.style.display = 'none';
            }
        });
    });
</script>
@endpush
