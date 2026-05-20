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
        $request->validate([
            'seats'  => 'sometimes|integer|min:1',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        $reserva->update($request->validated());

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reserva actualizada correctamente',
            'datos'   => $reserva,
        ], 200);
    }

    // POST /reservas
    public function crear(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'trip_id' => 'required|exists:viaje_compartidos,id',
            'seats'   => 'required|integer|min:1',
            'status'  => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        $validated['status'] = $validated['status'] ?? 'pending';

        $viaje = ViajeCompartidos::find($validated['trip_id']);

        if ($viaje->seats_available < $validated['seats']) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'No hay suficientes asientos disponibles',
                'datos'   => null,
            ], 422);
        }

        $reserva = Reserva::create($validated);
        $viaje->decrement('seats_available', $validated['seats']);

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
        $reservas = Reserva::where('user_id', auth()->id())->get();

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
        $viajesIds = $ruta->viajes()->pluck('id');
        $reservas = Reserva::whereIn('trip_id', $viajesIds)->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Reservas de la ruta obtenidas correctamente',
            'datos'   => $reservas,
        ], 200);
    }
}

    
    

