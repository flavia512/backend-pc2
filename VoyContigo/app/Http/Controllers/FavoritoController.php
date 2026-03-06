<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
   // Endpoint 25: Eliminar de favoritos
    public function destroy($id)
    {
        $favorito = Favorito::find($id);

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
