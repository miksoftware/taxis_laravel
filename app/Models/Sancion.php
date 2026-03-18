<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sancion extends Model
{
    public $timestamps = false;

    protected $table = 'sanciones';

    protected $fillable = [
        'vehiculo_id',
        'articulo_id',
        'usuario_id',
        'motivo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'fecha_registro' => 'datetime',
        ];
    }

    // ── Relaciones ──

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function articulo()
    {
        return $this->belongsTo(ArticuloSancion::class, 'articulo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialSancion::class, 'sancion_id')->orderByDesc('fecha');
    }

    // ── Scopes ──

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'activa')->where('fecha_fin', '<', now());
    }

    // ── Helpers ──

    public function estaVencida(): bool
    {
        return $this->estado === 'activa' && $this->fecha_fin->isPast();
    }

    public function segundosRestantes(): int
    {
        if ($this->estado !== 'activa' || $this->fecha_fin->isPast()) {
            return 0;
        }
        return (int) now()->diffInSeconds($this->fecha_fin, false);
    }

    public function tiempoRestanteFormateado(): string
    {
        if ($this->estado !== 'activa') return 'N/A';
        if ($this->fecha_fin->isPast()) return 'Vencida';

        $diff = now()->diff($this->fecha_fin);
        $partes = [];
        if ($diff->d > 0) $partes[] = $diff->d . 'd';
        $partes[] = sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);

        return implode(' ', $partes);
    }
}
