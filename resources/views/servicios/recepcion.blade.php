@extends('layouts.app')

@section('title', 'Recepción - Taxi Diamantes')
@section('page-title', 'Centro de Recepción')

@push('styles')
<style>
    .metric-card {
        border-radius: 12px;
        padding: 12px 16px;
        color: white;
        text-align: center;
        transition: transform 0.15s;
    }
    .metric-card:hover { transform: translateY(-2px); }
    .metric-card .metric-num { font-size: 1.8rem; font-weight: 700; line-height: 1; }
    .metric-card .metric-label { font-size: 0.75rem; opacity: 0.9; }

    .bg-pendiente { background: linear-gradient(135deg, #18dff5, #e6a800); }
    .bg-asignado { background: linear-gradient(135deg, #17a2b8, #138496); }
    .bg-encamino { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .bg-finalizado { background: linear-gradient(135deg, #198754, #146c43); }
    .bg-cancelado { background: linear-gradient(135deg, #dc3545, #b02a37); }
    .bg-total-serv { background: linear-gradient(135deg, #1a1a2e, #16213e); }

    /* Panel de creación rápida */
    .panel-crear {
        background: white;
        border: 2px solid #18dff5;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .panel-crear .form-label { font-weight: 600; font-size: 0.85rem; margin-bottom: 4px; }

    /* Tabla de servicios */
    .tabla-servicios { font-size: 0.85rem; }
    .tabla-servicios th { background: #1a1a2e; color: #18dff5; font-size: 0.78rem; text-transform: uppercase; white-space: nowrap; }
    .tabla-servicios td { vertical-align: middle; padding: 6px 10px; }
    .tabla-servicios tr { transition: background 0.2s; }
    .tabla-servicios tr.fila-nueva { animation: resaltar 2s ease-out; }
    .tabla-servicios tr.fila-actualizada { animation: resaltarAzul 1.5s ease-out; }

    @keyframes resaltar {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }
    @keyframes resaltarAzul {
        0% { background-color: #cfe2ff; }
        100% { background-color: transparent; }
    }

    .btn-accion { padding: 2px 8px; font-size: 0.75rem; border-radius: 6px; }
    .badge-estado { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; }

    /* Indicador de conexión */
    .conexion-indicator { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .conexion-ok { background: #198754; box-shadow: 0 0 6px #198754; }
    .conexion-error { background: #dc3545; box-shadow: 0 0 6px #dc3545; }
    .conexion-reconectando { background: #18dff5; animation: parpadeo 1s infinite; }

    @keyframes parpadeo {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    /* Búsqueda rápida de cliente */
    .autocomplete-dropdown {
        position: absolute;
        z-index: 1050;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .autocomplete-dropdown .item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .autocomplete-dropdown .item:hover { background: #18dff5; color: #1a1a2e; }

    .contenedor-tabla { max-height: 55vh; overflow-y: auto; }
</style>
@endpush

@section('content')
{{-- Métricas del día --}}
<div class="row g-2 mb-3">
    <div class="col"><div class="metric-card bg-total-serv"><div class="metric-num" id="m-total">{{ $metricas['total'] }}</div><div class="metric-label">Hoy</div></div></div>
    <div class="col"><div class="metric-card bg-pendiente"><div class="metric-num" id="m-pendientes">{{ $metricas['pendientes'] }}</div><div class="metric-label">Pendientes</div></div></div>
    <div class="col"><div class="metric-card bg-asignado"><div class="metric-num" id="m-asignados">{{ $metricas['asignados'] }}</div><div class="metric-label">Asignados</div></div></div>
    <div class="col"><div class="metric-card bg-finalizado"><div class="metric-num" id="m-finalizados">{{ $metricas['finalizados'] }}</div><div class="metric-label">Finalizados</div></div></div>
    <div class="col"><div class="metric-card bg-cancelado"><div class="metric-num" id="m-cancelados">{{ $metricas['cancelados'] }}</div><div class="metric-label">Cancelados</div></div></div>
</div>

{{-- Panel de creación rápida --}}
<div class="panel-crear">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-plus-circle me-1"></i> Nuevo Servicio</h6>
        <div>
            <span class="conexion-indicator conexion-ok" id="indicadorConexion" title="Conectado"></span>
            <small class="text-muted ms-1" id="estadoConexion">Conectado</small>
        </div>
    </div>
    <form id="formNuevoServicio" autocomplete="off">
        <div class="row g-2 align-items-end">
            {{-- Teléfono --}}
            <div class="col-md-2">
                <label class="form-label">Teléfono</label>
                <div class="position-relative">
                    <input type="text" class="form-control form-control-sm" id="inputTelefono" placeholder="Buscar..." maxlength="15" autofocus>
                    <div class="autocomplete-dropdown d-none" id="dropdownClientes"></div>
                </div>
            </div>
            {{-- Cliente --}}
            <div class="col-md-2">
                <label class="form-label">Cliente</label>
                <input type="text" class="form-control form-control-sm" id="inputClienteNombre" placeholder="Nombre" readonly>
                <input type="hidden" id="inputClienteId">
            </div>
            {{-- Dirección --}}
            <div class="col-md-3">
                <label class="form-label">Dirección</label>
                <div class="input-group input-group-sm">
                    <select class="form-select form-select-sm" id="selectDireccion" disabled>
                        <option value="">Primero busque un cliente</option>
                    </select>
                    <button type="button" class="btn btn-outline-success" id="btnNuevaDireccion" disabled title="Agregar dirección">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <input type="hidden" id="inputDireccionId">
            </div>
            {{-- Condición --}}
            <div class="col-md-2">
                <label class="form-label">Condición</label>
                <select class="form-select form-select-sm" id="selectCondicion">
                    <option value="ninguno">Ninguno</option>
                    <option value="aire">❄️ Aire</option>
                    <option value="baul">🧳 Baúl</option>
                    <option value="mascota">🐾 Mascota</option>
                    <option value="parrilla">📦 Parrilla</option>
                    <option value="transferencia">🏦 Transferencia</option>
                    <option value="daviplata">💳 Daviplata</option>
                    <option value="polarizados">🕶️ Polarizados</option>
                    <option value="silla_ruedas">♿ Silla de ruedas</option>
                </select>
            </div>
            {{-- Observaciones --}}
            <div class="col-md-2">
                <label class="form-label">Observaciones</label>
                <input type="text" class="form-control form-control-sm" id="inputObservaciones" placeholder="Opcional" maxlength="500">
            </div>
            {{-- Botón --}}
            <div class="col-md-1">
                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold" id="btnCrear" disabled>
                    <i class="bi bi-send"></i> Crear
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Tabla de servicios activos --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <span class="fw-bold"><i class="bi bi-list-ul me-1"></i> Servicios Activos</span>
        <div>
            <select class="form-select form-select-sm d-inline-block" style="width:auto" id="filtroEstado">
                <option value="todos">Todos activos</option>
                <option value="pendiente">Pendientes</option>
                <option value="asignado">Asignados</option>
            </select>
        </div>
    </div>
    <div class="contenedor-tabla">
        <table class="table table-hover tabla-servicios mb-0">
            <thead class="sticky-top">
                <tr>
                    <th>#</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Condición</th>
                    <th>Vehículo</th>
                    <th>Estado</th>
                    <th>Tiempo</th>
                    <th>Operador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaServicios">
                <tr><td colspan="9" class="text-center text-muted py-4">Cargando servicios...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Asignar Vehículo --}}
<div class="modal fade" id="modalAsignar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h6 class="modal-title"><i class="bi bi-truck me-1"></i> Asignar Vehículo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="asignarServicioId">
                <div class="mb-3 p-2 rounded" style="background:#f8f9fa;border-left:4px solid #18dff5">
                    <small class="text-muted d-block">Dirección del servicio:</small>
                    <strong id="asignarDireccionTexto">-</strong>
                    <small class="text-muted d-block" id="asignarReferenciaTexto"></small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Buscar vehículo disponible</label>
                    <input type="text" class="form-control" id="buscarVehiculo" placeholder="Placa o número móvil...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo</label>
                    <select class="form-select" id="tipoVehiculo">
                        <option value="unico">Único</option>
                        <option value="proximo">Próximo</option>
                    </select>
                </div>
                <div id="listaVehiculos" style="max-height:250px;overflow-y:auto">
                    <p class="text-muted text-center">Escriba para buscar...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cambiar Vehículo --}}
<div class="modal fade" id="modalCambiarVehiculo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title"><i class="bi bi-arrow-repeat me-1"></i> Cambiar Vehículo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cambiarServicioId">
                <div class="mb-3 p-2 rounded" style="background:#f8f9fa;border-left:4px solid #17a2b8">
                    <small class="text-muted d-block">Dirección del servicio:</small>
                    <strong id="cambiarDireccionTexto">-</strong>
                    <small class="text-muted d-block" id="cambiarReferenciaTexto"></small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Buscar nuevo vehículo</label>
                    <input type="text" class="form-control" id="buscarVehiculoCambio" placeholder="Placa o número móvil...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo</label>
                    <select class="form-select" id="tipoVehiculoCambio">
                        <option value="unico">Único</option>
                        <option value="proximo">Próximo</option>
                    </select>
                </div>
                <div id="listaVehiculosCambio" style="max-height:250px;overflow-y:auto">
                    <p class="text-muted text-center">Escriba para buscar...</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Modal Crear Dirección --}}
<div class="modal fade" id="modalNuevaDireccion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title"><i class="bi bi-geo-alt me-1"></i> Nueva Dirección</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Dirección <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nuevaDireccionTexto" placeholder="Ej: Calle 10 # 5-20" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Referencia</label>
                    <input type="text" class="form-control" id="nuevaDireccionReferencia" placeholder="Ej: Frente al parque, casa azul" maxlength="255">
                </div>
                <div id="errorNuevaDireccion" class="alert alert-danger d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm" id="btnGuardarDireccion">
                    <i class="bi bi-check-lg me-1"></i> Guardar y Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Servicio (Dirección + Condición) --}}
<div class="modal fade" id="modalEditarServicio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#18dff5">
                <h6 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Editar Servicio</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editarServicioId">
                <input type="hidden" id="editarClienteId">
                <div class="mb-3">
                    <label class="form-label fw-bold">Dirección</label>
                    <div class="input-group">
                        <select class="form-select" id="editarSelectDireccion">
                            <option value="">Cargando...</option>
                        </select>
                        <button type="button" class="btn btn-outline-warning" id="btnEditarDireccionActual" title="Editar dirección seleccionada">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success" id="btnNuevaDireccionEditar" title="Agregar dirección">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Condición</label>
                    <select class="form-select" id="editarSelectCondicion">
                        <option value="ninguno">Ninguno</option>
                        <option value="aire">❄️ Aire</option>
                        <option value="baul">🧳 Baúl</option>
                        <option value="mascota">🐾 Mascota</option>
                        <option value="parrilla">📦 Parrilla</option>
                        <option value="transferencia">🏦 Transferencia</option>
                        <option value="daviplata">💳 Daviplata</option>
                        <option value="polarizados">🕶️ Polarizados</option>
                        <option value="silla_ruedas">♿ Silla de ruedas</option>
                    </select>
                </div>
                <div id="errorEditarServicio" class="alert alert-danger d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm text-white" style="background:#1a1a2e" id="btnGuardarEditar">
                    <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Dirección Existente --}}
<div class="modal fade" id="modalEditarDireccion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h6 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Editar Dirección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editarDireccionId">
                <div class="mb-3">
                    <label class="form-label fw-bold">Dirección <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editarDireccionTexto" maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Referencia</label>
                    <input type="text" class="form-control" id="editarDireccionReferencia" maxlength="255">
                </div>
                <div id="errorEditarDireccion" class="alert alert-danger d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning btn-sm" id="btnGuardarEditarDireccion">
                    <i class="bi bi-check-lg me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const headers = { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' };

    // ══════════════════════════════════════════
    // ESTADO LOCAL — todos los servicios activos
    // ══════════════════════════════════════════
    let serviciosMap = new Map();
    let ultimoTimestamp = '';
    let filtroActual = 'todos';

    // ══════════════════════════════════════════
    // CARGA INICIAL
    // ══════════════════════════════════════════
    async function cargarServiciosIniciales() {
        try {
            const res = await fetch('{{ route("servicios.activos") }}');
            const data = await res.json();
            if (!data.error) {
                serviciosMap.clear();
                data.servicios.forEach(s => serviciosMap.set(s.id, s));
                ultimoTimestamp = data.timestamp;
                actualizarMetricas(data.metricas);
                renderTabla();
                iniciarPolling();
            }
        } catch (e) {
            console.error('Error carga inicial:', e);
            // Fallback a polling si falla
            setTimeout(cargarServiciosIniciales, 3000);
        }
    }

    // ══════════════════════════════════════════
    // POLLING INTELIGENTE (cada 3s, solo cambios)
    // ══════════════════════════════════════════
    let pollingInterval = null;

    function iniciarPolling() {
        detenerPolling();
        setConexion('ok');

        pollingInterval = setInterval(async () => {
            try {
                const res = await fetch('{{ route("servicios.cambios") }}?desde=' + encodeURIComponent(ultimoTimestamp));
                const data = await res.json();

                if (data.error) return;

                setConexion('ok');

                if (data.hayActualizaciones && data.servicios) {
                    data.servicios.forEach(s => {
                        const esNuevo = !serviciosMap.has(s.id);
                        if (['finalizado', 'cancelado'].includes(s.estado)) {
                            serviciosMap.delete(s.id);
                        } else {
                            serviciosMap.set(s.id, s);
                        }
                        setTimeout(() => {
                            const fila = document.querySelector(`tr[data-id="${s.id}"]`);
                            if (fila) {
                                fila.classList.add(esNuevo ? 'fila-nueva' : 'fila-actualizada');
                                setTimeout(() => fila.classList.remove('fila-nueva', 'fila-actualizada'), 2000);
                            }
                        }, 50);
                    });
                    if (data.metricas) actualizarMetricas(data.metricas);
                    renderTabla();
                }

                ultimoTimestamp = data.timestamp;
            } catch (e) {
                setConexion('reconectando');
            }
        }, 3000);
    }

    function detenerPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    function setConexion(estado) {
        const ind = document.getElementById('indicadorConexion');
        const txt = document.getElementById('estadoConexion');
        ind.className = 'conexion-indicator';
        if (estado === 'ok') { ind.classList.add('conexion-ok'); txt.textContent = 'Conectado'; }
        else if (estado === 'error') { ind.classList.add('conexion-error'); txt.textContent = 'Desconectado'; }
        else { ind.classList.add('conexion-reconectando'); txt.textContent = 'Reconectando...'; }
    }

    // ══════════════════════════════════════════
    // RENDER DE TABLA
    // ══════════════════════════════════════════
    function renderTabla() {
        const tbody = document.getElementById('tablaServicios');
        const contenedor = tbody.closest('.contenedor-tabla');
        const scrollTop = contenedor ? contenedor.scrollTop : 0;

        let servicios = Array.from(serviciosMap.values());

        // Filtro
        if (filtroActual !== 'todos') {
            servicios = servicios.filter(s => s.estado === filtroActual);
        }

        // Ordenar solo por ID desc (el más reciente primero, posición fija)
        servicios.sort((a, b) => b.id - a.id);

        if (servicios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay servicios activos</td></tr>';
            if (contenedor) contenedor.scrollTop = scrollTop;
            return;
        }

        tbody.innerHTML = servicios.map(s => {
            const condLabel = etiquetaCondicion(s.condicion);
            const vehiculoTxt = s.placa
                ? `${s.numero_movil} <small class="text-muted">(${s.placa})</small>${s.tipo_vehiculo === 'proximo' ? '<br><span class="badge" style="font-size:0.68rem;background:#495057;color:#fff">Próximo</span>' : '<br><span class="badge" style="font-size:0.68rem;background:#adb5bd;color:#000">Único</span>'}`
                : '<span class="text-muted">Sin asignar</span>';
            const tiempo = calcularTiempo(s.fecha_solicitud);
            const acciones = generarAcciones(s);

            return `<tr data-id="${s.id}">
                <td>${s.id}</td>
                <td><strong>${s.telefono || ''}</strong><br><small class="text-muted">${s.cliente_nombre || ''}</small></td>
                <td>${s.direccion || ''}${s.referencia ? '<br><small class="text-muted">' + s.referencia + '</small>' : ''}</td>
                <td>${condLabel}${s.observaciones ? '<br><small class="text-muted">' + escapeHtml(s.observaciones) + '</small>' : ''}</td>
                <td>${vehiculoTxt}</td>
                <td><span class="badge badge-estado bg-${colorEstado(s.estado)}">${labelEstado(s.estado)}</span></td>
                <td>${tiempo}</td>
                <td><small>${s.operador_nombre || ''}</small></td>
                <td class="text-nowrap">${acciones}</td>
            </tr>`;
        }).join('');

        if (contenedor) contenedor.scrollTop = scrollTop;
    }

    function generarAcciones(s) {
        let btns = '';
        if (s.estado === 'pendiente') {
            btns += `<button class="btn btn-outline-secondary btn-accion me-1" onclick="abrirEditarServicio(${s.id})" title="Editar dirección/condición"><i class="bi bi-pencil"></i></button>`;
            btns += `<button class="btn btn-info btn-accion me-1" onclick="abrirAsignar(${s.id})" title="Asignar vehículo"><i class="bi bi-truck"></i></button>`;
            btns += `<button class="btn btn-danger btn-accion" onclick="accionServicio(${s.id},'cancelar')" title="Cancelar"><i class="bi bi-x-lg"></i></button>`;
        } else if (s.estado === 'asignado' || s.estado === 'en_camino') {
            btns += `<button class="btn btn-outline-secondary btn-accion me-1" onclick="abrirEditarServicio(${s.id})" title="Editar dirección/condición"><i class="bi bi-pencil"></i></button>`;
            btns += `<button class="btn btn-outline-info btn-accion me-1" onclick="abrirCambiarVehiculo(${s.id})" title="Cambiar vehículo"><i class="bi bi-arrow-repeat"></i></button>`;
            btns += `<button class="btn btn-success btn-accion me-1" onclick="accionServicio(${s.id},'finalizar')" title="Finalizar"><i class="bi bi-check-lg"></i></button>`;
            btns += `<button class="btn btn-danger btn-accion" onclick="accionServicio(${s.id},'cancelar')" title="Cancelar"><i class="bi bi-x-lg"></i></button>`;
        }
        return btns;
    }

    function colorEstado(e) {
        return { pendiente:'warning', asignado:'info', en_camino:'primary', finalizado:'success', cancelado:'danger' }[e] || 'secondary';
    }
    function labelEstado(e) {
        return { pendiente:'Pendiente', asignado:'Asignado', en_camino:'En Camino', finalizado:'Finalizado', cancelado:'Cancelado' }[e] || e;
    }
    function etiquetaCondicion(c) {
        const map = {
            aire:          { icon:'❄️', label:'Aire',          bg:'#0dcaf0', color:'#000' },
            baul:          { icon:'🧳', label:'Baúl',          bg:'#6f42c1', color:'#fff' },
            mascota:       { icon:'🐾', label:'Mascota',       bg:'#d63384', color:'#fff' },
            parrilla:      { icon:'📦', label:'Parrilla',      bg:'#fd7e14', color:'#000' },
            transferencia: { icon:'🏦', label:'Transferencia',  bg:'#198754', color:'#fff' },
            daviplata:     { icon:'💳', label:'Daviplata',      bg:'#e6308a', color:'#fff' },
            polarizados:   { icon:'🕶️', label:'Polarizados',   bg:'#343a40', color:'#fff' },
            silla_ruedas:  { icon:'♿', label:'Silla de ruedas', bg:'#0d6efd', color:'#fff' },
            ninguno:       null,
        };
        const item = map[c];
        if (!item) return '';
        return `<span class="badge" style="background:${item.bg};color:${item.color};font-size:0.73rem">${item.icon} ${item.label}</span>`;
    }
    function calcularTiempo(fecha) {
        const diff = Math.floor((Date.now() - new Date(fecha).getTime()) / 60000);
        if (diff < 1) return '<1 min';
        if (diff < 60) return diff + ' min';
        return Math.floor(diff/60) + 'h ' + (diff%60) + 'm';
    }
    function escapeHtml(t) {
        const d = document.createElement('div'); d.textContent = t; return d.innerHTML;
    }

    // Actualizar tiempos cada 30 segundos
    setInterval(renderTabla, 30000);

    function actualizarMetricas(m) {
        document.getElementById('m-total').textContent = m.total;
        document.getElementById('m-pendientes').textContent = m.pendientes;
        document.getElementById('m-asignados').textContent = m.asignados;
        document.getElementById('m-finalizados').textContent = m.finalizados;
        document.getElementById('m-cancelados').textContent = m.cancelados;
    }

    // Filtro de estado
    document.getElementById('filtroEstado').addEventListener('change', function() {
        filtroActual = this.value;
        renderTabla();
    });

    // ══════════════════════════════════════════
    // BÚSQUEDA DE CLIENTE POR TELÉFONO
    // ══════════════════════════════════════════
    let buscarTimeout = null;
    const inputTel = document.getElementById('inputTelefono');
    const dropdown = document.getElementById('dropdownClientes');

    inputTel.addEventListener('input', function() {
        clearTimeout(buscarTimeout);
        const val = this.value.trim();
        if (val.length < 3) { dropdown.classList.add('d-none'); return; }

        buscarTimeout = setTimeout(async () => {
            try {
                // Primero buscar coincidencia exacta
                const res = await fetch('{{ route("clientes.buscar-telefono") }}?telefono=' + encodeURIComponent(val));
                const data = await res.json();
                if (data.cliente_existe) {
                    seleccionarCliente(data.cliente);
                    dropdown.classList.add('d-none');
                    return;
                }

                // Si no hay exacta, buscar sugerencias por autocompletar
                const res2 = await fetch('{{ route("clientes.autocompletar") }}?q=' + encodeURIComponent(val));
                const sugerencias = await res2.json();

                if (sugerencias.length > 0) {
                    dropdown.innerHTML = sugerencias.map(c =>
                        `<div class="item" onclick="seleccionarClienteDesdeDropdown(${c.id}, '${escapeHtml(c.telefono)}', '${escapeHtml(c.nombre || '')}')">
                            <strong>${c.telefono}</strong> - ${c.nombre || 'Sin nombre'}
                        </div>`
                    ).join('');
                    dropdown.classList.remove('d-none');
                } else {
                    // No encontrado — ofrecer crear
                    dropdown.innerHTML = `<div class="item text-success" onclick="crearClienteRapido('${escapeHtml(val)}')">
                        <i class="bi bi-plus-circle me-1"></i> Crear cliente con tel: ${val}
                    </div>`;
                    dropdown.classList.remove('d-none');
                }
            } catch (e) { console.error(e); }
        }, 300);
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#inputTelefono') && !e.target.closest('#dropdownClientes')) {
            dropdown.classList.add('d-none');
        }
    });

    window.seleccionarClienteDesdeDropdown = function(id, tel, nombre) {
        seleccionarCliente({ id, telefono: tel, nombre });
        dropdown.classList.add('d-none');
    };

    function seleccionarCliente(cliente) {
        document.getElementById('inputClienteId').value = cliente.id;
        document.getElementById('inputClienteNombre').value = cliente.nombre || 'Sin nombre';
        inputTel.value = cliente.telefono;
        document.getElementById('btnNuevaDireccion').disabled = false;
        cargarDirecciones(cliente.id);
    }

    window.crearClienteRapido = async function(telefono) {
        dropdown.classList.add('d-none');
        try {
            const res = await fetch('{{ route("clientes.crear-rapido") }}', {
                method: 'POST',
                headers,
                body: JSON.stringify({ telefono })
            });
            const data = await res.json();
            if (!data.error) {
                seleccionarCliente({ id: data.id, telefono: data.telefono, nombre: data.nombre });
            } else {
                alert(data.mensaje || 'Error al crear cliente');
            }
        } catch (e) { alert('Error de conexión'); }
    };

    // ══════════════════════════════════════════
    // CARGAR DIRECCIONES DEL CLIENTE
    // ══════════════════════════════════════════
    async function cargarDirecciones(clienteId) {
        const select = document.getElementById('selectDireccion');
        select.disabled = true;
        select.innerHTML = '<option value="">Cargando...</option>';

        try {
            const res = await fetch('{{ route("direcciones.por-cliente") }}?cliente_id=' + clienteId);
            const data = await res.json();

            if (data.direcciones && data.direcciones.length > 0) {
                select.innerHTML = '<option value="">Seleccione dirección</option>' +
                    data.direcciones.map(d =>
                        `<option value="${d.id}">${d.direccion}${d.referencia ? ' (' + d.referencia + ')' : ''}${d.es_frecuente ? ' ⭐' : ''}</option>`
                    ).join('');
            } else {
                select.innerHTML = '<option value="">Sin direcciones — cree una primero</option>';
            }
            select.disabled = false;
            validarFormulario();
        } catch (e) {
            select.innerHTML = '<option value="">Error al cargar</option>';
        }
    }

    document.getElementById('selectDireccion').addEventListener('change', function() {
        document.getElementById('inputDireccionId').value = this.value;
        validarFormulario();
    });

    function validarFormulario() {
        const clienteId = document.getElementById('inputClienteId').value;
        const direccionId = document.getElementById('selectDireccion').value;
        document.getElementById('btnCrear').disabled = !(clienteId && direccionId);
    }

    // ══════════════════════════════════════════
    // CREAR SERVICIO
    // ══════════════════════════════════════════
    document.getElementById('formNuevoServicio').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnCrear');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const body = {
                cliente_id: document.getElementById('inputClienteId').value,
                direccion_id: document.getElementById('selectDireccion').value,
                condicion: document.getElementById('selectCondicion').value,
                observaciones: document.getElementById('inputObservaciones').value,
            };

            const res = await fetch('{{ route("servicios.store") }}', {
                method: 'POST', headers, body: JSON.stringify(body)
            });
            const data = await res.json();

            if (!data.error) {
                // Agregar al mapa local inmediatamente (optimistic update)
                if (data.servicio) {
                    serviciosMap.set(data.servicio.id, data.servicio);
                    renderTabla();
                    // Animar la fila nueva
                    setTimeout(() => {
                        const fila = document.querySelector(`tr[data-id="${data.servicio.id}"]`);
                        if (fila) fila.classList.add('fila-nueva');
                    }, 50);
                }
                limpiarFormulario();
            } else {
                alert(data.mensaje || 'Error al crear servicio');
            }
        } catch (e) {
            alert('Error de conexión');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send"></i> Crear';
            validarFormulario();
        }
    });

    function limpiarFormulario() {
        document.getElementById('inputTelefono').value = '';
        document.getElementById('inputClienteNombre').value = '';
        document.getElementById('inputClienteId').value = '';
        document.getElementById('selectDireccion').innerHTML = '<option value="">Primero busque un cliente</option>';
        document.getElementById('selectDireccion').disabled = true;
        document.getElementById('inputDireccionId').value = '';
        document.getElementById('selectCondicion').value = 'ninguno';
        document.getElementById('inputObservaciones').value = '';
        document.getElementById('btnNuevaDireccion').disabled = true;
        document.getElementById('inputTelefono').focus();
    }

    // ══════════════════════════════════════════
    // ACCIONES DE SERVICIO (en_camino, finalizar, cancelar)
    // ══════════════════════════════════════════
    window.accionServicio = async function(servicioId, accion) {
        const rutas = {
            'finalizar': '{{ route("servicios.finalizar") }}',
            'cancelar': '{{ route("servicios.cancelar") }}'
        };

        if (accion === 'cancelar' && !confirm('¿Cancelar este servicio?')) return;

        try {
            const res = await fetch(rutas[accion], {
                method: 'POST', headers,
                body: JSON.stringify({ servicio_id: servicioId })
            });
            const data = await res.json();

            if (!data.error) {
                // Optimistic update
                serviciosMap.delete(servicioId);
                renderTabla();
            } else {
                alert(data.mensaje);
            }
        } catch (e) { alert('Error de conexión'); }
    };

    // ══════════════════════════════════════════
    // ASIGNAR VEHÍCULO
    // ══════════════════════════════════════════
    let vehiculosCache = [];

    window.abrirAsignar = function(servicioId) {
        document.getElementById('asignarServicioId').value = servicioId;
        document.getElementById('buscarVehiculo').value = '';
        document.getElementById('listaVehiculos').innerHTML = '<p class="text-muted text-center">Cargando...</p>';
        // Mostrar dirección del servicio
        const servicio = serviciosMap.get(servicioId);
        document.getElementById('asignarDireccionTexto').textContent = servicio?.direccion || '-';
        document.getElementById('asignarReferenciaTexto').textContent = servicio?.referencia || '';
        new bootstrap.Modal(document.getElementById('modalAsignar')).show();
        cargarVehiculosDisponibles('listaVehiculos', 'asignar');
    };

    async function cargarVehiculosDisponibles(contenedorId, modo) {
        try {
            const res = await fetch('{{ route("vehiculos.disponibles") }}');
            const data = await res.json();
            vehiculosCache = data.vehiculos || data;
            renderVehiculos(contenedorId, vehiculosCache, modo);
        } catch (e) {
            document.getElementById(contenedorId).innerHTML = '<p class="text-danger text-center">Error al cargar</p>';
        }
    }

    function renderVehiculos(contenedorId, vehiculos, modo) {
        const cont = document.getElementById(contenedorId);
        if (vehiculos.length === 0) {
            cont.innerHTML = '<p class="text-muted text-center">No hay vehículos disponibles</p>';
            return;
        }
        cont.innerHTML = vehiculos.map(v =>
            `<div class="d-flex justify-content-between align-items-center p-2 border-bottom" style="cursor:pointer"
                onclick="${modo === 'asignar' ? 'asignarVehiculo' : 'cambiarVehiculoConfirmar'}(${v.id})">
                <div><strong>${v.numero_movil}</strong> <small class="text-muted">(${v.placa})</small></div>
                <span class="badge bg-success">Disponible</span>
            </div>`
        ).join('');
    }

    // Filtro de búsqueda en modal asignar
    document.getElementById('buscarVehiculo').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        const filtrados = vehiculosCache.filter(v =>
            v.placa.toLowerCase().includes(term) || v.numero_movil.toLowerCase().includes(term)
        );
        renderVehiculos('listaVehiculos', filtrados, 'asignar');
    });

    window.asignarVehiculo = async function(vehiculoId) {
        const servicioId = document.getElementById('asignarServicioId').value;
        const tipo = document.getElementById('tipoVehiculo').value;

        try {
            const res = await fetch('{{ route("servicios.asignar") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ servicio_id: servicioId, vehiculo_id: vehiculoId, tipo_vehiculo: tipo })
            });
            const data = await res.json();

            if (!data.error) {
                bootstrap.Modal.getInstance(document.getElementById('modalAsignar')).hide();
                // Optimistic update
                const s = serviciosMap.get(parseInt(servicioId));
                if (s) {
                    const v = vehiculosCache.find(v => v.id === vehiculoId);
                    s.estado = 'asignado';
                    s.vehiculo_id = vehiculoId;
                    if (v) { s.placa = v.placa; s.numero_movil = v.numero_movil; }
                    serviciosMap.set(s.id, s);
                    renderTabla();
                }
            } else {
                alert(data.mensaje);
            }
        } catch (e) { alert('Error de conexión'); }
    };

    // ══════════════════════════════════════════
    // CAMBIAR VEHÍCULO
    // ══════════════════════════════════════════
    window.abrirCambiarVehiculo = function(servicioId) {
        document.getElementById('cambiarServicioId').value = servicioId;
        document.getElementById('buscarVehiculoCambio').value = '';
        document.getElementById('listaVehiculosCambio').innerHTML = '<p class="text-muted text-center">Cargando...</p>';
        const servicio = serviciosMap.get(servicioId);
        document.getElementById('cambiarDireccionTexto').textContent = servicio?.direccion || '-';
        document.getElementById('cambiarReferenciaTexto').textContent = servicio?.referencia || '';
        new bootstrap.Modal(document.getElementById('modalCambiarVehiculo')).show();
        cargarVehiculosDisponibles('listaVehiculosCambio', 'cambiar');
    };

    document.getElementById('buscarVehiculoCambio').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        const filtrados = vehiculosCache.filter(v =>
            v.placa.toLowerCase().includes(term) || v.numero_movil.toLowerCase().includes(term)
        );
        renderVehiculos('listaVehiculosCambio', filtrados, 'cambiar');
    });

    window.cambiarVehiculoConfirmar = async function(vehiculoId) {
        const servicioId = document.getElementById('cambiarServicioId').value;
        const tipo = document.getElementById('tipoVehiculoCambio').value;

        try {
            const res = await fetch('{{ route("servicios.cambiar-vehiculo") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ servicio_id: servicioId, vehiculo_id: vehiculoId, tipo_vehiculo: tipo })
            });
            const data = await res.json();

            if (!data.error) {
                bootstrap.Modal.getInstance(document.getElementById('modalCambiarVehiculo')).hide();
                const s = serviciosMap.get(parseInt(servicioId));
                if (s) {
                    const v = vehiculosCache.find(v => v.id === vehiculoId);
                    s.vehiculo_id = vehiculoId;
                    if (v) { s.placa = v.placa; s.numero_movil = v.numero_movil; }
                    serviciosMap.set(s.id, s);
                    renderTabla();
                }
            } else {
                alert(data.mensaje);
            }
        } catch (e) { alert('Error de conexión'); }
    };

    // ══════════════════════════════════════════
    // EDITAR SERVICIO (Dirección + Condición)
    // ══════════════════════════════════════════
    let editarDireccionOrigen = null; // 'editar' o 'crear' — para saber dónde volver con nueva dirección

    window.abrirEditarServicio = async function(servicioId) {
        const servicio = serviciosMap.get(servicioId);
        if (!servicio) return;

        document.getElementById('editarServicioId').value = servicioId;
        document.getElementById('editarClienteId').value = servicio.cliente_id;
        document.getElementById('editarSelectCondicion').value = servicio.condicion || 'ninguno';
        document.getElementById('errorEditarServicio').classList.add('d-none');

        const select = document.getElementById('editarSelectDireccion');
        select.innerHTML = '<option value="">Cargando...</option>';

        new bootstrap.Modal(document.getElementById('modalEditarServicio')).show();

        try {
            const res = await fetch('{{ route("direcciones.por-cliente") }}?cliente_id=' + servicio.cliente_id);
            const data = await res.json();

            if (data.direcciones && data.direcciones.length > 0) {
                select.innerHTML = data.direcciones.map(d =>
                    `<option value="${d.id}" ${d.id == servicio.direccion_id ? 'selected' : ''}>${d.direccion}${d.referencia ? ' (' + d.referencia + ')' : ''}${d.es_frecuente ? ' ⭐' : ''}</option>`
                ).join('');
            } else {
                select.innerHTML = '<option value="">Sin direcciones</option>';
            }
        } catch (e) {
            select.innerHTML = '<option value="">Error al cargar</option>';
        }
    };

    // Botón nueva dirección desde modal editar
    document.getElementById('btnNuevaDireccionEditar').addEventListener('click', function() {
        const clienteId = document.getElementById('editarClienteId').value;
        if (!clienteId) return;
        editarDireccionOrigen = 'editar';
        document.getElementById('nuevaDireccionTexto').value = '';
        document.getElementById('nuevaDireccionReferencia').value = '';
        document.getElementById('errorNuevaDireccion').classList.add('d-none');
        // Ocultar modal editar, abrir modal dirección
        bootstrap.Modal.getInstance(document.getElementById('modalEditarServicio')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalNuevaDireccion')).show();
            setTimeout(() => document.getElementById('nuevaDireccionTexto').focus(), 300);
        }, 300);
    });

    // Botón editar dirección seleccionada
    document.getElementById('btnEditarDireccionActual').addEventListener('click', async function() {
        const select = document.getElementById('editarSelectDireccion');
        const direccionId = select.value;
        if (!direccionId) return;

        document.getElementById('editarDireccionId').value = direccionId;
        document.getElementById('errorEditarDireccion').classList.add('d-none');

        // Cargar datos actuales de la dirección
        try {
            const res = await fetch('/direcciones/' + direccionId);
            const data = await res.json();
            document.getElementById('editarDireccionTexto').value = data.direccion || '';
            document.getElementById('editarDireccionReferencia').value = data.referencia || '';
        } catch (e) {
            // Fallback: tomar del texto del option
            const textoOpt = select.selectedOptions[0]?.textContent || '';
            const match = textoOpt.match(/^(.+?)(?:\s*\((.+?)\))?(?:\s*⭐)?$/);
            document.getElementById('editarDireccionTexto').value = match ? match[1].trim() : textoOpt;
            document.getElementById('editarDireccionReferencia').value = match?.[2] || '';
        }

        bootstrap.Modal.getInstance(document.getElementById('modalEditarServicio')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalEditarDireccion')).show();
            setTimeout(() => document.getElementById('editarDireccionTexto').focus(), 300);
        }, 300);
    });

    // Guardar edición de dirección existente
    document.getElementById('btnGuardarEditarDireccion').addEventListener('click', async function() {
        const direccionId = document.getElementById('editarDireccionId').value;
        const direccion = document.getElementById('editarDireccionTexto').value.trim();
        const referencia = document.getElementById('editarDireccionReferencia').value.trim();
        const errorDiv = document.getElementById('errorEditarDireccion');

        if (!direccion) {
            errorDiv.textContent = 'La dirección es obligatoria.';
            errorDiv.classList.remove('d-none');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        errorDiv.classList.add('d-none');

        try {
            const res = await fetch('/direcciones/' + direccionId, {
                method: 'PUT', headers,
                body: JSON.stringify({ direccion, referencia })
            });
            const data = await res.json();

            if (!data.error) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarDireccion')).hide();
                // Recargar direcciones en el modal editar y volver a abrirlo
                const clienteId = document.getElementById('editarClienteId').value;
                const servicioId = document.getElementById('editarServicioId').value;
                setTimeout(async () => {
                    const select = document.getElementById('editarSelectDireccion');
                    const resDir = await fetch('{{ route("direcciones.por-cliente") }}?cliente_id=' + clienteId);
                    const dataDir = await resDir.json();
                    if (dataDir.direcciones && dataDir.direcciones.length > 0) {
                        select.innerHTML = dataDir.direcciones.map(d =>
                            `<option value="${d.id}" ${d.id == direccionId ? 'selected' : ''}>${d.direccion}${d.referencia ? ' (' + d.referencia + ')' : ''}${d.es_frecuente ? ' ⭐' : ''}</option>`
                        ).join('');
                    }
                    // Actualizar optimistic en el mapa local
                    const s = serviciosMap.get(parseInt(servicioId));
                    if (s && s.direccion_id == direccionId) {
                        s.direccion = direccion;
                        s.referencia = referencia;
                        serviciosMap.set(s.id, s);
                        renderTabla();
                    }
                    new bootstrap.Modal(document.getElementById('modalEditarServicio')).show();
                }, 300);
            } else {
                errorDiv.textContent = data.mensaje || 'Error al actualizar.';
                errorDiv.classList.remove('d-none');
            }
        } catch (e) {
            errorDiv.textContent = 'Error de conexión.';
            errorDiv.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Guardar';
        }
    });

    // Enter en modal editar dirección = guardar
    document.getElementById('modalEditarDireccion').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnGuardarEditarDireccion').click();
        }
    });

    // Guardar cambios del servicio
    document.getElementById('btnGuardarEditar').addEventListener('click', async function() {
        const servicioId = document.getElementById('editarServicioId').value;
        const direccionId = document.getElementById('editarSelectDireccion').value;
        const condicion = document.getElementById('editarSelectCondicion').value;
        const errorDiv = document.getElementById('errorEditarServicio');

        if (!direccionId) {
            errorDiv.textContent = 'Seleccione una dirección.';
            errorDiv.classList.remove('d-none');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        errorDiv.classList.add('d-none');

        try {
            const res = await fetch('{{ route("servicios.actualizar-direccion") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ servicio_id: parseInt(servicioId), direccion_id: parseInt(direccionId), condicion })
            });
            const data = await res.json();

            if (!data.error) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarServicio')).hide();
                // Optimistic update
                const s = serviciosMap.get(parseInt(servicioId));
                if (s) {
                    s.direccion_id = parseInt(direccionId);
                    s.condicion = condicion;
                    // Actualizar texto de dirección desde el select
                    const opt = document.getElementById('editarSelectDireccion').selectedOptions[0];
                    if (opt) {
                        const textoCompleto = opt.textContent;
                        const match = textoCompleto.match(/^(.+?)(?:\s*\((.+?)\))?(?:\s*⭐)?$/);
                        if (match) {
                            s.direccion = match[1].trim();
                            s.referencia = match[2] || '';
                        }
                    }
                    serviciosMap.set(s.id, s);
                    renderTabla();
                }
            } else {
                errorDiv.textContent = data.mensaje || 'Error al actualizar.';
                errorDiv.classList.remove('d-none');
            }
        } catch (e) {
            errorDiv.textContent = 'Error de conexión.';
            errorDiv.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Guardar Cambios';
        }
    });

    // ══════════════════════════════════════════
    // CREAR DIRECCIÓN DESDE RECEPCIÓN
    // ══════════════════════════════════════════
    document.getElementById('btnNuevaDireccion').addEventListener('click', function() {
        const clienteId = document.getElementById('inputClienteId').value;
        if (!clienteId) return;
        editarDireccionOrigen = 'crear';
        document.getElementById('nuevaDireccionTexto').value = '';
        document.getElementById('nuevaDireccionReferencia').value = '';
        document.getElementById('errorNuevaDireccion').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('modalNuevaDireccion')).show();
        setTimeout(() => document.getElementById('nuevaDireccionTexto').focus(), 300);
    });

    document.getElementById('btnGuardarDireccion').addEventListener('click', async function() {
        const clienteId = editarDireccionOrigen === 'editar'
            ? document.getElementById('editarClienteId').value
            : document.getElementById('inputClienteId').value;
        const direccion = document.getElementById('nuevaDireccionTexto').value.trim();
        const referencia = document.getElementById('nuevaDireccionReferencia').value.trim();
        const errorDiv = document.getElementById('errorNuevaDireccion');

        if (!direccion) {
            errorDiv.textContent = 'La dirección es obligatoria.';
            errorDiv.classList.remove('d-none');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        errorDiv.classList.add('d-none');

        try {
            const res = await fetch('{{ route("direcciones.store") }}', {
                method: 'POST', headers,
                body: JSON.stringify({ cliente_id: clienteId, direccion, referencia })
            });
            const data = await res.json();

            if (!data.error) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuevaDireccion')).hide();

                if (editarDireccionOrigen === 'editar') {
                    // Volver al modal editar y recargar direcciones
                    setTimeout(async () => {
                        const servicioId = document.getElementById('editarServicioId').value;
                        const select = document.getElementById('editarSelectDireccion');
                        const resDir = await fetch('{{ route("direcciones.por-cliente") }}?cliente_id=' + clienteId);
                        const dataDir = await resDir.json();
                        if (dataDir.direcciones && dataDir.direcciones.length > 0) {
                            select.innerHTML = dataDir.direcciones.map(d =>
                                `<option value="${d.id}" ${d.id == data.direccion_id ? 'selected' : ''}>${d.direccion}${d.referencia ? ' (' + d.referencia + ')' : ''}${d.es_frecuente ? ' ⭐' : ''}</option>`
                            ).join('');
                        }
                        new bootstrap.Modal(document.getElementById('modalEditarServicio')).show();
                    }, 300);
                } else {
                    // Origen: formulario principal
                    await cargarDirecciones(clienteId);
                    const select = document.getElementById('selectDireccion');
                    select.value = data.direccion_id;
                    document.getElementById('inputDireccionId').value = data.direccion_id;
                    validarFormulario();
                }
            } else {
                errorDiv.textContent = data.mensaje || 'Error al crear dirección.';
                errorDiv.classList.remove('d-none');
            }
        } catch (e) {
            errorDiv.textContent = 'Error de conexión.';
            errorDiv.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Guardar y Seleccionar';
        }
    });

    // Enter en el modal de dirección = guardar
    document.getElementById('modalNuevaDireccion').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnGuardarDireccion').click();
        }
    });

    // ══════════════════════════════════════════
    // ATAJOS DE TECLADO
    // ══════════════════════════════════════════
    document.addEventListener('keydown', function(e) {
        // F2 = enfocar teléfono para nuevo servicio rápido
        if (e.key === 'F2') {
            e.preventDefault();
            document.getElementById('inputTelefono').focus();
        }
    });

    // ══════════════════════════════════════════
    // LIMPIAR AL SALIR DE LA PÁGINA
    // ══════════════════════════════════════════
    window.addEventListener('beforeunload', function() {
        detenerPolling();
    });

    // ══════════════════════════════════════════
    // INICIAR
    // ══════════════════════════════════════════
    cargarServiciosIniciales();
});
</script>
@endpush
