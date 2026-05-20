<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ViajeCompartidosController;



// ─── Rutas protegidas con JWT (auth:api) ─────────────────────
Route::middleware('auth:api')->group(function () {
    // Rutas del usuario
    Route::get('/users/obtener_rutas', [RutaController::class, 'getRutasByUser']);
    Route::post('/users/crear_rutas', [RutaController::class, 'store']);
    Route::delete('/users/delete_rutas/{id}', [RutaController::class, 'destroy']);
});

// ─── Rutas públicas (Auth) ───────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Aliases sin prefijo /auth (usados por el frontend)
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/profile',   [AuthController::class, 'me'])->middleware('auth:api');

// ─── Rutas protegidas (Auth) ─────────────────────────────────
Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me',       [AuthController::class, 'me']);
});

//Endpoints POST:
Route::middleware('auth:api')->group(function () {
    // Endpoint 5: Crear alerta por ruta (POST)
    Route::post('/users/crear_alerta', [AlertaController::class, 'crearAlerta']);

// Endpoint 15 - Desactivar alerta por ruta
Route::post('/users/desactivar_alerta', [AlertaController::class, 'desactivar']);

// Endpoint 12: Crear reserva por usuario (POST)
Route::post('/users/crear_reserva', [ReservaController::class, 'crearReserva']);

// Endpoint 0: Crear usuario (POST)
Route::post('/users', [UserController::class, 'store']);

// Endpoint 24: Añadir a favoritos (POST)
Route::post('/users/agregarFavorito', [FavoritoController::class, 'agregarFavorito']);

// Endpoint 21: Crear viaje compartido (POST)
Route::post('/driver/crear_viaje', [ViajeCompartidosController::class, 'crearViaje']);


// Endpoint GET:

// Endpoint 1 - Info del usuario (GET)
Route::get('/users/usuario', [UserController::class, 'show']);

// Endpoint 3: Listado de todas las rutas (GET)
Route::get('/rutas', [RutaController::class, 'index']);

// Endpoint 17: Obtener datos de viaje compartido (GET)
Route::get('/users/obtener_viajecompartido', [ViajeCompartidosController::class, 'obtenerViaje']);

// Endpoint listarViajes
Route::get('/users/viajes_compartidos', [ViajeCompartidosController::class, 'listarViajes']);

// Endpoint 14: Todas las reservas de una ruta (GET)
Route::get('/driver/reservas', [ReservaController::class, 'reservasPorRuta']);

// Endpoint 2: Lista de usuarios (GET)
Route::get('/users/listaUsuarios', [UserController::class, 'listaUsuarios']);

// Endpoint 9: Obtener predicciones por ruta (GET)
Route::get('/users/obtener_predicciones', [RutaController::class, 'obtenerPredicciones']);

// Endpoint 16: Obtener la alerta del usuario (GET)
Route::get('/users/obtener_alerta', [AlertaController::class, 'obtenerAlertaUsuario']);

// Endpoint 26: Listar favoritos del usuario autenticado (GET)
Route::get('/users/listar_favoritos', [FavoritoController::class, 'listarFavoritos']);

// Endpoint 10: Obtener reservas por usuario (GET)
Route::get('/users/obtener_reservas', [ReservaController::class, 'obtenerReservasPorUsuario']);

//Endpoint listarViajes
Route::get('/user/listar_viajes', [ViajeCompartidosController::class, 'listar']);

// Endpoint buscarViajes con filtros (origin, destiny, fecha)
Route::get('/user/buscar_viajes', [ViajeCompartidosController::class, 'buscarViajes']);

//Endpoints PUT:

// Endpoint 22 - Aumentar puntos al usuario
Route::put('/user/aumentar_puntos_usuario', [UserController::class, 'aumentarPuntos']);

// Endpoint 7: Actualizar rutas (PUT)
Route::put('/users/update_rutas/{id}', [RutaController::class, 'update']);

// Endpoint 11: Actualizar reservas por usuario (PUT)
Route::put('/reservas/{id}', [ReservaController::class, 'update']);

// Endpoint 18: Editar datos de usuarios (Admin) (PUT)
Route::put('/admin/usuarios/{id}', [UserController::class, 'update']);

// Endpoint 19: Actualizar viaje compartido (PUT)
Route::put('/driver/actualizar_viaje', [ViajeCompartidosController::class, 'actualizarViaje']);

// Endpoint 23: Quitar puntos al usuario (PUT)
Route::put('/user/quitar_puntos_usuario', [UserController::class, 'quitarPuntoUsuarios']);
 Route::post('/users/update', [UserController::class, 'update']);

// Endpoint DELETE:

// Endpoint 13: Eliminar reserva por ID (DELETE)
Route::delete('/users/eliminar_reserva/{id}', [ReservaController::class, 'eliminarReserva']);

// Endpoint 25: Eliminar de favoritos (DELETE)
Route::delete('/favoritos', [FavoritoController::class, 'eliminarFavorito']);

// Endpoint 20: Eliminar viaje compartido (DELETE)
Route::delete('/driver/eliminar_viaje/{idviaje}', [ViajeCompartidosController::class, 'eliminarViaje']);

// Endpoint 4: Eliminar usuario (DELETE)
Route::delete('/users/eliminar/{id}', [UserController::class, 'eliminarUsuario']);

});

