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

        $alertas = \App\Models\Alerta::where('user_id', $user_id)->get();

        return response()->json([
            'ok' => true,
            'alertas' => $alertas
        ], 200);
    }

}
