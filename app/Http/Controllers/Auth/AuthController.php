<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // POST /api/auth/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150','unique:users,email'],
            'password' => ['required','string','min:4'],
        ]);

        // Si en tu User tienes cast 'hashed', se hashea solo.
        // De lo contrario, usa: $data['password'] = Hash::make($data['password']);
        $user  = User::create($data);
        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user'  => ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email],
            'token' => $token,
        ], 201);
    }

    // POST /api/auth/login
    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $cred['email'])->first();
        if (!$user) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $stored   = (string) ($user->password ?? '');
        $plain    = (string) $cred['password'];
        $isHashed = Str::startsWith($stored, ['$2y$', '$argon2id$', '$argon2i$']);

        $valid = $isHashed ? Hash::check($plain, $stored)
                           : hash_equals($stored, $plain); // ← compatibilidad si quedaron contraseñas en texto plano

        if (!$valid) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Si estaba en texto plano o el hash está desactualizado, re-hashear
        if (!$isHashed || Hash::needsRehash($stored)) {
            $user->password = $plain; // con cast 'hashed' se aplicará Hash::make
            $user->save();
        }

        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user'  => ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email],
            'token' => $token,
        ]);
    }

    // POST /api/auth/logout  (requiere auth:sanctum)
    public function logout(Request $request)
    {
        // Revoca SOLO el token actual
        $request->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'OK']);
    }
}
