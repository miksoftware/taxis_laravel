<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Direccion;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Listado paginado con búsqueda
     */
    public function index(Request $request)
    {
        $filtro = $request->input('buscar', '');

        $clientes = Cliente::when($filtro, fn($q) => $q->buscar($filtro))
            ->withCount('direccionesActivas')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends(['buscar' => $filtro]);

        return view('clientes.index', compact('clientes', 'filtro'));
    }

    /**
     * Datos de un cliente (JSON para AJAX)
     */
    public function show(Cliente $cliente)
    {
        return response()->json($cliente->only(['id', 'telefono', 'nombre', 'notas']));
    }

    /**
     * Crear cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'telefono' => ['required', 'string', 'max:15', 'unique:clientes,telefono'],
            'nombre'   => ['nullable', 'string', 'max:100'],
            'notas'    => ['nullable', 'string', 'max:1000'],
        ], [
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.unique'   => 'Ya existe un cliente con este teléfono.',
            'nombre.max'        => 'El nombre no puede exceder 100 caracteres.',
        ]);

        $cliente = Cliente::create([
            'telefono' => $request->telefono,
            'nombre'   => $request->nombre ?: 'Cliente ' . $request->telefono,
            'notas'    => $request->notas,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => false,
                'mensaje' => 'Cliente registrado correctamente.',
                'cliente' => $cliente,
            ]);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => ['nullable', 'string', 'max:100'],
            'notas'  => ['nullable', 'string', 'max:1000'],
        ]);

        $cliente->update($request->only(['nombre', 'notas']));

        if ($request->expectsJson()) {
            return response()->json(['error' => false, 'mensaje' => 'Cliente actualizado correctamente.']);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Buscar por teléfono (AJAX - usado desde servicios)
     */
    public function buscarPorTelefono(Request $request)
    {
        $telefono = $request->input('telefono', '');

        if (empty($telefono)) {
            return response()->json(['error' => true, 'mensaje' => 'Teléfono no proporcionado.']);
        }

        $cliente = Cliente::where('telefono', $telefono)->first();

        if ($cliente) {
            $direcciones = $cliente->direccionesActivas()->limit(30)->get();

            return response()->json([
                'error' => false,
                'cliente_existe' => true,
                'cliente' => $cliente->only(['id', 'nombre', 'telefono']),
                'direcciones' => $direcciones,
            ]);
        }

        return response()->json([
            'error' => false,
            'cliente_existe' => false,
            'telefono' => $telefono,
        ]);
    }

    /**
     * Autocompletado (AJAX)
     */
    public function autocompletar(Request $request)
    {
        $termino = $request->input('q', '');

        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $clientes = Cliente::buscar($termino)
            ->select('id', 'telefono', 'nombre')
            ->orderByDesc('ultima_actualizacion')
            ->limit(10)
            ->get();

        return response()->json($clientes);
    }

    /**
     * Crear cliente rápido (AJAX - desde módulo servicios)
     */
    public function crearRapido(Request $request)
    {
        $telefono = trim($request->input('telefono', ''));

        if (empty($telefono)) {
            return response()->json(['error' => true, 'mensaje' => 'El teléfono es obligatorio.']);
        }

        $existe = Cliente::where('telefono', $telefono)->first();

        if ($existe) {
            return response()->json([
                'error' => false,
                'mensaje' => 'Cliente ya existe.',
                'id' => $existe->id,
                'nombre' => $existe->nombre,
                'telefono' => $existe->telefono,
                'ya_existe' => true,
            ]);
        }

        $cliente = Cliente::create([
            'telefono' => $telefono,
            'nombre' => 'Cliente ' . $telefono,
        ]);

        return response()->json([
            'error' => false,
            'mensaje' => 'Cliente creado correctamente.',
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'telefono' => $cliente->telefono,
            'ya_existe' => false,
        ]);
    }

    /**
     * Historial de servicios del cliente (AJAX - carga parcial)
     */
    public function historial(Cliente $cliente)
    {
        $servicios = $cliente->servicios()
            ->with(['direccion:id,direccion', 'vehiculo:id,placa,numero_movil'])
            ->orderByDesc('fecha_solicitud')
            ->get();

        // Direcciones más frecuentes
        $direccionesFrecuentes = $servicios
            ->pluck('direccion.direccion')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(3);

        $stats = [
            'total' => $servicios->count(),
            'finalizados' => $servicios->where('estado', 'finalizado')->count(),
            'cancelados' => $servicios->where('estado', 'cancelado')->count(),
        ];

        return view('clientes._historial', compact('cliente', 'servicios', 'stats', 'direccionesFrecuentes'));
    }
}
