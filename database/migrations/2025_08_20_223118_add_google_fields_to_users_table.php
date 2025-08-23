<?php

// -------------------------------------------------------------------------
// Importaciones necesarias para definir y ejecutar migraciones en Laravel.
// -------------------------------------------------------------------------
use Illuminate\Database\Migrations\Migration; // Clase base para todas las migraciones.
use Illuminate\Database\Schema\Blueprint;     // Clase para definir columnas y modificaciones en tablas.
use Illuminate\Support\Facades\Schema;        // Fachada para interactuar con el esquema de la BD.

// -------------------------------------------------------------------------
// Clase anónima que extiende de Migration. Define los métodos up() y down().
// -------------------------------------------------------------------------
return new class extends Migration {

    /**
     * Método up(): aplica cambios a la base de datos.
     * - Agrega las columnas 'google_id' y 'avatar' a la tabla 'users' si no existen.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si la columna 'google_id' no existe, la crea:
            // - Tipo: string (VARCHAR)
            // - Nullable: sí (puede ser NULL)
            // - Unique: sí (no pueden repetirse valores)
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique();
            }

            // Si la columna 'avatar' no existe, la crea:
            // - Tipo: string (VARCHAR)
            // - Nullable: sí (puede ser NULL)
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
        });
    }

    /**
     * Método down(): revierte los cambios aplicados en up().
     * - Elimina las columnas 'google_id' y 'avatar' si existen.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si la columna 'google_id' existe, elimina primero su índice único
            // y luego la columna.
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropUnique(['google_id']); // Elimina restricción UNIQUE
                $table->dropColumn('google_id');   // Elimina columna
            }

            // Si la columna 'avatar' existe, la elimina.
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });
    }
};
