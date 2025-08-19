<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Middleware\PlainAuth;

/*
|--------------------------------------------------------------------------
| Rutas API (Laravel 11)
|--------------------------------------------------------------------------
| - /ping                → público (salud de API)
| - /plain/register      → registro (guarda password en TEXTO PLANO)
| - /plain/login         → login (acepta password en TEXTO PLANO o HASH)
| - /plain/me            → protegido por PlainAuth
| - /vehicles (CRUD R/O) → protegidas por PlainAuth
*/

/* ---------- Público ---------- */

// Salud de API
Route::get('/ping', fn () => response()->json(['pong' => true]));

// Registro (guarda contraseña en TEXTO PLANO)
Route::post('/plain/register', function (Request $request) {
    $data = $request->validate([
        'name'     => ['required', 'string', 'max:100'],
        'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
        'password' => ['required', 'string', 'min:4'],
    ]);

    $user = User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => $data['password'], // ⚠️ TEXTO PLANO a petición del usuario
    ]);

    return response()->json([
        'message' => 'OK',
        'user'    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
    ], 201);
});

// Login (acepta texto plano o hash en BD)
Route::post('/plain/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = User::where('email', $credentials['email'])->first();
    if (!$user) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    $stored   = (string) ($user->password ?? '');
    $isHashed = Str::startsWith($stored, ['$2y$', '$argon2id$', '$argon2i$']);

    $valid = $isHashed
        ? Hash::check($credentials['password'], $stored)      // BD: hash
        : hash_equals($stored, $credentials['password']);     // BD: texto plano

    if (!$valid) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    return response()->json([
        'message' => 'OK',
        'user'    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
    ]);
});

/* ---------- Protegido por PlainAuth ---------- */

// “Quién soy” (requiere X-Email / X-Password)
Route::get('/plain/me', fn (Request $r) => response()->json($r->user()))
    ->middleware(PlainAuth::class);

// Vehículos (listar, crear, ver detalle) protegidos
Route::middleware(PlainAuth::class)->group(function () {
    Route::get('/vehicles', [VehicleController::class, 'index']);   // Listar (con filtros y paginación)
    Route::post('/vehicles', [VehicleController::class, 'store']);  // Crear JSON
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']); // Detalle
});
