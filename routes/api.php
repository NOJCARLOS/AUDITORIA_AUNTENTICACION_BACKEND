<?php

// routes/api.php
use App\Http\Controllers\Auth\GoogleController;

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// Rutas protegidas por token (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', fn(\Illuminate\Http\Request $r) => $r->user());
    // tus vehículos:
    Route::get('/vehicles', [\App\Http\Controllers\Api\VehicleController::class, 'index']);
    Route::post('/vehicles', [\App\Http\Controllers\Api\VehicleController::class, 'store']);
    Route::get('/vehicles/{vehicle}', [\App\Http\Controllers\Api\VehicleController::class, 'show']);
});
