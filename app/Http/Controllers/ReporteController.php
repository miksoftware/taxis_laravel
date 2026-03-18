<?php

namespace App\Http\Controllers;

use App\Exports\ReporteServiciosExport;
use App\Exports\ReporteOperadoresExport;
use App\Exports\ReporteClientesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    // ══════════════════════════════════════════
    // REPORTE DE SERVICIOS
    // ══════════════════════════════════════════

    public function servicios(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $estado = $request->input('estado', '');
        $operadorId = $request->input('operador_id', '');
        $vehiculoId = $request->input('vehiculo_id', '');

        $filtros = compact('fechaInicio', 'fechaFin', 'estado', 'operadorId', 'vehiculoId');

        $estadisticas = $this->statsServicios($filtros);
        $topVehiculos = $this->topVehiculos($filtros);
        $topOperadores = $this->topOperadoresServicios($filtros);
        $tendencia = $this->tendenciaServicios($filtros);
        $servicios = $this->listarServicios($filtros);
        $operadores = DB::table('usuarios')->whereIn('rol', ['operador', 'administrador'])->where('estado', 'activo')->get(['id', 'nombre', 'apellidos']);
        $vehiculos = DB::table('vehiculos')->where('estado', '!=', 'inactivo')->orderBy('numero_movil')->get(['id', 'placa', 'numero_movil']);

        return view('reportes.servicios', compact(
            'filtros', 'estadisticas', 'topVehiculos', 'topOperadores', 'tendencia', 'servicios', 'operadores', 'vehiculos'
        ));
    }

    public function exportarServicios(Request $request)
    {
        $filtros = [
            'fechaInicio' => $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d')),
            'fechaFin'    => $request->input('fecha_fin', now()->format('Y-m-d')),
            'estado'      => $request->input('estado', ''),
            'operadorId'  => $request->input('operador_id', ''),
            'vehiculoId'  => $request->input('vehiculo_id', ''),
        ];

        $nombre = 'reporte_servicios_' . $filtros['fechaInicio'] . '_' . $filtros['fechaFin'] . '.xlsx';
        return Excel::download(new ReporteServiciosExport($filtros), $nombre);
    }

    // ══════════════════════════════════════════
    // REPORTE DE OPERADORES
    // ══════════════════════════════════════════

    public function operadores(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $filtros = compact('fechaInicio', 'fechaFin');

        $operadores = $this->statsOperadores($filtros);
        $totales = $this->totalesOperadores($operadores);

        return view('reportes.operadores', compact('filtros', 'operadores', 'totales'));
    }

    public function exportarOperadores(Request $request)
    {
        $filtros = [
            'fechaInicio' => $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d')),
            'fechaFin'    => $request->input('fecha_fin', now()->format('Y-m-d')),
        ];

        $nombre = 'reporte_operadores_' . $filtros['fechaInicio'] . '_' . $filtros['fechaFin'] . '.xlsx';
        return Excel::download(new ReporteOperadoresExport($filtros), $nombre);
    }

    // ══════════════════════════════════════════
    // REPORTE DE CLIENTES
    // ══════════════════════════════════════════

    public function clientes(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $buscarCliente = $request->input('buscar_cliente', '');

        $filtros = compact('fechaInicio', 'fechaFin', 'buscarCliente');

        $clientes = $this->statsClientes($filtros);
        $totales = $this->totalesClientes($clientes);

        return view('reportes.clientes', compact('filtros', 'clientes', 'totales'));
    }

    public function exportarClientes(Request $request)
    {
        $filtros = [
            'fechaInicio' => $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d')),
            'fechaFin'    => $request->input('fecha_fin', now()->format('Y-m-d')),
        ];

        $nombre = 'reporte_clientes_' . $filtros['fechaInicio'] . '_' . $filtros['fechaFin'] . '.xlsx';
        return Excel::download(new ReporteClientesExport($filtros), $nombre);
    }

    // ══════════════════════════════════════════
    // QUERIES PRIVADAS
    // ══════════════════════════════════════════

    private function baseServiciosQuery(array $filtros)
    {
        $query = DB::table('servicios as s')
            ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$filtros['fechaInicio'], $filtros['fechaFin']]);

        if (!empty($filtros['estado'])) {
            $query->where('s.estado', $filtros['estado']);
        }
        if (!empty($filtros['operadorId'])) {
            $query->where('s.operador_id', $filtros['operadorId']);
        }
        if (!empty($filtros['vehiculoId'])) {
            $query->where('s.vehiculo_id', $filtros['vehiculoId']);
        }

        return $query;
    }

    private function statsServicios(array $filtros): array
    {
        $row = $this->baseServiciosQuery($filtros)
            ->selectRaw("
                COUNT(*) as total,
                SUM(s.estado = 'finalizado') as finalizados,
                SUM(s.estado = 'cancelado') as cancelados,
                SUM(s.estado = 'pendiente') as pendientes,
                SUM(s.estado = 'asignado') as asignados,
                SUM(s.estado = 'en_camino') as en_camino,
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, s.fecha_solicitud, s.fecha_asignacion)), 1) as tiempo_asignacion,
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, s.fecha_asignacion, s.fecha_fin)), 1) as tiempo_servicio
            ")
            ->first();

        $total = (int) $row->total;
        $fin = (int) $row->finalizados;

        return [
            'total'             => $total,
            'finalizados'       => $fin,
            'cancelados'        => (int) $row->cancelados,
            'pendientes'        => (int) $row->pendientes,
            'asignados'         => (int) $row->asignados,
            'en_camino'         => (int) $row->en_camino,
            'efectividad'       => $total > 0 ? round(($fin / $total) * 100, 1) : 0,
            'tiempo_asignacion' => $row->tiempo_asignacion ?? 0,
            'tiempo_servicio'   => $row->tiempo_servicio ?? 0,
        ];
    }

    private function topVehiculos(array $filtros, int $limite = 10): array
    {
        return $this->baseServiciosQuery($filtros)
            ->join('vehiculos as v', 's.vehiculo_id', '=', 'v.id')
            ->groupBy('v.id', 'v.placa', 'v.numero_movil')
            ->selectRaw("
                v.id, v.placa, v.numero_movil,
                COUNT(s.id) as total_servicios,
                SUM(s.estado = 'finalizado') as finalizados,
                SUM(s.estado = 'cancelado') as cancelados
            ")
            ->orderByDesc('total_servicios')
            ->limit($limite)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();
    }

    private function topOperadoresServicios(array $filtros, int $limite = 10): array
    {
        return $this->baseServiciosQuery($filtros)
            ->join('usuarios as u', 's.operador_id', '=', 'u.id')
            ->groupBy('u.id', 'u.nombre', 'u.apellidos')
            ->selectRaw("
                u.id, CONCAT(u.nombre, ' ', u.apellidos) as nombre,
                COUNT(s.id) as total_servicios,
                SUM(s.estado = 'finalizado') as finalizados,
                SUM(s.estado = 'cancelado') as cancelados,
                CASE WHEN COUNT(s.id) > 0
                    THEN ROUND(SUM(s.estado = 'finalizado') * 100.0 / COUNT(s.id), 1)
                    ELSE 0 END as efectividad
            ")
            ->orderByDesc('total_servicios')
            ->limit($limite)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();
    }

    private function tendenciaServicios(array $filtros): array
    {
        $datos = $this->baseServiciosQuery($filtros)
            ->selectRaw("
                DATE(s.fecha_solicitud) as fecha,
                COUNT(*) as total,
                SUM(s.estado = 'finalizado') as finalizados,
                SUM(s.estado = 'cancelado') as cancelados
            ")
            ->groupByRaw('DATE(s.fecha_solicitud)')
            ->orderBy('fecha')
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        return [
            'labels'      => array_column($datos, 'fecha'),
            'total'       => array_map('intval', array_column($datos, 'total')),
            'finalizados' => array_map('intval', array_column($datos, 'finalizados')),
            'cancelados'  => array_map('intval', array_column($datos, 'cancelados')),
        ];
    }

    private function listarServicios(array $filtros)
    {
        return $this->baseServiciosQuery($filtros)
            ->leftJoin('clientes as c', 's.cliente_id', '=', 'c.id')
            ->leftJoin('direcciones as d', 's.direccion_id', '=', 'd.id')
            ->leftJoin('vehiculos as v', 's.vehiculo_id', '=', 'v.id')
            ->leftJoin('usuarios as u', 's.operador_id', '=', 'u.id')
            ->select(
                's.id', 's.estado', 's.condicion', 's.fecha_solicitud', 's.fecha_asignacion', 's.fecha_fin',
                'c.telefono', 'c.nombre as cliente_nombre',
                'd.direccion',
                'v.placa', 'v.numero_movil',
                'u.nombre as operador_nombre'
            )
            ->orderByDesc('s.fecha_solicitud')
            ->paginate(25)
            ->appends(request()->query());
    }

    private function statsOperadores(array $filtros): array
    {
        return DB::table('usuarios as u')
            ->leftJoin('servicios as s', function ($j) use ($filtros) {
                $j->on('u.id', '=', 's.operador_id')
                  ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$filtros['fechaInicio'], $filtros['fechaFin']]);
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
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();
    }

    private function totalesOperadores(array $operadores): array
    {
        $total = array_sum(array_column($operadores, 'total_servicios'));
        $fin = array_sum(array_column($operadores, 'finalizados'));
        $can = array_sum(array_column($operadores, 'cancelados'));

        return [
            'total_servicios' => $total,
            'finalizados'     => $fin,
            'cancelados'      => $can,
            'efectividad'     => $total > 0 ? round(($fin / $total) * 100, 1) : 0,
        ];
    }

    private function statsClientes(array $filtros): array
    {
        $query = DB::table('clientes as c')
            ->leftJoin('servicios as s', function ($j) use ($filtros) {
                $j->on('c.id', '=', 's.cliente_id')
                  ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$filtros['fechaInicio'], $filtros['fechaFin']]);
            })
            ->leftJoin('direcciones as d', 'c.id', '=', 'd.cliente_id')
            ->groupBy('c.id', 'c.telefono', 'c.nombre')
            ->selectRaw("
                c.id, c.telefono, c.nombre,
                COUNT(DISTINCT s.id) as total_servicios,
                SUM(CASE WHEN s.estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
                SUM(CASE WHEN s.estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                COUNT(DISTINCT d.id) as total_direcciones
            ");

        if (!empty($filtros['buscarCliente'])) {
            $term = $filtros['buscarCliente'];
            $query->where(function ($q) use ($term) {
                $q->where('c.telefono', 'like', "%{$term}%")
                  ->orWhere('c.nombre', 'like', "%{$term}%");
            });
        }

        return $query
            ->having('total_servicios', '>', 0)
            ->orderByDesc('total_servicios')
            ->limit(50)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();
    }

    private function totalesClientes(array $clientes): array
    {
        $total = array_sum(array_column($clientes, 'total_servicios'));
        $fin = array_sum(array_column($clientes, 'finalizados'));

        return [
            'total_servicios'  => $total,
            'finalizados'      => $fin,
            'cancelados'       => array_sum(array_column($clientes, 'cancelados')),
            'clientes_activos' => count($clientes),
        ];
    }

    // ══════════════════════════════════════════
    // DETALLE DE CLIENTE (JSON para modales)
    // ══════════════════════════════════════════

    public function clienteServicios(Request $request, int $cliente)
    {
        $fechaInicio = $request->input('fecha_inicio', '');
        $fechaFin = $request->input('fecha_fin', '');
        $estado = $request->input('estado', '');

        $query = DB::table('servicios as s')
            ->where('s.cliente_id', $cliente)
            ->leftJoin('direcciones as d', 's.direccion_id', '=', 'd.id')
            ->leftJoin('vehiculos as v', 's.vehiculo_id', '=', 'v.id')
            ->leftJoin('usuarios as u', 's.operador_id', '=', 'u.id')
            ->select(
                's.id', 's.estado', 's.condicion', 's.observaciones',
                's.fecha_solicitud', 's.fecha_asignacion', 's.fecha_fin',
                'd.direccion', 'd.referencia',
                'v.placa', 'v.numero_movil',
                'u.nombre as operador_nombre'
            );

        if ($fechaInicio) $query->whereDate('s.fecha_solicitud', '>=', $fechaInicio);
        if ($fechaFin) $query->whereDate('s.fecha_solicitud', '<=', $fechaFin);
        if ($estado) $query->where('s.estado', $estado);

        $servicios = $query->orderByDesc('s.fecha_solicitud')->limit(200)->get();

        return response()->json(['error' => false, 'servicios' => $servicios]);
    }

    public function clienteDirecciones(int $cliente)
    {
        $direcciones = DB::table('direcciones as d')
            ->where('d.cliente_id', $cliente)
            ->leftJoin(DB::raw('(SELECT direccion_id, COUNT(*) as total_servicios FROM servicios GROUP BY direccion_id) as sc'), 'sc.direccion_id', '=', 'd.id')
            ->select('d.id', 'd.direccion', 'd.referencia', 'd.es_frecuente', 'd.activa', 'd.fecha_registro', 'd.ultimo_uso', DB::raw('COALESCE(sc.total_servicios, 0) as total_servicios'))
            ->orderByDesc('d.ultimo_uso')
            ->get();

        return response()->json(['error' => false, 'direcciones' => $direcciones]);
    }
}
