<?php

namespace App\Http\Controllers;

use App\Models\Sancion;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');
        [$fechaInicio, $fechaFin] = $this->rangoFechas($periodo);

        $statsServicios = $this->statsServicios($fechaInicio, $fechaFin);
        $statsVehiculos = Vehiculo::estadisticas();
        $topOperadores = $this->topOperadores($fechaInicio, $fechaFin);
        $actividadReciente = $this->actividadReciente();
        $alertas = $this->alertas();
        $serviciosPorHora = $this->serviciosPorHora($fechaInicio, $fechaFin, $periodo);

        return view('dashboard', compact(
            'periodo', 'statsServicios', 'statsVehiculos',
            'topOperadores', 'actividadReciente', 'alertas', 'serviciosPorHora'
        ));
    }

    /**
     * Endpoint AJAX para cambiar período sin recargar toda la página
     */
    public function stats(Request $request): JsonResponse
    {
        $periodo = $request->input('periodo', 'hoy');
        [$fechaInicio, $fechaFin] = $this->rangoFechas($periodo);

        return response()->json([
            'servicios' => $this->statsServicios($fechaInicio, $fechaFin),
            'vehiculos' => Vehiculo::estadisticas(),
            'topOperadores' => $this->topOperadores($fechaInicio, $fechaFin),
            'serviciosPorHora' => $this->serviciosPorHora($fechaInicio, $fechaFin, $periodo),
        ]);
    }

    private function rangoFechas(string $periodo): array
    {
        return match ($periodo) {
            'semana' => [now()->subDays(7)->startOfDay(), now()],
            'mes'    => [now()->subDays(30)->startOfDay(), now()],
            default  => [today()->startOfDay(), now()],
        };
    }

    private function statsServicios($desde, $hasta): array
    {
        $stats = DB::table('servicios')
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->selectRaw("
                COUNT(*) as total,
                SUM(estado = 'finalizado') as finalizados,
                SUM(estado = 'cancelado') as cancelados,
                SUM(estado = 'pendiente') as pendientes,
                SUM(estado = 'asignado') as asignados,
                SUM(estado = 'en_camino') as en_camino,
                ROUND(AVG(TIMESTAMPDIFF(SECOND, fecha_solicitud, fecha_asignacion)) / 60, 1) as tiempo_promedio_min
            ")
            ->first();

        $total = (int) $stats->total;
        $finalizados = (int) $stats->finalizados;

        return [
            'total'              => $total,
            'finalizados'        => $finalizados,
            'cancelados'         => (int) $stats->cancelados,
            'pendientes'         => (int) $stats->pendientes,
            'asignados'          => (int) $stats->asignados,
            'en_camino'          => (int) $stats->en_camino,
            'efectividad'        => $total > 0 ? round(($finalizados / $total) * 100, 1) : 0,
            'tiempo_promedio_min' => $stats->tiempo_promedio_min ?? 0,
        ];
    }

    private function topOperadores($desde, $hasta, int $limite = 5): array
    {
        return DB::table('usuarios as u')
            ->leftJoin('servicios as s', function ($join) use ($desde, $hasta) {
                $join->on('u.id', '=', 's.operador_id')
                     ->whereBetween('s.fecha_solicitud', [$desde, $hasta]);
            })
            ->whereIn('u.rol', ['operador', 'administrador'])
            ->where('u.estado', 'activo')
            ->groupBy('u.id', 'u.nombre', 'u.apellidos')
            ->selectRaw("
                u.id, CONCAT(u.nombre, ' ', u.apellidos) as nombre,
                COUNT(s.id) as total_servicios,
                SUM(CASE WHEN s.estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
                SUM(CASE WHEN s.estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                CASE WHEN COUNT(s.id) > 0
                    THEN ROUND(SUM(CASE WHEN s.estado = 'finalizado' THEN 1 ELSE 0 END) * 100.0 / COUNT(s.id), 1)
                    ELSE 0 END as efectividad,
                ROUND(AVG(TIMESTAMPDIFF(SECOND, s.fecha_solicitud, s.fecha_asignacion)) / 60, 1) as tiempo_promedio
            ")
            ->orderByDesc('total_servicios')
            ->limit($limite)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    private function actividadReciente(int $limite = 8): array
    {
        return DB::table('servicios as s')
            ->join('clientes as c', 's.cliente_id', '=', 'c.id')
            ->leftJoin('vehiculos as v', 's.vehiculo_id', '=', 'v.id')
            ->leftJoin('usuarios as u', 's.operador_id', '=', 'u.id')
            ->select(
                's.id', 's.estado', 's.fecha_solicitud', 's.fecha_actualizacion',
                'c.telefono', 'c.nombre as cliente_nombre',
                'v.numero_movil', 'v.placa',
                'u.nombre as operador_nombre'
            )
            ->orderByDesc('s.fecha_actualizacion')
            ->limit($limite)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    private function alertas(): array
    {
        $alertas = [];

        // Servicios pendientes > 15 min sin asignar
        $pendientesViejos = DB::table('servicios')
            ->where('estado', 'pendiente')
            ->where('fecha_solicitud', '<', now()->subMinutes(15))
            ->count();

        if ($pendientesViejos > 0) {
            $alertas[] = [
                'tipo'    => 'warning',
                'icono'   => 'bi-clock-history',
                'titulo'  => 'Servicios sin asignar',
                'mensaje' => "{$pendientesViejos} servicio(s) pendiente(s) por más de 15 minutos.",
                'url'     => route('servicios.index'),
            ];
        }

        // Sanciones activas
        $sancionesActivas = Sancion::where('estado', 'activa')->count();
        if ($sancionesActivas > 0) {
            $alertas[] = [
                'tipo'    => 'danger',
                'icono'   => 'bi-exclamation-triangle',
                'titulo'  => 'Vehículos sancionados',
                'mensaje' => "{$sancionesActivas} vehículo(s) con sanción activa.",
                'url'     => route('sanciones.index'),
            ];
        }

        // Vehículos en mantenimiento
        $enMantenimiento = Vehiculo::where('estado', 'mantenimiento')->count();
        if ($enMantenimiento > 0) {
            $alertas[] = [
                'tipo'    => 'info',
                'icono'   => 'bi-tools',
                'titulo'  => 'En mantenimiento',
                'mensaje' => "{$enMantenimiento} vehículo(s) en mantenimiento.",
                'url'     => route('vehiculos.index', ['estado' => 'mantenimiento']),
            ];
        }

        // Baja disponibilidad
        $statsV = Vehiculo::estadisticas();
        $operativos = $statsV['total'] - ($statsV['inactivo'] ?? 0);
        if ($operativos > 0) {
            $pctDisponible = round((($statsV['disponible'] ?? 0) / $operativos) * 100);
            if ($pctDisponible < 30) {
                $alertas[] = [
                    'tipo'    => 'danger',
                    'icono'   => 'bi-battery-half',
                    'titulo'  => 'Baja disponibilidad',
                    'mensaje' => "Solo {$pctDisponible}% de vehículos disponibles.",
                    'url'     => route('vehiculos.index'),
                ];
            }
        }

        return $alertas;
    }

    private function serviciosPorHora($desde, $hasta, string $periodo): array
    {
        if ($periodo === 'hoy') {
            $datos = DB::table('servicios')
                ->whereDate('fecha_solicitud', today())
                ->selectRaw("HOUR(fecha_solicitud) as hora, COUNT(*) as total")
                ->groupByRaw("HOUR(fecha_solicitud)")
                ->pluck('total', 'hora')
                ->toArray();

            $labels = [];
            $values = [];
            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $values[] = $datos[$i] ?? 0;
            }
        } else {
            $datos = DB::table('servicios')
                ->whereBetween('fecha_solicitud', [$desde, $hasta])
                ->selectRaw("DATE(fecha_solicitud) as fecha, COUNT(*) as total")
                ->groupByRaw("DATE(fecha_solicitud)")
                ->orderBy('fecha')
                ->pluck('total', 'fecha')
                ->toArray();

            $labels = [];
            $values = [];
            $current = $desde->copy();
            while ($current->lte($hasta)) {
                $key = $current->format('Y-m-d');
                $labels[] = $current->format('d/m');
                $values[] = $datos[$key] ?? 0;
                $current->addDay();
            }
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
