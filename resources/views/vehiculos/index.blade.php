@extends('layouts.app')

@section('title', 'Vehículos - Taxi Diamantes')
@section('page-title', 'Gestión de Vehículos')

@section('content')
{{-- Tarjetas de estadísticas --}}
<div class="row mb-4">
    @php
        $cards = [
            ['label' => 'Disponibles', 'key' => 'disponible', 'bg' => 'success', 'icon' => 'bi-check-circle'],
            ['label' => 'Ocupados', 'key' => 'ocupado', 'bg' => 'info', 'icon' => 'bi-arrow-repeat'],
            ['label' => 'Sancionados', 'key' => 'sancionado', 'bg' => 'danger', 'icon' => 'bi-exclamation-triangle'],
            ['label' => 'Mantenimiento', 'key' => 'mantenimiento', 'bg' => 'warning', 'icon' => 'bi-wrench'],
            ['label' => 'Inactivos', 'key' => 'inactivo', 'bg' => 'secondary', 'icon' => 'bi-x-circle'],
        ];
    @endphp
    @foreach($cards as $c)
    <div class="col">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="text-{{ $c['bg'] }} mb-1"><i class="bi {{ $c['icon'] }} fs-4"></i></div>
                <h4 class="mb-0">{{ $estadisticas[$c['key']] }}</h4>
                <small class="text-muted">{{ $c['label'] }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Lista de Vehículos</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearVehiculoModal">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Vehículo
        </button>
    </div>
    <div class="card-body">
        {{-- Filtros --}}
        <form method="GET" action="{{ route('vehiculos.index') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control" placeholder="Placa o móvil..." value="{{ $buscar }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="disponible" {{ $filtroEstado === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="ocupado" {{ $filtroEstado === 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                    <option value="sancionado" {{ $filtroEstado === 'sancionado' ? 'selected' : '' }}>Sancionado</option>
                    <option value="mantenimiento" {{ $filtroEstado === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="inactivo" {{ $filtroEstado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm">Filtrar</button>
                @if($buscar || $filtroEstado)
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Móvil</th>
                        <th>Placa</th>
                        <th>Marca / Modelo</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $vehiculo)
                    <tr>
                        <td><span class="fw-semibold">{{ $vehiculo->numero_movil }}</span></td>
                        <td>{{ $vehiculo->placa }}</td>
                        <td>{{ $vehiculo->marca ?? '-' }} {{ $vehiculo->modelo ?? '' }}</td>
                        <td>
                            @php
                                $badge = match($vehiculo->estado) {
                                    'disponible' => 'bg-success',
                                    'ocupado' => 'bg-info',
                                    'sancionado' => 'bg-danger',
                                    'mantenimiento' => 'bg-warning text-dark',
                                    'inactivo' => 'bg-secondary',
                                    default => 'bg-dark',
                                };
                                $label = match($vehiculo->estado) {
                                    'mantenimiento' => 'En mantenimiento',
                                    default => ucfirst($vehiculo->estado),
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td>{{ $vehiculo->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="Editar"
                                    onclick="editarVehiculo({{ $vehiculo->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" title="Detalle"
                                    onclick="verDetalle({{ $vehiculo->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(!$vehiculo->estaSancionado())
                                    @if($vehiculo->estado !== 'inactivo')
                                        <button type="button" class="btn btn-outline-warning" title="Cambiar estado"
                                            onclick="cambiarEstado({{ $vehiculo->id }}, '{{ $vehiculo->placa }}', '{{ $vehiculo->estado }}')">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    @endif
                                    @if($vehiculo->estado !== 'inactivo')
                                        <form method="POST" action="{{ route('vehiculos.destroy', $vehiculo) }}" class="d-inline"
                                            onsubmit="return confirm('¿Dar de baja al vehículo {{ $vehiculo->placa }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Dar de baja">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-outline-success" title="Reactivar"
                                            onclick="reactivar({{ $vehiculo->id }})">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No se encontraron vehículos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Vehículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearVehiculoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('vehiculos.store') }}" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Nuevo Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="placa" value="{{ old('placa') }}" required maxlength="10" placeholder="ABC123">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nº Móvil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_movil" value="{{ old('numero_movil') }}" required maxlength="20">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" value="{{ old('marca') }}" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo" value="{{ old('modelo') }}" maxlength="50">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado inicial</label>
                        <select class="form-select" name="estado">
                            <option value="disponible" {{ old('estado') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="mantenimiento" {{ old('estado') === 'mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
                            <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Editar Vehículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarVehiculoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditarVehiculo" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="placa" id="editar_placa" required maxlength="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nº Móvil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_movil" id="editar_numero_movil" required maxlength="20">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" id="editar_marca" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo" id="editar_modelo" maxlength="50">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Cambiar Estado --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formCambiarEstado">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Cambiar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Cambiar estado del vehículo <span id="estado_placa" class="fw-bold text-primary"></span>:</p>
                    <select class="form-select" name="estado" id="nuevo_estado">
                        <option value="disponible">Disponible</option>
                        <option value="mantenimiento">En mantenimiento</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle del Vehículo --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detalle del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContenido">
                <div class="text-center text-muted py-3">Cargando...</div>
            </div>
        </div>
    </div>
</div>

{{-- Errores de validación --}}
@if($errors->any())
<div class="modal fade" id="erroresModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Errores de validación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const baseUrlVehiculos = '{{ url("vehiculos") }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('erroresModal')).show();
});
@endif

function editarVehiculo(id) {
    fetch(`${baseUrlVehiculos}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('formEditarVehiculo').action = `${baseUrlVehiculos}/${data.id}`;
        document.getElementById('editar_placa').value = data.placa;
        document.getElementById('editar_numero_movil').value = data.numero_movil;
        document.getElementById('editar_marca').value = data.marca || '';
        document.getElementById('editar_modelo').value = data.modelo || '';
        new bootstrap.Modal(document.getElementById('editarVehiculoModal')).show();
    })
    .catch(() => alert('Error al cargar datos del vehículo'));
}

function cambiarEstado(id, placa, estadoActual) {
    document.getElementById('formCambiarEstado').action = `${baseUrlVehiculos}/${id}/estado`;
    document.getElementById('estado_placa').textContent = placa;

    const select = document.getElementById('nuevo_estado');
    // Deshabilitar el estado actual
    Array.from(select.options).forEach(opt => {
        opt.disabled = (opt.value === estadoActual);
        opt.selected = false;
    });
    // Seleccionar el primer habilitado
    const first = Array.from(select.options).find(o => !o.disabled);
    if (first) first.selected = true;

    new bootstrap.Modal(document.getElementById('cambiarEstadoModal')).show();
}

function reactivar(id) {
    if (!confirm('¿Reactivar este vehículo?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${baseUrlVehiculos}/${id}/estado`;
    form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">
                      <input type="hidden" name="_method" value="PATCH">
                      <input type="hidden" name="estado" value="disponible">`;
    document.body.appendChild(form);
    form.submit();
}

function verDetalle(id) {
    const contenedor = document.getElementById('detalleContenido');
    contenedor.innerHTML = '<div class="text-center text-muted py-3">Cargando...</div>';
    new bootstrap.Modal(document.getElementById('detalleModal')).show();

    fetch(`${baseUrlVehiculos}/${id}/detalle`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => { contenedor.innerHTML = html; })
    .catch(() => { contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>'; });
}
</script>
@endpush
