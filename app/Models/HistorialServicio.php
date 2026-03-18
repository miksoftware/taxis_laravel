<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialServicio extends Model
{
    public $timestamps = false;

    protected $table = 'historial_servicios';

    protected $fillable = [
        'servicio_id',
        'estado_anterior',
        'estado_nuevo',
        'fecha_cambio',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cambio' => 'datetime',
        ];
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
