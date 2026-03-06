<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

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
    // POST api/users/crear_reserva.php?iduser=10
    public function crearReserva(Request $request)
    {
        $userId = $request->query('iduser');
        $data = $request->all();
        $data['user_id'] = $userId;

        // Validar campos requeridos
        $validator = \Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'trip_id' => 'required|exists:viaje_compartidos,id',
            'seats' => 'required|integer|min:1',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $reserva = Reserva::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada correctamente',
            'data' => $reserva
        ], 201);
    }

    // ENDPOINT 13: Eliminar reservas por usuario
    // DELETE api/users/eliminar_reserva.php?iduser=10&idreserva=10
    public function eliminarReserva(Request $request)
    {
        $userId = $request->query('iduser');
        $reservaId = $request->query('idreserva');

        $reserva = Reserva::where('id', $reservaId)
            ->where('user_id', $userId)
            ->first();

        if (!$reserva) {
            return response()->json([
                'success' => false,
                'message' => 'Reserva no encontrada para este usuario'
            ], 404);
        }

        $reserva->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reserva eliminada correctamente'
        ], 200);
    }
}
