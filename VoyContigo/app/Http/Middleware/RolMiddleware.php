<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, string $rol): mixed
    {
        $usuario = auth('api')->user();

        if (!$usuario || $usuario->rol !== $rol) {
            return response()->json([
                'exito'  => false,
                'mensaje' => 'Acceso denegado. Se requiere rol: ' . $rol,
                'datos'   => null,
            ], 403);
        }

        return $next($request);
    }
}
