<?php

namespace App\Http\Controllers;

use App\Models\ViajeCompartidos;
use Illuminate\Http\Request;

class ViajeCompartidosController extends Controller
{
    // ENDPOINT 20: Actualizar datos de viaje compartido
    // PUT api/driver/actualizar_viaje.php?idviaje=10
    public function actualizarViaje(Request $request)
    {
        $viajeId = $request->query('idviaje');
        $viaje = ViajeCompartidos::find($viajeId);

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

        $viaje = ViajeCompartidos::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Viaje compartido creado correctamente',
            'data' => $viaje
        ], 201);
    }

    // ENDPOINT: ENDPOINT 17:  Obtener datos de viaje compartido
    // GET api/users/obtener_viajecompartido?idviaje=10
    public function obtenerViaje(Request $request)
    {
        $viaje = ViajeCompartidos::find($request->query('idviaje'));

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje compartido no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $viaje
        ], 200);
    }

    // ENDPOINT 20: Eliminar viaje compartido
    // DELETE api/driver/eliminar_viaje?idviaje=10
    public function eliminarViaje(Request $request)
    {
        $viaje = ViajeCompartidos::find($request->query('idviaje'));

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje compartido no encontrado'
            ], 404);
        }

        $viaje->delete();

        return response()->json([
            'success' => true,
            'message' => 'Viaje compartido eliminado correctamente'
        ], 200);
    }
}
