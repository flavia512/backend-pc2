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
    // PUT api/conductor/actualizar_viaje?idviaje=10
    public function actualizarViaje(Request $request)
    {
        $request->validate([
            'idviaje'         => 'required|exists:viaje_compartidos,id',
            'driver_user_id'  => 'sometimes|exists:users,id',
            'route_id'        => 'sometimes|exists:rutas,id',
            'origin'          => 'sometimes|nullable|string',
            'destiny'         => 'sometimes|nullable|string',
            'trip_datetime'   => 'sometimes|date',
            'seats_total'     => 'sometimes|integer|min:1',
            'seats_available' => 'sometimes|integer|min:0',
            'status'          => 'sometimes|nullable|string',
        ]);

        $viaje = ViajeCompartidos::find($request->idviaje);
        $datos = collect($request->validated())->except('idviaje')->all();
        $viaje->update($datos);

        return response()->json([
            'success' => true,
            'message' => 'Viaje compartido actualizado correctamente',
            'data'    => $viaje
        ], 200);
    }

    // ENDPOINT 21: Crear viaje compartido
    // POST api/conductor/crear_viaje
    public function crearViaje(Request $request)
    {
        $validated = $request->validate([
            'route_id'        => 'required|exists:rutas,id',
            'trip_datetime'   => 'required|date',
            'seats_total'     => 'required|integer|min:1',
            'seats_available' => 'required|integer|min:0',
        ]);

        $userId = auth('api')->id() ?? auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Laravel no puede leer tu token JWT.'
            ], 401);
        }

        $validated['driver_user_id'] = $userId;
        $validated['status'] = 'activo';

        $rutaElegida = \App\Models\Ruta::find($validated['route_id']);
        $validated['origin'] = $rutaElegida->origin_text;
        $validated['destiny'] = $rutaElegida->dest_text;

        $viaje = ViajeCompartidos::create($validated);

        return response()->json(['success' => true, 'data' => $viaje], 201);
    }


    // ENDPOINT 17: Obtener datos de viaje compartido
    // GET api/users/obtener_viajecompartido?idviaje=10
    public function obtenerViaje(Request $request)
    {
        $request->validate([
            'idviaje' => 'required|exists:viaje_compartidos,id',
        ]);

        $viaje = ViajeCompartidos::find($request->idviaje);

        return response()->json([
            'success' => true,
            'data'    => $viaje
        ], 200);
    }

    // ENDPOINT 20: Eliminar viaje compartido
    // DELETE api/conductor/eliminar_viaje/{viaje}
    public function eliminarViaje(ViajeCompartidos $viaje)
    {
        $viaje->delete();

        return response()->json([
            'success' => true,
            'message' => 'Viaje compartido eliminado correctamente'
        ], 200);
    }
    
    public function listar(Request $request)
    {
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

        return response()->json([
            'success' => true,
            'data'    => $query->orderBy('trip_datetime', 'desc')->get()
        ], 200);
    }
}
