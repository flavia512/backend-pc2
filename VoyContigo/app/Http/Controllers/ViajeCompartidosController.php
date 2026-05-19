<?php

namespace App\Http\Controllers;

use App\Models\ViajeCompartidos;
use Illuminate\Http\Request;

class ViajeCompartidosController extends Controller
{
    protected function normalizarPayload(Request $request): array
    {
        $data = $request->all();

        if (empty($data['origin']) && !empty($data['station_name'])) {
            $data['origin'] = $data['station_name'];
        }

        return $data;
    }

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

        $data = $this->normalizarPayload($request);

        $validator = \Validator::make($data, [
            'driver_user_id' => 'sometimes|exists:users,id',
            'route_id' => 'sometimes|exists:rutas,id',
            'origin' => 'sometimes|string',
            'destiny' => 'nullable|string',
            'trip_datetime' => 'sometimes|date',
            'seats_total' => 'sometimes|integer|min:1',
            'seats_available' => 'sometimes|integer|min:0',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $viaje->update($validator->validated());

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
        $data = $request->all();

        $validator = \Validator::make($data, [
            'route_id' => 'required|exists:rutas,id', 
            'trip_datetime' => 'required|date',
            'seats_total' => 'required|integer|min:1',
            'seats_available' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();


        // Intentamos sacar el ID usando el guardia 'api' (típico en JWT) o el por defecto
        $userId = auth('api')->id() ?? auth()->id();

        // Si sigue siendo nulo, frenamos la petición ANTES de tocar la base de datos
        if (!$userId) {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado. Laravel no puede leer tu token JWT.'
            ], 401);
        }

        $validatedData['driver_user_id'] = $userId;
        $validatedData['status'] = 'activo';

        // Copiamos los textos de la ruta al viaje
        $rutaElegida = \App\Models\Ruta::find($validatedData['route_id']);
        $validatedData['origin'] = $rutaElegida->origin_text;
        $validatedData['destiny'] = $rutaElegida->dest_text;

        // Guardamos el viaje
        $viaje = ViajeCompartidos::create($validatedData);

        return response()->json(['success' => true, 'data' => $viaje], 201);
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
    public function eliminarViaje($idviaje)
    {
        $viaje = ViajeCompartidos::find($idviaje);

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
    
    public function listar(Request $request)
    {
<<<<<<< HEAD
        $viajes = ViajeCompartidos::with('conductor', 'ruta', 'reservas.usuario')->get();
=======
        $viajes = ViajeCompartidos::with('conductor', 'ruta', 'reservas')
            ->orderBy('trip_datetime', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $viajes]);
    }

    // ENDPOINT: Buscar viajes compartidos con filtros
    // GET api/driver/buscar_viajes?origin=X&destiny=Y&fecha=2026-05-02
    public function buscarViajes(Request $request)
    {
        $query = ViajeCompartidos::with('conductor', 'ruta', 'reservas');

        if ($request->filled('origin')) {
            $query->where('origin', 'like', '%' . $request->origin . '%');
        }

        if ($request->filled('destiny')) {
            $query->where('destiny', 'like', '%' . $request->destiny . '%');
        }

        if ($request->filled('fecha')) {
            $query->whereDate('trip_datetime', $request->fecha);
        }
>>>>>>> 45ae2367aea7adb36febd113736ca23f96cd0691

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('trip_datetime', 'desc')->get()
        ], 200);
    }
}
