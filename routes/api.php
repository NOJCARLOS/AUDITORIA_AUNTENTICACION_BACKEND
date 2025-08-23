<?php

// -------------------------------------------------------------------------
// Importación de clases y controladores necesarios para definir rutas API.
// -------------------------------------------------------------------------
use Illuminate\Support\Facades\Route;           // Facade para definir rutas en Laravel.
use Illuminate\Http\Request;                   // Permite acceder a datos de la petición en closures.
use App\Http\Controllers\Api\VehicleController; // Controlador para gestionar vehículos (API REST).
use App\Http\Controllers\Auth\GoogleController; // Controlador para autenticación con Google OAuth2.
use App\Http\Controllers\Auth\AuthController;   // Controlador para registro/login/logout con email/contraseña.

// -------------------------------------------------------------------------
// Ruta de "salud" o prueba básica de disponibilidad del API.
// - Endpoint: GET /api/ping
// - Respuesta: {"pong": true}
// -------------------------------------------------------------------------
Route::get('/ping', fn () => response()->json(['pong' => true]));

// -------------------------------------------------------------------------
// Rutas de autenticación mediante Google OAuth2 (públicas).
// - /auth/google/redirect : redirige al usuario hacia Google para login.
// - /auth/google/callback : procesa la respuesta de Google.
// -------------------------------------------------------------------------
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// -------------------------------------------------------------------------
// Rutas de autenticación local con email y contraseña (públicas).
// - /auth/register : registra un nuevo usuario.
// - /auth/login    : autentica un usuario existente y entrega token.
// -------------------------------------------------------------------------
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// -------------------------------------------------------------------------
// Grupo de rutas protegidas por middleware "auth:sanctum".
// Requieren un token válido emitido por Laravel Sanctum.
// -------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // ---------------------------------------------------------------------
    // Endpoint: GET /auth/me
    // Devuelve la información del usuario autenticado.
    // ---------------------------------------------------------------------
    Route::get('/auth/me', function (Request $r) {
        return $r->user(); // Retorna el usuario autenticado en formato JSON.
    });

    // ---------------------------------------------------------------------
    // Endpoint: POST /auth/logout
    // Invalida (revoca) el token actual del usuario.
    // ---------------------------------------------------------------------
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // ---------------------------------------------------------------------
    // Endpoints relacionados con la gestión de vehículos.
    // ---------------------------------------------------------------------

    // GET /vehicles: Lista todos los vehículos.
    Route::get('/vehicles', [VehicleController::class, 'index']);

    // POST /vehicles: Crea un nuevo vehículo.
    Route::post('/vehicles', [VehicleController::class, 'store']);

    // GET /vehicles/{vehicle}: Muestra un vehículo específico.
    // - {vehicle} es un parámetro de ruta, con binding automático al modelo.
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
});
