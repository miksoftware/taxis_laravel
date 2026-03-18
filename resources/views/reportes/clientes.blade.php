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
                        <td class="text-center">
                            <a href="#" class="fw-bold text-decoration-none" onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', '')">{{ $c['total_servicios'] }}</a>
                        </td>
                        <td class="text-center">
                            <a href="#" class="text-success text-decoration-none" onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', 'finalizado')">{{ $c['finalizados'] }}</a>
                        </td>
                        <td class="text-center">
                            <a href="#" class="text-danger text-decoration-none" onclick="verServicios({{ $c['id'] }}, '{{ $c['telefono'] }}', 'cancelado')">{{ $c['cancelados'] }}</a>
                        </td>
                        <td class="text-center">
                            <a href="#" class="text-decoration-none" onclick="verDirecciones({{ $c['id'] }}, '{{ $c['telefono'] }}')">
                                <span class="badge bg-secondary">{{ $c['total_direcciones'] }}</span>
                            </a>
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

{{-- Modal Servicios del Cliente --}}
<div class="modal fade" id="modalServicios" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#18dff5">
                <h6 class="modal-title"><i class="bi bi-list-ul me-1"></i> Servicios — <span id="modalServiciosTel"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="srvFechaInicio" placeholder="Desde">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="srvFechaFin" placeholder="Hasta">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm w-100" style="background:#1a1a2e;color:#18dff5" onclick="filtrarServicios()">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-secondary" id="srvTotal"></span>
                    </div>
                </div>
                <div style="max-height:400px;overflow-y:auto">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>ID</th><th>Estado</th><th>Dirección</th><th>Vehículo</th>
                                <th>Condición</th><th>Operador</th><th>Solicitud</th><th>Asignación</th><th>Fin</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyServicios">
                            <tr><td colspan="9" class="text-center text-muted py-3">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Direcciones del Cliente --}}
<div class="modal fade" id="modalDirecciones" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title"><i class="bi bi-geo-alt me-1"></i> Direcciones — <span id="modalDireccionesTel"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="max-height:400px;overflow-y:auto">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Dirección</th><th>Referencia</th><th class="text-center">Servicios</th>
                                <th class="text-center">Frecuente</th><th class="text-center">Activa</th><th>Último Uso</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDirecciones">
                            <tr><td colspan="6" class="text-center text-muted py-3">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentClienteId = null;
let currentEstadoFiltro = '';

function formatFecha(f) {
    if (!f) return '-';
    const d = new Date(f);
    return d.toLocaleDateString('es-CO', { day:'2-digit', month:'2-digit', year:'numeric' }) + ' ' +
           d.toLocaleTimeString('es-CO', { hour:'2-digit', minute:'2-digit' });
}

function colorEstado(e) {
    return { pendiente:'warning', asignado:'info', en_camino:'primary', finalizado:'success', cancelado:'danger' }[e] || 'secondary';
}

function verServicios(clienteId, telefono, estado) {
    event.preventDefault();
    currentClienteId = clienteId;
    currentEstadoFiltro = estado;
    document.getElementById('modalServiciosTel').textContent = telefono;
    document.getElementById('srvFechaInicio').value = '';
    document.getElementById('srvFechaFin').value = '';
    document.getElementById('tbodyServicios').innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Cargando...</td></tr>';
    new bootstrap.Modal(document.getElementById('modalServicios')).show();
    cargarServicios();
}

async function cargarServicios() {
    const fi = document.getElementById('srvFechaInicio').value;
    const ff = document.getElementById('srvFechaFin').value;
    let url = '/reportes/clientes/' + currentClienteId + '/servicios?estado=' + encodeURIComponent(currentEstadoFiltro);
    if (fi) url += '&fecha_inicio=' + fi;
    if (ff) url += '&fecha_fin=' + ff;

    try {
        const res = await fetch(url);
        const data = await res.json();
        const tbody = document.getElementById('tbodyServicios');
        document.getElementById('srvTotal').textContent = data.servicios.length + ' registros';

        if (data.servicios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Sin servicios</td></tr>';
            return;
        }

        tbody.innerHTML = data.servicios.map(s => `<tr>
            <td>${s.id}</td>
            <td><span class="badge bg-${colorEstado(s.estado)}">${s.estado.replace('_',' ')}</span></td>
            <td>${s.direccion || '-'}${s.referencia ? '<br><small class="text-muted">' + s.referencia + '</small>' : ''}</td>
            <td>${s.numero_movil ? s.numero_movil + ' <small class="text-muted">(' + s.placa + ')</small>' : '-'}</td>
            <td><small>${s.condicion !== 'ninguno' ? s.condicion : '-'}</small></td>
            <td><small>${s.operador_nombre || '-'}</small></td>
            <td><small>${formatFecha(s.fecha_solicitud)}</small></td>
            <td><small>${formatFecha(s.fecha_asignacion)}</small></td>
            <td><small>${formatFecha(s.fecha_fin)}</small></td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tbodyServicios').innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3">Error al cargar</td></tr>';
    }
}

function filtrarServicios() {
    cargarServicios();
}

function verDirecciones(clienteId, telefono) {
    event.preventDefault();
    document.getElementById('modalDireccionesTel').textContent = telefono;
    document.getElementById('tbodyDirecciones').innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Cargando...</td></tr>';
    new bootstrap.Modal(document.getElementById('modalDirecciones')).show();
    cargarDirecciones(clienteId);
}

async function cargarDirecciones(clienteId) {
    try {
        const res = await fetch('/reportes/clientes/' + clienteId + '/direcciones');
        const data = await res.json();
        const tbody = document.getElementById('tbodyDirecciones');

        if (data.direcciones.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Sin direcciones</td></tr>';
            return;
        }

        tbody.innerHTML = data.direcciones.map(d => `<tr>
            <td>${d.direccion}</td>
            <td><small class="text-muted">${d.referencia || '-'}</small></td>
            <td class="text-center fw-bold">${d.total_servicios}</td>
            <td class="text-center">${d.es_frecuente ? '⭐' : '-'}</td>
            <td class="text-center">${d.activa ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</td>
            <td><small>${d.ultimo_uso ? new Date(d.ultimo_uso).toLocaleDateString('es-CO') : '-'}</small></td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tbodyDirecciones').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3">Error al cargar</td></tr>';
    }
}
</script>
@endpush
