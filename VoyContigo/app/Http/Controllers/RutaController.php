<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    // ENDPOINT 5 - GET /api/users/obtener_rutas?iduser=10
    public function getRutasByUser(Request $request) {
        $rutas = Ruta::where('user_id', $request->query('iduser'))->get();

        if ($rutas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron rutas'], 404);
        }

        return response()->json($rutas, 200);
    }

    // ENDPOINT 8 - DELETE /api/users/delete_rutas/{id}
    public function destroy($id) {
        $ruta = Ruta::find($id);

        if (!$ruta) {
            return response()->json(['message' => 'Ruta no encontrada'], 404);
        }

        $ruta->delete();

        return response()->json(['message' => 'Ruta eliminada correctamente'], 200);
    }
}
