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
}
