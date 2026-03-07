<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    // ENDPOINT 5 - GET /api/users/obtener_rutas?user_id=10
    public function getRutasByUser(Request $request) {
        $rutas = Ruta::where('user_id', $request->query('user_id'))->get();

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
    public function update(Request $request, $id) {
        $ruta = Ruta::find($id); if (!$ruta) {
            return response()->json(['success' => false, 'message' => 'Ruta no encontrada'], 404);
        } // Actualizamos con los datos que vengan en el body de la petición
        $ruta->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Ruta actualizada correctamente',
            'data' => $ruta ], 200);
    }
    // ENDPOINT 9 - Get /api/users/obtener_predicciones?route_id=10
    public function obtenerPredicciones(Request $request)
    {
        $route_id = $request->query('route_id');

        if (!$route_id) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El parámetro route_id es obligatorio'
            ], 400);
        }

        $predicciones = \App\Models\Prediccion::where('route_id', $route_id)->get();

        return response()->json([
            'ok' => true,
            'predicciones' => $predicciones
        ], 200);
    }
    // Endpoint 6: Crear rutas
    public function store(Request $request)
    {
        // Validación de los campos requeridos
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'origin_text' => 'required|string|max:255',
            'origin_lat'  => 'required|numeric',
            'origin_lng'  => 'required|numeric',
            'dest_text'   => 'required|string|max:255',
            'dest_lat'    => 'required|numeric',
            'dest_lng'    => 'required|numeric',
            'arrival_time'=> 'nullable|date_format:H:i:s',
            'duration_min'=> 'nullable|integer|min:0',
        ]);

        // Verificar que el usuario existe
        $user = User::findOrFail($request->user_id);

        // Crear la ruta
        $ruta = Ruta::create([
            'user_id'      => $request->user_id,
            'origin_text'  => $request->origin_text,
            'origin_lat'   => $request->origin_lat,
            'origin_lng'   => $request->origin_lng,
            'dest_text'    => $request->dest_text,
            'dest_lat'     => $request->dest_lat,
            'dest_lng'     => $request->dest_lng,
            'arrival_time' => $request->arrival_time ?? null,
            'duration_min' => $request->duration_min ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ruta creada correctamente',
            'data'    => $ruta
        ], 201);
    }
}
