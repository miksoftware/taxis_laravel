@extends('layouts.app')

@section('title', 'Clientes - Taxi Diamantes')
@section('page-title', 'Gestión de Clientes')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Lista de Clientes</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearClienteModal">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Cliente
        </button>
    </div>
    <div class="card-body">
        {{-- Buscador --}}
        <form method="GET" action="{{ route('clientes.index') }}" class="row g-2 mb-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por teléfono o nombre..." value="{{ $filtro }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
                @if($filtro)
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Teléfono</th>
                        <th>Nombre</th>
                        <th>Direcciones</th>
                        <th>Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td><span class="fw-semibold">{{ $cliente->telefono }}</span></td>
                        <td>{{ $cliente->nombre }}</td>
                        <td><span class="badge bg-info">{{ $cliente->direcciones_activas_count }}</span></td>
                        <td>{{ $cliente->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="Editar"
                                    onclick="editarCliente({{ $cliente->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" title="Direcciones"
                                    onclick="verDirecciones({{ $cliente->id }}, '{{ $cliente->nombre }}')">
                                    <i class="bi bi-geo-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" title="Historial"
                                    onclick="verHistorial({{ $cliente->id }})">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No se encontraron clientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $clientes->links() }}
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('clientes.store') }}" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}" required maxlength="15">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" maxlength="100" placeholder="Si no se indica, se usará 'Cliente + teléfono'">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="2" maxlength="1000">{{ old('notas') }}</textarea>
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
{{-- MODAL: Editar Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditarCliente" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="editar_telefono_display" disabled>
                        <div class="form-text">El teléfono no se puede modificar.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="editar_nombre" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" id="editar_notas" rows="2" maxlength="1000"></textarea>
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
{{-- MODAL: Direcciones del Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="direccionesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-geo-alt me-2"></i>Direcciones de <span id="dir_nombre_cliente"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Formulario nueva dirección --}}
                <form id="formNuevaDireccion" class="row g-2 mb-3">
                    <input type="hidden" id="dir_cliente_id">
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" id="nueva_direccion" placeholder="Nueva dirección..." required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" id="nueva_referencia" placeholder="Referencia (opcional)">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-plus-lg me-1"></i>Agregar</button>
                    </div>
                </form>

                <div id="listaDirecciones">
                    <div class="text-center text-muted py-3">Cargando...</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Historial del Cliente --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="historialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Historial de Servicios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historialContenido">
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
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('erroresModal')).show();
});
@endif

// ── Editar Cliente ──
function editarCliente(id) {
    fetch(`{{ url('clientes') }}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('formEditarCliente').action = `{{ url('clientes') }}/${data.id}`;
        document.getElementById('editar_telefono_display').value = data.telefono;
        document.getElementById('editar_nombre').value = data.nombre || '';
        document.getElementById('editar_notas').value = data.notas || '';
        new bootstrap.Modal(document.getElementById('editarClienteModal')).show();
    })
    .catch(() => alert('Error al cargar datos del cliente'));
}

// ── Ver Direcciones ──
function verDirecciones(clienteId, nombre) {
    document.getElementById('dir_cliente_id').value = clienteId;
    document.getElementById('dir_nombre_cliente').textContent = nombre;
    cargarDirecciones(clienteId);
    new bootstrap.Modal(document.getElementById('direccionesModal')).show();
}

function cargarDirecciones(clienteId) {
    const contenedor = document.getElementById('listaDirecciones');
    contenedor.innerHTML = '<div class="text-center text-muted py-3">Cargando...</div>';

    fetch(`{{ url('direcciones') }}?cliente_id=${clienteId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.error || !data.direcciones.length) {
            contenedor.innerHTML = '<div class="text-center text-muted py-3">No hay direcciones registradas</div>';
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-sm table-hover align-middle"><thead class="table-light"><tr>';
        html += '<th>Dirección</th><th>Referencia</th><th class="text-center">Frecuente</th><th class="text-center">Acciones</th></tr></thead><tbody>';

        data.direcciones.forEach(d => {
            const frecIcon = d.es_frecuente ? 'bi-star-fill text-warning' : 'bi-star text-muted';
            html += `<tr>
                <td>${d.direccion}</td>
                <td class="text-muted small">${d.referencia || '-'}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-link p-0" onclick="toggleFrecuente(${d.id}, ${!d.es_frecuente})">
                        <i class="bi ${frecIcon}"></i>
                    </button>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarDireccion(${d.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        contenedor.innerHTML = html;
    })
    .catch(() => { contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar direcciones</div>'; });
}

// ── Agregar dirección ──
document.getElementById('formNuevaDireccion').addEventListener('submit', function(e) {
    e.preventDefault();
    const clienteId = document.getElementById('dir_cliente_id').value;
    const direccion = document.getElementById('nueva_direccion').value.trim();
    const referencia = document.getElementById('nueva_referencia').value.trim();

    if (!direccion) return;

    fetch('{{ route("direcciones.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ cliente_id: clienteId, direccion, referencia })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.mensaje); return; }
        document.getElementById('nueva_direccion').value = '';
        document.getElementById('nueva_referencia').value = '';
        cargarDirecciones(clienteId);
    })
    .catch(() => alert('Error al guardar dirección'));
});

// ── Toggle frecuente ──
function toggleFrecuente(id, valor) {
    fetch(`{{ url('direcciones') }}/${id}/frecuente`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ es_frecuente: valor })
    })
    .then(r => r.json())
    .then(() => cargarDirecciones(document.getElementById('dir_cliente_id').value))
    .catch(() => alert('Error al actualizar'));
}

// ── Eliminar dirección ──
function eliminarDireccion(id) {
    if (!confirm('¿Está seguro de eliminar esta dirección?')) return;

    fetch(`{{ url('direcciones') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.mensaje); return; }
        cargarDirecciones(document.getElementById('dir_cliente_id').value);
    })
    .catch(() => alert('Error al eliminar'));
}

// ── Ver Historial ──
function verHistorial(clienteId) {
    const contenedor = document.getElementById('historialContenido');
    contenedor.innerHTML = '<div class="text-center text-muted py-3">Cargando...</div>';
    new bootstrap.Modal(document.getElementById('historialModal')).show();

    fetch(`{{ url('clientes') }}/${clienteId}/historial`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => { contenedor.innerHTML = html; })
    .catch(() => { contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar historial</div>'; });
}
</script>
@endpush
