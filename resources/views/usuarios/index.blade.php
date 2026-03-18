@extends('layouts.app')

@section('title', 'Usuarios - Taxi Diamantes')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Lista de Usuarios</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="tablaUsuarios">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->nombreCompleto() }}</td>
                        <td>{{ $usuario->username }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->rol === 'superadmin')
                                <span class="badge bg-dark">Super Admin</span>
                            @elseif($usuario->rol === 'administrador')
                                <span class="badge bg-danger">Administrador</span>
                            @else
                                <span class="badge bg-primary">Operador</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->estado === 'activo')
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                {{-- Editar --}}
                                <button type="button" class="btn btn-outline-primary" title="Editar"
                                    onclick="editarUsuario({{ $usuario->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                @if($usuario->id !== auth()->id() && !$usuario->es_protegido)
                                    {{-- Cambiar estado --}}
                                    @if($usuario->estado === 'activo')
                                        <button type="button" class="btn btn-outline-warning" title="Desactivar"
                                            onclick="cambiarEstado({{ $usuario->id }}, 'inactivo', '{{ $usuario->username }}')">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success" title="Activar"
                                            onclick="cambiarEstado({{ $usuario->id }}, 'activo', '{{ $usuario->username }}')">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    @endif

                                    {{-- Reset password --}}
                                    <button type="button" class="btn btn-outline-info" title="Restablecer contraseña"
                                        onclick="resetPassword({{ $usuario->id }}, '{{ $usuario->username }}')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay usuarios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear Usuario --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('usuarios.store') }}" id="formCrear" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" value="{{ old('apellidos') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required minlength="8">
                            <div class="form-text">Mínimo 8 caracteres</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmar <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" name="rol" required>
                            <option value="">Seleccione un rol</option>
                            <option value="administrador" {{ old('rol') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="operador" {{ old('rol') === 'operador' ? 'selected' : '' }}>Operador</option>
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
{{-- MODAL: Editar Usuario --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditar" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="editar_nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" id="editar_apellidos" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="editar_email" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" id="editar_username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="editar_telefono">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" name="rol" id="editar_rol" required>
                            <option value="superadmin">Super Admin</option>
                            <option value="administrador">Administrador</option>
                            <option value="operador">Operador</option>
                        </select>
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
                <input type="hidden" name="estado" id="estado_valor">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo_estado"><i class="bi bi-toggle2-off me-2"></i>Cambiar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea <span id="accion_estado" class="fw-bold"></span> al usuario <span id="nombre_usuario" class="fw-bold text-primary"></span>?</p>
                    <div id="advertencia_desactivar" class="alert alert-warning" style="display:none;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        El usuario no podrá iniciar sesión mientras esté desactivado.
                    </div>
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
{{-- MODAL: Restablecer Contraseña --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formResetPassword" novalidate>
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i>Restablecer Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Nueva contraseña para <span id="reset_username" class="fw-bold text-primary"></span>:</p>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                        <div class="form-text">Mínimo 8 caracteres</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Restablecer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Errores de validación (abrir modal correspondiente) --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
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
    const baseUrl = '{{ url("usuarios") }}';

    // Mostrar errores de validación al cargar
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('erroresModal')).show();
        });
    @endif

    function editarUsuario(id) {
        fetch(`${baseUrl}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('formEditar').action = `${baseUrl}/${data.id}`;
            document.getElementById('editar_nombre').value = data.nombre;
            document.getElementById('editar_apellidos').value = data.apellidos;
            document.getElementById('editar_email').value = data.email;
            document.getElementById('editar_username').value = data.username;
            document.getElementById('editar_telefono').value = data.telefono || '';
            document.getElementById('editar_rol').value = data.rol;

            new bootstrap.Modal(document.getElementById('editarUsuarioModal')).show();
        })
        .catch(() => alert('Error al cargar datos del usuario'));
    }

    function cambiarEstado(id, estado, username) {
        document.getElementById('formCambiarEstado').action = `${baseUrl}/${id}/estado`;
        document.getElementById('estado_valor').value = estado;
        document.getElementById('nombre_usuario').textContent = username;

        if (estado === 'activo') {
            document.getElementById('titulo_estado').innerHTML = '<i class="bi bi-person-check me-2"></i>Activar Usuario';
            document.getElementById('accion_estado').textContent = 'activar';
            document.getElementById('advertencia_desactivar').style.display = 'none';
        } else {
            document.getElementById('titulo_estado').innerHTML = '<i class="bi bi-person-x me-2"></i>Desactivar Usuario';
            document.getElementById('accion_estado').textContent = 'desactivar';
            document.getElementById('advertencia_desactivar').style.display = 'block';
        }

        new bootstrap.Modal(document.getElementById('cambiarEstadoModal')).show();
    }

    function resetPassword(id, username) {
        document.getElementById('formResetPassword').action = `${baseUrl}/${id}/reset-password`;
        document.getElementById('reset_username').textContent = username;

        // Limpiar campos
        document.querySelectorAll('#formResetPassword input[type="password"]').forEach(i => i.value = '');

        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }
</script>
@endpush
