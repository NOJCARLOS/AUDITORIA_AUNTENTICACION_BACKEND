<?php

// -------------------------------------------------------------------------
// Namespace del modelo: define su ubicación dentro del proyecto.
// -------------------------------------------------------------------------
namespace App\Models;

// -------------------------------------------------------------------------
// Dependencias necesarias para el modelo User.
// -------------------------------------------------------------------------
use Illuminate\Foundation\Auth\User as Authenticatable; // Clase base para usuarios autenticables en Laravel.
use Laravel\Sanctum\HasApiTokens;                      // Trait que permite la gestión de tokens API con Sanctum.
use Illuminate\Notifications\Notifiable;              // Trait que permite el envío/recepción de notificaciones.
use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait para habilitar factories en pruebas y seeds.

// -------------------------------------------------------------------------
// Modelo User: Representa la tabla "users" en la base de datos.
// Integra autenticación, tokens API, notificaciones y factories.
// -------------------------------------------------------------------------
class User extends Authenticatable
{
    // Incluye traits que agregan funcionalidades adicionales al modelo.
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos que pueden ser asignados de forma masiva (mass assignment).
     * - Protege contra asignación no intencionada de campos sensibles.
     */
    protected $fillable = [
        'name',       // Nombre del usuario.
        'email',      // Dirección de correo.
        'password',   // Contraseña (se almacenará hasheada).
        'google_id',  // ID de Google para login OAuth.
        'avatar',     // URL del avatar del usuario.
    ];

    /**
     * Atributos que deben ocultarse al serializar el modelo (JSON, arrays).
     * - Protege datos sensibles en respuestas API.
     */
    protected $hidden = [
        'password',        // Contraseña (nunca se expone).
        'remember_token',  // Token de "recordar sesión" de Laravel.
    ];

    /**
     * Conversión automática de atributos a tipos nativos.
     * - email_verified_at: se convierte automáticamente a instancia de Carbon (fecha/hora).
     * - password: se hashea automáticamente al asignar un valor (gracias al cast 'hashed').
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed', // Aplica Hash::make() de forma automática al asignar contraseña.
    ];
}

