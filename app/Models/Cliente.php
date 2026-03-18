<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'telefono',
        'nombre',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
            'ultima_actualizacion' => 'datetime',
        ];
    }

    // ── Relaciones ──

    public function direcciones()
    {
        return $this->hasMany(Direccion::class);
    }

    public function direccionesActivas()
    {
        return $this->hasMany(Direccion::class)->where('activa', true)->orderByDesc('es_frecuente')->orderByDesc('ultimo_uso');
    }

    public function servicios()
    {
        return $this->hasMany(\App\Models\Servicio::class);
    }

    // ── Scopes ──

    public function scopeBuscar($query, string $termino)
    {
        return $query->where('telefono', 'like', "%{$termino}%")
                     ->orWhere('nombre', 'like', "%{$termino}%");
    }
}
