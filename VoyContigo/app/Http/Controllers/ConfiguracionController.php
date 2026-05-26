<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    // GET /configuracion/{clave} — pública
    public function mostrar($clave)
    {
        $config = Configuracion::where('clave', $clave)->first();

        if (!$config) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'Configuración no encontrada',
                'datos'   => null,
            ], 404);
        }

        return response()->json([
            'exito'   => true,
            'mensaje' => 'OK',
            'datos'   => $config,
        ]);
    }

    // PUT /admin/configuracion/{clave} — solo admin
    public function actualizar(Request $request, $clave)
    {
        $request->validate([
            'valor' => 'required|string|max:2048',
        ]);

        $config = Configuracion::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $request->valor]
        );

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Configuración actualizada correctamente',
            'datos'   => $config,
        ]);
    }
}
