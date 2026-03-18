<?php

namespace App\Http\Controllers;

use App\Models\Sancion;
use App\Models\Vehiculo;
use App\Models\ArticuloSancion;
use App\Models\HistorialSancion;
use App\Jobs\VerificarSancionesVencidas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SancionController extends Controller
{
    /**
     * Listado de sanciones con filtros
     */
    public function index(Request $request)
    {
        // Auto-liberar sanciones vencidas
        $this->liberarSancionesVencidas();

        $filtroEstado = $request->input('estado', '');
        $filtroVehiculo = $request->input('vehiculo', '');

        $sanciones = Sancion::with(['vehiculo:id,placa,numero_movil', 'articulo:id,codigo,descripcion,tiempo_sancion', 'usuario:id,nombre,apellidos'])
            ->when($filtroEstado, fn($q) => $q->where('estado', $filtroEstado))
            ->when($filtroVehiculo, function ($q) use ($filtroVehiculo) {
                $q->whereHas('vehiculo', fn($v) => $v->where('placa', 'like', "%{$filtroVehiculo}%")
                    ->orWhere('numero_movil', 'like', "%{$filtroVehiculo}%"));
            })
            ->orderByDesc('fecha_inicio')
            ->paginate(20)
            ->appends($request->only(['estado', 'vehiculo']));

        $stats = [
            'activa' => Sancion::where('estado', 'activa')->count(),
            'cumplida' => Sancion::where('estado', 'cumplida')->count(),
            'anulada' => Sancion::where('estado', 'anulada')->count(),
        ];

        return view('sanciones.index', compact('sanciones', 'stats', 'filtroEstado', 'filtroVehiculo'));
    }

    /**
     * Detalle de una sanción (AJAX parcial)
     */
    public function detalle(Sancion $sancion)
    {
        $sancion->load([
            'vehiculo:id,placa,numero_movil',
            'articulo:id,codigo,descripcion,tiempo_sancion',
            'usuario:id,nombre,apellidos',
            'historial.usuario:id,nombre,apellidos',
        ]);

        return view('sanciones._detalle', compact('sancion'));
    }

    /**
     * Aplicar sanción a un vehículo
     */
    public function aplicar(Request $request)
    {
        $request->validate([
            'vehiculo_id' => ['required', 'exists:vehiculos,id'],
            'articulo_id' => ['required', 'exists:articulos_sancion,id'],
            'motivo' => ['required', 'string', 'min:5'],
        ], [
            'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
            'vehiculo_id.exists' => 'El vehículo no existe.',
            'articulo_id.required' => 'Debe seleccionar un artículo de sanción.',
            'articulo_id.exists' => 'El artículo de sanción no existe.',
            'motivo.required' => 'Debe ingresar un motivo.',
            'motivo.min' => 'El motivo debe tener al menos 5 caracteres.',
        ]);

        $vehiculo = Vehiculo::findOrFail($request->vehiculo_id);

        if ($vehiculo->estaSancionado()) {
            return back()->with('error', 'El vehículo ya se encuentra sancionado.');
        }

        $articulo = ArticuloSancion::findOrFail($request->articulo_id);

        $fechaInicio = now();
        $fechaFin = now()->addMinutes($articulo->tiempo_sancion);

        DB::transaction(function () use ($request, $vehiculo, $fechaInicio, $fechaFin) {
            $sancion = Sancion::create([
                'vehiculo_id' => $vehiculo->id,
                'articulo_id' => $request->articulo_id,
                'usuario_id' => auth()->id(),
                'motivo' => $request->motivo,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado' => 'activa',
            ]);

            $vehiculo->update(['estado' => 'sancionado']);

            HistorialSancion::create([
                'sancion_id' => $sancion->id,
                'accion' => 'aplicada',
                'usuario_id' => auth()->id(),
                'comentario' => $request->motivo,
                'fecha' => now(),
            ]);
        });

        return redirect()->route('sanciones.index')
            ->with('success', "Sanción aplicada. Vehículo inhabilitado hasta {$fechaFin->format('d/m/Y H:i')}.");
    }

    /**
     * Anular una sanción activa
     */
    public function anular(Request $request, Sancion $sancion)
    {
        $request->validate([
            'comentario' => ['required', 'string', 'min:5'],
        ], [
            'comentario.required' => 'Debe ingresar un motivo de anulación.',
            'comentario.min' => 'El motivo debe tener al menos 5 caracteres.',
        ]);

        if ($sancion->estado !== 'activa') {
            return back()->with('error', 'Solo se pueden anular sanciones activas.');
        }

        DB::transaction(function () use ($request, $sancion) {
            $sancion->update(['estado' => 'anulada']);
            $sancion->vehiculo->update(['estado' => 'disponible']);

            HistorialSancion::create([
                'sancion_id' => $sancion->id,
                'accion' => 'anulada',
                'usuario_id' => auth()->id(),
                'comentario' => $request->comentario,
                'fecha' => now(),
            ]);
        });

        return redirect()->route('sanciones.index')
            ->with('success', 'Sanción anulada. El vehículo está disponible nuevamente.');
    }

    /**
     * Verificar vencimientos manualmente (admin)
     */
    public function verificarVencimientos()
    {
        VerificarSancionesVencidas::dispatchSync();

        return redirect()->route('sanciones.index')
            ->with('success', 'Verificación de vencimientos ejecutada correctamente.');
    }

    /**
     * Datos para el countdown en tiempo real (JSON)
     * También verifica y libera sanciones vencidas automáticamente.
     */
    public function sancionesActivas()
    {
        // Auto-verificar vencidas antes de devolver datos
        $this->liberarSancionesVencidas();

        $activas = Sancion::activas()
            ->with(['vehiculo:id,placa,numero_movil', 'articulo:id,codigo,tiempo_sancion'])
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'placa' => $s->vehiculo->placa,
                'movil' => $s->vehiculo->numero_movil,
                'articulo' => $s->articulo->codigo,
                'fecha_fin' => $s->fecha_fin->toIso8601String(),
                'segundos_restantes' => $s->segundosRestantes(),
            ]);

        return response()->json($activas);
    }

    /**
     * Libera sanciones vencidas automáticamente (no depende del cron)
     */
    private function liberarSancionesVencidas(): void
    {
        $vencidas = Sancion::vencidas()->with('vehiculo')->get();

        if ($vencidas->isEmpty()) return;

        DB::transaction(function () use ($vencidas) {
            foreach ($vencidas as $sancion) {
                $sancion->update(['estado' => 'cumplida']);
                $sancion->vehiculo->update(['estado' => 'disponible']);

                HistorialSancion::create([
                    'sancion_id' => $sancion->id,
                    'accion'     => 'cumplida',
                    'usuario_id' => 1,
                    'comentario' => 'Sanción cumplida automáticamente.',
                    'fecha'      => now(),
                ]);
            }
        });
    }
}
