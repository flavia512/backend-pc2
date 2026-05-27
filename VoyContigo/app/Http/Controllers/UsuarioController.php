<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    // GET /usuarios/yo
    public function obtenerPerfil()
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'Usuario no autenticado',
                'datos'   => null,
            ], 401);
        }
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Perfil obtenido correctamente',
            'datos'   => $usuario,
        ], 200);
    }

    // PUT /usuarios/yo
    public function actualizarPerfil(Request $request)
    {
        $usuario = auth()->user();
        $validados = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:users,email,' . $usuario->id,
        ]);

        $usuario->update($validados);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Perfil actualizado correctamente',
            'datos'   => $usuario,
        ], 200);
    }

    // PUT /usuarios/puntos/aumentar
    public function aumentarPuntos(Request $request)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $usuario = auth()->user();
        $usuario->puntos += $request->cantidad;
        $usuario->save();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Puntos actualizados correctamente',
            'datos'   => ['puntos_totales' => $usuario->puntos],
        ], 200);
    }

    // PUT /usuarios/puntos/quitar
    public function quitarPuntos(Request $request)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $usuario = auth()->user();
        $usuario->puntos = max(0, $usuario->puntos - $request->cantidad);
        $usuario->save();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Puntos descontados correctamente',
            'datos'   => $usuario,
        ], 200);
    }

    // PUT /admin/usuarios/{usuario}
    public function actualizar(Request $request, User $usuario)
    {
        $validados = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:users,email,' . $usuario->id,
            'is_active' => 'sometimes|boolean',
            'rol'       => 'sometimes|string|in:admin,user',
        ]);

        $usuario->update($validados);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Datos del usuario actualizados correctamente',
            'datos'   => $usuario,
        ], 200);
    }

    // DELETE /admin/usuarios/{usuario}
    public function eliminar(User $usuario)
    {
        $usuario->delete();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Usuario eliminado correctamente',
            'datos'   => null,
        ], 200);
    }

    // GET /admin/usuarios
    public function listarTodos()
    {
        $usuarios = User::all();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Listado de usuarios obtenido correctamente',
            'datos'   => $usuarios,
        ], 200);
    }

    // POST /admin/usuarios
    public function crear(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:255',
            'password'  => 'required|string|min:6',
            'is_active' => 'sometimes|boolean',
            'puntos'    => 'sometimes|integer|min:0',
            'rol'       => 'sometimes|string|in:admin,user',
        ]);

        $usuario = User::create([
            'email'         => $request->email,
            'full_name'     => $request->full_name,
            'password_hash' => bcrypt($request->password),
            'puntos'        => $request->puntos ?? 0,
            'is_active'     => $request->is_active ?? true,
            'rol'           => $request->rol ?? 'user',
            'last_login_at' => null,
        ]);

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Usuario creado correctamente',
            'datos'   => $usuario,
        ], 201);
    }

    // GET /admin/estadisticas
    public function estadisticas()
    {
        $total   = User::count();
        $admins  = User::where('rol', 'admin')->count();
        $activos = User::where('is_active', true)->count();

        return response()->json([
            'exito'   => true,
            'mensaje' => 'Estadísticas obtenidas correctamente',
            'datos'   => compact('total', 'admins', 'activos'),
        ], 200);
    }
}
