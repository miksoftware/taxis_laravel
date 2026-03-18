@extends('layouts.app')
@section('title', 'Reporte de Operadores')
@section('page-title', 'Reporte de Operadores')

@section('content')
{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.operadores') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ $filtros['fechaInicio'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ $filtros['fechaFin'] }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm" style="background: #1a1a2e; color: #18dff5;">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('reportes.exportar-operadores', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Totales --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Total Servicios</div>
                <div class="fs-4 fw-bold" style="color: #1a1a2e;">{{ number_format($totales['total_servicios']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Finalizados</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($totales['finalizados']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Cancelados</div>
                <div class="fs-4 fw-bold text-danger">{{ number_format($totales['cancelados']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Efectividad Global</div>
                <div class="fs-4 fw-bold" style="color: #1a1a2e;">{{ $totales['efectividad'] }}%</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de Operadores --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-people me-2"></i>Rendimiento por Operador</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Operador</th><th>Usuario</th><th>Rol</th><th>Estado</th>
                        <th class="text-center">Servicios</th><th class="text-center">Finalizados</th>
                        <th class="text-center">Cancelados</th><th>Efectividad</th><th class="text-center">T. Prom.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operadores as $i => $op)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($i === 0 && $op['total_servicios'] > 0)
                                <i class="bi bi-trophy-fill text-warning me-1"></i>
                            @endif
                            {{ $op['nombre'] }}
                        </td>
                        <td class="text-muted">{{ $op['username'] }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($op['rol']) }}</span></td>
                        <td>
                            <span class="badge bg-{{ $op['estado'] === 'activo' ? 'success' : 'secondary' }}">
                                {{ ucfirst($op['estado']) }}
                            </span>
                        </td>
                        <td class="text-center fw-bold">{{ $op['total_servicios'] }}</td>
                        <td class="text-center text-success">{{ $op['finalizados'] }}</td>
                        <td class="text-center text-danger">{{ $op['cancelados'] }}</td>
                        <td>
                            @if($op['total_servicios'] > 0)
                            <div class="progress" style="height: 20px; min-width: 100px;">
                                <div class="progress-bar {{ $op['efectividad'] >= 80 ? 'bg-success' : ($op['efectividad'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                     style="width: {{ $op['efectividad'] }}%">{{ $op['efectividad'] }}%</div>
                            </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $op['tiempo_promedio'] ? $op['tiempo_promedio'] . ' min' : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-3">No se encontraron operadores</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
