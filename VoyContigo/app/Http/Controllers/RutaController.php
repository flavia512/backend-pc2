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

    // ENDPOINT 3: Listado de todas las rutas
    public function index()
    {
        // Traemos todas las rutas junto con la info del usuario que las creó
        $rutas = Ruta::with('usuario')->get(); 
        
        return response()->json([
            'success' => true,
            'data' => $rutas
        ], 200);
    }

    // ENDPOINT 7: Actualizar rutas
    public function update(Request $request, $id)
    {
        $ruta = Ruta::find($id);

        if (!$ruta) {
            return response()->json(['success' => false, 'message' => 'Ruta no encontrada'], 404);
        }

        // Actualizamos con los datos que vengan en el body de la petición
        $ruta->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruta actualizada correctamente',
            'data' => $ruta
        ], 200);
    }
}
