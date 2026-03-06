<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\ReservaController;

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

// Endpoint 3: Listado de todas las rutas (GET)
Route::get('/rutas', [RutaController::class, 'index']);

// Endpoint 7: Actualizar rutas (PUT)
Route::put('/rutas/{id}', [RutaController::class, 'update']);

// Endpoint 11: Actualizar reservas por usuario (PUT)
Route::put('/reservas/{id}', [ReservaController::class, 'update']);

// Endpoint 12: Crear reserva por usuario (POST)
Route::post('/users/crear_reserva', [ReservaController::class, 'crearReserva']);

// Endpoint 13: Eliminar reserva por usuario (DELETE)
Route::delete('/users/eliminar_reserva', [ReservaController::class, 'eliminarReserva']);

// Endpoint 18: Editar datos de usuarios (Admin) (PUT)
Route::put('/admin/usuarios/{id}', [UserController::class, 'update']);

// Endpoint 25: Eliminar de favoritos (DELETE)
Route::delete('/favoritos/{id}', [FavoritoController::class, 'destroy']);