<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="p-3 rounded-3" style="background:#f0f9ff; border: 1px solid #bae6fd;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-person-badge text-primary fs-5"></i>
                <h6 class="fw-bold mb-0" style="color: #0a2540;">Datos del Cliente</h6>
            </div>
            <p class="mb-1 small"><strong>Nombre:</strong> <span class="text-secondary">{{ $cliente->nombre }}</span></p>
            <p class="mb-0 small"><strong>Teléfono:</strong> <span class="badge" style="background:#ffffff; color:#0284c7; border:1px solid #bae6fd;">{{ $cliente->telefono }}</span></p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 rounded-3" style="background:#ffffff; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-graph-up-arrow text-primary fs-5"></i>
                <h6 class="fw-bold mb-0" style="color: #0a2540;">Estadísticas del Cliente</h6>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <div class="small">Total carreras: <strong class="text-dark">{{ $stats['total'] }}</strong></div>
                    <div class="small">Finalizadas: <strong class="text-success">{{ $stats['finalizados'] }}</strong></div>
                </div>
                <div class="col-6">
                    <div class="small">Canceladas: <strong class="text-danger">{{ $stats['cancelados'] }}</strong></div>
                    <div class="small">Otras: <strong class="text-muted">{{ $stats['total'] - $stats['finalizados'] - $stats['cancelados'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($servicios->isEmpty())
    <div class="alert alert-info border-0 rounded-3 text-center py-4 mb-0" style="background:#f0f9ff; color:#0284c7;">
        <i class="bi bi-info-circle display-6 d-block mb-2"></i>
        No hay servicios registrados para este cliente.
    </div>
@else
    <div class="table-responsive mb-3">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead style="background:#f8fafc; color:#475569; font-size:0.73rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                <tr>
                    <th class="py-2 ps-3">Fecha y Hora</th>
                    <th class="py-2">Dirección</th>
                    <th class="py-2">Vehículo</th>
                    <th class="py-2 pe-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                <tr>
                    <td class="ps-3 small text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($servicio->fecha_solicitud)->format('d/m/Y H:i') }}</td>
                    <td class="fw-semibold" style="color:#0a2540; font-size:0.85rem;">{{ $servicio->direccion->direccion ?? 'N/A' }}</td>
                    <td>
                        @if($servicio->vehiculo)
                            <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-weight:600;">
                                <i class="bi bi-truck me-1"></i>{{ $servicio->vehiculo->numero_movil }} <small>({{ $servicio->vehiculo->placa }})</small>
                            </span>
                        @else
                            <span class="text-muted small">No asignado</span>
                        @endif
                    </td>
                    <td class="pe-3 text-center">
                        @php
                            $pills = [
                                'pendiente' => 'background:#fffbeb; color:#b45309; border:1px solid #fde68a;',
                                'asignado' => 'background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd;',
                                'en_camino' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
                                'finalizado' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
                                'cancelado' => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                            ];
                            $style = $pills[$servicio->estado] ?? 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;';
                        @endphp
                        <span class="badge rounded-pill" style="{{ $style }} font-size:0.72rem; font-weight:700; padding:4px 9px;">
                            {{ ucfirst(str_replace('_', ' ', $servicio->estado)) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($direccionesFrecuentes->isNotEmpty())
    <div class="card border-0 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-transparent py-2 px-3 border-bottom" style="border-color: #e2e8f0 !important;">
            <h6 class="card-title mb-0 small fw-bold" style="color: #0a2540;">
                <i class="bi bi-pin-map text-primary me-1"></i> Direcciones más frecuentes
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                @foreach($direccionesFrecuentes as $dir => $cantidad)
                <div class="col-md-6 col-lg-4">
                    <div class="p-2 rounded-3 bg-white border d-flex align-items-center gap-2" style="border-color: #e2e8f0 !important;">
                        <i class="bi bi-geo-alt text-primary fs-5"></i>
                        <div class="overflow-hidden">
                            <div class="small fw-semibold text-truncate" style="color: #0a2540;" title="{{ $dir }}">{{ $dir }}</div>
                            <span class="badge" style="background:#f0f9ff; color:#0284c7; font-size:0.68rem; font-weight:600;">{{ $cantidad }} servicios</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
@endif
