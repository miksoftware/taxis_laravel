<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * Listado con filtros
     */
    public function index(Request $request)
    {
        $filtroEstado = $request->input('estado', '');
        $buscar = $request->input('buscar', '');

        $vehiculos = Vehiculo::with('sancionActiva')
            ->when($filtroEstado, fn($q) => $q->porEstado($filtroEstado))
            ->when($buscar, fn($q) => $q->buscar($buscar))
            ->orderByRaw("numero_movil REGEXP '^[0-9]+$' DESC, CAST(numero_movil AS UNSIGNED), numero_movil")
            ->get();

        // Agregar info de sanción activa a los sancionados
        $vehiculos->each(function ($v) {
            if ($v->estado === 'sancionado' && $v->sancionActiva) {
                $v->sancion_info = $v->sancionActiva;
            }
        });

        $estadisticas = Vehiculo::estadisticas();

        return view('vehiculos.index', compact('vehiculos', 'estadisticas', 'filtroEstado', 'buscar'));
    }

    /**
     * Datos de un vehículo (JSON para AJAX)
     */
    public function show(Vehiculo $vehiculo)
    {
        return response()->json($vehiculo->only([
            'id', 'placa', 'numero_movil', 'modelo', 'marca', 'conductor_id', 'estado',
        ]));
    }

    /**
     * Registrar vehículo
     */
    public function store(Request $request)
    {
        $request->validate([
            'placa' => ['required', 'string', 'max:10', 'unique:vehiculos,placa', 'regex:/^[A-Za-z0-9]{4,10}$/'],
            'numero_movil' => ['required', 'string', 'max:20', 'unique:vehiculos,numero_movil'],
            'modelo' => ['nullable', 'string', 'max:50'],
            'marca' => ['nullable', 'string', 'max:50'],
            'estado' => ['nullable', Rule::in(['disponible', 'mantenimiento', 'inactivo'])],
        ], [
            'placa.required' => 'La placa es obligatoria.',
            'placa.unique' => 'Esta placa ya está registrada.',
            'placa.regex' => 'Formato de placa inválido (solo letras y números).',
            'numero_movil.required' => 'El número de móvil es obligatorio.',
            'numero_movil.unique' => 'Este número de móvil ya está registrado.',
            'estado.in' => 'Estado no válido.',
        ]);

        Vehiculo::create([
            'placa' => strtoupper(trim($request->placa)),
            'numero_movil' => trim($request->numero_movil),
            'modelo' => $request->modelo,
            'marca' => $request->marca,
            'estado' => $request->estado ?? 'disponible',
        ]);

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo registrado correctamente.');
    }

    /**
     * Actualizar vehículo
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'placa' => ['required', 'string', 'max:10', Rule::unique('vehiculos')->ignore($vehiculo->id), 'regex:/^[A-Za-z0-9]{4,10}$/'],
            'numero_movil' => ['required', 'string', 'max:20', Rule::unique('vehiculos')->ignore($vehiculo->id)],
            'modelo' => ['nullable', 'string', 'max:50'],
            'marca' => ['nullable', 'string', 'max:50'],
            'estado' => ['nullable', Rule::in(['disponible', 'ocupado', 'mantenimiento', 'sancionado', 'inactivo'])],
        ], [
            'placa.required' => 'La placa es obligatoria.',
            'placa.unique' => 'Esta placa ya está asignada a otro vehículo.',
            'placa.regex' => 'Formato de placa inválido.',
            'numero_movil.required' => 'El número de móvil es obligatorio.',
            'numero_movil.unique' => 'Este número de móvil ya está asignado a otro vehículo.',
        ]);

        $vehiculo->update([
            'placa' => strtoupper(trim($request->placa)),
            'numero_movil' => trim($request->numero_movil),
            'modelo' => $request->modelo,
            'marca' => $request->marca,
            'estado' => $request->estado ?? $vehiculo->estado,
        ]);

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado correctamente.');
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'estado' => ['required', Rule::in(['disponible', 'mantenimiento', 'inactivo'])],
        ]);

        // No permitir cambiar estado si tiene sanción activa
        if ($vehiculo->estaSancionado() && $vehiculo->tieneSancionActiva()) {
            return redirect()->route('vehiculos.index')
                ->with('error', 'No se puede cambiar el estado: el vehículo tiene una sanción activa.');
        }

        $vehiculo->update(['estado' => $request->estado]);

        $estados = [
            'disponible' => 'disponible',
            'mantenimiento' => 'en mantenimiento',
            'inactivo' => 'inactivo',
        ];

        return redirect()->route('vehiculos.index')
            ->with('success', "Vehículo marcado como {$estados[$request->estado]}.");
    }

    /**
     * Eliminar (baja lógica)
     */
    public function destroy(Vehiculo $vehiculo)
    {
        if ($vehiculo->tieneSancionActiva()) {
            return redirect()->route('vehiculos.index')
                ->with('error', 'No se puede dar de baja: el vehículo tiene sanciones activas.');
        }

        $vehiculo->update(['estado' => 'inactivo']);

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo dado de baja correctamente.');
    }

    /**
     * Detalle del vehículo con historial de sanciones (AJAX parcial)
     */
    public function detalle(Vehiculo $vehiculo)
    {
        $vehiculo->load(['sancionActiva', 'sanciones' => function ($q) {
            $q->with('articulo:id,codigo,descripcion,tiempo_sancion')
              ->orderByDesc('fecha_inicio');
        }]);

        return view('vehiculos._detalle', compact('vehiculo'));
    }

    /**
     * Vehículos disponibles (JSON - usado desde servicios)
     * Verifica sanciones vencidas antes de devolver la lista.
     */
    public function disponibles()
    {
        // Liberar vehículos con sanciones vencidas
        $vencidas = \App\Models\Sancion::vencidas()->with('vehiculo')->get();
        if ($vencidas->isNotEmpty()) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($vencidas) {
                foreach ($vencidas as $sancion) {
                    $sancion->update(['estado' => 'cumplida']);
                    $sancion->vehiculo->update(['estado' => 'disponible']);
                    \App\Models\HistorialSancion::create([
                        'sancion_id' => $sancion->id,
                        'accion'     => 'cumplida',
                        'usuario_id' => 1,
                        'comentario' => 'Sanción cumplida automáticamente.',
                        'fecha'      => now(),
                    ]);
                }
            });
        }

        $vehiculos = Vehiculo::disponibles()
            ->orderByRaw("numero_movil REGEXP '^[0-9]+$' DESC, CAST(numero_movil AS UNSIGNED), numero_movil")
            ->get(['id', 'placa', 'numero_movil']);

        return response()->json(['error' => false, 'vehiculos' => $vehiculos]);
    }
}
