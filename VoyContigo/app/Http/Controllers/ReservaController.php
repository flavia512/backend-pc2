<?php

namespace App\Http\Controllers;
use App\Models\Ruta;

use App\Models\Reserva;
use App\Models\ViajeCompartidos;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // ENDPOINT 11: Actualizar reservas por usuario
    public function actualizar(Request $request, Reserva $reserva)
    {
        $request->validate([
            'seats'  => 'sometimes|integer|min:1',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled',
        ]);

        $reserva->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada correctamente',
            'data' => $reserva
        ], 200);
    }

    // ENDPOINT 12: Crear reserva por usuario
    public function crearReserva(Request $request)
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
                'success' => false,
                'message' => 'No hay suficientes asientos disponibles'
            ], 422);
        }

        $reserva = Reserva::create($validated);

        $viaje->decrement('seats_available', $validated['seats']);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada correctamente',
            'data' => $reserva
        ], 201);
    }

    // ENDPOINT 13: Eliminar reserva por ID
    // DELETE api/users/eliminar_reserva/{reserva}
    public function eliminarReserva(Reserva $reserva)
    {
        $viaje = ViajeCompartidos::find($reserva->trip_id);

        $reserva->delete();

        if ($viaje) {
            $viaje->increment('seats_available', $reserva->seats);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva cancelada correctamente'
        ], 200);
    }
    // ENDPOINT 10 - GET /api/users/obtener_reservas
    public function obtenerReservasPorUsuario(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $reservas = Reserva::where('user_id', $request->user_id)->get();

        return response()->json([
            'ok' => true,
            'reservas' => $reservas
        ], 200);
    }

    // Endpoint 14: Obtener todas las reservas de una ruta
    public function reservasPorRuta(Request $request)
    {
        // Validación: necesitamos el id de la ruta
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id'
        ]);

        // Obtener la ruta
        $ruta = Ruta::find($request->ruta_id);

        // Obtener todos los viajes asociados a la ruta
        $viajesIds = $ruta->viajes()->pluck('id'); // ids de ViajeCompartidos

        // Obtener todas las reservas de esos viajes
        $reservas = Reserva::whereIn('trip_id', $viajesIds)->get();

        return response()->json([
            'success' => true,
            'data' => $reservas
        ]);
    }
}
