<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Middleware\PlainAuth;

/*
|-----------------------------------------------------------------------
| API pública
|-----------------------------------------------------------------------
*/

// Salud de API (público)
Route::get('/ping', fn () => response()->json(['pong' => true]));

// Registro: guarda HASH (gracias al cast 'hashed' en User)
Route::post('/plain/register', function (Request $request) {
    $data = $request->validate([
        'name'     => ['required', 'string', 'max:100'],
        'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
        'password' => ['required', 'string', 'min:4'],
    ]);

    // El cast 'hashed' en el modelo hace Hash::make automáticamente
    $user = User::create($data);

    return response()->json([
        'message' => 'OK',
        'user'    => ['id'=>$user->id, 'name'=>$user->name, 'email'=>$user->email],
    ], 201);
});

// Login: acepta hash o texto plano en BD (transición) y re-hashea al vuelo
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
    $plain    = (string) $credentials['password'];
    $isHashed = Str::startsWith($stored, ['$2y$', '$argon2id$', '$argon2i$']);

    $valid = $isHashed ? Hash::check($plain, $stored)
                       : hash_equals($stored, $plain); // permite texto plano durante migración

    if (!$valid) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    // Re-hasheo al vuelo si estaba en texto plano o si el hash quedó desactualizado
    if (!$isHashed || Hash::needsRehash($stored)) {
        $user->password = $plain; // el cast 'hashed' aplicará Hash::make()
        $user->save();
    }

    return response()->json([
        'message' => 'OK',
        'user'    => ['id'=>$user->id, 'name'=>$user->name, 'email'=>$user->email],
    ]);
});

/*
|-----------------------------------------------------------------------
| API protegida por PlainAuth (X-Email / X-Password)
|-----------------------------------------------------------------------
*/

// "Quién soy" (requiere headers o body con email/password)
Route::get('/plain/me', fn (Request $r) => response()->json($r->user()))
    ->middleware(PlainAuth::class);

// Vehículos
Route::middleware(PlainAuth::class)->group(function () {
    Route::get('/vehicles', [VehicleController::class, 'index']);   // Listar
    Route::post('/vehicles', [VehicleController::class, 'store']);  // Crear
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']); // Detalle
});

