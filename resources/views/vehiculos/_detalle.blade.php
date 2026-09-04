<div class="row g-3 mb-3">
    {{-- Datos del Vehículo --}}
    <div class="col-lg-{{ ($vehiculo->estado === 'sancionado' && $vehiculo->sancionActiva) ? '6' : '12' }}">
        <div class="p-3 rounded-3" style="background: #ffffff; border: 1px solid rgba(186, 230, 253, 0.8); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.04);">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: #0a2540; font-size: 0.95rem;">Datos del Vehículo</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Ficha técnica y estado actual</small>
                    </div>
                </div>
                @php
                    $statusMap = [
                        'disponible'    => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Disponible', 'icon' => 'bi-check-circle-fill'],
                        'ocupado'       => ['bg' => '#f0f9ff', 'color' => '#0284c7', 'border' => '#bae6fd', 'label' => 'Ocupado', 'icon' => 'bi-arrow-repeat'],
                        'sancionado'    => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Sancionado', 'icon' => 'bi-exclamation-triangle-fill'],
                        'mantenimiento' => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'label' => 'Mantenimiento', 'icon' => 'bi-wrench'],
                        'inactivo'      => ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#cbd5e1', 'label' => 'Inactivo', 'icon' => 'bi-dash-circle-fill'],
                    ];
                    $st = $statusMap[$vehiculo->estado] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($vehiculo->estado), 'icon' => 'bi-circle'];
                @endphp
                <span class="badge rounded-pill" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; border: 1px solid {{ $st['border'] }}; font-size: 0.75rem; font-weight: 700; padding: 5px 12px;">
                    <i class="bi {{ $st['icon'] }} me-1"></i>{{ $st['label'] }}
                </span>
            </div>

            <div class="row g-2 small">
                <div class="col-sm-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Número Móvil</span>
                        <span class="fw-bold" style="color: #0284c7; font-size: 1.05rem;">
                            <i class="bi bi-hash"></i>{{ $vehiculo->numero_movil }}
                        </span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Placa</span>
                        <span class="badge" style="background: #ffffff; color: #0a2540; border: 1px solid #cbd5e1; font-size: 0.88rem; font-weight: 800; letter-spacing: 0.6px; padding: 4px 8px; border-radius: 6px;">
                            {{ $vehiculo->placa }}
                        </span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Marca</span>
                        <span class="fw-semibold text-dark">{{ $vehiculo->marca ?: 'No registrada' }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Modelo / Año</span>
                        <span class="fw-semibold text-dark">{{ $vehiculo->modelo ?: 'No registrado' }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Fecha de Registro</span>
                        <span class="text-secondary"><i class="bi bi-calendar3 me-1"></i>{{ $vehiculo->fecha_registro?->format('d/m/Y h:i A') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sanción Activa (si aplica) --}}
    @if($vehiculo->estado === 'sancionado' && $vehiculo->sancionActiva)
    <div class="col-lg-6">
        <div class="p-3 rounded-3 h-100" style="background: #fef2f2; border: 1px solid #fecaca; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.05);">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: #fecaca !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #fee2e2; color: #dc2626;">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-danger" style="font-size: 0.95rem;">Sanción Activa</h6>
                        <small class="text-danger opacity-75" style="font-size: 0.75rem;">Vehículo inhabilitado para despacho</small>
                    </div>
                </div>
                <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold" style="font-size: 0.72rem;">Activa</span>
            </div>

            <div class="small">
                @if($vehiculo->sancionActiva->articulo)
                <div class="mb-2 p-2 rounded-2 bg-white" style="border: 1px solid #fee2e2;">
                    <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Artículo Aplicado</span>
                    <strong class="text-danger">{{ $vehiculo->sancionActiva->articulo->codigo }}</strong>: {{ $vehiculo->sancionActiva->articulo->descripcion }}
                    <div class="mt-1 text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-stopwatch text-danger me-1"></i> Duración: <strong>{{ $vehiculo->sancionActiva->articulo->tiempo_sancion }} min</strong>
                    </div>
                </div>
                @endif

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-white" style="border: 1px solid #fee2e2;">
                            <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Inicio</span>
                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($vehiculo->sancionActiva->fecha_inicio)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-white" style="border: 1px solid #fee2e2;">
                            <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Fin Estimado</span>
                            <span class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($vehiculo->sancionActiva->fecha_fin)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                @if($vehiculo->sancionActiva->motivo)
                <div class="p-2 rounded-2 bg-white" style="border: 1px solid #fee2e2;">
                    <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Motivo Observado</span>
                    <span class="text-secondary">{{ $vehiculo->sancionActiva->motivo }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Historial de Sanciones --}}
<div class="rounded-3 overflow-hidden" style="border: 1px solid rgba(186, 230, 253, 0.8); background: #ffffff;">
    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between" style="background: #f8fafc;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i>
            <h6 class="mb-0 fw-bold" style="color: #0a2540; font-size: 0.9rem;">Historial de Sanciones</h6>
        </div>
        <span class="badge rounded-pill" style="background: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.72rem;">
            {{ $vehiculo->sanciones->count() }} registro(s)
        </span>
    </div>

    <div>
        @if($vehiculo->sanciones->isEmpty())
            <div class="text-center py-4 px-3">
                <i class="bi bi-shield-check text-success display-6 d-block mb-2 opacity-50"></i>
                <p class="text-muted small mb-0 fw-semibold">Este vehículo no presenta historial de infracciones ni sanciones.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Artículo</th>
                            <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Motivo</th>
                            <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Inicio</th>
                            <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Fin</th>
                            <th class="pe-3 py-2 text-center text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculo->sanciones as $sancion)
                        <tr>
                            <td class="ps-3 fw-bold" style="color: #0a2540;">
                                <span class="badge" style="background: #f1f5f9; color: #0a2540; border: 1px solid #e2e8f0; font-weight: 700;">
                                    {{ $sancion->articulo->codigo ?? 'S/C' }}
                                </span>
                                <span class="ms-1 small text-muted">{{ $sancion->articulo->descripcion ?? '' }}</span>
                            </td>
                            <td>
                                <span class="text-secondary">{{ Str::limit($sancion->motivo, 55) ?: '-' }}</span>
                            </td>
                            <td>
                                <span class="text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($sancion->fecha_inicio)->format('d/m/Y H:i') }}</span>
                            </td>
                            <td>
                                <span class="text-muted"><i class="bi bi-check2 me-1"></i>{{ \Carbon\Carbon::parse($sancion->fecha_fin)->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="pe-3 text-center">
                                @php
                                    $sanMap = [
                                        'activa'   => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Activa'],
                                        'cumplida' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Cumplida'],
                                        'anulada'  => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'label' => 'Anulada'],
                                    ];
                                    $sInfo = $sanMap[$sancion->estado] ?? ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#cbd5e1', 'label' => ucfirst($sancion->estado)];
                                @endphp
                                <span class="badge rounded-pill" style="background: {{ $sInfo['bg'] }}; color: {{ $sInfo['color'] }}; border: 1px solid {{ $sInfo['border'] }}; font-weight: 700; padding: 4px 10px; font-size: 0.72rem;">
                                    {{ $sInfo['label'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
