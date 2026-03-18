<div class="row mb-3">
    <div class="col-md-6">
        <h6 class="fw-bold">Vehículo</h6>
        <p>
            <span class="badge bg-secondary">Placa</span> {{ $sancion->vehiculo->placa }}<br>
            <span class="badge bg-secondary">Móvil</span> {{ $sancion->vehiculo->numero_movil }}
        </p>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold">Estado</h6>
        @php
            $bs = match($sancion->estado) {
                'activa' => 'bg-danger',
                'cumplida' => 'bg-success',
                'anulada' => 'bg-warning text-dark',
                default => 'bg-secondary',
            };
        @endphp
        <span class="badge {{ $bs }}">{{ ucfirst($sancion->estado) }}</span>
        @if($sancion->estado === 'activa')
            <span class="ms-2 countdown text-danger fw-bold" data-fin="{{ $sancion->fecha_fin->toIso8601String() }}">
                {{ $sancion->tiempoRestanteFormateado() }}
            </span>
        @endif
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <h6 class="fw-bold">Artículo</h6>
        <span class="badge bg-warning text-dark">{{ $sancion->articulo->codigo }}</span>
        {{ $sancion->articulo->descripcion }}<br>
        <small class="text-muted">Duración: {{ \App\Models\ArticuloSancion::formatearMinutos($sancion->articulo->tiempo_sancion) }}</small>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold">Fechas</h6>
        <span class="badge bg-secondary">Inicio</span> {{ $sancion->fecha_inicio->format('d/m/Y H:i:s') }}<br>
        <span class="badge bg-secondary">Fin</span> {{ $sancion->fecha_fin->format('d/m/Y H:i:s') }}
    </div>
</div>

<div class="mb-3">
    <h6 class="fw-bold">Motivo</h6>
    <p>{{ $sancion->motivo }}</p>
</div>

<div class="mb-3">
    <h6 class="fw-bold">Aplicada por</h6>
    <p>{{ $sancion->usuario->nombre }} {{ $sancion->usuario->apellidos }}</p>
</div>

@if($sancion->historial->isNotEmpty())
<h6 class="fw-bold">Historial</h6>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr><th>Fecha</th><th>Acción</th><th>Usuario</th><th>Comentario</th></tr>
        </thead>
        <tbody>
            @foreach($sancion->historial as $h)
            <tr>
                <td>{{ $h->fecha->format('d/m/Y H:i:s') }}</td>
                <td>
                    @php
                        $hb = match($h->accion) {
                            'aplicada' => 'bg-primary',
                            'anulada' => 'bg-warning text-dark',
                            'cumplida' => 'bg-success',
                            default => 'bg-info',
                        };
                    @endphp
                    <span class="badge {{ $hb }}">{{ ucfirst($h->accion) }}</span>
                </td>
                <td>{{ $h->usuario->nombre }} {{ $h->usuario->apellidos }}</td>
                <td>{{ $h->comentario }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
