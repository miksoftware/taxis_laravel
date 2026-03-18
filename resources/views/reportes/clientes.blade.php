@extends('layouts.app')
@section('title', 'Reporte de Clientes')
@section('page-title', 'Reporte de Clientes')

@section('content')
{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.clientes') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ $filtros['fechaInicio'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ $filtros['fechaFin'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Cliente (teléfono o nombre)</label>
                <input type="text" name="buscar_cliente" class="form-control form-control-sm" placeholder="Buscar..." value="{{ $filtros['buscarCliente'] }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm" style="background: #1a1a2e; color: #18dff5;">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('reportes.exportar-clientes', request()->query()) }}" class="btn btn-sm btn-success">
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
                <div class="text-muted small">Clientes Activos</div>
                <div class="fs-4 fw-bold" style="color: #1a1a2e;">{{ number_format($totales['clientes_activos']) }}</div>
            </div>
        </div>
    </div>
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
</div>

{{-- Tabla de Clientes --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Top Clientes por Uso</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Teléfono</th><th>Nombre</th>
                        <th class="text-center">Servicios</th><th class="text-center">Finalizados</th>
                        <th class="text-center">Cancelados</th><th class="text-center">Direcciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $i => $c)
                    <tr>
                        <td>
                            @if($i < 3 && $c['total_servicios'] > 0)
                                @php $medallas = ['🥇','🥈','🥉']; @endphp
                                {{ $medallas[$i] }}
                            @else
                                {{ $i + 1 }}
                            @endif
                        </td>
                        <td class="fw-bold">{{ $c['telefono'] }}</td>
                        <td>{{ $c['nombre'] ?: '-' }}</td>
                        <td class="text-center fw-bold">{{ $c['total_servicios'] }}</td>
                        <td class="text-center text-success">{{ $c['finalizados'] }}</td>
                        <td class="text-center text-danger">{{ $c['cancelados'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $c['total_direcciones'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">No se encontraron clientes con servicios en este período</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
