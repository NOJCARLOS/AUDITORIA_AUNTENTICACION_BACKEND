<?php

use Illuminate\Support\Facades\Route;          // Se importa la clase Route para definir rutas de la API
use App\Http\Controllers\Api\VehicleController; // Se importa el controlador que gestionará las rutas de vehículos

// ============================
// API de Vehículos
// ============================

// Ruta de prueba (ping → pong)
// Sirve para verificar que la API está en funcionamiento.
// Devuelve {"pong": true} con HTTP 200.
Route::get('/ping', fn() => response()->json(['pong' => true]));

// Ruta GET /vehicles
// Llama al método index() del VehicleController.
// Permite listar los vehículos con posibilidad de filtros (marca, estado, año) y paginación.
Route::get('/vehicles', [VehicleController::class, 'index']);

// Ruta POST /vehicles
// Llama al método store() del VehicleController.
// Permite insertar un nuevo vehículo enviando los datos en formato JSON.
Route::post('/vehicles', [VehicleController::class, 'store']);

// Ruta GET /vehicles/{vehicle}
// Llama al método show() del VehicleController.
// Devuelve el detalle de un vehículo específico identificado por su ID.
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
