@extends('layouts.app')

@section('title', 'Dashboard - Taxi Diamantes')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* Tarjetas KPI Super Uniformes y Minimalistas */
    .kpi-card {
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
        min-height: 142px;
        position: relative;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -4px rgba(2, 132, 199, 0.12);
        border-color: #38bdf8 !important;
    }
    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin: 0;
    }
    .kpi-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }
    .kpi-blue { background: #e0f2fe; color: #0284c7; }
    .kpi-green { background: #dcfce7; color: #10b981; }
    .kpi-red { background: #fee2e2; color: #ef4444; }
    .kpi-amber { background: #fef3c7; color: #f59e0b; }
    .kpi-cyan { background: #e0f7fa; color: #0097a7; }
    .kpi-purple { background: #ede9fe; color: #8b5cf6; }

    .kpi-body {
        margin: 10px 0 8px 0;
    }
    .kpi-value {
        font-size: 2.15rem;
        font-weight: 800;
        color: #0a2540;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .kpi-footer {
        display: flex;
        align-items: center;
        height: 24px;
    }
    .kpi-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.72rem;
    }
    .kpi-pill-blue { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
    .kpi-pill-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .kpi-pill-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .kpi-pill-amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .kpi-pill-cyan { background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
    .kpi-pill-purple { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }

    /* Segmented Period Selector */
    .periodo-selector {
        background: #e2e8f0;
        padding: 3px;
        border-radius: 30px;
        display: inline-flex;
        gap: 3px;
        border: 1px solid rgba(186, 230, 253, 0.6);
    }
    .periodo-pill {
        padding: 5px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .periodo-pill:hover {
        color: #0a2540;
    }
    .periodo-pill.active {
        background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.35);
    }

    /* Tarjetas del Dashboard Modernas */
    .card-modern {
        background: #ffffff;
        border: 1px solid rgba(186, 230, 253, 0.7) !important;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(2, 132, 199, 0.05);
        overflow: hidden;
    }
    .card-modern .card-header-modern {
        background: #ffffff;
        padding: 14px 18px;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-modern .card-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-modern .card-icon-circle {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }
    .card-modern .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0a2540;
        margin: 0;
    }
    .card-modern .card-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }

    /* Filas y listas */
    .operador-row { transition: background 0.15s; }
    .operador-row:hover { background: #f8fafc; }

    .alerta-card {
        padding: 12px 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        border: 1px solid;
        transition: transform 0.15s;
    }
    .alerta-card:hover { transform: translateX(2px); }
    .alerta-card.warning { background: #fffbeb; border-color: #fde68a; }
    .alerta-card.danger { background: #fef2f2; border-color: #fecaca; }
    .alerta-card.info { background: #f0f9ff; border-color: #bae6fd; }

    .actividad-card-item {
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .actividad-card-item:last-child { border-bottom: none; }
    .actividad-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
{{-- Encabezado con selector de período estilizado --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #0a2540; letter-spacing: -0.3px;">Panel de Control</h4>
        <p class="text-muted small mb-0"><i class="bi bi-broadcast text-primary me-1"></i> Resumen de operaciones y estado del sistema</p>
    </div>
    <div class="periodo-selector">
        <a href="{{ route('dashboard', ['periodo' => 'hoy']) }}" class="periodo-pill {{ $periodo === 'hoy' ? 'active' : '' }}">Hoy</a>
        <a href="{{ route('dashboard', ['periodo' => 'semana']) }}" class="periodo-pill {{ $periodo === 'semana' ? 'active' : '' }}">Semana</a>
        <a href="{{ route('dashboard', ['periodo' => 'mes']) }}" class="periodo-pill {{ $periodo === 'mes' ? 'active' : '' }}">Mes</a>
    </div>
</div>

{{-- 6 Tarjetas Principales Totalmente Uniformes --}}
<div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4">
    {{-- 1. Servicios --}}
    <div class="col">
        <div class="kpi-card">
            <div class="kpi-header">
                <span class="kpi-label">Servicios</span>
                <div class="kpi-icon-wrap kpi-blue">
                    <i class="bi bi-headset"></i>
                </div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($statsServicios['total']) }}</div>
            </div>
            <div class="kpi-footer">
                <span class="kpi-pill kpi-pill-blue">
                    <i class="bi bi-calendar3 me-1"></i> Período: {{ ucfirst($periodo) }}
                </span>
            </div>
        </div>
    </div>

    {{-- 2. Finalizados --}}
    <div class="col">
        <div class="kpi-card">
            <div class="kpi-header">
                <span class="kpi-label">Finalizados</span>
                <div class="kpi-icon-wrap kpi-green">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($statsServicios['finalizados']) }}</div>
            </div>
            <div class="kpi-footer">
                <span class="kpi-pill kpi-pill-green">
                    <i class="bi bi-graph-up-arrow me-1"></i> {{ $statsServicios['efectividad'] }}% efectividad
                </span>
            </div>
        </div>
    </div>

    {{-- 3. Cancelados --}}
    <div class="col">
        <div class="kpi-card">
            <div class="kpi-header">
                <span class="kpi-label">Cancelados</span>
                <div class="kpi-icon-wrap kpi-red">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($statsServicios['cancelados']) }}</div>
            </div>
            <div class="kpi-footer">
                <span class="kpi-pill kpi-pill-red">
                    <i class="bi bi-pie-chart me-1"></i> {{ $statsServicios['total'] > 0 ? round(($statsServicios['cancelados'] / $statsServicios['total']) * 100) : 0 }}% tasa
                </span>
            </div>
        </div>
    </div>

    {{-- 4. Pendientes --}}
    <div class="col">
        <div class="kpi-card">
            <div class="kpi-header">
                <span class="kpi-label">Pendientes</span>
                <div class="kpi-icon-wrap kpi-amber">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value">{{ number_format($statsServicios['pendientes']) }}</div>
            </div>
            <div class="kpi-footer">
                <span class="kpi-pill kpi-pill-amber">
                    <i class="bi bi-clock-history me-1"></i> Por despachar
                </span>
            </div>
        </div>
    </div>

    {{-- 5. Tiempo Promedio --}}
    <div class="col">
        <div class="kpi-card">
            <div class="kpi-header">
                <span class="kpi-label">T. Promedio</span>
                <div class="kpi-icon-wrap kpi-cyan">
                    <i class="bi bi-speedometer2"></i>
                </div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value">{{ $statsServicios['tiempo_promedio_min'] }}<span style="font-size: 1.1rem; font-weight: 600; color: #64748b; margin-left: 2px;">m</span></div>
            </div>
            <div class="kpi-footer">
                <span class="kpi-pill kpi-pill-cyan">
                    <i class="bi bi-lightning-charge me-1"></i> Asignación
                </span>
            </div>
        </div>
    </div>

    {{-- 6. Disponibles --}}
    <div class="col">
        <div class="kpi-card">
            <div class="kpi-header">
                <span class="kpi-label">Disponibles</span>
                <div class="kpi-icon-wrap kpi-purple">
                    <i class="bi bi-truck"></i>
                </div>
            </div>
            <div class="kpi-body">
                <div class="kpi-value">{{ $statsVehiculos['disponible'] ?? 0 }}</div>
            </div>
            <div class="kpi-footer">
                <span class="kpi-pill kpi-pill-purple">
                    <i class="bi bi-check-all me-1"></i> de {{ $statsVehiculos['total'] ?? 0 }} móviles
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Gráfico de servicios --}}
    <div class="col-lg-8">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-bar-chart-line-fill"></i></div>
                    <div>
                        <h6 class="card-title">Servicios {{ $periodo === 'hoy' ? 'por hora' : 'por día' }}</h6>
                        <p class="card-subtitle">Demanda de solicitudes en el período seleccionado</p>
                    </div>
                </div>
                <span class="badge" style="background:#e0f2fe; color:#0284c7; font-weight:600; padding:6px 14px; border-radius:20px; font-size: 0.78rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ $statsServicios['total'] }} registrados
                </span>
            </div>
            <div class="card-body p-3" style="height: 290px;">
                <canvas id="chartServicios"></canvas>
            </div>
        </div>
    </div>

    {{-- Estado de vehículos --}}
    <div class="col-lg-4">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-truck"></i></div>
                    <div>
                        <h6 class="card-title">Flota de Vehículos</h6>
                        <p class="card-subtitle">Estado actual en tiempo real</p>
                    </div>
                </div>
                <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:600; padding:5px 12px; border-radius:14px; font-size: 0.75rem;">
                    {{ $statsVehiculos['total'] ?? 0 }} Total
                </span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-around">
                @php
                    $total = max($statsVehiculos['total'] ?? 1, 1);
                    $estados = [
                        ['label' => 'Disponibles', 'key' => 'disponible', 'color' => '#10b981', 'icon' => 'bi-check-circle-fill'],
                        ['label' => 'Ocupados', 'key' => 'ocupado', 'color' => '#0284c7', 'icon' => 'bi-arrow-repeat'],
                        ['label' => 'Sancionados', 'key' => 'sancionado', 'color' => '#ef4444', 'icon' => 'bi-exclamation-triangle-fill'],
                        ['label' => 'Mantenimiento', 'key' => 'mantenimiento', 'color' => '#f59e0b', 'icon' => 'bi-wrench-adjustable'],
                        ['label' => 'Inactivos', 'key' => 'inactivo', 'color' => '#64748b', 'icon' => 'bi-slash-circle'],
                    ];
                @endphp
                @foreach($estados as $e)
                    @php $val = $statsVehiculos[$e['key']] ?? 0; $pct = round(($val / $total) * 100); @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi {{ $e['icon'] }} me-1" style="color: {{ $e['color'] }}; font-size: 0.78rem;"></i>
                                <span class="fw-semibold" style="color: #334155; font-size: 0.84rem;">{{ $e['label'] }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold" style="color: #0a2540; font-size: 0.86rem;">{{ $val }}</span>
                                <small class="text-muted" style="font-size: 0.72rem; min-width: 32px; text-align: right;">{{ $pct }}%</small>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 6px; background-color: #f1f5f9;">
                            <div class="progress-bar" style="width: {{ $pct }}%; background-color: {{ $e['color'] }}; border-radius: 6px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    {{-- Top operadores --}}
    <div class="col-lg-5">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background:#fef3c7; color:#f59e0b;"><i class="bi bi-trophy-fill"></i></div>
                    <div>
                        <h6 class="card-title">Top Operadores</h6>
                        <p class="card-subtitle">Rendimiento por despachos</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($topOperadores) > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" style="font-size: 0.84rem;">
                        <thead>
                            <tr style="background:#f8fafc; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">
                                <th class="ps-3 py-2">Operador</th>
                                <th class="text-center py-2">Servicios</th>
                                <th class="text-center py-2">Efectividad</th>
                                <th class="text-center pe-3 py-2">T. Prom.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topOperadores as $i => $op)
                            <tr class="operador-row">
                                <td class="ps-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($i === 0) <span style="font-size: 1.1rem;">🥇</span>
                                        @elseif($i === 1) <span style="font-size: 1.1rem;">🥈</span>
                                        @elseif($i === 2) <span style="font-size: 1.1rem;">🥉</span>
                                        @else <span class="badge rounded-pill bg-light text-muted" style="border:1px solid #cbd5e1; width:22px;">{{ $i + 1 }}</span>
                                        @endif
                                        <span class="fw-semibold text-truncate" style="max-width: 170px; color: #1e293b;">{{ $op['nombre'] ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="text-center fw-bold" style="color:#0a2540;">{{ $op['total_servicios'] ?? 0 }}</td>
                                <td class="text-center">
                                    @php $ef = $op['efectividad'] ?? 0; @endphp
                                    <span class="kpi-pill kpi-pill-{{ $ef >= 80 ? 'green' : ($ef >= 50 ? 'amber' : 'red') }}">
                                        {{ $ef }}%
                                    </span>
                                </td>
                                <td class="text-center text-muted pe-3">{{ $op['tiempo_promedio'] ?? '-' }} min</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-people text-muted opacity-50" style="font-size: 2.2rem;"></i>
                    <p class="small mt-2 mb-0">Sin datos de operadores para este período</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Alertas del sistema --}}
    <div class="col-lg-3">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle" style="background:#fee2e2; color:#ef4444;"><i class="bi bi-bell-fill"></i></div>
                    <div>
                        <h6 class="card-title">Alertas</h6>
                        <p class="card-subtitle">Atención requerida</p>
                    </div>
                </div>
                @if(count($alertas) > 0)
                    <span class="badge rounded-pill bg-danger">{{ count($alertas) }}</span>
                @endif
            </div>
            <div class="card-body p-3" style="max-height: 310px; overflow-y: auto;">
                @if(count($alertas) > 0)
                    @foreach($alertas as $alerta)
                    <div class="alerta-card {{ $alerta['tipo'] }}">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi {{ $alerta['icono'] }} mt-1 text-{{ $alerta['tipo'] }}" style="font-size: 1rem;"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold" style="font-size: 0.82rem; color: #0a2540;">{{ $alerta['titulo'] }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $alerta['mensaje'] }}</div>
                                <a href="{{ $alerta['url'] }}" class="text-decoration-none fw-semibold d-inline-flex align-items-center gap-1 mt-1 text-primary" style="font-size: 0.74rem;">
                                    Resolver ahora <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 48px; height: 48px; background: #dcfce7; color: #10b981;">
                            <i class="bi bi-shield-check" style="font-size: 1.5rem;"></i>
                        </div>
                        <p class="fw-semibold mb-0" style="color: #0a2540; font-size: 0.88rem;">Todo en orden</p>
                        <small class="text-muted" style="font-size: 0.75rem;">No hay alertas pendientes</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actividad reciente --}}
    <div class="col-lg-4">
        <div class="card card-modern h-100">
            <div class="card-header-modern">
                <div class="card-title-wrap">
                    <div class="card-icon-circle"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <h6 class="card-title">Actividad Reciente</h6>
                        <p class="card-subtitle">Flujo de servicios en curso</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-3" style="max-height: 310px; overflow-y: auto;">
                @if(count($actividadReciente) > 0)
                    @foreach($actividadReciente as $act)
                    @php
                        $badgeConfig = [
                            'pendiente' => ['bg' => 'amber', 'icon' => 'bi-hourglass-split', 'text' => 'Pendiente'],
                            'asignado' => ['bg' => 'blue', 'icon' => 'bi-truck', 'text' => 'Asignado'],
                            'en_camino' => ['bg' => 'cyan', 'icon' => 'bi-geo-alt', 'text' => 'En camino'],
                            'finalizado' => ['bg' => 'green', 'icon' => 'bi-check2', 'text' => 'Finalizado'],
                            'cancelado' => ['bg' => 'red', 'icon' => 'bi-x', 'text' => 'Cancelado'],
                        ];
                        $estado = $act['estado'] ?? '';
                        $conf = $badgeConfig[$estado] ?? ['bg' => 'blue', 'icon' => 'bi-circle', 'text' => ucfirst($estado)];
                    @endphp
                    <div class="actividad-card-item">
                        <div class="actividad-avatar kpi-{{ $conf['bg'] }}">
                            <i class="bi {{ $conf['icon'] }}"></i>
                        </div>
                        <div class="flex-grow-1" style="font-size: 0.8rem;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold" style="color: #0a2540;">#{{ $act['id'] ?? '' }}</span>
                                <span class="kpi-pill kpi-pill-{{ $conf['bg'] }}" style="font-size: 0.68rem;">
                                    {{ $conf['text'] }}
                                </span>
                            </div>
                            <div class="text-truncate text-secondary" style="max-width: 230px;">
                                {{ $act['telefono'] ?? '' }} · {{ $act['cliente_nombre'] ?? 'Cliente' }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1 text-muted" style="font-size: 0.72rem;">
                                <span>
                                    @if($act['numero_movil'] ?? null)
                                        <i class="bi bi-taxi-front me-1 text-primary"></i> Móvil {{ $act['numero_movil'] }}
                                    @else
                                        <i class="bi bi-person me-1"></i> Sin móvil
                                    @endif
                                </span>
                                <span>{{ \Carbon\Carbon::parse($act['fecha_actualizacion'] ?? now())->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox text-muted opacity-50" style="font-size: 2.2rem;"></i>
                        <p class="small mt-2 mb-0">Sin actividad registrada en este período</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('chartServicios');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Gradiente suave y moderno para las barras
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(2, 132, 199, 0.85)');
    gradient.addColorStop(1, 'rgba(56, 189, 248, 0.25)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($serviciosPorHora['labels']),
            datasets: [{
                label: 'Servicios',
                data: @json($serviciosPorHora['values']),
                backgroundColor: gradient,
                borderColor: '#0284c7',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: 0.65,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0a2540',
                    titleColor: '#ffffff',
                    bodyColor: '#e0f2fe',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Servicios: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1, 
                        font: { size: 11, family: "'Segoe UI', sans-serif" },
                        color: '#64748b'
                    },
                    grid: { 
                        color: 'rgba(226, 232, 240, 0.7)' 
                    }
                },
                x: {
                    ticks: { 
                        font: { size: 10, family: "'Segoe UI', sans-serif" }, 
                        color: '#64748b',
                        maxRotation: 45 
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush

