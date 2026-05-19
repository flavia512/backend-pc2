<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
   // Endpoint 25: Eliminar de favoritos
    // DELETE /api/favoritos/{route_id}?user_id=X
    public function eliminarFavorito(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'route_id' => 'required|exists:rutas,id',
        ]);

        $userId = $request->query('user_id');
        $routeId = $request->query('route_id');

        // Borrar usando where directamente
        $deleted = Favorito::where('user_id', $userId)
            ->where('route_id', $routeId)
            ->delete(); // devuelve 0 si no borró nada

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Favorito no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Favorito eliminado correctamente'
        ], 200);
    }
    
    public function agregarFavorito(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'route_id' => 'required|exists:rutas,id'
        ]);

        $userId = $request->user_id;
        $routeId = $request->route_id;

        // comprobar duplicado
        $favorito = Favorito::where('user_id', $userId)
            ->where('route_id', $routeId)
            ->first();

        if ($favorito) {
            return response()->json([
                'message' => 'Esta ruta ya está en favoritos'
            ], 409);
        }

        $favorito = Favorito::create([
            'user_id' => $userId,
            'route_id' => $routeId
        ]);

        return response()->json([
            'message' => 'Ruta añadida a favoritos',
            'data' => $favorito
        ], 201);
    }

    // Endpoint 26: Listar favoritos del usuario autenticado
    // GET /api/users/listar_favoritos
    public function listarFavoritos(Request $request)
    {
        $favoritos = Favorito::with('ruta')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'ok'        => true,
            'favoritos' => $favoritos,
        ], 200);
    }
}