/*
|--------------------------------------------------------------------------
| AUTH PÚBLICO
|--------------------------------------------------------------------------
| Login y registro
*/

// ─── Rutas públicas (Auth) ───────────────────────────────────
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);
});

// Aliases sin prefijo /auth (usados por el frontend)
Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);



/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
| Requieren iniciar sesión
*/

Route::middleware('auth:api')->group(function () {

    // ─── Rutas protegidas (Auth) ─────────────────────────────────
    Route::prefix('auth')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/refresh', [AuthController::class, 'refresh']);

        Route::get('/me', [AuthController::class, 'me']);
    });

    // Endpoint Profile
    Route::get('/profile', [AuthController::class, 'me']);



    //Endpoints POST:

    // Endpoint 15 - Desactivar alerta por ruta
    Route::post('/users/desactivar_alerta', [AlertaController::class, 'desactivar']);

    // Endpoint 12: Crear reserva por usuario (POST)
    Route::post('/users/crear_reserva', [ReservaController::class, 'crearReserva']);

    // Endpoint 0: Crear usuario (POST)
    Route::post('/users', [UserController::class, 'store']);

    // Endpoint 24: Añadir a favoritos (POST)
    Route::post('/users/agregarFavorito', [FavoritoController::class, 'agregarFavorito']);

    // Endpoint 21: Crear viaje compartido (POST)
    Route::post('/driver/crear_viaje', [ViajeCompartidosController::class, 'crearViaje']);

    // Endpoint 6 Postear las rutas (POST)
    Route::post('/users/crear_rutas', [RutaController::class, 'store']);



    // Endpoint GET:

    // Endpoint 1 - Info del usuario (GET)
    Route::get('/users/usuario', [UserController::class, 'show']);

    // Endpoint 5 - Rutas guardadas del usuario
    Route::get('/users/obtener_rutas', [RutaController::class, 'getRutasByUser']);

    // Endpoint 2: Lista de usuarios (GET)
    Route::get('/users/listaUsuarios', [UserController::class, 'listaUsuarios']);

    // Endpoint 9: Obtener predicciones por ruta (GET)
    Route::get('/users/obtener_predicciones', [RutaController::class, 'obtenerPredicciones']);

    // Endpoint 16: Obtener la alerta del usuario (GET)
    Route::get('/users/obtener_alerta', [AlertaController::class, 'obtenerAlertaUsuario']);

    // Endpoint 10: Obtener reservas por usuario (GET)
    Route::get('/users/obtener_reservas', [ReservaController::class, 'obtenerReservasPorUsuario']);



    //Endpoints PUT:

    // Endpoint 22 - Aumentar puntos al usuario
    Route::put('/user/aumentar_puntos_usuario', [UserController::class, 'aumentarPuntos']);

    // Endpoint 7: Actualizar rutas (PUT)
    Route::put('/users/update_rutas/{id}', [RutaController::class, 'update']);

    // Endpoint 11: Actualizar reservas por usuario (PUT)
    Route::put('/reservas/{id}', [ReservaController::class, 'update']);

    // Endpoint 18: Editar datos de usuarios (Admin) (PUT)
    Route::put('/admin/usuarios/{id}', [UserController::class, 'update']);

    // Endpoint 19: Actualizar viaje compartido (PUT)
    Route::put('/driver/actualizar_viaje', [ViajeCompartidosController::class, 'actualizarViaje']);

    // Endpoint 23: Quitar puntos al usuario (PUT)
    Route::put('/user/quitar_puntos_usuario', [UserController::class, 'quitarPuntoUsuarios']);



    // Endpoint DELETE:

    // Endpoint 8 - Eliminar ruta
    Route::delete('/users/delete_rutas/{id}', [RutaController::class, 'destroy']);

    // Endpoint 13: Eliminar reserva por ID (DELETE)
    Route::delete('/users/eliminar_reserva/{id}', [ReservaController::class, 'eliminarReserva']);

    // Endpoint 25: Eliminar de favoritos (DELETE)
    Route::delete('/favoritos', [FavoritoController::class, 'eliminarFavorito']);

    // Endpoint 20: Eliminar viaje compartido (DELETE)
    Route::delete('/driver/eliminar_viaje/{idviaje}', [ViajeCompartidosController::class, 'eliminarViaje']);

    // Endpoint 4: Eliminar usuario (DELETE)
    Route::delete('/users/eliminar/{id}', [UserController::class, 'eliminarUsuario']);
});