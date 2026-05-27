<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    // POST /admin/configuracion/logo — solo admin
    public function subirLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Eliminar logo anterior si existe
        $anterior = Configuracion::where('clave', 'logo_url')->first();
        if ($anterior) {
            $oldPath = str_replace(url('storage') . '/', '', $anterior->valor);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $request->file('logo')->store('logos', 'public');
        $url  = url('storage/' . $path);

        Configuracion::updateOrCreate(
            ['clave' => 'logo_url'],
            ['valor' => $url]
        );

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Logo actualizado correctamente',
            'datos'   => ['url' => $url],
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
