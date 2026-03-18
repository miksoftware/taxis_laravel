<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReporteClientesExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected array $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return DB::table('clientes as c')
            ->leftJoin('servicios as s', function ($j) {
                $j->on('c.id', '=', 's.cliente_id')
                  ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$this->filtros['fechaInicio'], $this->filtros['fechaFin']]);
            })
            ->leftJoin('direcciones as d', 'c.id', '=', 'd.cliente_id')
            ->groupBy('c.id', 'c.telefono', 'c.nombre')
            ->selectRaw("
                c.id, c.telefono, c.nombre,
                COUNT(DISTINCT s.id) as total_servicios,
                SUM(CASE WHEN s.estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
                SUM(CASE WHEN s.estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                COUNT(DISTINCT d.id) as total_direcciones
            ")
            ->having('total_servicios', '>', 0)
            ->orderByDesc('total_servicios')
            ->limit(50)
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Teléfono', 'Nombre',
            'Total Servicios', 'Finalizados', 'Cancelados', 'Direcciones',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->telefono,
            $row->nombre,
            $row->total_servicios,
            $row->finalizados,
            $row->cancelados,
            $row->total_direcciones,
        ];
    }

    public function title(): string
    {
        return 'Clientes';
    }
}
