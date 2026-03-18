<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'username',
        'password',
        'telefono',
        'rol',
        'estado',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token_recuperacion',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'ultimo_acceso' => 'datetime',
            'fecha_token' => 'datetime',
            'es_protegido' => 'boolean',
        ];
    }

    // ── Scopes ──

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeByRol($query, string $rol)
    {
        return $query->where('rol', $rol);
    }

    // ── Helpers ──

    public function esSuperAdmin(): bool
    {
        return $this->rol === 'superadmin';
    }

    public function esAdmin(): bool
    {
        return in_array($this->rol, ['superadmin', 'administrador']);
    }

    public function esOperador(): bool
    {
        return $this->rol === 'operador';
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function nombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellidos;
    }

    public function puedeSerEliminado(): bool
    {
        return !$this->es_protegido;
    }
}
