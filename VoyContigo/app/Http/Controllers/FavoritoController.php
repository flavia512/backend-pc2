<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
   // Endpoint 25: Eliminar de favoritos
    // DELETE /api/favoritos/{route_id}?user_id=X
    public function destroy(Request $request, $routeId)
    {
        $userId = $request->query('user_id');

        $favorito = Favorito::where('user_id', $userId)
            ->where('route_id', $routeId)
            ->first();

        if (!$favorito) {
            return response()->json(['success' => false, 'message' => 'Favorito no encontrado'], 404);
        }

        $favorito->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ruta eliminada de tus favoritos'
        ], 200);
    }
}
