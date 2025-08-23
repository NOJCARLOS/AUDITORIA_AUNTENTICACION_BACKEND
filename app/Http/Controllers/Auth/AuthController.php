<?php

// -------------------------------------------------------------------------
// Namespace: Define la ubicación de este controlador dentro del proyecto.
// Esto permite a Laravel localizarlo como parte del módulo Auth.
// -------------------------------------------------------------------------
namespace App\Http\Controllers\Auth;

// -------------------------------------------------------------------------
// Importación de dependencias necesarias para el controlador.
// -------------------------------------------------------------------------
use App\Http\Controllers\Controller;   // Clase base de controladores en Laravel.
use Illuminate\Http\Request;          // Manejo de solicitudes HTTP.
use Illuminate\Support\Str;           // Utilidades para manipulación de strings.
use Illuminate\Support\Facades\Hash;  // Funciones de hashing seguro (bcrypt, argon2, etc.).
use App\Models\User;                  // Modelo Eloquent para la tabla "users".

// -------------------------------------------------------------------------
// Clase principal del controlador de autenticación.
// Contiene endpoints para registro, login y logout.
// -------------------------------------------------------------------------
class AuthController extends Controller
{
    // ---------------------------------------------------------------------
    // POST /api/auth/register
    // Endpoint para registrar un nuevo usuario.
    // ---------------------------------------------------------------------
    public function register(Request $request)
    {
        // Valida la información recibida desde la petición HTTP.
        // Si la validación falla, Laravel devolverá automáticamente un error 422.
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],                   // Nombre: requerido, texto, máx 100 caracteres.
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'], // Email: requerido, válido, único en la tabla users.
            'password' => ['required', 'string', 'min:4'],                     // Contraseña: requerida, texto, mínimo 4 caracteres.
        ]);

        // Crea un nuevo usuario en la base de datos.
        // Si el modelo User posee $casts = ['password' => 'hashed'], 
        // el hash se aplica automáticamente.
        $user = User::create($data);

        // Genera un token de acceso personal mediante Laravel Sanctum.
        $token = $user->createToken('web')->plainTextToken;

        // Devuelve respuesta JSON con datos del usuario y token, status 201 (Created).
        return response()->json([
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email
            ],
            'token' => $token,
        ], 201);
    }

    // ---------------------------------------------------------------------
    // POST /api/auth/login
    // Endpoint para autenticar usuarios existentes.
    // ---------------------------------------------------------------------
    public function login(Request $request)
    {
        // Valida que se reciban email y password.
        $cred = $request->validate([
            'email'    => ['required', 'email'],   // Email requerido y válido.
            'password' => ['required', 'string'], // Contraseña requerida.
        ]);

        // Busca el usuario por email.
        $user = User::where('email', $cred['email'])->first();

        // Si no existe el usuario, retorna error 401 (Unauthorized).
        if (!$user) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Obtiene la contraseña almacenada en DB (puede ser hash o texto plano legacy).
        $stored = (string)($user->password ?? '');
        // Convierte la contraseña ingresada a string.
        $plain = (string)$cred['password'];

        // Verifica si la contraseña almacenada está hasheada.
        $isHashed = Str::startsWith($stored, ['$2y$', '$argon2id$', '$argon2i$']);

        // Valida la contraseña:
        // - Si está hasheada, usa Hash::check.
        // - Si no está hasheada, compara texto plano (para casos legacy).
        $valid = $isHashed
            ? Hash::check($plain, $stored)
            : hash_equals($stored, $plain);

        // Si la contraseña es incorrecta, retorna error 401.
        if (!$valid) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Si la contraseña estaba en texto plano o el hash está obsoleto, re-hashear.
        if (!$isHashed || Hash::needsRehash($stored)) {
            $user->password = $plain; // El cast 'hashed' aplicará Hash::make.
            $user->save();
        }

        // Genera un nuevo token de acceso personal (Sanctum).
        $token = $user->createToken('web')->plainTextToken;

        // Devuelve respuesta JSON con datos del usuario y token.
        return response()->json([
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email
            ],
            'token' => $token,
        ]);
    }

    // ---------------------------------------------------------------------
    // POST /api/auth/logout
    // Endpoint para revocar el token actual (requiere auth:sanctum).
    // ---------------------------------------------------------------------
    public function logout(Request $request)
    {
        // Elimina el token actual del usuario autenticado.
        // Operador nullsafe "?->" previene errores si no hay usuario/token.
        $request->user()?->currentAccessToken()?->delete();

        // Responde con confirmación de logout (200 OK).
        return response()->json(['message' => 'OK']);
    }
}
