<?php

namespace App\Http\Controllers;

use App\Models\Prediccion;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    // GET /rutas — rutas del usuario autenticado
    public function listarPorUsuario(Request $request)
    {
        $rutas = Ruta::where('user_id', auth()->id())->get();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Rutas obtenidas correctamente',
            'datos'   => $rutas,
        ], 200);
    }

    // DELETE /rutas/{ruta}
    public function eliminar(Ruta $ruta)
    {
        abort_if($ruta->user_id !== auth()->id(), 403, 'No autorizado');
        $ruta->delete();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Ruta eliminada correctamente',
            'datos'   => null,
        ], 200);
    }

    // GET /rutas/todas
    public function listarTodas()
    {
        $rutas = Ruta::with('usuario')->get();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Listado de rutas obtenido correctamente',
            'datos'   => $rutas,
        ], 200);
    }

    // PUT /rutas/{ruta}
    public function actualizar(Request $request, Ruta $ruta)
    {
        abort_if($ruta->user_id !== auth()->id(), 403, 'No autorizado');

        $request->validate([
            'nombre'       => 'sometimes|nullable|string|max:255',
            'origin_text'  => 'sometimes|string|max:255',
            'origin_lat'   => 'sometimes|numeric',
            'origin_lng'   => 'sometimes|numeric',
            'dest_text'    => 'sometimes|string|max:255',
            'dest_lat'     => 'sometimes|numeric',
            'dest_lng'     => 'sometimes|numeric',
            'arrival_time' => 'sometimes|nullable|date_format:H:i',
            'duration_min' => 'sometimes|nullable|integer|min:0',
            'hora_salida'  => 'sometimes|nullable|date_format:H:i',
            'pasa_por_m30' => 'sometimes|boolean',
        ]);

        $ruta->update($request->validated());
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Ruta actualizada correctamente',
            'datos'   => $ruta,
        ], 200);
    }

    // GET /predicciones?route_id=X
    public function listarPredicciones(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:rutas,id',
        ]);

        $predicciones = Prediccion::where('route_id', $request->route_id)->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Predicciones obtenidas correctamente',
            'datos'   => $predicciones,
        ], 200);
    }

    // POST /rutas
    public function crear(Request $request)
    {
        $request->validate([
            'nombre'       => 'nullable|string|max:255',
            'origin_text'  => 'required|string|max:255',
            'origin_lat'   => 'required|numeric',
            'origin_lng'   => 'required|numeric',
            'dest_text'    => 'required|string|max:255',
            'dest_lat'     => 'required|numeric',
            'dest_lng'     => 'required|numeric',
            'arrival_time' => 'nullable|date_format:H:i',
            'duration_min' => 'nullable|integer|min:0',
            'hora_salida'  => 'nullable|date_format:H:i',
            'pasa_por_m30' => 'nullable|boolean',
        ]);

        $ruta = Ruta::create([
            'user_id'      => auth()->id(),
            'nombre'       => $request->nombre ?? null,
            'origin_text'  => $request->origin_text,
            'origin_lat'   => $request->origin_lat,
            'origin_lng'   => $request->origin_lng,
            'dest_text'    => $request->dest_text,
            'dest_lat'     => $request->dest_lat,
            'dest_lng'     => $request->dest_lng,
            'arrival_time' => $request->arrival_time ?? null,
            'duration_min' => $request->duration_min ?? null,
            'hora_salida'  => $request->hora_salida ?? null,
            'pasa_por_m30' => $request->pasa_por_m30 ?? false,
        ]);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Ruta creada correctamente',
            'datos'   => $ruta,
        ], 201);
    }
}
