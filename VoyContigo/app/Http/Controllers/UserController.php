<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ENDPOINT 1 - GET /api/users/usuario?user_id=10
    public function mostrar() {
        $usuario = auth()->user();
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }
        return response()->json($usuario, 200);
    }

    // ENDPOINT 22 - PUT /api/user/aumentar_puntos_usuario?cantidad=20
    public function aumentarPuntos(Request $request) {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);
        $usuario = auth()->user();
        $usuario->puntos += $request->cantidad;
        $usuario->save();
        return response()->json([
            'message' => 'Puntos actualizados correctamente',
            'puntos_totales' => $usuario->puntos
        ], 200);
    }

    // ENDPOINT 18: Editar datos de usuarios (Admin)
    public function actualizar(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'is_active' => 'sometimes|boolean',
        ]);

        $usuario->update($request->only([
            'full_name',
            'email',
            'is_active'
        ]));

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
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ], 200);
    }
    // ENDPOINT 2 - Get/ api/admin/lista_usuarios
    public function listaUsuarios(){
        $usuarios = \App\Models\User::all();

        return response()->json([
            'ok' => true,
            'usuarios' => $usuarios
        ], 200);
    }
    // ENDPOINT 23 - PUT /api/user/quitar_punto_usuarios?user_id=10&cantidad=20
    public function quitarPuntoUsuarios(Request $request){
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);
        $usuario = auth()->user();
        $usuario->puntos = max(0, $usuario->puntos - $request->cantidad);
        $usuario->save();
        return response()->json([
            'ok' => true,
            'mensaje' => 'Puntos descontados correctamente',
            'usuario' => $usuario
        ], 200);
    }


    public function crear(Request $request)
    {
        // Validación de los campos requeridos
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:255',
            'password'  => 'required|string|min:6',
            'is_active' => 'sometimes|boolean',
            'puntos'    => 'sometimes|integer|min:0',
            'rol'       => 'sometimes|string|in:admin,user',
        ]);

        // Crear el usuario en la base de datos
        $usuario = User::create([
            'email'         => $request->email,
            'full_name'     => $request->full_name,
            'password_hash' => bcrypt($request->password), // almacenar contraseña hasheada
            'puntos'        => $request->puntos ?? 0,
            'is_active'     => $request->is_active ?? true,
            'rol'           => $request->rol ?? 'user',
            'last_login_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'data'    => $usuario
        ], 201);
    }
}
