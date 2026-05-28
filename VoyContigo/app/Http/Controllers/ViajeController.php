<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\ViajeCompartidos;
use Illuminate\Http\Request;

class ViajeController extends Controller
{
    // POST /viajes
    public function crear(Request $request)
    {
        $datos = $request->validate([
            'route_id'        => 'required|exists:rutas,id',
            'trip_datetime'   => 'required|date',
            'seats_total'     => 'required|integer|min:1',
            'seats_available' => 'required|integer|min:0',
        ]);

        $idUsuario = auth('api')->id();
        if (!$idUsuario) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'No autorizado.',
                'datos'   => null,
            ], 401);
        }

        $datos['driver_user_id'] = $idUsuario;
        $datos['status']         = 'activo';

        $ruta               = Ruta::find($datos['route_id']);
        $datos['origin']  = $ruta->origin_text;
        $datos['destiny'] = $ruta->dest_text;

        $viaje = ViajeCompartidos::create($datos);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Viaje compartido creado correctamente',
            'datos'   => $viaje,
        ], 201);
    }

    // PUT /viajes/{viaje}
    public function actualizar(Request $request, ViajeCompartidos $viaje)
    {
        $request->validate([
            'driver_user_id'  => 'sometimes|exists:users,id',
            'route_id'        => 'sometimes|exists:rutas,id',
            'origin'          => 'sometimes|nullable|string',
            'destiny'         => 'sometimes|nullable|string',
            'trip_datetime'   => 'sometimes|date',
            'seats_total'     => 'sometimes|integer|min:1',
            'seats_available' => 'sometimes|integer|min:0',
            'status'          => 'sometimes|nullable|string',
        ]);

        $viaje->update($request->validated());

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Viaje compartido actualizado correctamente',
            'datos'   => $viaje,
        ], 200);
    }

    // DELETE /viajes/{viaje}
    public function eliminar(ViajeCompartidos $viaje)
    {
        $viaje->delete();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Viaje compartido eliminado correctamente',
            'datos'   => null,
        ], 200);
    }

    // GET /viajes/{viaje}
    public function obtener(ViajeCompartidos $viaje)
    {
        $viaje->load('reservas.usuario');
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Viaje obtenido correctamente',
            'datos'   => $viaje,
        ], 200);
    }

    // GET /viajes
    public function listar(Request $request)
    {
        $idUsuario = auth('api')->id();

        $viajes = ViajeCompartidos::with('conductor', 'ruta', 'reservas.usuario')
            ->when($idUsuario, fn($q) => $q->where('driver_user_id', '!=', $idUsuario))
            ->orderBy('trip_datetime', 'desc')
            ->get();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Listado de viajes obtenido correctamente',
            'datos'   => $viajes,
        ], 200);
    }

    // GET /viajes/buscar
    public function buscar(Request $request)
    {
        $idUsuario = auth('api')->id();

        $consulta = ViajeCompartidos::with('conductor', 'ruta', 'reservas.usuario')
            ->when($idUsuario, fn($q) => $q->where('driver_user_id', '!=', $idUsuario));

        if ($request->filled('origin')) {
            $consulta->where('origin', 'like', '%' . $request->origin . '%');
        }

        if ($request->filled('destiny')) {
            $consulta->where('destiny', 'like', '%' . $request->destiny . '%');
        }

        if ($request->filled('fecha')) {
            $consulta->whereDate('trip_datetime', $request->fecha);
        }

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Búsqueda realizada correctamente',
            'datos'   => $consulta->orderBy('trip_datetime', 'desc')->get(),
        ], 200);
    }
}
