@extends('layouts.app')
@section('title', 'Reporte de Servicios')
@section('page-title', 'Reporte de Servicios')

@section('content')
{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.servicios') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ $filtros['fechaInicio'] }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ $filtros['fechaFin'] }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach(['pendiente','asignado','en_camino','finalizado','cancelado'] as $e)
                        <option value="{{ $e }}" {{ $filtros['estado'] == $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Operador</label>
                <select name="operador_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($operadores as $op)
                        <option value="{{ $op->id }}" {{ $filtros['operadorId'] == $op->id ? 'selected' : '' }}>{{ $op->nombre }} {{ $op->apellidos }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Vehículo</label>
                <select name="vehiculo_id" class="form-select form-select-sm" id="selectVehiculo">
                    <option value="">Todos</option>
                    @foreach($vehiculos as $v)
                        <option value="{{ $v->id }}" {{ $filtros['vehiculoId'] == $v->id ? 'selected' : '' }}>{{ $v->numero_movil }} — {{ $v->placa }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm" style="background: #1a1a2e; color: #18dff5;">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('reportes.exportar-servicios', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Total</div>
                <div class="fs-4 fw-bold" style="color: #1a1a2e;">{{ number_format($estadisticas['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Finalizados</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($estadisticas['finalizados']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Cancelados</div>
                <div class="fs-4 fw-bold text-danger">{{ number_format($estadisticas['cancelados']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Pendientes</div>
                <div class="fs-4 fw-bold text-warning">{{ number_format($estadisticas['pendientes']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">Efectividad</div>
                <div class="fs-4 fw-bold" style="color: #1a1a2e;">{{ $estadisticas['efectividad'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="text-muted small">T. Asignación</div>
                <div class="fs-4 fw-bold" style="color: #1a1a2e;">{{ $estadisticas['tiempo_asignacion'] }} min</div>
            </div>
        </div>
    </div>
</div>

{{-- Tendencia Chart --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Tendencia de Servicios</h6>
    </div>
    <div class="card-body">
        <canvas id="chartTendencia" height="80"></canvas>
    </div>
</div>

{{-- Top Vehículos y Operadores --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Top Vehículos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th><th>Placa</th><th>Móvil</th><th>Servicios</th><th>Finalizados</th><th>Cancelados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topVehiculos as $i => $v)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $v['placa'] }}</td>
                                <td><span class="badge bg-primary">{{ $v['numero_movil'] }}</span></td>
                                <td class="fw-bold">{{ $v['total_servicios'] }}</td>
                                <td class="text-success">{{ $v['finalizados'] }}</td>
                                <td class="text-danger">{{ $v['cancelados'] }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Top Operadores</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th><th>Operador</th><th>Servicios</th><th>Finalizados</th><th>Efectividad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topOperadores as $i => $op)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $op['nombre'] }}</td>
                                <td class="fw-bold">{{ $op['total_servicios'] }}</td>
                                <td class="text-success">{{ $op['finalizados'] }}</td>
                                <td>
                                    <div class="progress" style="height: 18px;">
                                        <div class="progress-bar {{ $op['efectividad'] >= 80 ? 'bg-success' : ($op['efectividad'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width: {{ $op['efectividad'] }}%">{{ $op['efectividad'] }}%</div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Listado de Servicios --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Detalle de Servicios</h6>
        <span class="badge bg-secondary">{{ $servicios->total() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th><th>Estado</th><th>Cliente</th><th>Teléfono</th><th>Dirección</th>
                        <th>Vehículo</th><th>Operador</th><th>Fecha Solicitud</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicios as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>
                            @php
                                $colores = ['pendiente'=>'warning','asignado'=>'info','en_camino'=>'primary','finalizado'=>'success','cancelado'=>'danger'];
                            @endphp
                            <span class="badge bg-{{ $colores[$s->estado] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$s->estado)) }}</span>
                        </td>
                        <td>{{ $s->cliente_nombre ?? '-' }}</td>
                        <td>{{ $s->telefono ?? '-' }}</td>
                        <td class="text-truncate" style="max-width: 200px;">{{ $s->direccion ?? '-' }}</td>
                        <td>
                            @if($s->numero_movil)
                                <span class="badge bg-primary">{{ $s->numero_movil }}</span> {{ $s->placa }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $s->operador_nombre ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($s->fecha_solicitud)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">No se encontraron servicios</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($servicios->hasPages())
    <div class="card-footer bg-white">
        {{ $servicios->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .vehiculo-search-wrap { position: relative; }
    .vehiculo-search-wrap input { border: 1px solid #ced4da; border-radius: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.875rem; width: 100%; }
    .vehiculo-search-wrap .vehiculo-dropdown { position: absolute; z-index: 1050; background: white; border: 1px solid #dee2e6; border-radius: 6px; max-height: 200px; overflow-y: auto; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; }
    .vehiculo-search-wrap .vehiculo-dropdown .veh-item { padding: 6px 10px; cursor: pointer; font-size: 0.85rem; border-bottom: 1px solid #f0f0f0; }
    .vehiculo-search-wrap .veh-item:hover { background: #18dff5; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('chartTendencia').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($tendencia['labels']),
        datasets: [
            {
                label: 'Total',
                data: @json($tendencia['total']),
                borderColor: '#1a1a2e',
                backgroundColor: 'rgba(26,26,46,0.1)',
                fill: true, tension: 0.3
            },
            {
                label: 'Finalizados',
                data: @json($tendencia['finalizados']),
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.1)',
                fill: true, tension: 0.3
            },
            {
                label: 'Cancelados',
                data: @json($tendencia['cancelados']),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.1)',
                fill: true, tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Buscador para select de vehículos
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

    // Obtener opciones del select original
    const opciones = Array.from(select.options).map(o => ({
        value: o.value,
        text: o.textContent,
        selected: o.selected
    }));

    // Si hay uno seleccionado, mostrarlo
    const seleccionado = opciones.find(o => o.selected && o.value);
    if (seleccionado) input.value = seleccionado.text;

    function renderOpciones(filtro) {
        const term = filtro.toLowerCase();
        const filtradas = opciones.filter(o => o.value === '' || o.text.toLowerCase().includes(term));
        dropdown.innerHTML = filtradas.map(o =>
            `<div class="veh-item" data-value="${o.value}">${o.value ? o.text : '<em>Todos</em>'}</div>`
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
