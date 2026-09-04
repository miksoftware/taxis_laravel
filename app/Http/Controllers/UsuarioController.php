<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Http\Requests\Usuario\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $buscar = $request->input('buscar', '');
        $filtroRol = $request->input('rol', '');

        $metricas = [
            'total' => Usuario::count(),
            'admins' => Usuario::whereIn('rol', ['superadmin', 'administrador'])->count(),
            'operadores' => Usuario::where('rol', 'operador')->count(),
            'activos' => Usuario::where('estado', 'activo')->count(),
        ];

        $query = Usuario::orderBy('created_at', 'desc');

        if ($buscar) {
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('username', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        if ($filtroRol) {
            $query->where('rol', $filtroRol);
        }

        if ($perPage === 'todos' || $perPage == -1) {
            $totalCount = (clone $query)->count();
            $limit = max($totalCount, 1);
        } else {
            $limit = in_array((int)$perPage, [10, 20, 30, 50]) ? (int)$perPage : 10;
        }

        $usuarios = $query->paginate($limit)->appends($request->query());

        return view('usuarios.index', compact('usuarios', 'metricas', 'perPage', 'buscar', 'filtroRol'));
    }

    public function show(Usuario $usuario)
    {
        return response()->json($usuario->only([
            'id', 'nombre', 'apellidos', 'email', 'username', 'telefono', 'rol', 'estado',
        ]));
    }

    public function store(StoreUsuarioRequest $request)
    {
        Usuario::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password, // El cast 'hashed' del modelo lo encripta
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'estado' => 'activo',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        // No permitir editar superadmin protegido (cambiarle el rol)
        if ($usuario->es_protegido && $request->rol !== 'superadmin') {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede cambiar el rol del Super Administrador.');
        }

        $usuario->update($request->only([
            'nombre', 'apellidos', 'email', 'username', 'telefono', 'rol',
        ]));

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function cambiarEstado(Request $request, Usuario $usuario)
    {
        // No permitir cambiar estado del propio usuario
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puede cambiar su propio estado.');
        }

        // No permitir desactivar superadmin protegido
        if ($usuario->es_protegido) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede desactivar al Super Administrador.');
        }

        $request->validate(['estado' => 'required|in:activo,inactivo']);

        $usuario->update(['estado' => $request->estado]);

        $accion = $request->estado === 'activo' ? 'activado' : 'desactivado';
        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$accion} correctamente.");
    }

    public function resetPassword(ResetPasswordRequest $request, Usuario $usuario)
    {
        $usuario->update(['password' => $request->password]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Contraseña restablecida correctamente.');
    }
}
