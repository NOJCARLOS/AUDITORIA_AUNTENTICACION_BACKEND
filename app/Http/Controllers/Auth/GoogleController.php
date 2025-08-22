<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Redirige a Google (sin popup) usando Socialite.
     * Fuerza selector de cuenta en desarrollo.
     */
    public function redirect(Request $request)
    {
        $callback = $request->getSchemeAndHttpHost() . '/api/auth/google/callback';

        $driver = Socialite::driver('google')
            ->stateless()
            ->redirectUrl($callback)
            ->with(['prompt' => 'select_account']);

        // (Opcional) Si defines OAUTH_CAINFO_PATH en .env, fuerza el CA bundle para evitar cURL 60
        if ($cafile = env('OAUTH_CAINFO_PATH')) {
            if (is_file($cafile)) {
                $driver->setHttpClient(new Client([
                    'verify'  => $cafile,
                    'timeout' => 15,
                ]));
            }
        }

        return $driver->redirect();
    }

    /**
     * Callback de Google: crea/une usuario y devuelve token de Sanctum.
     * Redirige al frontend con token en el hash: /#/oauth?token=...
     */
    public function callback(Request $request)
    {
        $callback = $request->getSchemeAndHttpHost() . '/api/auth/google/callback';
        $frontend = rtrim(env('FRONTEND_URL', 'http://127.0.0.1:5173'), '/');

        try {
            $driver = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($callback);

            // (Opcional) Forzar CA bundle si está definido
            if ($cafile = env('OAUTH_CAINFO_PATH')) {
                if (is_file($cafile)) {
                    $driver->setHttpClient(new Client([
                        'verify'  => $cafile,
                        'timeout' => 15,
                    ]));
                }
            }

            $googleUser = $driver->user();

            $gid    = (string) $googleUser->getId();
            $email  = $googleUser->getEmail();
            $name   = $googleUser->getName() ?: ($googleUser->getNickname() ?: $email);
            $avatar = $googleUser->getAvatar();

            // Unir por google_id o email para no duplicar
            $user = User::where('google_id', $gid)
                ->orWhere('email', $email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $name,
                    'email'             => $email,
                    'google_id'         => $gid,
                    'avatar'            => $avatar,
                    'email_verified_at' => now(),
                    'password'          => Str::random(40), // relleno; no se usa para login con Google
                ]);
            } else {
                $user->google_id         = $user->google_id ?: $gid;
                $user->avatar            = $avatar ?: $user->avatar;
                $user->email_verified_at = $user->email_verified_at ?: now();
                $user->save();
            }

            // Token de acceso (Sanctum)
            $token = $user->createToken('web')->plainTextToken;

            // Redirección COMPLETA al frontend; el front leerá el token desde el hash
            return redirect()->away($frontend . '/#/oauth?token=' . urlencode($token));
        } catch (Throwable $e) {
            Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Página de error amigable con fallback al frontend (sin interrumpir el flujo)
            $url = $frontend . '/#/oauth?error=' . rawurlencode('oauth_error');
            return response(
                '<!doctype html><meta charset="utf-8"><title>Error</title>'.
                '<p>La autenticación con Google falló. Intenta nuevamente.</p>'.
                '<script>setTimeout(function(){location.href="'.$url.'"}, 800)</script>',
                500
            )->header('Content-Type', 'text/html; charset=utf-8');
        }
    }
}
