<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'placa',
        'numero_movil',
        'modelo',
        'marca',
        'conductor_id',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
            'ultima_actualizacion' => 'datetime',
        ];
    }

    // ── Relaciones ──

    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'conductor_id');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function sanciones()
    {
        return $this->hasMany(Sancion::class);
    }

    public function sancionActiva()
    {
        return $this->hasOne(Sancion::class)->where('estado', 'activa');
    }

    // ── Scopes ──

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', '!=', 'inactivo');
    }

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('placa', 'like', "%{$termino}%")
              ->orWhere('numero_movil', 'like', "%{$termino}%");
        });
    }

    // ── Helpers ──

    public function estaDisponible(): bool
    {
        return $this->estado === 'disponible';
    }

    public function estaSancionado(): bool
    {
        return $this->estado === 'sancionado';
    }

    public function tieneSancionActiva(): bool
    {
        return $this->sanciones()->where('estado', 'activa')->exists();
    }

    public static function estadisticas(): array
    {
        $porEstado = static::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $base = [
            'disponible' => 0, 'ocupado' => 0, 'sancionado' => 0,
            'mantenimiento' => 0, 'inactivo' => 0,
        ];

        $stats = array_merge($base, $porEstado);
        $stats['total'] = array_sum($stats);

        return $stats;
    }
}
