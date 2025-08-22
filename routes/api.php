<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\AuthController;

// Salud (pública)
Route::get('/ping', fn () => response()->json(['pong' => true]));

// ---- Google OAuth (redirección completa) ----
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// ---- Auth local (email/contraseña) ----
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// ---- Rutas protegidas con Sanctum ----
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', function (Request $r) {
        return $r->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Vehículos
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
});

