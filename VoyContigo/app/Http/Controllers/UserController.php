<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ENDPOINT 1 - GET /api/users/usuario?Iduser=10
    public function show(Request $request) {
        $user = User::find($request->query('Iduser'));

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    // ENDPOINT 22 - PUT /api/user/aumentar_puntos_usuario?cantidad=20
    public function aumentarPuntos(Request $request) {
        $request->validate([
            'Iduser'   => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        $user = User::find($request->query('Iduser'));

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->puntos += $request->query('cantidad');
        $user->save();

        return response()->json([
            'message' => 'Puntos actualizados correctamente',
            'puntos_totales' => $user->puntos
        ], 200);
    }

    // ENDPOINT 18: Editar datos de usuarios (Admin)
    public function update(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $usuario->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Datos del usuario actualizados',
            'data' => $usuario
        ], 200);
    }
    // ENDPOINT 4: Eliminar usuario con el ID especificado (DELETE)
    // DELETE api/admin/eliminarUsuarios/{id}
    public function eliminarUsuario($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ], 200);
    }
}
