<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    // ENDPOINT 15 - POST /api/users/desactivar_alerta?idruta=10
    public function desactivar(Request $request) {
        $request->validate([
            'idruta' => 'required|exists:rutas,id',
        ]);

        $alertas = Alerta::where('route_id', $request->idruta)
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

    // ENDPOINT 16 - GET /api/users/obtener_alerta
    public function obtenerAlertaUsuario(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $alertas = Alerta::with('ruta')->where('user_id', $request->user_id)->get();

        return response()->json([
            'ok' => true,
            'alertas' => $alertas
        ], 200);
    }

}
