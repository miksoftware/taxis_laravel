@extends('layouts.app')

@section('title', 'Dashboard - Taxi Diamantes')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        color: white;
        transition: transform 0.15s;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 2.5rem;
        opacity: 0.2;
    }
    .stat-card .stat-num { font-size: 2rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.8rem; opacity: 0.9; }
    .stat-card .stat-sub { font-size: 0.75rem; opacity: 0.7; margin-top: 4px; }

    .bg-grad-primary { background: linear-gradient(135deg, #1a1a2e, #16213e); }
    .bg-grad-success { background: linear-gradient(135deg, #198754, #146c43); }
    .bg-grad-warning { background: linear-gradient(135deg, #18dff5, #e6a800); }
    .bg-grad-danger { background: linear-gradient(135deg, #dc3545, #b02a37); }
    .bg-grad-info { background: linear-gradient(135deg, #0dcaf0, #0aa2c0); }
    .bg-grad-purple { background: linear-gradient(135deg, #6f42c1, #5a32a3); }

    .card-dashboard { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .card-dashboard .card-header { background: white; border-bottom: 2px solid #18dff5; border-radius: 12px 12px 0 0; font-weight: 600; }

    .vehiculo-bar { height: 8px; border-radius: 4px; }
    .operador-row { transition: background 0.15s; }
    .operador-row:hover { background: #f8f9fa; }

    .alerta-item { border-left: 4px solid; padding: 10px 15px; margin-bottom: 8px; border-radius: 0 8px 8px 0; background: white; }
    .alerta-item.warning { border-color: #18dff5; }
    .alerta-item.danger { border-color: #dc3545; }
    .alerta-item.info { border-color: #0dcaf0; }

    .actividad-item { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .actividad-item:last-child { border-bottom: none; }

    .periodo-btn.active { background: #18dff5; color: #1a1a2e; font-weight: 600; }
</style>
@endpush

@section('content')
{{-- Selector de período --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <small class="text-muted">Resumen del sistema</small>
    </div>
    <div class="btn-group btn-group-sm">
        <a href="{{ route('dashboard', ['periodo' => 'hoy']) }}" class="btn btn-outline-secondary periodo-btn {{ $periodo === 'hoy' ? 'active' : '' }}">Hoy</a>
        <a href="{{ route('dashboard', ['periodo' => 'semana']) }}" class="btn btn-outline-secondary periodo-btn {{ $periodo === 'semana' ? 'active' : '' }}">Semana</a>
        <a href="{{ route('dashboard', ['periodo' => 'mes']) }}" class="btn btn-outline-secondary periodo-btn {{ $periodo === 'mes' ? 'active' : '' }}">Mes</a>
    </div>
</div>

{{-- Tarjetas principales --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-card bg-grad-primary">
            <i class="bi bi-headset stat-icon"></i>
            <div class="stat-num">{{ $statsServicios['total'] }}</div>
            <div class="stat-label">Servicios</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-grad-success">
            <i class="bi bi-check-circle stat-icon"></i>
            <div class="stat-num">{{ $statsServicios['finalizados'] }}</div>
            <div class="stat-label">Finalizados</div>
            <div class="stat-sub">{{ $statsServicios['efectividad'] }}% efectividad</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-grad-danger">
            <i class="bi bi-x-circle stat-icon"></i>
            <div class="stat-num">{{ $statsServicios['cancelados'] }}</div>
            <div class="stat-label">Cancelados</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-grad-warning">
            <i class="bi bi-clock stat-icon"></i>
            <div class="stat-num">{{ $statsServicios['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-grad-info">
            <i class="bi bi-speedometer2 stat-icon"></i>
            <div class="stat-num">{{ $statsServicios['tiempo_promedio_min'] }}</div>
            <div class="stat-label">Min. promedio</div>
            <div class="stat-sub">Tiempo asignación</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-grad-purple">
            <i class="bi bi-truck stat-icon"></i>
            <div class="stat-num">{{ $statsVehiculos['disponible'] ?? 0 }}</div>
            <div class="stat-label">Disponibles</div>
            <div class="stat-sub">de {{ $statsVehiculos['total'] ?? 0 }} vehículos</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Gráfico de servicios --}}
    <div class="col-md-8">
        <div class="card card-dashboard">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span><i class="bi bi-bar-chart me-1"></i> Servicios {{ $periodo === 'hoy' ? 'por hora' : 'por día' }}</span>
            </div>
            <div class="card-body" style="height: 280px;">
                <canvas id="chartServicios"></canvas>
            </div>
        </div>
    </div>

    {{-- Estado de vehículos --}}
    <div class="col-md-4">
        <div class="card card-dashboard">
            <div class="card-header py-2">
                <i class="bi bi-truck me-1"></i> Flota de Vehículos
            </div>
            <div class="card-body">
                @php
                    $total = max($statsVehiculos['total'] ?? 1, 1);
                    $estados = [
                        ['label' => 'Disponibles', 'key' => 'disponible', 'color' => '#198754'],
                        ['label' => 'Ocupados', 'key' => 'ocupado', 'color' => '#0dcaf0'],
                        ['label' => 'Sancionados', 'key' => 'sancionado', 'color' => '#dc3545'],
                        ['label' => 'Mantenimiento', 'key' => 'mantenimiento', 'color' => '#18dff5'],
                        ['label' => 'Inactivos', 'key' => 'inactivo', 'color' => '#6c757d'],
                    ];
                @endphp
                @foreach($estados as $e)
                    @php $val = $statsVehiculos[$e['key']] ?? 0; $pct = round(($val / $total) * 100); @endphp
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small>{{ $e['label'] }}</small>
                        <span class="fw-bold">{{ $val }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $pct }}%; background: {{ $e['color'] }};"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    {{-- Top operadores --}}
    <div class="col-md-5">
        <div class="card card-dashboard">
            <div class="card-header py-2">
                <i class="bi bi-trophy me-1"></i> Top Operadores
            </div>
            <div class="card-body p-0">
                @if(count($topOperadores) > 0)
                <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr class="text-muted">
                            <th class="ps-3">Operador</th>
                            <th class="text-center">Servicios</th>
                            <th class="text-center">Efectividad</th>
                            <th class="text-center">T. Prom.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topOperadores as $i => $op)
                        <tr class="operador-row">
                            <td class="ps-3">
                                @if($i === 0) <span class="badge bg-warning text-dark me-1">🥇</span>
                                @elseif($i === 1) <span class="badge bg-secondary me-1">🥈</span>
                                @elseif($i === 2) <span class="badge bg-danger me-1" style="opacity:0.7">🥉</span>
                                @endif
                                {{ $op['nombre'] ?? '' }}
                            </td>
                            <td class="text-center fw-bold">{{ $op['total_servicios'] ?? 0 }}</td>
                            <td class="text-center">
                                @php $ef = $op['efectividad'] ?? 0; @endphp
                                <span class="badge bg-{{ $ef >= 80 ? 'success' : ($ef >= 50 ? 'warning' : 'danger') }}">{{ $ef }}%</span>
                            </td>
                            <td class="text-center text-muted">{{ $op['tiempo_promedio'] ?? '-' }} min</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-4">Sin datos para este período</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Alertas del sistema --}}
    <div class="col-md-3">
        <div class="card card-dashboard">
            <div class="card-header py-2">
                <i class="bi bi-bell me-1"></i> Alertas
            </div>
            <div class="card-body">
                @if(count($alertas) > 0)
                    @foreach($alertas as $alerta)
                    <div class="alerta-item {{ $alerta['tipo'] }}">
                        <div class="d-flex align-items-start">
                            <i class="bi {{ $alerta['icono'] }} me-2 mt-1 text-{{ $alerta['tipo'] }}"></i>
                            <div>
                                <div class="fw-bold" style="font-size: 0.8rem;">{{ $alerta['titulo'] }}</div>
                                <small class="text-muted">{{ $alerta['mensaje'] }}</small>
                                <br><a href="{{ $alerta['url'] }}" class="text-decoration-none" style="font-size: 0.75rem;">Ver detalle →</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;">Todo en orden</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actividad reciente --}}
    <div class="col-md-4">
        <div class="card card-dashboard">
            <div class="card-header py-2">
                <i class="bi bi-activity me-1"></i> Actividad Reciente
            </div>
            <div class="card-body" style="max-height: 320px; overflow-y: auto;">
                @if(count($actividadReciente) > 0)
                    @foreach($actividadReciente as $act)
                    @php
                        $iconos = [
                            'pendiente' => 'bi-plus-circle text-warning',
                            'asignado' => 'bi-check-circle text-info',
                            'en_camino' => 'bi-geo-alt text-primary',
                            'finalizado' => 'bi-check-all text-success',
                            'cancelado' => 'bi-x-circle text-danger',
                        ];
                        $estado = $act['estado'] ?? '';
                        $icono = $iconos[$estado] ?? 'bi-circle text-muted';
                    @endphp
                    <div class="actividad-item">
                        <div class="d-flex align-items-start">
                            <i class="bi {{ $icono }} me-2 mt-1"></i>
                            <div style="font-size: 0.82rem;">
                                <span class="fw-bold">#{{ $act['id'] ?? '' }}</span>
                                <span class="badge bg-{{ ['pendiente'=>'warning','asignado'=>'info','en_camino'=>'primary','finalizado'=>'success','cancelado'=>'danger'][$estado] ?? 'secondary' }}" style="font-size: 0.7rem;">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                                <br>
                                <small class="text-muted">
                                    {{ $act['telefono'] ?? '' }} — {{ $act['cliente_nombre'] ?? '' }}
                                    @if($act['numero_movil'] ?? null)
                                        · Móvil {{ $act['numero_movil'] }}
                                    @endif
                                </small>
                                <br>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ \Carbon\Carbon::parse($act['fecha_actualizacion'] ?? now())->diffForHumans() }}
                                    · Op: {{ $act['operador_nombre'] ?? '' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">Sin actividad reciente</div>
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
    const ctx = document.getElementById('chartServicios').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($serviciosPorHora['labels']),
            datasets: [{
                label: 'Servicios',
                data: @json($serviciosPorHora['values']),
                backgroundColor: 'rgba(245, 197, 24, 0.7)',
                borderColor: '#18dff5',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 10 }, maxRotation: 45 },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
