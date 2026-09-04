<?php

namespace App\Http\Controllers;

use App\Models\ArticuloSancion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticuloSancionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar', '');
        $filtroEstado = $request->input('estado', '');
        $perPage = $request->input('per_page', 10);

        $limit = match($perPage) {
            '10' => 10,
            '20' => 20,
            '30' => 30,
            '50' => 50,
            'todos' => 10000,
            default => 10,
        };

        $articulos = ArticuloSancion::when($buscar, function ($q) use ($buscar) {
                $q->where(function ($sq) use ($buscar) {
                    $sq->where('codigo', 'like', "%{$buscar}%")
                       ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($filtroEstado, fn($q) => $q->where('estado', $filtroEstado))
            ->orderBy('codigo')
            ->paginate($limit)
            ->appends($request->query());

        $stats = [
            'total' => ArticuloSancion::count(),
            'activos' => ArticuloSancion::where('estado', 'activo')->count(),
            'inactivos' => ArticuloSancion::where('estado', 'inactivo')->count(),
            'promedio_tiempo' => (int) round(ArticuloSancion::avg('tiempo_sancion') ?? 0),
        ];

        return view('articulos-sancion.index', compact('articulos', 'stats', 'buscar', 'filtroEstado', 'perPage'));
    }

    public function show(ArticuloSancion $articulo)
    {
        return response()->json($articulo);
    }

    /**
     * Artículos activos (JSON - para select en formulario de sanciones)
     */
    public function activos()
    {
        $articulos = ArticuloSancion::activos()->orderBy('codigo')->get();
        return response()->json($articulos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'max:20', 'unique:articulos_sancion,codigo'],
            'descripcion' => ['required', 'string'],
            'tiempo_sancion' => ['required', 'integer', 'min:1'],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe un artículo con este código.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'tiempo_sancion.required' => 'El tiempo de sanción es obligatorio.',
            'tiempo_sancion.min' => 'El tiempo debe ser al menos 1 minuto.',
        ]);

        ArticuloSancion::create([
            'codigo' => strtoupper(trim($request->codigo)),
            'descripcion' => trim($request->descripcion),
            'tiempo_sancion' => $request->tiempo_sancion,
            'estado' => $request->estado ?? 'activo',
        ]);

        return redirect()->route('articulos-sancion.index')->with('success', 'Artículo registrado correctamente.');
    }

    public function update(Request $request, ArticuloSancion $articulo)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('articulos_sancion')->ignore($articulo->id)],
            'descripcion' => ['required', 'string'],
            'tiempo_sancion' => ['required', 'integer', 'min:1'],
            'estado' => ['nullable', Rule::in(['activo', 'inactivo'])],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe otro artículo con este código.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'tiempo_sancion.required' => 'El tiempo de sanción es obligatorio.',
        ]);

        $articulo->update([
            'codigo' => strtoupper(trim($request->codigo)),
            'descripcion' => trim($request->descripcion),
            'tiempo_sancion' => $request->tiempo_sancion,
            'estado' => $request->estado ?? $articulo->estado,
        ]);

        return redirect()->route('articulos-sancion.index')->with('success', 'Artículo actualizado correctamente.');
    }

    public function cambiarEstado(Request $request, ArticuloSancion $articulo)
    {
        $request->validate(['estado' => ['required', Rule::in(['activo', 'inactivo'])]]);

        if ($request->estado === 'inactivo' && $articulo->tienesSancionesActivas()) {
            return redirect()->route('articulos-sancion.index')
                ->with('error', 'No se puede inactivar: tiene sanciones activas asociadas.');
        }

        $articulo->update(['estado' => $request->estado]);
        $accion = $request->estado === 'activo' ? 'activado' : 'desactivado';

        return redirect()->route('articulos-sancion.index')->with('success', "Artículo {$accion} correctamente.");
    }

    public function destroy(ArticuloSancion $articulo)
    {
        if ($articulo->tienesSancionesActivas()) {
            return redirect()->route('articulos-sancion.index')
                ->with('error', 'No se puede eliminar: tiene sanciones activas asociadas.');
        }

        $articulo->update(['estado' => 'inactivo']);
        return redirect()->route('articulos-sancion.index')->with('success', 'Artículo desactivado correctamente.');
    }
}
