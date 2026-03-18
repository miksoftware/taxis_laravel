<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloSancion extends Model
{
    public $timestamps = false;

    protected $table = 'articulos_sancion';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tiempo_sancion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'tiempo_sancion' => 'integer',
            'fecha_registro' => 'datetime',
        ];
    }

    // ── Relaciones ──

    public function sanciones()
    {
        return $this->hasMany(Sancion::class, 'articulo_id');
    }

    // ── Scopes ──

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // ── Helpers ──

    public function tienesSancionesActivas(): bool
    {
        return $this->sanciones()->where('estado', 'activa')->exists();
    }

    /**
     * Formatea el tiempo en minutos a texto legible
     */
    public function tiempoFormateado(): string
    {
        return self::formatearMinutos($this->tiempo_sancion);
    }

    public static function formatearMinutos(int $minutos): string
    {
        if ($minutos < 60) {
            return $minutos . ' minuto' . ($minutos !== 1 ? 's' : '');
        }

        $dias = intdiv($minutos, 1440);
        $horas = intdiv($minutos % 1440, 60);
        $min = $minutos % 60;

        $partes = [];
        if ($dias > 0) $partes[] = $dias . ' día' . ($dias !== 1 ? 's' : '');
        if ($horas > 0) $partes[] = $horas . ' hora' . ($horas !== 1 ? 's' : '');
        if ($min > 0) $partes[] = $min . ' minuto' . ($min !== 1 ? 's' : '');

        return implode(' ', $partes);
    }
}
