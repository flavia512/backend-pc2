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

    public function store(Request $request)
    {
        // Validación de los campos requeridos
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:255',
            'password'  => 'required|string|min:6',
            'is_active' => 'sometimes|boolean',
            'puntos'    => 'sometimes|integer|min:0',
        ]);

        // Crear el usuario en la base de datos
        $user = User::create([
            'email'         => $request->email,
            'full_name'     => $request->full_name,
            'password_hash' => bcrypt($request->password), // almacenar contraseña hasheada
            'puntos'        => $request->puntos ?? 0,
            'is_active'     => $request->is_active ?? true,
            'last_login_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data'    => $user
        ], 201);
    }
}
