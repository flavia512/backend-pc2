<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Ruta;
use App\Models\ViajeCompartidos;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // PUT /reservas/{reserva}
    public function actualizar(Request $request, Reserva $reserva)
    {
        $datos = $request->validate([
            'seats'  => 'sometimes|integer|min:1',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        $reserva->update($datos);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reserva actualizada correctamente',
            'datos'   => $reserva,
        ], 200);
    }

    // POST /reservas
    public function crear(Request $request)
    {
        $datos = $request->validate([
            'trip_id' => 'required|exists:viaje_compartidos,id',
            'seats'   => 'required|integer|min:1',
            'status'  => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        $datos['user_id'] = auth()->id();
        $datos['status']  = $datos['status'] ?? 'pending';

        $viaje = ViajeCompartidos::find($datos['trip_id']);

        if ($viaje->seats_available < $datos['seats']) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'No hay suficientes asientos disponibles',
                'datos'   => null,
            ], 422);
        }

        $reserva = Reserva::create($datos);
        $viaje->decrement('seats_available', $datos['seats']);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reserva creada correctamente',
            'datos'   => $reserva,
        ], 201);
    }

    // DELETE /reservas/{reserva}
    public function eliminar(Reserva $reserva)
    {
        $viaje = ViajeCompartidos::find($reserva->trip_id);
        $reserva->delete();

        if ($viaje) {
            $viaje->increment('seats_available', $reserva->seats);
        }

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reserva cancelada correctamente',
            'datos'   => null,
        ], 200);
    }

    // GET /reservas — reservas del usuario autenticado
    public function listar(Request $request)
    {
        $reservas = Reserva::with([
                'viaje:id,origin,destiny,trip_datetime,seats_total,seats_available,status,driver_user_id',
                'viaje.conductor:id,full_name,email',
            ])
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reservas obtenidas correctamente',
            'datos'   => $reservas,
        ], 200);
    }

    // GET /admin/reservas/ruta?ruta_id=X
    public function listarPorRuta(Request $request)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id',
        ]);

        $ruta = Ruta::find($request->ruta_id);
        $idsViajes = $ruta->viajes()->pluck('id');
        $reservas = Reserva::whereIn('trip_id', $idsViajes)->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reservas de la ruta obtenidas correctamente',
            'datos'   => $reservas,
        ], 200);
    }
}
