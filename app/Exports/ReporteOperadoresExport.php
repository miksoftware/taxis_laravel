<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReporteOperadoresExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected array $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return DB::table('usuarios as u')
            ->leftJoin('servicios as s', function ($j) {
                $j->on('u.id', '=', 's.operador_id')
                  ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$this->filtros['fechaInicio'], $this->filtros['fechaFin']]);
            })
            ->whereIn('u.rol', ['operador', 'administrador'])
            ->groupBy('u.id', 'u.nombre', 'u.apellidos', 'u.username', 'u.rol', 'u.estado')
            ->selectRaw("
                u.id, CONCAT(u.nombre, ' ', u.apellidos) as nombre, u.username, u.rol, u.estado,
                COUNT(s.id) as total_servicios,
                SUM(CASE WHEN s.estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
                SUM(CASE WHEN s.estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                CASE WHEN COUNT(s.id) > 0
                    THEN ROUND(SUM(CASE WHEN s.estado = 'finalizado' THEN 1 ELSE 0 END) * 100.0 / COUNT(s.id), 1)
                    ELSE 0 END as efectividad,
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, s.fecha_solicitud, s.fecha_asignacion)), 1) as tiempo_promedio
            ")
            ->orderByDesc('total_servicios')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Nombre', 'Usuario', 'Rol', 'Estado',
            'Total Servicios', 'Finalizados', 'Cancelados',
            'Efectividad %', 'Tiempo Prom. (min)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nombre,
            $row->username,
            ucfirst($row->rol),
            ucfirst($row->estado),
            $row->total_servicios,
            $row->finalizados,
            $row->cancelados,
            $row->efectividad . '%',
            $row->tiempo_promedio ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Operadores';
    }
}
