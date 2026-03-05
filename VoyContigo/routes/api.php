<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\AlertaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint 1 - Info del usuario
Route::get('/users/usuario', [UserController::class, 'show']);

// Endpoint 5 - Rutas guardadas del usuario
Route::get('/users/obtener_rutas', [RutaController::class, 'getRutasByUser']);

// Endpoint 8 - Eliminar ruta
Route::delete('/users/delete_rutas/{id}', [RutaController::class, 'destroy']);

// Endpoint 15 - Desactivar alerta por ruta
Route::post('/users/desactivar_alerta', [AlertaController::class, 'desactivar']);

// Endpoint 22 - Aumentar puntos al usuario
Route::put('/user/aumentar_puntos_usuario', [UserController::class, 'aumentarPuntos']);