<?php

use App\Http\Controllers\ArticuloSancionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupImportController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SancionController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas ──
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ── Rutas autenticadas ──
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // ── Usuarios (solo admins) ──
    Route::middleware('role:superadmin,administrador')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->only(['index', 'store', 'show', 'update']);
        Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.cambiar-estado');
        Route::patch('usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.reset-password');
    });

    // ── Clientes ──
    Route::get('clientes/buscar-telefono', [ClienteController::class, 'buscarPorTelefono'])->name('clientes.buscar-telefono');
    Route::get('clientes/autocompletar', [ClienteController::class, 'autocompletar'])->name('clientes.autocompletar');
    Route::post('clientes/crear-rapido', [ClienteController::class, 'crearRapido'])->name('clientes.crear-rapido');
    Route::resource('clientes', ClienteController::class)->only(['index', 'store', 'show', 'update']);
    Route::get('clientes/{cliente}/historial', [ClienteController::class, 'historial'])->name('clientes.historial');

    // ── Direcciones (API JSON) ──
    Route::prefix('direcciones')->name('direcciones.')->group(function () {
        Route::get('/', [DireccionController::class, 'porCliente'])->name('por-cliente');
        Route::get('/autocompletar', [DireccionController::class, 'autocompletar'])->name('autocompletar');
        Route::post('/', [DireccionController::class, 'store'])->name('store');
        Route::get('/{direccion}', [DireccionController::class, 'show'])->name('show');
        Route::put('/{direccion}', [DireccionController::class, 'update'])->name('update');
        Route::delete('/{direccion}', [DireccionController::class, 'destroy'])->name('destroy');
        Route::patch('/{direccion}/estado', [DireccionController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::patch('/{direccion}/frecuente', [DireccionController::class, 'marcarFrecuente'])->name('marcar-frecuente');
    });

    // ── Vehículos ──
    Route::resource('vehiculos', VehiculoController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::patch('vehiculos/{vehiculo}/estado', [VehiculoController::class, 'cambiarEstado'])->name('vehiculos.cambiar-estado');
    Route::get('vehiculos/{vehiculo}/detalle', [VehiculoController::class, 'detalle'])->name('vehiculos.detalle');
    Route::get('api/vehiculos-disponibles', [VehiculoController::class, 'disponibles'])->name('vehiculos.disponibles');

    // ── Artículos de Sanción (solo admins) ──
    Route::middleware('role:superadmin,administrador')->group(function () {
        Route::resource('articulos-sancion', ArticuloSancionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::patch('articulos-sancion/{articulo}/estado', [ArticuloSancionController::class, 'cambiarEstado'])->name('articulos-sancion.cambiar-estado');
    });
    Route::get('api/articulos-sancion-activos', [ArticuloSancionController::class, 'activos'])->name('articulos-sancion.activos');

    // ── Sanciones ──
    Route::get('sanciones', [SancionController::class, 'index'])->name('sanciones.index');
    Route::post('sanciones/aplicar', [SancionController::class, 'aplicar'])->name('sanciones.aplicar');
    Route::post('sanciones/{sancion}/anular', [SancionController::class, 'anular'])->name('sanciones.anular');
    Route::get('sanciones/{sancion}/detalle', [SancionController::class, 'detalle'])->name('sanciones.detalle');
    Route::get('api/sanciones-activas', [SancionController::class, 'sancionesActivas'])->name('sanciones.activas-json');
    Route::middleware('role:superadmin,administrador')->group(function () {
        Route::post('sanciones/verificar-vencimientos', [SancionController::class, 'verificarVencimientos'])->name('sanciones.verificar-vencimientos');
    });

    // ── Recepción / Servicios ──
    Route::get('recepcion', [ServicioController::class, 'index'])->name('servicios.index');
    Route::post('servicios', [ServicioController::class, 'store'])->name('servicios.store');
    Route::post('servicios/asignar', [ServicioController::class, 'asignar'])->name('servicios.asignar');
    Route::post('servicios/en-camino', [ServicioController::class, 'enCamino'])->name('servicios.en-camino');
    Route::post('servicios/finalizar', [ServicioController::class, 'finalizar'])->name('servicios.finalizar');
    Route::post('servicios/cancelar', [ServicioController::class, 'cancelar'])->name('servicios.cancelar');
    Route::post('servicios/cambiar-vehiculo', [ServicioController::class, 'cambiarVehiculo'])->name('servicios.cambiar-vehiculo');
    Route::post('servicios/actualizar-direccion', [ServicioController::class, 'actualizarDireccion'])->name('servicios.actualizar-direccion');
    Route::get('api/servicios-activos', [ServicioController::class, 'listarActivos'])->name('servicios.activos');
    Route::get('api/servicios-cambios', [ServicioController::class, 'cambiosRecientes'])->name('servicios.cambios');

    // ── Reportes (solo admins) ──
    Route::middleware('role:superadmin,administrador')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/servicios', [ReporteController::class, 'servicios'])->name('servicios');
        Route::get('/servicios/exportar', [ReporteController::class, 'exportarServicios'])->name('exportar-servicios');
        Route::get('/operadores', [ReporteController::class, 'operadores'])->name('operadores');
        Route::get('/operadores/exportar', [ReporteController::class, 'exportarOperadores'])->name('exportar-operadores');
        Route::get('/clientes', [ReporteController::class, 'clientes'])->name('clientes');
        Route::get('/clientes/exportar', [ReporteController::class, 'exportarClientes'])->name('exportar-clientes');
    });

    // ── Backup / Importar SQL (solo superadmin) ──
    Route::middleware('role:superadmin')->prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupImportController::class, 'index'])->name('index');
        Route::post('/importar', [BackupImportController::class, 'importar'])->name('importar');
    });
});
