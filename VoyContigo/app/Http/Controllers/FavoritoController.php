<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
    // DELETE /favoritos?route_id=Y
    public function eliminar(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:rutas,id',
        ]);

        $eliminado = Favorito::where('user_id', auth()->id())
            ->where('route_id', $request->query('route_id'))
            ->delete();

        if (!$eliminado) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'Favorito no encontrado',
                'datos'   => null,
            ], 404);
        }

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Favorito eliminado correctamente',
            'datos'   => null,
        ], 200);
    }

    // POST /favoritos
    public function agregar(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:rutas,id',
        ]);

        $duplicado = Favorito::where('user_id', auth()->id())
            ->where('route_id', $request->route_id)
            ->first();

        if ($duplicado) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'Esta ruta ya está en favoritos',
                'datos'   => null,
            ], 409);
        }

        $favorito = Favorito::create([
            'user_id'  => auth()->id(),
            'route_id' => $request->route_id,
        ]);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Ruta añadida a favoritos',
            'datos'   => $favorito,
        ], 201);
    }

    // GET /favoritos — favoritos del usuario autenticado
    public function listar(Request $request)
    {
        $favoritos = Favorito::with('ruta')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Favoritos obtenidos correctamente',
            'datos'   => $favoritos,
        ], 200);
    }
}

