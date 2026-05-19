<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    // ENDPOINT 15 - POST /api/users/desactivar_alerta?idruta=10
    public function desactivar(Request $request) {
        $alertas = Alerta::where('route_id', $request->query('idruta'))
                         ->where('status', 'activa')
                         ->get();

        if ($alertas->isEmpty()) {
            return response()->json(['message' => 'No hay alertas activas para esta ruta'], 404);
        }

        foreach ($alertas as $alerta) {
            $alerta->status = 'inactiva';
            $alerta->save();
        }

        return response()->json(['message' => 'Alertas desactivadas correctamente'], 200);
    }
    // ENDPOINT 5 - POST /api/users/crear_alerta
    public function crearAlerta(Request $request)
    {
        $request->validate([
            'route_id'     => 'required|exists:rutas,id',
            'for_datetime' => 'required|date',
        ]);

        $alerta = Alerta::create([
            'user_id'      => auth()->id(),
            'route_id'     => $request->route_id,
            'for_datetime' => $request->for_datetime,
            'status'       => 'activa',
        ]);

        return response()->json([
            'ok'     => true,
            'alerta' => $alerta,
        ], 201);
    }

    // ENDPOINT 16 - GET /api/admin/obtener_alerta_usuario?user_id=10
    public function obtenerAlertaUsuario(Request $request)
    {
        $user_id = $request->query('user_id');

        if (!$user_id) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El parámetro user_id es obligatorio'
            ], 400);
        }

        $alertas = \App\Models\Alerta::with('ruta')->where('user_id', $user_id)->get();

        return response()->json([
            'ok' => true,
            'alertas' => $alertas
        ], 200);
    }

}
