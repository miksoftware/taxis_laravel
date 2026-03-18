<div class="row mb-3">
    <div class="col-md-6">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="card-title">Datos del cliente</h6>
                <p class="mb-1"><strong>Nombre:</strong> {{ $cliente->nombre }}</p>
                <p class="mb-1"><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="card-title">Estadísticas</h6>
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1"><strong>Total servicios:</strong> {{ $stats['total'] }}</p>
                        <p class="mb-1"><strong>Finalizados:</strong> {{ $stats['finalizados'] }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Cancelados:</strong> {{ $stats['cancelados'] }}</p>
                        <p class="mb-1"><strong>Otros:</strong> {{ $stats['total'] - $stats['finalizados'] - $stats['cancelados'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($servicios->isEmpty())
    <div class="alert alert-info">No hay servicios registrados para este cliente.</div>
@else
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Dirección</th>
                    <th>Vehículo</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($servicio->fecha_solicitud)->format('d/m/Y H:i') }}</td>
                    <td>{{ $servicio->direccion->direccion ?? 'N/A' }}</td>
                    <td>
                        @if($servicio->vehiculo)
                            {{ $servicio->vehiculo->placa }} ({{ $servicio->vehiculo->numero_movil }})
                        @else
                            No asignado
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $badge = match($servicio->estado) {
                                'pendiente' => 'bg-warning text-dark',
                                'asignado' => 'bg-info',
                                'en_camino' => 'bg-primary',
                                'finalizado' => 'bg-success',
                                'cancelado' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $servicio->estado)) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($direccionesFrecuentes->isNotEmpty())
    <div class="card mt-3">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">Direcciones más frecuentes</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($direccionesFrecuentes as $dir => $cantidad)
                <div class="col-md-4 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        <div>
                            <div class="small text-truncate" style="max-width: 200px;">{{ $dir }}</div>
                            <div class="text-muted small">{{ $cantidad }} servicios</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
@endif
