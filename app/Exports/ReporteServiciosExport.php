<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReporteServiciosExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected array $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = DB::table('servicios as s')
            ->leftJoin('clientes as c', 's.cliente_id', '=', 'c.id')
            ->leftJoin('direcciones as d', 's.direccion_id', '=', 'd.id')
            ->leftJoin('vehiculos as v', 's.vehiculo_id', '=', 'v.id')
            ->leftJoin('usuarios as u', 's.operador_id', '=', 'u.id')
            ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$this->filtros['fechaInicio'], $this->filtros['fechaFin']]);

        if (!empty($this->filtros['estado'])) {
            $query->where('s.estado', $this->filtros['estado']);
        }
        if (!empty($this->filtros['operadorId'])) {
            $query->where('s.operador_id', $this->filtros['operadorId']);
        }
        if (!empty($this->filtros['vehiculoId'])) {
            $query->where('s.vehiculo_id', $this->filtros['vehiculoId']);
        }

        return $query->select(
            's.id', 's.estado', 's.condicion', 's.tipo_vehiculo',
            's.fecha_solicitud', 's.fecha_asignacion', 's.fecha_fin',
            'c.telefono', 'c.nombre as cliente_nombre',
            'd.direccion',
            'v.placa', 'v.numero_movil',
            'u.nombre as operador_nombre'
        )
        ->orderByDesc('s.fecha_solicitud')
        ->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Estado', 'Condición', 'Tipo Vehículo',
            'Fecha Solicitud', 'Fecha Asignación', 'Fecha Fin',
            'Teléfono Cliente', 'Nombre Cliente',
            'Dirección', 'Placa', 'Móvil', 'Operador',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            ucfirst($row->estado),
            ucfirst($row->condicion ?? ''),
            ucfirst($row->tipo_vehiculo ?? ''),
            $row->fecha_solicitud,
            $row->fecha_asignacion,
            $row->fecha_fin,
            $row->telefono,
            $row->cliente_nombre,
            $row->direccion,
            $row->placa,
            $row->numero_movil,
            $row->operador_nombre,
        ];
    }

    public function title(): string
    {
        return 'Servicios';
    }
}
