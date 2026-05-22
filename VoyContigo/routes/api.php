<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ViajeController;

// ─── Rutas públicas de autenticación ─────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/registro', [AuthController::class, 'registro']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ─── Rutas públicas de lectura (sin autenticación) ─────────────
Route::get('/viajes/buscar', [ViajeController::class, 'buscar']);
Route::get('/viajes',        [ViajeController::class, 'listar']);
Route::get('/rutas/todas',   [RutaController::class,  'listarTodas']);

// ─── Rutas protegidas (usuario autenticado) ───────────────────
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/auth/logout',    [AuthController::class, 'logout']);
    Route::post('/auth/refrescar', [AuthController::class, 'refrescar']);
    Route::get('/auth/yo',         [AuthController::class, 'yo']);

    // Perfil propio
    Route::get('/usuarios/yo',              [UsuarioController::class, 'obtenerPerfil']);
    Route::put('/usuarios/yo',              [UsuarioController::class, 'actualizarPerfil']);
    Route::put('/usuarios/puntos/aumentar', [UsuarioController::class, 'aumentarPuntos']);
    Route::put('/usuarios/puntos/quitar',   [UsuarioController::class, 'quitarPuntos']);

    // Rutas (estáticas antes que paramétricas)
    Route::get('/rutas',           [RutaController::class, 'listarPorUsuario']);
    Route::post('/rutas',          [RutaController::class, 'crear']);
    Route::delete('/rutas/{ruta}', [RutaController::class, 'eliminar']);

    // Viajes (estáticas antes que paramétricas)
    Route::post('/viajes',              [ViajeController::class, 'crear']);
    Route::get('/viajes/{viaje}',       [ViajeController::class, 'obtener']);
    Route::put('/viajes/{viaje}',       [ViajeController::class, 'actualizar']);
    Route::delete('/viajes/{viaje}',    [ViajeController::class, 'eliminar']);

    // Reservas
    Route::get('/reservas',              [ReservaController::class, 'listar']);
    Route::post('/reservas',             [ReservaController::class, 'crear']);
    Route::put('/reservas/{reserva}',    [ReservaController::class, 'actualizar']);
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'eliminar']);

    // Alertas (estáticas antes que paramétricas)
    Route::put('/alertas/desactivar', [AlertaController::class, 'desactivar']);
    Route::get('/alertas',            [AlertaController::class, 'listar']);
    Route::post('/alertas',           [AlertaController::class, 'crear']);

    // Favoritos
    Route::get('/favoritos',    [FavoritoController::class, 'listar']);
    Route::post('/favoritos',   [FavoritoController::class, 'agregar']);
    Route::delete('/favoritos', [FavoritoController::class, 'eliminar']);

    // Predicciones
    Route::get('/predicciones',  [RutaController::class, 'listarPredicciones']);
    Route::post('/predicciones', [RutaController::class, 'guardarPrediccion']);
});

// ─── Rutas de administrador ────────────────────────────────────
Route::middleware(['auth:api', 'rol:admin'])->prefix('admin')->group(function () {
    Route::get('/usuarios',              [UsuarioController::class, 'listarTodos']);
    Route::post('/usuarios',             [UsuarioController::class, 'crear']);
    Route::put('/usuarios/{usuario}',    [UsuarioController::class, 'actualizar']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'eliminar']);
    Route::get('/reservas/ruta',         [ReservaController::class, 'listarPorRuta']);
    Route::get('/estadisticas',          [UsuarioController::class, 'estadisticas']);
});



