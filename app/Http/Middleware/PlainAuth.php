<?php

// -------------------------------------------------------------------------
// Namespace del middleware: define su ubicación dentro del proyecto.
// -------------------------------------------------------------------------
namespace App\Http\Middleware;

// -------------------------------------------------------------------------
// Dependencias necesarias.
// -------------------------------------------------------------------------
use Closure;                        // Tipo callable que representa la siguiente acción en la cadena middleware.
use Illuminate\Http\Request;        // Para acceder a datos de la solicitud HTTP.
use App\Models\User;               // Modelo Eloquent de la tabla "users".
use Illuminate\Support\Facades\Hash; // Funciones de hashing seguro de Laravel (bcrypt, argon2).
use Illuminate\Support\Str;        // Utilidades para trabajar con cadenas (ej. detectar prefijos).

// -------------------------------------------------------------------------
// Middleware PlainAuth: Autenticación básica con email/contraseña.
// - Soporta credenciales en headers o cuerpo de la solicitud.
// - Re-hashea contraseñas legacy en texto plano.
// -------------------------------------------------------------------------
class PlainAuth
{
    /**
     * Maneja la autenticación basada en credenciales planas (email/password).
     * - Headers soportados: X-Email, X-Password.
     * - Parámetros soportados: email, password.
     * - Si la autenticación falla, devuelve HTTP 401 (Unauthorized).
     * - Si es exitosa, adjunta el usuario autenticado a la Request.
     */
    public function handle(Request $request, Closure $next)
    {
        // -----------------------------------------------------------------
        // Obtención de credenciales desde headers o parámetros de request.
        // -----------------------------------------------------------------
        $email = (string) ($request->header('X-Email') ?? $request->input('email'));
        $password = (string) ($request->header('X-Password') ?? $request->input('password'));

        // Si faltan credenciales, responder 401.
        if ($email === '' || $password === '') {
            return response()->json(['message' => 'Faltan credenciales'], 401);
        }

        // -----------------------------------------------------------------
        // Búsqueda del usuario en base de datos por email.
        // -----------------------------------------------------------------
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // -----------------------------------------------------------------
        // Verificación de la contraseña:
        // - Si está hasheada (bcrypt, argon2id, argon2i), usar Hash::check().
        // - Si está en texto plano (legacy), usar comparación segura hash_equals().
        // -----------------------------------------------------------------
        $stored   = (string) ($user->password ?? ''); // Contraseña almacenada.
        $isHashed = Str::startsWith($stored, ['$2y$', '$argon2id$', '$argon2i$']); // Detecta prefijo de hash.

        $valid = $isHashed
            ? Hash::check($password, $stored)
            : hash_equals($stored, $password);

        if (!$valid) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // -----------------------------------------------------------------
        // Re-hash al vuelo si:
        // - La contraseña era texto plano.
        // - El hash actual necesita actualización (cost obsoleto).
        // -----------------------------------------------------------------
        if (!$isHashed || Hash::needsRehash($stored)) {
            $user->password = $password; // El cast 'hashed' del modelo aplicará Hash::make().
            $user->save();
        }

        // -----------------------------------------------------------------
        // Asigna el usuario autenticado al request:
        // - Permite usar $request->user() en controladores subsiguientes.
        // -----------------------------------------------------------------
        $request->setUserResolver(fn () => $user);

        // Continúa la ejecución de la request.
        return $next($request);
    }
}
