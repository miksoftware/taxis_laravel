@extends('layouts.app')
@section('title', 'Importar Copia de Seguridad')
@section('page-title', 'Importar Copia de Seguridad')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        {{-- Advertencia --}}
        <div class="alert alert-danger d-flex align-items-start mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 mt-1"></i>
            <div>
                <strong>Atención:</strong> Esta operación reemplazará los datos actuales con los del archivo SQL.
                Solo se preservará la cuenta de SuperAdmin protegida. Asegúrese de que el archivo SQL
                proviene de una copia de seguridad válida del sistema original (taxisdiamantes).
            </div>
        </div>

        {{-- Formulario --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-upload me-2"></i>Subir archivo SQL</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('backup.importar') }}" enctype="multipart/form-data" id="formImport">
                    @csrf
                    <div class="mb-3">
                        <label for="sql_file" class="form-label">Archivo de copia de seguridad (.sql)</label>
                        <input type="file" name="sql_file" id="sql_file" class="form-control @error('sql_file') is-invalid @enderror" accept=".sql" required>
                        @error('sql_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Tamaño máximo: 100MB. Solo archivos .sql</div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="confirmar" required>
                        <label class="form-check-label" for="confirmar">
                            Confirmo que deseo importar este archivo y entiendo que los datos actuales serán reemplazados.
                        </label>
                    </div>

                    <button type="submit" class="btn" style="background: #1a1a2e; color: #18dff5;" id="btnImportar">
                        <i class="bi bi-database-up me-1"></i> Importar Datos
                    </button>
                </form>
            </div>
        </div>

        {{-- Resultados --}}
        @if(session('resultados'))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Resultado de la Importación</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tabla</th>
                                <th class="text-center">Registros</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('resultados') as $tabla => $info)
                            <tr>
                                <td><code>{{ $tabla }}</code></td>
                                <td class="text-center">{{ $info['importados'] }}</td>
                                <td class="text-center">
                                    @if($info['estado'] === 'ok')
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> OK</span>
                                    @else
                                        <span class="badge bg-secondary">Sin datos</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Errores parciales --}}
        @if(session('errores') && count(session('errores')) > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-danger">
                <h6 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Advertencias durante la importación</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0 small">
                    @foreach(session('errores') as $err)
                        <li class="text-danger">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Tablas soportadas --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Tablas soportadas</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach(['usuarios', 'clientes', 'direcciones', 'vehiculos', 'articulos_sancion', 'sanciones', 'historial_sanciones', 'servicios', 'historial_servicios', 'patrones_direccion', 'configuracion'] as $t)
                    <div class="col-md-4 mb-1">
                        <span class="badge bg-light text-dark border"><i class="bi bi-table me-1"></i>{{ $t }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('formImport').addEventListener('submit', function(e) {
    const btn = document.getElementById('btnImportar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Importando... esto puede tardar';
});
</script>
@endpush
