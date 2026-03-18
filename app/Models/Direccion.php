<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    public $timestamps = false;

    protected $table = 'direcciones';

    protected $fillable = [
        'cliente_id',
        'direccion',
        'direccion_normalizada',
        'referencia',
        'es_frecuente',
        'activa',
        'ultimo_uso',
        'fecha_registro',
    ];

    protected function casts(): array
    {
        return [
            'es_frecuente' => 'boolean',
            'activa' => 'boolean',
            'ultimo_uso' => 'datetime',
            'fecha_registro' => 'datetime',
        ];
    }

    // ── Relaciones ──

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // ── Scopes ──

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeFrecuentes($query)
    {
        return $query->where('es_frecuente', true)->where('activa', true);
    }

    // ── Normalización de direcciones colombianas ──

    public static function normalizar(string $direccion): string
    {
        $dir = mb_strtolower(trim($direccion), 'UTF-8');

        $patrones = [
            'calle' => 'cl', 'cl.' => 'cl', 'cll' => 'cl',
            'carrera' => 'kr', 'cra' => 'kr', 'kr.' => 'kr', 'carr' => 'kr', 'crr' => 'kr',
            'avenida' => 'av', 'av.' => 'av',
            'diagonal' => 'dg', 'dg.' => 'dg',
            'transversal' => 'tv', 'tv.' => 'tv', 'trans' => 'tv', 'tra' => 'tv',
            'numero' => '#', 'No.' => '#', 'No' => '#', 'nro' => '#', 'n°' => '#',
            'apartamento' => 'apto', 'apt' => 'apto', 'ap' => 'apto',
            'interior' => 'int', 'int.' => 'int',
            'bloque' => 'bl', 'bl.' => 'bl',
            'torre' => 'tr', 'tr.' => 'tr',
            'edificio' => 'ed', 'ed.' => 'ed',
            'urbanización' => 'urb', 'urb.' => 'urb',
            'conjunto' => 'cj', 'cj.' => 'cj', 'conj' => 'cj',
            'manzana' => 'mz', 'mz.' => 'mz',
        ];

        foreach ($patrones as $patron => $reemplazo) {
            $dir = str_replace($patron, $reemplazo, $dir);
        }

        $dir = preg_replace('/[^\w\s\#\-\/]/u', '', $dir);
        $dir = preg_replace('/\s+/', ' ', $dir);

        return trim($dir);
    }
}
