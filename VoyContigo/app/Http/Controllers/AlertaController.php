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
}
