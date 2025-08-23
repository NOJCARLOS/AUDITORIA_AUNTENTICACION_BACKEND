<?php

// -------------------------------------------------------------------------
// Importación de clases necesarias para definir migraciones en Laravel.
// -------------------------------------------------------------------------
use Illuminate\Database\Migrations\Migration; // Clase base de migraciones.
use Illuminate\Database\Schema\Blueprint;     // Define la estructura de tablas.
use Illuminate\Support\Facades\Schema;        // Proporciona métodos para crear/modificar tablas.

// -------------------------------------------------------------------------
// Clase anónima que extiende de Migration. Contiene los métodos up() y down().
// -------------------------------------------------------------------------
return new class extends Migration
{
    /**
     * Método up(): crea la tabla 'personal_access_tokens'.
     * Esta tabla es utilizada por Laravel Sanctum para almacenar tokens API.
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {

            $table->id(); // Columna autoincremental primaria 'id' (BIGINT UNSIGNED).

            // -----------------------------------------------------------------
            // 'tokenable': crea dos columnas polimórficas:
            // - tokenable_type: nombre de la clase del modelo propietario del token.
            // - tokenable_id: ID del modelo propietario.
            // Esto permite que diferentes modelos (ej. User, Admin) usen tokens.
            // -----------------------------------------------------------------
            $table->morphs('tokenable');

            $table->text('name'); // Nombre del token (ej. 'web', 'mobile').
            
            // -----------------------------------------------------------------
            // 'token': cadena única de 64 caracteres.
            // - Contiene el hash SHA256 del token generado.
            // - Se marca como UNIQUE para garantizar que no haya duplicados.
            // -----------------------------------------------------------------
            $table->string('token', 64)->unique();

            // -----------------------------------------------------------------
            // 'abilities': lista de permisos asociados al token.
            // - Puede ser NULL si el token tiene acceso completo.
            // -----------------------------------------------------------------
            $table->text('abilities')->nullable();

            // -----------------------------------------------------------------
            // 'last_used_at': fecha/hora de último uso del token.
            // - Puede ser NULL si nunca se ha utilizado.
            // -----------------------------------------------------------------
            $table->timestamp('last_used_at')->nullable();

            // -----------------------------------------------------------------
            // 'expires_at': fecha/hora de expiración del token.
            // - Se indexa para facilitar búsqueda de tokens expirados.
            // -----------------------------------------------------------------
            $table->timestamp('expires_at')->nullable()->index();

            // -----------------------------------------------------------------
            // 'created_at' y 'updated_at': timestamps automáticos de Laravel.
            // -----------------------------------------------------------------
            $table->timestamps();
        });
    }

    /**
     * Método down(): revierte los cambios aplicados por up().
     * - Elimina la tabla 'personal_access_tokens' si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};

