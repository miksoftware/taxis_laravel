<div class="row g-3 mb-3">
    {{-- Vehículo y Estado --}}
    <div class="col-md-6">
        <div class="p-3 rounded-3 h-100" style="background: #ffffff; border: 1px solid rgba(186, 230, 253, 0.8); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.04);">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: #0a2540; font-size: 0.92rem;">Vehículo Móvil</h6>
                    </div>
                </div>
                @php
                    $statusMap = [
                        'activa'   => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Activa', 'icon' => 'bi-exclamation-octagon-fill'],
                        'cumplida' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Cumplida', 'icon' => 'bi-check-circle-fill'],
                        'anulada'  => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'label' => 'Anulada', 'icon' => 'bi-x-circle-fill'],
                    ];
                    $st = $statusMap[$sancion->estado] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($sancion->estado), 'icon' => 'bi-circle'];
                @endphp
                <span class="badge rounded-pill" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; border: 1px solid {{ $st['border'] }}; font-size: 0.72rem; font-weight: 700; padding: 4px 10px;">
                    <i class="bi {{ $st['icon'] }} me-1"></i>{{ $st['label'] }}
                </span>
            </div>

            <div class="row g-2 small">
                <div class="col-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Número Móvil</span>
                        <span class="fw-bold" style="color: #0284c7; font-size: 1.05rem;">
                            <i class="bi bi-hash"></i>{{ $sancion->vehiculo->numero_movil }}
                        </span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Placa</span>
                        <span class="badge" style="background: #ffffff; color: #0a2540; border: 1px solid #cbd5e1; font-size: 0.85rem; font-weight: 800; letter-spacing: 0.5px; padding: 4px 8px; border-radius: 6px;">
                            {{ $sancion->vehiculo->placa }}
                        </span>
                    </div>
                </div>
                @if($sancion->estado === 'activa')
                <div class="col-12">
                    <div class="p-2 rounded-2" style="background: #fef2f2; border: 1px solid #fee2e2;">
                        <span class="text-danger d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Tiempo Restante</span>
                        <span class="countdown fw-bold text-danger" data-fin="{{ $sancion->fecha_fin->toIso8601String() }}" style="font-size: 0.95rem;">
                            <i class="bi bi-stopwatch me-1"></i>{{ $sancion->tiempoRestanteFormateado() }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Artículo y Duración --}}
    <div class="col-md-6">
        <div class="p-3 rounded-3 h-100" style="background: #ffffff; border: 1px solid rgba(186, 230, 253, 0.8); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.04);">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fef3c7; color: #b45309;">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #0a2540; font-size: 0.92rem;">Artículo de Infracción</h6>
                </div>
            </div>

            <div class="p-2 rounded-2 mb-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 800; font-size: 0.75rem;">
                        {{ $sancion->articulo->codigo }}
                    </span>
                    <strong style="color: #0a2540; font-size: 0.85rem;">{{ $sancion->articulo->descripcion }}</strong>
                </div>
                <small class="text-muted d-block">
                    <i class="bi bi-stopwatch text-primary me-1"></i>Duración reglamentaria: <strong>{{ \App\Models\ArticuloSancion::formatearMinutos($sancion->articulo->tiempo_sancion) }}</strong>
                </small>
            </div>

            <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Registrada por</span>
                <span class="fw-semibold text-dark small"><i class="bi bi-person-check text-primary me-1"></i>{{ $sancion->usuario->nombre }} {{ $sancion->usuario->apellidos }}</span>
            </div>
        </div>
    </div>

    {{-- Período de Tiempo y Motivo --}}
    <div class="col-12">
        <div class="p-3 rounded-3" style="background: #ffffff; border: 1px solid rgba(186, 230, 253, 0.8); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.04);">
            <div class="row g-2 mb-2 small">
                <div class="col-sm-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Fecha de Inicio</span>
                        <span class="fw-semibold text-dark"><i class="bi bi-calendar-check text-success me-1"></i>{{ $sancion->fecha_inicio->format('d/m/Y h:i:s A') }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Fecha de Fin Estimado</span>
                        <span class="fw-semibold text-danger"><i class="bi bi-calendar-x text-danger me-1"></i>{{ $sancion->fecha_fin->format('d/m/Y h:i:s A') }}</span>
                    </div>
                </div>
            </div>

            <div class="p-2 rounded-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                <span class="text-muted d-block" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Motivo Observado</span>
                <p class="mb-0 text-secondary small">{{ $sancion->motivo }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Historial de Acciones / Trazabilidad --}}
@if($sancion->historial->isNotEmpty())
<div class="rounded-3 overflow-hidden" style="border: 1px solid rgba(186, 230, 253, 0.8); background: #ffffff;">
    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between" style="background: #f8fafc;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i>
            <h6 class="mb-0 fw-bold" style="color: #0a2540; font-size: 0.88rem;">Historial de Trazabilidad</h6>
        </div>
        <span class="badge rounded-pill" style="background: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.72rem;">
            {{ $sancion->historial->count() }} evento(s)
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Fecha</th>
                    <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Acción</th>
                    <th class="py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Usuario</th>
                    <th class="pe-3 py-2 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Comentario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sancion->historial as $h)
                <tr>
                    <td class="ps-3 text-muted"><i class="bi bi-clock me-1"></i>{{ $h->fecha->format('d/m/Y H:i:s') }}</td>
                    <td>
                        @php
                            $hbMap = [
                                'aplicada' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'],
                                'anulada'  => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a'],
                                'cumplida' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
                            ];
                            $hb = $hbMap[$h->accion] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0'];
                        @endphp
                        <span class="badge rounded-pill" style="background: {{ $hb['bg'] }}; color: {{ $hb['color'] }}; border: 1px solid {{ $hb['border'] }}; font-weight: 700; padding: 4px 8px; font-size: 0.72rem;">
                            {{ ucfirst($h->accion) }}
                        </span>
                    </td>
                    <td class="fw-semibold text-dark">{{ $h->usuario->nombre }} {{ $h->usuario->apellidos }}</td>
                    <td class="pe-3 text-secondary">{{ $h->comentario ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
