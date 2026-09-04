@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-headset" style="font-size: 3rem; color: #0284c7;"></i>
                </div>
                <h5 class="card-title fw-bold" style="color: #0a2540;">Reporte de Servicios</h5>
                <p class="text-muted small">Estadísticas generales, tendencia, top vehículos y operadores, listado detallado con filtros.</p>
                <a href="{{ route('reportes.servicios') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-bar-chart me-1"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-people" style="font-size: 3rem; color: #0284c7;"></i>
                </div>
                <h5 class="card-title fw-bold" style="color: #0a2540;">Reporte de Operadores</h5>
                <p class="text-muted small">Rendimiento individual, efectividad, tiempos promedio de asignación por operador.</p>
                <a href="{{ route('reportes.operadores') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-badge me-1"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-person-lines-fill" style="font-size: 3rem; color: #0284c7;"></i>
                </div>
                <h5 class="card-title fw-bold" style="color: #0a2540;">Reporte de Clientes</h5>
                <p class="text-muted small">Top clientes por uso del servicio, direcciones registradas, servicios finalizados y cancelados.</p>
                <a href="{{ route('reportes.clientes') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-graph-up me-1"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
