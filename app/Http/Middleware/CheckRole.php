<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (!$usuario || !in_array($usuario->rol, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => true, 'mensaje' => 'No tiene permisos para esta acción'], 403);
            }
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        if ($usuario->estado !== 'activo') {
            auth()->logout();
            return redirect()->route('login')->withErrors(['username' => 'Su cuenta está inactiva.']);
        }

        return $next($request);
    }
}
