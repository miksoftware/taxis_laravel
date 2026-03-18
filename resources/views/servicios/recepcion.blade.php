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
                <select class="form-select form-select-sm" id="selectDireccion" disabled>
                    <option value="">Primero busque un cliente</option>
                </select>
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
        let servicios = Array.from(serviciosMap.values());

        // Filtro
        if (filtroActual !== 'todos') {
            servicios = servicios.filter(s => s.estado === filtroActual);
        }

        // Ordenar: pendientes primero, luego por fecha desc
        const orden = { pendiente: 0, asignado: 1, en_camino: 2 };
        servicios.sort((a, b) => {
            if (orden[a.estado] !== orden[b.estado]) return orden[a.estado] - orden[b.estado];
            return new Date(b.fecha_solicitud) - new Date(a.fecha_solicitud);
        });

        if (servicios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay servicios activos</td></tr>';
            return;
        }

        tbody.innerHTML = servicios.map(s => {
            const condLabel = etiquetaCondicion(s.condicion);
            const vehiculoTxt = s.placa ? `${s.numero_movil} <small class="text-muted">(${s.placa})</small>` : '<span class="text-muted">Sin asignar</span>';
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
    }

    function generarAcciones(s) {
        let btns = '';
        if (s.estado === 'pendiente') {
            btns += `<button class="btn btn-info btn-accion me-1" onclick="abrirAsignar(${s.id})" title="Asignar vehículo"><i class="bi bi-truck"></i></button>`;
            btns += `<button class="btn btn-danger btn-accion" onclick="accionServicio(${s.id},'cancelar')" title="Cancelar"><i class="bi bi-x-lg"></i></button>`;
        } else if (s.estado === 'asignado' || s.estado === 'en_camino') {
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
        const map = { aire:'❄️', baul:'🧳', mascota:'🐾', parrilla:'📦', transferencia:'🏦', daviplata:'💳', polarizados:'🕶️', silla_ruedas:'♿', ninguno:'' };
        return map[c] || c;
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
