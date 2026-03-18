<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        // Intentar login con username o email
        $credentials = $request->getCredentials();

        if (!Auth::attempt($credentials, $request->boolean('recordar'))) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Credenciales incorrectas o usuario inactivo.']);
        }

        $usuario = Auth::user();

        // Verificar que el usuario esté activo
        if ($usuario->estado !== 'activo') {
            Auth::logout();
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Su cuenta está inactiva. Contacte al administrador.']);
        }

        // Actualizar último acceso
        $usuario->update(['ultimo_acceso' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
