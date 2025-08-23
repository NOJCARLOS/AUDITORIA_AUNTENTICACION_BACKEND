<?php

// -------------------------------------------------------------------------
// Namespace: Ubicación del controlador dentro del módulo Auth del proyecto.
// -------------------------------------------------------------------------
namespace App\Http\Controllers\Auth;

// -------------------------------------------------------------------------
// Dependencias necesarias.
// -------------------------------------------------------------------------
use App\Http\Controllers\Controller;    // Clase base de controladores en Laravel.
use Illuminate\Http\Request;           // Para manejar solicitudes HTTP entrantes.
use Laravel\Socialite\Facades\Socialite; // Librería Socialite para autenticación con Google.
use App\Models\User;                   // Modelo Eloquent para la tabla "users".
use Illuminate\Support\Str;           // Utilidades para cadenas (ej. generación de contraseñas aleatorias).
use Illuminate\Support\Facades\Log;    // Para registrar logs de errores o eventos.
use GuzzleHttp\Client;                // Cliente HTTP para configurar SSL personalizado.
use Throwable;                        // Interfaz para capturar cualquier excepción/error.

// -------------------------------------------------------------------------
// Controlador para autenticación mediante Google OAuth2.
// Gestiona redirección, callback y unión/creación de usuarios.
// -------------------------------------------------------------------------
class GoogleController extends Controller
{
    /**
     * Redirige al usuario hacia Google para iniciar el proceso OAuth2.
     * - Endpoint: GET /api/auth/google/redirect
     * - Configura Socialite en modo stateless (sin sesiones Laravel).
     * - Fuerza el selector de cuenta ("select_account") para entornos de desarrollo.
     */
    public function redirect(Request $request)
    {
        // Construye la URL del callback (en este mismo backend).
        $callback = $request->getSchemeAndHttpHost() . '/api/auth/google/callback';

        // Configura el driver de Google con Socialite.
        $driver = Socialite::driver('google')
            ->stateless()            // No usa sesiones Laravel (ideal para API REST).
            ->redirectUrl($callback) // Define URL de retorno.
            ->with(['prompt' => 'select_account']); // Fuerza selección de cuenta en el flujo OAuth.

        // (Opcional) Configura un CA bundle personalizado si se define en .env
        // útil para evitar errores de cURL (SSL 60) en entornos corporativos.
        if ($cafile = env('OAUTH_CAINFO_PATH')) {
            if (is_file($cafile)) {
                $driver->setHttpClient(new Client([
                    'verify'  => $cafile, // Ruta del certificado CA.
                    'timeout' => 15,      // Tiempo máximo de espera en la conexión.
                ]));
            }
        }

        // Redirige al usuario hacia Google para iniciar el login.
        return $driver->redirect();
    }

    /**
     * Callback de Google: procesa el retorno del OAuth2.
     * - Endpoint: GET /api/auth/google/callback
     * - Obtiene datos del usuario, crea o actualiza su registro en DB,
     *   genera un token Sanctum y redirige al frontend con el token en la URL.
     */
    public function callback(Request $request)
    {
        // Define la URL del callback y del frontend.
        $callback = $request->getSchemeAndHttpHost() . '/api/auth/google/callback';
        $frontend = rtrim(env('FRONTEND_URL', 'http://127.0.0.1:5173'), '/');

        try {
            // Configura Socialite para obtener el usuario de Google.
            $driver = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($callback);

            // Aplica CA bundle personalizado si existe (como en redirect()).
            if ($cafile = env('OAUTH_CAINFO_PATH')) {
                if (is_file($cafile)) {
                    $driver->setHttpClient(new Client([
                        'verify'  => $cafile,
                        'timeout' => 15,
                    ]));
                }
            }

            // Obtiene datos del usuario autenticado en Google.
            $googleUser = $driver->user();

            // Extrae atributos principales: ID de Google, email, nombre y avatar.
            $gid    = (string) $googleUser->getId();
            $email  = $googleUser->getEmail();
            $name   = $googleUser->getName() ?: ($googleUser->getNickname() ?: $email);
            $avatar = $googleUser->getAvatar();

            // Busca usuario existente por google_id o email para evitar duplicados.
            $user = User::where('google_id', $gid)
                ->orWhere('email', $email)
                ->first();

            // Si no existe, crea un nuevo usuario con datos de Google.
            if (!$user) {
                $user = User::create([
                    'name'              => $name,
                    'email'             => $email,
                    'google_id'         => $gid,
                    'avatar'            => $avatar,
                    'email_verified_at' => now(),         // Marca como verificado.
                    'password'          => Str::random(40), // Contraseña aleatoria (no usada).
                ]);
            } else {
                // Si ya existe, actualiza datos relevantes si están vacíos.
                $user->google_id         = $user->google_id ?: $gid;
                $user->avatar            = $avatar ?: $user->avatar;
                $user->email_verified_at = $user->email_verified_at ?: now();
                $user->save();
            }

            // Genera un token de acceso personal (Sanctum).
            $token = $user->createToken('web')->plainTextToken;

            // Redirige al frontend con el token en el hash (#/oauth?token=...).
            return redirect()->away($frontend . '/#/oauth?token=' . urlencode($token));

        } catch (Throwable $e) {
            // Captura cualquier excepción y la registra en el log.
            Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Construye URL de error para el frontend.
            $url = $frontend . '/#/oauth?error=' . rawurlencode('oauth_error');

            // Devuelve página HTML simple informando el fallo, con redirección automática.
            return response(
                '<!doctype html><meta charset="utf-8"><title>Error</title>'.
                '<p>La autenticación con Google falló. Intenta nuevamente.</p>'.
                '<script>setTimeout(function(){location.href="'.$url.'"}, 800)</script>',
                500
            )->header('Content-Type', 'text/html; charset=utf-8');
        }
    }
}
