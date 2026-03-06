<?php

namespace App\Http\Controllers;

use App\Models\ViajeCompartido;
use Illuminate\Http\Request;

class ViajeCompartidoController extends Controller
{
    // ENDPOINT 20: Actualizar datos de viaje compartido
    // PUT api/driver/actualizar_viaje.php?idviaje=10
    public function actualizarViaje(Request $request)
    {
        $viajeId = $request->query('idviaje');
        $viaje = ViajeCompartido::find($viajeId);

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje compartido no encontrado'
            ], 404);
        }

        $viaje->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Viaje compartido actualizado correctamente',
            'data' => $viaje
        ], 200);
    }

    // ENDPOINT 21: Crear viaje compartido
    // POST api/driver/crear_viaje.php
    public function crearViaje(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'driver_user_id' => 'required|exists:users,id',
            'route_id' => 'required|exists:rutas,id',
            'station_name' => 'nullable|string',
            'trip_datetime' => 'required|date',
            'seats_total' => 'required|integer|min:1',
            'seats_available' => 'required|integer|min:0',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $viaje = ViajeCompartido::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Viaje compartido creado correctamente',
            'data' => $viaje
        ], 201);
    }
}
