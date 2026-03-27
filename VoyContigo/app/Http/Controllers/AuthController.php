<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:255',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'email'         => $request->email,
            'full_name'     => $request->full_name,
            'password_hash' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Iniciar sesión y obtener token JWT.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Cerrar sesión (invalidar token).
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * Refrescar el token JWT.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Estructura de respuesta con token.
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
