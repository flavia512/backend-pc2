<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    // PUT /alertas/desactivar?idruta=X
    public function desactivar(Request $request)
    {
        $request->validate([
            'idruta' => 'required|exists:rutas,id',
        ]);

        $alertas = Alerta::where('route_id', $request->idruta)
                         ->where('status', 'activa')
                         ->get();

        if ($alertas->isEmpty()) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'No hay alertas activas para esta ruta',
                'datos'   => null,
            ], 404);
        }

        foreach ($alertas as $alerta) {
            $alerta->status = 'inactiva';
            $alerta->save();
        }

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Alertas desactivadas correctamente',
            'datos'   => null,
        ], 200);
    }

    // POST /alertas
    public function crear(Request $request)
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
            'exito'   => true,
            'mensaje' => 'Alerta creada correctamente',
            'datos'   => $alerta,
        ], 201);
    }

    // GET /alertas â€” alertas del usuario autenticado
    public function listar(Request $request)
    {
        $alertas = Alerta::with('ruta')->where('user_id', auth()->id())->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Alertas obtenidas correctamente',
            'datos'   => $alertas,
        ], 200);
    }
}