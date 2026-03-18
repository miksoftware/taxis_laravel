<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Direccion;
use App\Models\HistorialServicio;
use App\Models\Servicio;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioController extends Controller
{
    /**
     * Pantalla principal de recepción
     */
    public function index()
    {
        $metricas = Servicio::metricasHoy();
        return view('servicios.recepcion', compact('metricas'));
    }

    /**
     * Crear servicio — OPTIMIZADO para alto throughput
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cliente_id'   => 'required|integer|exists:clientes,id',
            'direccion_id' => 'required|integer|exists:direcciones,id',
            'condicion'    => 'nullable|in:aire,baul,mascota,parrilla,transferencia,daviplata,polarizados,silla_ruedas,ninguno',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $servicio = DB::transaction(function () use ($request) {
                $servicio = Servicio::create([
                    'cliente_id'      => $request->cliente_id,
                    'direccion_id'    => $request->direccion_id,
                    'condicion'       => $request->condicion ?? 'ninguno',
                    'observaciones'   => $request->observaciones,
                    'estado'          => 'pendiente',
                    'fecha_solicitud' => now(),
                    'operador_id'     => auth()->id(),
                ]);

                // Actualizar último uso de la dirección
                Direccion::where('id', $request->direccion_id)
                    ->update(['ultimo_uso' => now()]);

                // Registrar historial
                HistorialServicio::create([
                    'servicio_id'    => $servicio->id,
                    'estado_anterior' => null,
                    'estado_nuevo'   => 'pendiente',
                    'fecha_cambio'   => now(),
                    'usuario_id'     => auth()->id(),
                ]);

                return $servicio;
            });

            // Recargar con relaciones para devolver datos completos
            $servicio = Servicio::activos()
                ->select('servicios.id', 'servicios.cliente_id', 'servicios.direccion_id',
                    'servicios.vehiculo_id', 'servicios.tipo_vehiculo', 'servicios.condicion',
                    'servicios.observaciones', 'servicios.estado', 'servicios.fecha_solicitud',
                    'servicios.fecha_asignacion', 'servicios.fecha_actualizacion', 'servicios.operador_id')
                ->join('clientes as c', 'servicios.cliente_id', '=', 'c.id')
                ->join('direcciones as d', 'servicios.direccion_id', '=', 'd.id')
                ->leftJoin('vehiculos as v', 'servicios.vehiculo_id', '=', 'v.id')
                ->leftJoin('usuarios as u', 'servicios.operador_id', '=', 'u.id')
                ->addSelect('c.telefono', 'c.nombre as cliente_nombre')
                ->addSelect('d.direccion', 'd.referencia')
                ->addSelect('v.placa', 'v.numero_movil')
                ->addSelect('u.nombre as operador_nombre')
                ->where('servicios.id', $servicio->id)
                ->first();

            return response()->json([
                'error'    => false,
                'mensaje'  => 'Servicio creado correctamente',
                'servicio' => $servicio,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'mensaje' => 'Error al crear servicio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Asignar vehículo a servicio pendiente
     */
    public function asignar(Request $request): JsonResponse
    {
        $request->validate([
            'servicio_id'   => 'required|integer',
            'vehiculo_id'   => 'required|integer',
            'tipo_vehiculo' => 'nullable|in:unico,proximo',
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
                $servicio = Servicio::lockForUpdate()->findOrFail($request->servicio_id);

                if ($servicio->estado !== 'pendiente') {
                    throw new \Exception('El servicio no está en estado pendiente');
                }

                $vehiculo = Vehiculo::lockForUpdate()->findOrFail($request->vehiculo_id);

                if ($vehiculo->estado !== 'disponible') {
                    throw new \Exception('El vehículo no está disponible');
                }

                $estadoAnterior = $servicio->estado;

                $servicio->update([
                    'vehiculo_id'     => $vehiculo->id,
                    'tipo_vehiculo'   => $request->tipo_vehiculo ?? 'unico',
                    'estado'          => 'asignado',
                    'fecha_asignacion' => now(),
                ]);

                $vehiculo->update(['estado' => 'ocupado']);

                HistorialServicio::create([
                    'servicio_id'    => $servicio->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo'   => 'asignado',
                    'fecha_cambio'   => now(),
                    'usuario_id'     => auth()->id(),
                ]);

                return $servicio;
            });

            return response()->json([
                'error'   => false,
                'mensaje' => 'Vehículo asignado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cambiar vehículo de un servicio asignado/en_camino
     */
    public function cambiarVehiculo(Request $request): JsonResponse
    {
        $request->validate([
            'servicio_id'   => 'required|integer',
            'vehiculo_id'   => 'required|integer',
            'tipo_vehiculo' => 'nullable|in:unico,proximo',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $servicio = Servicio::lockForUpdate()->findOrFail($request->servicio_id);

                if (!in_array($servicio->estado, ['asignado', 'en_camino'])) {
                    throw new \Exception('Solo se puede cambiar vehículo en estado asignado o en camino');
                }

                $nuevoVehiculo = Vehiculo::lockForUpdate()->findOrFail($request->vehiculo_id);

                if ($nuevoVehiculo->estado !== 'disponible') {
                    throw new \Exception('El vehículo seleccionado no está disponible');
                }

                $vehiculoAnteriorId = $servicio->vehiculo_id;

                $servicio->update([
                    'vehiculo_id'     => $nuevoVehiculo->id,
                    'tipo_vehiculo'   => $request->tipo_vehiculo ?? 'unico',
                    'fecha_asignacion' => now(),
                ]);

                $nuevoVehiculo->update(['estado' => 'ocupado']);

                // Liberar vehículo anterior
                if ($vehiculoAnteriorId) {
                    Vehiculo::where('id', $vehiculoAnteriorId)
                        ->update(['estado' => 'disponible']);
                }
            });

            return response()->json([
                'error'   => false,
                'mensaje' => 'Vehículo cambiado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Marcar servicio como en camino
     */
    public function enCamino(Request $request): JsonResponse
    {
        $request->validate(['servicio_id' => 'required|integer']);

        try {
            DB::transaction(function () use ($request) {
                $servicio = Servicio::lockForUpdate()->findOrFail($request->servicio_id);

                if ($servicio->estado !== 'asignado') {
                    throw new \Exception('El servicio no está en estado asignado');
                }

                $estadoAnterior = $servicio->estado;
                $servicio->update(['estado' => 'en_camino']);

                HistorialServicio::create([
                    'servicio_id'    => $servicio->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo'   => 'en_camino',
                    'fecha_cambio'   => now(),
                    'usuario_id'     => auth()->id(),
                ]);
            });

            return response()->json([
                'error'   => false,
                'mensaje' => 'Servicio marcado en camino',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Finalizar servicio
     */
    public function finalizar(Request $request): JsonResponse
    {
        $request->validate(['servicio_id' => 'required|integer']);

        try {
            DB::transaction(function () use ($request) {
                $servicio = Servicio::lockForUpdate()->findOrFail($request->servicio_id);

                if (!in_array($servicio->estado, ['asignado', 'en_camino'])) {
                    throw new \Exception('El servicio no se puede finalizar en su estado actual');
                }

                $estadoAnterior = $servicio->estado;

                $servicio->update([
                    'estado'    => 'finalizado',
                    'fecha_fin' => now(),
                ]);

                // Liberar vehículo
                if ($servicio->vehiculo_id) {
                    Vehiculo::where('id', $servicio->vehiculo_id)
                        ->update(['estado' => 'disponible']);
                }

                HistorialServicio::create([
                    'servicio_id'    => $servicio->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo'   => 'finalizado',
                    'fecha_cambio'   => now(),
                    'usuario_id'     => auth()->id(),
                ]);
            });

            return response()->json([
                'error'   => false,
                'mensaje' => 'Servicio finalizado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancelar servicio
     */
    public function cancelar(Request $request): JsonResponse
    {
        $request->validate(['servicio_id' => 'required|integer']);

        try {
            DB::transaction(function () use ($request) {
                $servicio = Servicio::lockForUpdate()->findOrFail($request->servicio_id);

                if (in_array($servicio->estado, ['finalizado', 'cancelado'])) {
                    throw new \Exception('El servicio ya está finalizado o cancelado');
                }

                $estadoAnterior = $servicio->estado;

                // Liberar vehículo si tiene uno asignado
                if ($servicio->vehiculo_id) {
                    Vehiculo::where('id', $servicio->vehiculo_id)
                        ->update(['estado' => 'disponible']);
                }

                $servicio->update([
                    'estado'    => 'cancelado',
                    'fecha_fin' => now(),
                ]);

                HistorialServicio::create([
                    'servicio_id'    => $servicio->id,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo'   => 'cancelado',
                    'fecha_cambio'   => now(),
                    'usuario_id'     => auth()->id(),
                ]);
            });

            return response()->json([
                'error'   => false,
                'mensaje' => 'Servicio cancelado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Actualizar dirección de un servicio
     */
    public function actualizarDireccion(Request $request): JsonResponse
    {
        $request->validate([
            'servicio_id'  => 'required|integer',
            'direccion_id' => 'required|integer|exists:direcciones,id',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);

        if (in_array($servicio->estado, ['finalizado', 'cancelado'])) {
            return response()->json(['error' => true, 'mensaje' => 'No se puede modificar un servicio terminado'], 422);
        }

        $servicio->update(['direccion_id' => $request->direccion_id]);

        return response()->json(['error' => false, 'mensaje' => 'Dirección actualizada']);
    }

    // ══════════════════════════════════════════════
    // ENDPOINTS DE DATOS (para AJAX y SSE)
    // ══════════════════════════════════════════════

    /**
     * Listado completo de servicios activos (carga inicial)
     */
    public function listarActivos(): JsonResponse
    {
        $servicios = Servicio::listarActivos(100);
        $metricas = Servicio::metricasHoy();

        return response()->json([
            'error'     => false,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'tipo'      => 'completa',
            'servicios' => $servicios,
            'metricas'  => $metricas,
        ]);
    }

    /**
     * Polling ligero — reemplaza SSE para compatibilidad con PHP tradicional.
     * Consulta solo cambios desde el último timestamp, muy liviano.
     */
    public function cambiosRecientes(Request $request): JsonResponse
    {
        $desde = $request->query('desde', now()->subMinutes(5)->format('Y-m-d H:i:s'));

        $cambios = Servicio::cambiosDespuesDe($desde);

        if ($cambios->isEmpty()) {
            return response()->json([
                'error'              => false,
                'timestamp'          => now()->format('Y-m-d H:i:s'),
                'hayActualizaciones' => false,
            ]);
        }

        $metricas = Servicio::metricasHoy();

        return response()->json([
            'error'              => false,
            'timestamp'          => now()->format('Y-m-d H:i:s'),
            'hayActualizaciones' => true,
            'tipo'               => 'incremental',
            'servicios'          => $cambios,
            'metricas'           => $metricas,
        ]);
    }
}
