<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3 border-0 bg-light">
            <div class="card-body">
                <h6 class="card-title">Datos del Vehículo</h6>
                <table class="table table-sm mb-0">
                    <tr><th width="35%">Placa:</th><td>{{ $vehiculo->placa }}</td></tr>
                    <tr><th>Nº Móvil:</th><td>{{ $vehiculo->numero_movil }}</td></tr>
                    <tr><th>Marca:</th><td>{{ $vehiculo->marca ?? '-' }}</td></tr>
                    <tr><th>Modelo:</th><td>{{ $vehiculo->modelo ?? '-' }}</td></tr>
                    <tr>
                        <th>Estado:</th>
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
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($vehiculo->estado) }}</span>
                        </td>
                    </tr>
                    <tr><th>Registro:</th><td>{{ $vehiculo->fecha_registro?->format('d/m/Y H:i') ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        @if($vehiculo->estado === 'sancionado' && $vehiculo->sancionActiva)
        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Sanción Activa</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    @if($vehiculo->sancionActiva->articulo)
                    <tr><th width="35%">Artículo:</th><td>{{ $vehiculo->sancionActiva->articulo->codigo }} - {{ $vehiculo->sancionActiva->articulo->descripcion }}</td></tr>
                    <tr><th>Tiempo:</th><td>{{ $vehiculo->sancionActiva->articulo->tiempo_sancion }} minutos</td></tr>
                    @endif
                    <tr><th>Inicio:</th><td>{{ \Carbon\Carbon::parse($vehiculo->sancionActiva->fecha_inicio)->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Fin estimado:</th><td>{{ \Carbon\Carbon::parse($vehiculo->sancionActiva->fecha_fin)->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Motivo:</th><td>{{ $vehiculo->sancionActiva->motivo ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Historial de sanciones --}}
<div class="card border-0">
    <div class="card-header bg-light">
        <h6 class="mb-0">Historial de Sanciones</h6>
    </div>
    <div class="card-body">
        @if($vehiculo->sanciones->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-1"></i> Este vehículo no tiene historial de sanciones.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Motivo</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculo->sanciones as $sancion)
                        <tr>
                            <td>{{ $sancion->articulo->codigo ?? 'N/A' }} - {{ $sancion->articulo->descripcion ?? '' }}</td>
                            <td>{{ Str::limit($sancion->motivo, 50) ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($sancion->fecha_inicio)->format('d/m/Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sancion->fecha_fin)->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
