@extends('layouts.app')

@section('title', 'Artículos de Sanción - Taxi Diamantes')
@section('page-title', 'Artículos de Sanción')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Catálogo de Artículos</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearArticuloModal">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Artículo
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tiempo de Sanción</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articulos as $articulo)
                    <tr>
                        <td><span class="badge bg-warning text-dark">{{ $articulo->codigo }}</span></td>
                        <td>{{ $articulo->descripcion }}</td>
                        <td>{{ $articulo->tiempoFormateado() }}</td>
                        <td>
                            <span class="badge {{ $articulo->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($articulo->estado) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" title="Editar"
                                    onclick="editarArticulo({{ $articulo->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($articulo->estado === 'activo')
                                    <form method="POST" action="{{ route('articulos-sancion.cambiar-estado', $articulo) }}" class="d-inline"
                                        onsubmit="return confirm('¿Desactivar este artículo?')">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="inactivo">
                                        <button type="submit" class="btn btn-outline-warning" title="Desactivar"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('articulos-sancion.cambiar-estado', $articulo) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="activo">
                                        <button type="submit" class="btn btn-outline-success" title="Activar"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay artículos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL: Crear Artículo --}}
<div class="modal fade" id="crearArticuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('articulos-sancion.store') }}" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Nuevo Artículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="codigo" value="{{ old('codigo') }}" required maxlength="20" placeholder="ART-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descripcion" rows="2" required>{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tiempo de sanción (minutos) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="tiempo_sancion" value="{{ old('tiempo_sancion') }}" required min="1">
                            <div class="form-text">Ej: 60 = 1 hora, 1440 = 1 día</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
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

{{-- MODAL: Editar Artículo --}}
<div class="modal fade" id="editarArticuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditarArticulo" novalidate>
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Artículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="codigo" id="editar_codigo" required maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descripcion" id="editar_descripcion" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tiempo (minutos) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="tiempo_sancion" id="editar_tiempo" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="editar_estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
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
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal(document.getElementById('erroresModal')).show());
@endif

function editarArticulo(id) {
    fetch(`{{ url('articulos-sancion') }}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('formEditarArticulo').action = `{{ url('articulos-sancion') }}/${data.id}`;
        document.getElementById('editar_codigo').value = data.codigo;
        document.getElementById('editar_descripcion').value = data.descripcion;
        document.getElementById('editar_tiempo').value = data.tiempo_sancion;
        document.getElementById('editar_estado').value = data.estado;
        new bootstrap.Modal(document.getElementById('editarArticuloModal')).show();
    })
    .catch(() => alert('Error al cargar datos'));
}
</script>
@endpush
