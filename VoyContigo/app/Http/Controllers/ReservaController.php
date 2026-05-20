<?php

namespace App\Http\Controllers;
use App\Models\Ruta;
use App\Models\ViajeCompartidos;


use App\Models\Reserva;
//use App\Models\ViajeCompartidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    // ENDPOINT 11: Actualizar reservas por usuario
    public function update(Request $request, $id)
    {
        // Llave primaria de reserva es reserva_id,
        // pero Laravel con find() lo maneja porque lo definimos en el Modelo.
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        }

        $reserva->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada correctamente',
            'data' => $reserva
        ], 200);
    }

    // ENDPOINT 12: Crear reserva por usuario
    // POST api/users/crear_reserva.php?user_id=10
    public function crearReserva(Request $request)
    {
        // Verificar si el usuario ha iniciado sesión
        if (!$request->user()) {

            return response()->json([
                'success' => false,
                'message' => 'Para reservar un viaje tienes que iniciar sesión'
            ], 401);
        }

        $validator = \Validator::make($request->all(), [

            'trip_id' => 'required|exists:viaje_compartidos,id',

            'seats' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $validated = $validator->validated();

        $reserva = DB::transaction(function () use ($validated, $user) {
            $viaje = ViajeCompartidos::where('id', $validated['trip_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($viaje->driver_user_id === $user->id) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'No puedes reservar tu propio viaje'
                ], 422));
            }

            $reservaExistente = Reserva::where('user_id', $user->id)
                ->where('trip_id', $viaje->id)
                ->exists();

            if ($reservaExistente) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Ya tienes una reserva para este viaje'
                ], 422));
            }

            if ($viaje->seats_available < $validated['seats']) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'No quedan suficientes asientos disponibles'
                ], 422));
            }

            $reserva = Reserva::create([
                'user_id' => $user->id,
                'trip_id' => $viaje->id,
                'seats' => $validated['seats'],
                'status' => 'pending',
            ]);

            $viaje->decrement('seats_available', $validated['seats']);

            return $reserva->load(['usuario', 'viaje.conductor']);
        });
        $viaje = ViajeCompartidos::find($data['trip_id']);

        if ($viaje->seats_available < $data['seats']) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficientes asientos disponibles'
            ], 422);
        }

        $reserva = Reserva::create($data);

        $viaje->decrement('seats_available', $data['seats']);

        return response()->json([

            'success' => true,

            'message' => 'Reserva creada correctamente',

            'data' => $reserva

        ], 201);
    }

    // ENDPOINT 13: Eliminar reserva por ID
    // DELETE api/users/eliminar_reserva/{id}
    public function eliminarReserva($id)
    {
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada'
            ], 404);
        }


        DB::transaction(function () use ($reserva) {
            $viaje = ViajeCompartidos::where('id', $reserva->trip_id)
                ->lockForUpdate()
                ->first();

            if ($viaje) {
                $viaje->increment('seats_available', $reserva->seats);
            }

            $reserva->delete();
        });

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
    // ENDPOINT 10 - GET /api/users/crearreservas?user_id=10
    public function obtenerReservasPorUsuario(Request $request){
        $user_id = $request->user()?->id ?? $request->query('user_id');

        if (!$user_id) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El parámetro user_id es obligatorio'
            ], 400);
        }

        $reservas = \App\Models\Reserva::with(['viaje.conductor', 'usuario'])
            ->where('user_id', $user_id)
            ->latest()
            ->get();

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
        $reservas = Reserva::with(['usuario', 'viaje'])
            ->whereIn('trip_id', $viajesIds)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservas
        ]);
    }
}
