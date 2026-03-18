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
    public function index()
    {
        $usuarios = Usuario::orderBy('created_at', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
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
