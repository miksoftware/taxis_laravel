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
        $perPage = $request->input('per_page', 25);

        $filtros = compact('fechaInicio', 'fechaFin', 'estado', 'operadorId', 'vehiculoId', 'perPage');

        $estadisticas = $this->statsServicios($filtros);
        $topVehiculos = $this->topVehiculos($filtros);
        $topOperadores = $this->topOperadoresServicios($filtros);
        $tendencia = $this->tendenciaServicios($filtros);
        $distribucionEstados = $this->distribucionEstados($filtros);
        $distribucionHoras = $this->distribucionHoras($filtros);
        $distribucionCondiciones = $this->distribucionCondiciones($filtros);
        $servicios = $this->listarServicios($filtros);
        $operadores = DB::table('usuarios')->whereIn('rol', ['operador', 'administrador'])->where('estado', 'activo')->get(['id', 'nombre', 'apellidos']);
        $vehiculos = DB::table('vehiculos')->where('estado', '!=', 'inactivo')->orderBy('numero_movil')->get(['id', 'placa', 'numero_movil']);

        return view('reportes.servicios', compact(
            'filtros', 'estadisticas', 'topVehiculos', 'topOperadores', 'tendencia',
            'distribucionEstados', 'distribucionHoras', 'distribucionCondiciones',
            'servicios', 'operadores', 'vehiculos', 'perPage'
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
        $rol = $request->input('rol', '');

        $filtros = compact('fechaInicio', 'fechaFin', 'rol');

        $operadores = $this->statsOperadores($filtros);
        $totales = $this->totalesOperadores($operadores);
        $graficos = $this->graficosOperadores($operadores);

        return view('reportes.operadores', compact('filtros', 'operadores', 'totales', 'graficos'));
    }

    public function exportarOperadores(Request $request)
    {
        $filtros = [
            'fechaInicio' => $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d')),
            'fechaFin'    => $request->input('fecha_fin', now()->format('Y-m-d')),
            'rol'         => $request->input('rol', ''),
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
        $graficos = $this->graficosClientes($clientes);

        return view('reportes.clientes', compact('filtros', 'clientes', 'totales', 'graficos'));
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
        $canc = (int) $row->cancelados;

        return [
            'total'             => $total,
            'finalizados'       => $fin,
            'cancelados'        => $canc,
            'pendientes'        => (int) $row->pendientes,
            'asignados'         => (int) $row->asignados,
            'en_camino'         => (int) $row->en_camino,
            'efectividad'       => $total > 0 ? round(($fin / $total) * 100, 1) : 0,
            'tasa_cancelacion'  => $total > 0 ? round(($canc / $total) * 100, 1) : 0,
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

    private function distribucionEstados(array $filtros): array
    {
        $rows = $this->baseServiciosQuery($filtros)
            ->selectRaw("s.estado, COUNT(*) as total")
            ->groupBy('s.estado')
            ->pluck('total', 'estado')
            ->toArray();

        $estadosDef = [
            'finalizado' => ['label' => 'Finalizados', 'color' => '#10b981'],
            'cancelado'  => ['label' => 'Cancelados',  'color' => '#ef4444'],
            'asignado'   => ['label' => 'Asignados',   'color' => '#0284c7'],
            'en_camino'  => ['label' => 'En Camino',   'color' => '#06b6d4'],
            'pendiente'  => ['label' => 'Pendientes',  'color' => '#f59e0b'],
        ];

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($estadosDef as $key => $conf) {
            $cnt = (int) ($rows[$key] ?? 0);
            if ($cnt > 0) {
                $labels[] = $conf['label'];
                $data[] = $cnt;
                $colors[] = $conf['color'];
            }
        }

        // Si no hay datos, devolver estado vacío para que Chart.js no falle
        if (empty($labels)) {
            $labels = ['Sin servicios'];
            $data = [0];
            $colors = ['#e2e8f0'];
        }

        return compact('labels', 'data', 'colors');
    }

    private function distribucionHoras(array $filtros): array
    {
        $rows = $this->baseServiciosQuery($filtros)
            ->selectRaw("HOUR(s.fecha_solicitud) as hora, COUNT(*) as total")
            ->groupByRaw("HOUR(s.fecha_solicitud)")
            ->pluck('total', 'hora')
            ->toArray();

        $labels = [];
        $data = [];
        for ($h = 0; $h < 24; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $data[] = (int) ($rows[$h] ?? 0);
        }

        return compact('labels', 'data');
    }

    private function distribucionCondiciones(array $filtros): array
    {
        $rows = $this->baseServiciosQuery($filtros)
            ->selectRaw("s.condicion, COUNT(*) as total")
            ->groupBy('s.condicion')
            ->orderByDesc('total')
            ->limit(7)
            ->pluck('total', 'condicion')
            ->toArray();

        $nombres = [
            'ninguno'       => 'Estándar',
            'aire'          => 'Con Aire',
            'baul'          => 'Baúl Grande',
            'mascota'       => 'Con Mascota',
            'parrilla'      => 'Con Parrilla',
            'transferencia' => 'Transferencia',
            'daviplata'     => 'Daviplata',
            'polarizados'   => 'Polarizados',
            'silla_ruedas'  => 'Silla Ruedas',
        ];

        $labels = [];
        $data = [];
        foreach ($rows as $cond => $cnt) {
            $labels[] = $nombres[$cond] ?? ucfirst(str_replace('_', ' ', $cond));
            $data[] = (int) $cnt;
        }

        if (empty($labels)) {
            $labels = ['Estándar'];
            $data = [0];
        }

        return compact('labels', 'data');
    }

    private function listarServicios(array $filtros)
    {
        $perPage = $filtros['perPage'] ?? 25;
        $limit = match((string) $perPage) {
            '10' => 10,
            '20' => 20,
            '30' => 30,
            '50' => 50,
            '100' => 100,
            'todos' => 5000,
            default => 25,
        };

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
            ->paginate($limit)
            ->appends(request()->query());
    }

    private function statsOperadores(array $filtros): array
    {
        $query = DB::table('usuarios as u')
            ->leftJoin('servicios as s', function ($j) use ($filtros) {
                $j->on('u.id', '=', 's.operador_id')
                  ->whereBetween(DB::raw('DATE(s.fecha_solicitud)'), [$filtros['fechaInicio'], $filtros['fechaFin']]);
            })
            ->whereIn('u.rol', ['operador', 'administrador', 'superadmin']);

        if (!empty($filtros['rol'])) {
            $query->where('u.rol', $filtros['rol']);
        }

        return $query
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
        $tiempos = array_filter(array_column($operadores, 'tiempo_promedio'));
        $tProm = count($tiempos) > 0 ? round(array_sum($tiempos) / count($tiempos), 1) : 0;

        $topOp = null;
        if (!empty($operadores) && $operadores[0]['total_servicios'] > 0) {
            $topOp = $operadores[0]['nombre'];
        }

        return [
            'total_servicios'  => $total,
            'finalizados'      => $fin,
            'cancelados'       => $can,
            'efectividad'      => $total > 0 ? round(($fin / $total) * 100, 1) : 0,
            'tasa_cancelacion' => $total > 0 ? round(($can / $total) * 100, 1) : 0,
            'tiempo_promedio'  => $tProm,
            'top_operador'     => $topOp,
            'activos_count'    => count(array_filter($operadores, fn($o) => $o['total_servicios'] > 0)),
            'total_operadores' => count($operadores),
        ];
    }

    private function graficosOperadores(array $operadores): array
    {
        $top = array_slice($operadores, 0, 10);

        $nombres = [];
        $servicios = [];
        $finalizados = [];
        $cancelados = [];
        $efectividad = [];

        foreach ($top as $op) {
            $partes = explode(' ', trim($op['nombre']));
            $corto = $partes[0] . (isset($partes[1]) ? ' ' . mb_substr($partes[1], 0, 1) . '.' : '');
            $nombres[] = $corto;
            $servicios[] = (int) $op['total_servicios'];
            $finalizados[] = (int) $op['finalizados'];
            $cancelados[] = (int) $op['cancelados'];
            $efectividad[] = (float) $op['efectividad'];
        }

        // Participación en Despacho (Doughnut)
        $activos = array_filter($operadores, fn($o) => $o['total_servicios'] > 0);
        $shareLabels = [];
        $shareData = [];
        $palette = ['#0284c7', '#10b981', '#f59e0b', '#06b6d4', '#8b5cf6', '#ec4899', '#64748b', '#3b82f6', '#14b8a6', '#f97316'];
        $shareColors = [];

        $i = 0;
        foreach (array_slice($activos, 0, 8) as $op) {
            $partes = explode(' ', trim($op['nombre']));
            $shareLabels[] = $partes[0] . (isset($partes[1]) ? ' ' . mb_substr($partes[1], 0, 1) . '.' : '');
            $shareData[] = (int) $op['total_servicios'];
            $shareColors[] = $palette[$i % count($palette)];
            $i++;
        }

        if (empty($shareLabels)) {
            $shareLabels = ['Sin servicios'];
            $shareData = [0];
            $shareColors = ['#e2e8f0'];
        }

        return [
            'nombres'      => $nombres,
            'servicios'    => $servicios,
            'finalizados'  => $finalizados,
            'cancelados'   => $cancelados,
            'efectividad'  => $efectividad,
            'share_labels' => $shareLabels,
            'share_data'   => $shareData,
            'share_colors' => $shareColors,
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
        $canc = array_sum(array_column($clientes, 'cancelados'));
        $dirs = array_sum(array_column($clientes, 'total_direcciones'));

        return [
            'total_servicios'   => $total,
            'finalizados'       => $fin,
            'cancelados'        => $canc,
            'clientes_activos'  => count($clientes),
            'total_direcciones' => $dirs,
            'efectividad'       => $total > 0 ? round(($fin / $total) * 100, 1) : 0,
            'tasa_cancelacion'  => $total > 0 ? round(($canc / $total) * 100, 1) : 0,
        ];
    }

    private function graficosClientes(array $clientes): array
    {
        $top = array_slice($clientes, 0, 10);

        $nombres = [];
        $servicios = [];
        $finalizados = [];
        $cancelados = [];

        foreach ($top as $c) {
            $nom = $c['nombre'] ?: $c['telefono'];
            $partes = explode(' ', trim($nom));
            $corto = $partes[0] . (isset($partes[1]) ? ' ' . mb_substr($partes[1], 0, 1) . '.' : '');
            $nombres[] = $corto;
            $servicios[] = (int) $c['total_servicios'];
            $finalizados[] = (int) $c['finalizados'];
            $cancelados[] = (int) $c['cancelados'];
        }

        // Concentración de servicios (Donut)
        $shareLabels = [];
        $shareData = [];
        $palette = ['#0284c7', '#10b981', '#f59e0b', '#06b6d4', '#8b5cf6', '#ec4899', '#64748b', '#3b82f6'];
        $shareColors = [];

        $i = 0;
        foreach (array_slice($clientes, 0, 7) as $c) {
            $nom = $c['nombre'] ?: $c['telefono'];
            $partes = explode(' ', trim($nom));
            $shareLabels[] = $partes[0] . (isset($partes[1]) ? ' ' . mb_substr($partes[1], 0, 1) . '.' : '');
            $shareData[] = (int) $c['total_servicios'];
            $shareColors[] = $palette[$i % count($palette)];
            $i++;
        }

        $resto = array_slice($clientes, 7);
        if (!empty($resto)) {
            $otrosTotal = array_sum(array_column($resto, 'total_servicios'));
            if ($otrosTotal > 0) {
                $shareLabels[] = 'Otros Clientes';
                $shareData[] = (int) $otrosTotal;
                $shareColors[] = '#cbd5e1';
            }
        }

        if (empty($shareLabels)) {
            $shareLabels = ['Sin servicios'];
            $shareData = [0];
            $shareColors = ['#e2e8f0'];
        }

        return [
            'nombres'      => $nombres,
            'servicios'    => $servicios,
            'finalizados'  => $finalizados,
            'cancelados'   => $cancelados,
            'share_labels' => $shareLabels,
            'share_data'   => $shareData,
            'share_colors' => $shareColors,
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

        $clienteObj = DB::table('clientes')->where('id', $cliente)->first(['id', 'nombre', 'telefono']);

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

        return response()->json([
            'error'     => false,
            'cliente'   => $clienteObj,
            'servicios' => $servicios
        ]);
    }

    public function clienteDirecciones(int $cliente)
    {
        $clienteObj = DB::table('clientes')->where('id', $cliente)->first(['id', 'nombre', 'telefono']);

        $direcciones = DB::table('direcciones as d')
            ->where('d.cliente_id', $cliente)
            ->leftJoin(DB::raw('(SELECT direccion_id, COUNT(*) as total_servicios FROM servicios GROUP BY direccion_id) as sc'), 'sc.direccion_id', '=', 'd.id')
            ->select('d.id', 'd.direccion', 'd.referencia', 'd.es_frecuente', 'd.activa', 'd.fecha_registro', 'd.ultimo_uso', DB::raw('COALESCE(sc.total_servicios, 0) as total_servicios'))
            ->orderByDesc('d.ultimo_uso')
            ->get();

        return response()->json([
            'error'       => false,
            'cliente'     => $clienteObj,
            'direcciones' => $direcciones
        ]);
    }
}
