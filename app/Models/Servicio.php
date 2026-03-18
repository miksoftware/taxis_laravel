<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Servicio extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'direccion_id',
        'vehiculo_id',
        'tipo_vehiculo',
        'condicion',
        'observaciones',
        'estado',
        'fecha_solicitud',
        'fecha_asignacion',
        'fecha_fin',
        'operador_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_solicitud'    => 'datetime',
            'fecha_asignacion'   => 'datetime',
            'fecha_fin'          => 'datetime',
            'fecha_actualizacion' => 'datetime',
        ];
    }

    // ── Relaciones ──

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function operador()
    {
        return $this->belongsTo(Usuario::class, 'operador_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialServicio::class)->orderByDesc('fecha_cambio');
    }

    // ── Scopes ──

    public function scopeActivos($query)
    {
        return $query->whereIn('servicios.estado', ['pendiente', 'asignado', 'en_camino']);
    }

    public function scopePendientes($query)
    {
        return $query->where('servicios.estado', 'pendiente');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('servicios.fecha_solicitud', today());
    }

    public function scopeModificadosDespuesDe($query, string $timestamp)
    {
        return $query->where('servicios.fecha_actualizacion', '>', $timestamp);
    }

    // ── Helpers ──

    public function tiempoTranscurrido(): string
    {
        $inicio = $this->fecha_solicitud;
        $ahora = now();
        $diff = $inicio->diff($ahora);

        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }
        return $diff->i . ' min';
    }

    public function etiquetaCondicion(): string
    {
        $etiquetas = [
            'aire'          => '❄️ Aire',
            'baul'          => '🧳 Baúl',
            'mascota'       => '🐾 Mascota',
            'parrilla'      => '📦 Parrilla',
            'transferencia' => '🏦 Transferencia',
            'daviplata'     => '💳 Daviplata',
            'polarizados'   => '🕶️ Polarizados',
            'silla_ruedas'  => '♿ Silla de ruedas',
            'ninguno'       => '',
        ];

        return $etiquetas[$this->condicion] ?? $this->condicion;
    }

    public function colorEstado(): string
    {
        return match ($this->estado) {
            'pendiente' => 'warning',
            'asignado'  => 'info',
            'en_camino' => 'primary',
            'finalizado' => 'success',
            'cancelado' => 'danger',
            default     => 'secondary',
        };
    }

    // ── Estadísticas ──

    public static function metricasHoy(): array
    {
        $stats = static::hoy()
            ->selectRaw("
                COUNT(*) as total,
                SUM(estado = 'pendiente') as pendientes,
                SUM(estado = 'asignado') as asignados,
                SUM(estado = 'en_camino') as en_camino,
                SUM(estado = 'finalizado') as finalizados,
                SUM(estado = 'cancelado') as cancelados
            ")
            ->first();

        return [
            'total'       => (int) $stats->total,
            'pendientes'  => (int) $stats->pendientes,
            'asignados'   => (int) $stats->asignados,
            'en_camino'   => (int) $stats->en_camino,
            'finalizados' => (int) $stats->finalizados,
            'cancelados'  => (int) $stats->cancelados,
        ];
    }

    // ── Query optimizado para listado de recepción ──

    public static function listarActivos(int $limite = 100)
    {
        return static::activos()
            ->select('servicios.id', 'servicios.cliente_id', 'servicios.direccion_id',
                'servicios.vehiculo_id', 'servicios.tipo_vehiculo', 'servicios.condicion',
                'servicios.observaciones', 'servicios.estado', 'servicios.fecha_solicitud',
                'servicios.fecha_asignacion', 'servicios.fecha_actualizacion', 'servicios.operador_id')
            ->join('clientes as c', 'servicios.cliente_id', '=', 'c.id')
            ->join('direcciones as d', 'servicios.direccion_id', '=', 'd.id')
            ->leftJoin('vehiculos as v', 'servicios.vehiculo_id', '=', 'v.id')
            ->leftJoin('usuarios as u', 'servicios.operador_id', '=', 'u.id')
            ->addSelect('c.telefono', 'c.nombre as cliente_nombre')
            ->addSelect('d.direccion', 'd.referencia')
            ->addSelect('v.placa', 'v.numero_movil')
            ->addSelect('u.nombre as operador_nombre')
            ->orderByDesc('servicios.fecha_solicitud')
            ->limit($limite)
            ->get();
    }

    public static function cambiosDespuesDe(string $timestamp, int $limite = 50)
    {
        return static::where('servicios.fecha_actualizacion', '>', $timestamp)
            ->select('servicios.id', 'servicios.cliente_id', 'servicios.direccion_id',
                'servicios.vehiculo_id', 'servicios.tipo_vehiculo', 'servicios.condicion',
                'servicios.observaciones', 'servicios.estado', 'servicios.fecha_solicitud',
                'servicios.fecha_asignacion', 'servicios.fecha_fin', 'servicios.fecha_actualizacion',
                'servicios.operador_id')
            ->join('clientes as c', 'servicios.cliente_id', '=', 'c.id')
            ->join('direcciones as d', 'servicios.direccion_id', '=', 'd.id')
            ->leftJoin('vehiculos as v', 'servicios.vehiculo_id', '=', 'v.id')
            ->leftJoin('usuarios as u', 'servicios.operador_id', '=', 'u.id')
            ->addSelect('c.telefono', 'c.nombre as cliente_nombre')
            ->addSelect('d.direccion', 'd.referencia')
            ->addSelect('v.placa', 'v.numero_movil')
            ->addSelect('u.nombre as operador_nombre')
            ->orderByDesc('servicios.fecha_actualizacion')
            ->limit($limite)
            ->get();
    }
}
