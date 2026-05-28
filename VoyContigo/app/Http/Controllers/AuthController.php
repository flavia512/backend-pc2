<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registro(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:255',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $usuario = User::create([
            'email'         => $request->email,
            'full_name'     => $request->full_name,
            'password_hash' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => $usuario,
        ], 201);
    }

    public function iniciarSesion(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credenciales = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (!$token = auth('api')->attempt($credenciales)) {
            return response()->json([
                'exito'   => false,
                'mensaje' => 'Credenciales incorrectas',
                'datos'   => null,
            ], 401);
        }

        return $this->responderConToken($token);
    }

    // GET /auth/yo
    public function yo()
    {
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Perfil obtenido correctamente',
            'datos'   => auth('api')->user(),
        ]);
    }

    // POST /auth/logout
    public function cerrarSesion()
    {
        auth('api')->logout();
        return response()->json([
            'exito'   => true,
            'mensaje' => 'Sesión cerrada correctamente',
            'datos'   => null,
        ]);
    }

    // POST /auth/refrescar
    public function refrescar()
    {
        return $this->responderConToken(auth('api')->refresh());
    }

    protected function responderConToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => auth('api')->user(),
        ]);
    }
}
