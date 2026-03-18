<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    /**
     * Direcciones de un cliente (JSON)
     */
    public function porCliente(Request $request)
    {
        $clienteId = $request->input('cliente_id');

        if (!$clienteId) {
            return response()->json(['error' => true, 'mensaje' => 'ID de cliente no proporcionado.']);
        }

        $query = Direccion::where('cliente_id', $clienteId)
            ->orderByDesc('es_frecuente')
            ->orderByDesc('ultimo_uso');

        // Por defecto solo activas
        if (!$request->boolean('mostrar_inactivas')) {
            $query->activas();
        }

        $direcciones = $query->get();

        return response()->json([
            'error' => false,
            'direcciones' => $direcciones,
            'total' => $direcciones->count(),
        ]);
    }

    /**
     * Detalle de una dirección (JSON)
     */
    public function show(Direccion $direccion)
    {
        $direccion->load('cliente:id,telefono,nombre');
        return response()->json($direccion);
    }

    /**
     * Crear dirección
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'direccion'  => ['required', 'string', 'max:255'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'es_frecuente' => ['nullable', 'boolean'],
        ], [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'cliente_id.exists'   => 'El cliente no existe.',
            'direccion.required'  => 'La dirección es obligatoria.',
        ]);

        $normalizada = Direccion::normalizar($request->direccion);

        // Verificar duplicado por dirección normalizada
        $existe = Direccion::where('cliente_id', $request->cliente_id)
            ->where('direccion_normalizada', $normalizada)
            ->first();

        if ($existe) {
            // Actualizar último uso y devolver la existente
            $existe->update(['ultimo_uso' => now()]);

            return response()->json([
                'error' => false,
                'mensaje' => 'Dirección existente seleccionada.',
                'direccion_id' => $existe->id,
                'direccion' => $existe->direccion,
                'es_nueva' => false,
            ]);
        }

        $direccion = Direccion::create([
            'cliente_id' => $request->cliente_id,
            'direccion' => $request->direccion,
            'direccion_normalizada' => $normalizada,
            'referencia' => $request->referencia ?? '',
            'es_frecuente' => $request->boolean('es_frecuente'),
            'activa' => true,
            'ultimo_uso' => now(),
            'fecha_registro' => now(),
        ]);

        // Auto-marcar frecuente si tiene pocas direcciones
        $total = Direccion::where('cliente_id', $request->cliente_id)->count();
        if ($total < 5) {
            $direccion->update(['es_frecuente' => true]);
        }

        return response()->json([
            'error' => false,
            'mensaje' => 'Dirección creada correctamente.',
            'direccion_id' => $direccion->id,
            'direccion' => $direccion->direccion,
            'es_nueva' => true,
        ]);
    }

    /**
     * Actualizar dirección
     */
    public function update(Request $request, Direccion $direccion)
    {
        $request->validate([
            'direccion'  => ['nullable', 'string', 'max:255'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'es_frecuente' => ['nullable', 'boolean'],
        ]);

        $datos = $request->only(['referencia', 'es_frecuente']);

        if ($request->filled('direccion') && $request->direccion !== $direccion->direccion) {
            $normalizada = Direccion::normalizar($request->direccion);

            // Verificar duplicado
            $duplicada = Direccion::where('cliente_id', $direccion->cliente_id)
                ->where('direccion_normalizada', $normalizada)
                ->where('id', '!=', $direccion->id)
                ->exists();

            if ($duplicada) {
                return response()->json(['error' => true, 'mensaje' => 'Esta dirección ya existe para este cliente.']);
            }

            $datos['direccion'] = $request->direccion;
            $datos['direccion_normalizada'] = $normalizada;
        }

        $direccion->update($datos);

        return response()->json(['error' => false, 'mensaje' => 'Dirección actualizada correctamente.']);
    }

    /**
     * Eliminar o desactivar dirección
     */
    public function destroy(Direccion $direccion)
    {
        // Si tiene servicios asociados, solo desactivar
        $tieneServicios = \DB::table('servicios')->where('direccion_id', $direccion->id)->exists();

        if ($tieneServicios) {
            $direccion->update(['activa' => false]);
            return response()->json(['error' => false, 'mensaje' => 'Dirección marcada como inactiva (tiene servicios asociados).']);
        }

        $direccion->delete();
        return response()->json(['error' => false, 'mensaje' => 'Dirección eliminada correctamente.']);
    }

    /**
     * Cambiar estado activa/inactiva
     */
    public function cambiarEstado(Request $request, Direccion $direccion)
    {
        $request->validate(['activa' => 'required|boolean']);

        if (!$request->boolean('activa')) {
            $tieneServicios = \DB::table('servicios')->where('direccion_id', $direccion->id)->exists();
            if (!$tieneServicios) {
                $direccion->delete();
                return response()->json(['error' => false, 'mensaje' => 'Dirección eliminada (sin servicios asociados).']);
            }
        }

        $direccion->update(['activa' => $request->boolean('activa')]);
        $estado = $request->boolean('activa') ? 'activada' : 'desactivada';

        return response()->json(['error' => false, 'mensaje' => "Dirección {$estado} correctamente."]);
    }

    /**
     * Marcar/desmarcar como frecuente
     */
    public function marcarFrecuente(Request $request, Direccion $direccion)
    {
        $request->validate(['es_frecuente' => 'required|boolean']);

        $direccion->update(['es_frecuente' => $request->boolean('es_frecuente')]);

        return response()->json(['error' => false, 'mensaje' => 'Dirección actualizada correctamente.']);
    }

    /**
     * Buscar para autocompletar (AJAX)
     */
    public function autocompletar(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $termino = $request->input('q', '');

        if (!$clienteId) {
            return response()->json([]);
        }

        $direcciones = Direccion::where('cliente_id', $clienteId)
            ->activas()
            ->where(function ($q) use ($termino) {
                $q->where('direccion', 'like', "%{$termino}%")
                  ->orWhere('direccion_normalizada', 'like', "%{$termino}%")
                  ->orWhere('referencia', 'like', "%{$termino}%");
            })
            ->orderByDesc('es_frecuente')
            ->orderByDesc('ultimo_uso')
            ->limit(10)
            ->get(['id', 'direccion', 'referencia', 'es_frecuente']);

        return response()->json($direcciones);
    }
}
