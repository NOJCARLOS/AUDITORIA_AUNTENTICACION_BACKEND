<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlainAuth
{
    public function handle(Request $request, Closure $next)
    {
        $email = (string) ($request->header('X-Email') ?? $request->input('email'));
        $password = (string) ($request->header('X-Password') ?? $request->input('password'));

        if ($email === '' || $password === '') {
            return response()->json(['message' => 'Faltan credenciales'], 401);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $stored   = (string) ($user->password ?? '');
        $isHashed = Str::startsWith($stored, ['$2y$', '$argon2id$', '$argon2i$']);

        $valid = $isHashed ? Hash::check($password, $stored)
                           : hash_equals($stored, $password);

        if (!$valid) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Re-hashear al vuelo si era texto plano o si el hash quedó viejo
        if (!$isHashed || Hash::needsRehash($stored)) {
            $user->password = $password; // el cast 'hashed' aplicará Hash::make()
            $user->save();
        }

        $request->setUserResolver(fn () => $user);
        return $next($request);
    }
}
