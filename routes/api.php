<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;

// API de vehículos
Route::get('/ping', fn() => response()->json(['pong' => true]));
Route::get('/vehicles', [VehicleController::class, 'index']);   // Listar
Route::post('/vehicles', [VehicleController::class, 'store']);  // Insertar JSON
Route::get('/vehicles/{vehiculo}', [VehicleController::class, 'show']); // Ver detalle