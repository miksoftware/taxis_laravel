<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialSancion extends Model
{
    public $timestamps = false;

    protected $table = 'historial_sanciones';

    protected $fillable = [
        'sancion_id',
        'accion',
        'usuario_id',
        'comentario',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }

    public function sancion()
    {
        return $this->belongsTo(Sancion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
