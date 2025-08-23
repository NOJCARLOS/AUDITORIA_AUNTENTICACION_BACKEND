<?php

// -------------------------------------------------------------------------
// Importación de clases necesarias para definir migraciones en Laravel.
// -------------------------------------------------------------------------
use Illuminate\Database\Migrations\Migration; // Clase base de todas las migraciones.
use Illuminate\Database\Schema\Blueprint;     // Permite definir columnas/modificaciones en tablas.
use Illuminate\Support\Facades\Schema;        // Fachada para interactuar con el esquema de la BD.

// -------------------------------------------------------------------------
// Clase anónima que extiende de Migration.
// Contiene los métodos up() y down() para aplicar y revertir cambios.
// -------------------------------------------------------------------------
return new class extends Migration
{
    /**
     * Run the migrations.
     * Método up(): aplica los cambios a la base de datos.
     * - Aquí se definen las modificaciones a la tabla 'users'.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Aquí puedes agregar/modificar/eliminar columnas.
            // Ejemplo:
            // $table->string('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Método down(): revierte los cambios aplicados en up().
     * - Se utiliza para deshacer modificaciones y restaurar el esquema previo.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Aquí debes eliminar/ revertir los cambios realizados en up().
            // Ejemplo:
            // $table->dropColumn('phone');
        });
    }
};

