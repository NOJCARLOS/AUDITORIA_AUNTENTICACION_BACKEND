<?php

use Illuminate\Support\Facades\Route;          // Se importa la clase Route para definir rutas de la API
use App\Http\Controllers\Api\VehicleController; // Se importa el controlador que gestionará las rutas de vehículos
use App\Http\Middleware\PlainAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// ============================
// API de Vehículos
// ============================

// Login “texto plano” (solo valida y responde; sin sesión/token)
Route::post('/plain/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required'],
    ]);

    $user = User::where('email', $credentials['email'])->first();
    if (!$user || !Hash::check($credentials['password'], $credentials['password'])) {
        // ↑↑ si usas hash real, cambia la 2ª comparación por: !Hash::check($credentials['password'], $user->password)
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    return response()->json([
        'message' => 'OK',
        'user' => ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email],
    ]);
});

// Ver usuario enviando X-Email / X-Password (cabeceras) o email/password en body
Route::get('/plain/me', fn (Request $r) => response()->json($r->user()))
    ->middleware(PlainAuth::class); // usa la clase

Route::middleware(PlainAuth::class)->group(function () {

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

});