@extends('layouts.app')

@section('title', 'Sanciones - Taxi Diamantes')
@section('page-title', 'Gestión de Sanciones')

@section('content')
{{-- Estadísticas --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="text-danger mb-1"><i class="bi bi-exclamation-triangle fs-4"></i></div>
                <h4 class="mb-0">{{ $stats['activa'] }}</h4>
                <small class="text-muted">Activas</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="text-success mb-1"><i class="bi bi-check-circle fs-4"></i></div>
                <h4 class="mb-0">{{ $stats['cumplida'] }}</h4>
                <small class="text-muted">Cumplidas</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="text-warning mb-1"><i class="bi bi-slash-circle fs-4"></i></div>
                <h4 class="mb-0">{{ $stats['anulada'] }}</h4>
                <small class="text-muted">Anuladas</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Sanciones</h5>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#aplicarSancionModal">
                <i class="bi bi-plus-lg me-1"></i> Aplicar Sanción
            </button>
            @if(auth()->user()->esAdmin())
            <form method="POST" action="{{ route('sanciones.verificar-vencimientos') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm" title="Verificar vencimientos manualmente">
                    <i class="bi bi-arrow-clockwise me-1"></i> Verificar Vencimientos
                </button>
            </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        {{-- Filtros --}}
        <form method="GET" action="{{ route('sanciones.index') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="vehiculo" class="form-control form-control-sm" placeholder="Placa o móvil..." value="{{ $filtroVehiculo }}">
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="activa" {{ $filtroEstado === 'activa' ? 'selected' : '' }}>Activas</option>
                    <option value="cumplida" {{ $filtroEstado === 'cumplida' ? 'selected' : '' }}>Cumplidas</option>
                    <option value="anulada" {{ $filtroEstado === 'anulada' ? 'selected' : '' }}>Anuladas</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filtrar</button>
                @if($filtroEstado || $filtroVehiculo)
                    <a href="{{ route('sanciones.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Vehículo</th>
                        <th>Artículo</th>
                        <th>Motivo</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th class="text-center">Tiempo Restante</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sanciones as $sancion)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $sancion->vehiculo->placa }}</span>
                            <br><small class="text-muted">Móvil {{ $sancion->vehiculo->numero_movil }}</small>
                        </td>
                        <td><span class="badge bg-warning text-dark">{{ $sancion->articulo->codigo }}</span></td>
                        <td>{{ Str::limit($sancion->motivo, 40) }}</td>
                        <td>{{ $sancion->fecha_inicio->format('d/m/Y H:i') }}</td>
                        <td>{{ $sancion->fecha_fin->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            @if($sancion->estado === 'activa')
                                <span class="countdown fw-bold text-danger" data-fin="{{ $sancion->fecha_fin->toIso8601String() }}">
                                    {{ $sancion->tiempoRestanteFormateado() }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $bs = match($sancion->estado) {
                                    'activa' => 'bg-danger',
                                    'cumplida' => 'bg-success',
                                    'anulada' => 'bg-warning text-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $bs }}">{{ ucfirst($sancion->estado) }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-info" title="Detalle"
                                    onclick="verDetalle({{ $sancion->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($sancion->estado === 'activa')
                                <button type="button" class="btn btn-outline-warning" title="Anular"
                                    onclick="anularSancion({{ $sancion->id }}, '{{ $sancion->vehiculo->placa }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron sanciones</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">{{ $sanciones->links() }}</div>
    </div>
</div>

{{-- MODAL: Aplicar Sanción --}}
<div class="modal fade" id="aplicarSancionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('sanciones.aplicar') }}" novalidate>
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Aplicar Sanción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Vehículo <span class="text-danger">*</span></label>
                        <select class="form-select" name="vehiculo_id" id="select_vehiculo" required>
                            <option value="">Cargando vehículos...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Artículo de Sanción <span class="text-danger">*</span></label>
                        <select class="form-select" name="articulo_id" id="select_articulo" required>
                            <option value="">Cargando artículos...</option>
                        </select>
                        <div class="form-text" id="info_tiempo"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motivo" rows="3" required minlength="5" placeholder="Describa el motivo de la sanción..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-exclamation-triangle me-1"></i>Aplicar Sanción</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Anular Sanción --}}
<div class="modal fade" id="anularSancionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formAnular" novalidate>
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Anular Sanción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anular sanción del vehículo <span id="anular_placa" class="fw-bold text-primary"></span>:</p>
                    <div class="mb-3">
                        <label class="form-label">Motivo de anulación <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="comentario" rows="3" required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle me-1"></i>Confirmar Anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Detalle --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detalle de Sanción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContenido">
                <div class="text-center text-muted py-3">Cargando...</div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<div class="modal fade" id="erroresModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Errores</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
    </div></div>
</div>
@endif
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let articulosData = [];

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal(document.getElementById('erroresModal')).show());
@endif

// ── Countdown en tiempo real ──
function actualizarCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const fin = new Date(el.dataset.fin);
        const ahora = new Date();
        const diff = fin - ahora;

        if (diff <= 0) {
            el.textContent = 'Vencida';
            el.classList.remove('text-danger');
            el.classList.add('text-muted');
            return;
        }

        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        let texto = '';
        if (d > 0) texto += d + 'd ';
        texto += String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        el.textContent = texto;
    });
}
setInterval(actualizarCountdowns, 1000);
actualizarCountdowns();

// ── Cargar vehículos disponibles al abrir modal ──
document.getElementById('aplicarSancionModal').addEventListener('show.bs.modal', function() {
    const sel = document.getElementById('select_vehiculo');
    sel.innerHTML = '<option value="">Cargando...</option>';

    fetch('{{ route("vehiculos.disponibles") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        sel.innerHTML = '<option value="">Seleccione un vehículo</option>';
        data.vehiculos.forEach(v => {
            sel.innerHTML += `<option value="${v.id}">${v.placa} - Móvil ${v.numero_movil}</option>`;
        });
    });

    // Cargar artículos
    const selArt = document.getElementById('select_articulo');
    selArt.innerHTML = '<option value="">Cargando...</option>';

    fetch('{{ route("articulos-sancion.activos") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        articulosData = data;
        selArt.innerHTML = '<option value="">Seleccione un artículo</option>';
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
        info.textContent = 'Duración: ' + texto;
    } else {
        info.textContent = '';
    }
});

function anularSancion(id, placa) {
    document.getElementById('formAnular').action = `{{ url('sanciones') }}/${id}/anular`;
    document.getElementById('anular_placa').textContent = placa;
    new bootstrap.Modal(document.getElementById('anularSancionModal')).show();
}

function verDetalle(id) {
    const c = document.getElementById('detalleContenido');
    c.innerHTML = '<div class="text-center text-muted py-3">Cargando...</div>';
    new bootstrap.Modal(document.getElementById('detalleModal')).show();

    fetch(`{{ url('sanciones') }}/${id}/detalle`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text())
    .then(html => { c.innerHTML = html; })
    .catch(() => { c.innerHTML = '<div class="alert alert-danger">Error al cargar</div>'; });
}
</script>
@endpush
