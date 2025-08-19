<?php

use Illuminate\Database\Migrations\Migration; // Se importa la clase base Migration de Laravel
use Illuminate\Database\Schema\Blueprint;    // Se importa Blueprint para definir la estructura de la tabla
use Illuminate\Support\Facades\Schema;       // Se importa Schema para ejecutar operaciones sobre la BD

// Retorna una clase anónima que extiende de Migration
return new class extends Migration
{
    /**
     * Método up()
     * Se ejecuta al aplicar la migración. Crea la tabla "vehicles"
     * con sus columnas, restricciones y los índices necesarios.
     */
    public function up(): void
    {
        // Se crea la tabla "vehicles"
        Schema::create('vehicles', function (Blueprint $table) {
            // Campo ID autoincremental (clave primaria)
            $table->id(); 
            // Marca del vehículo, con un límite de 100 caracteres
            $table->string('marca', 100); // Ejemplo: Toyota, Ford, Chevrolet
            // Modelo del vehículo, con un límite de 100 caracteres
            $table->string('modelo', 100); // Ejemplo: Camry, F-150, Silverado
            // Año de fabricación, almacenado en tipo "year"
            $table->year('anio');
            // Precio con precisión de 12 dígitos y 2 decimales
            $table->decimal('precio', 12, 2); // Ejemplo: 120000.50
            // Estado del vehículo: restringido a valores fijos (enum)
            // Valor por defecto: Disponible
            $table->enum('estado', ['Disponible', 'Vendido', 'En mantenimiento'])
                  ->default('Disponible');
            // Kilometraje recorrido, solo enteros positivos
            $table->unsignedInteger('kilometraje'); // Ejemplo: 35000
            // Color del vehículo, hasta 50 caracteres
            $table->string('color', 50);
            // Campos created_at y updated_at (control de auditoría)
            $table->timestamps();
            // Índices para optimizar búsquedas y filtros frecuentes
            $table->index(['marca', 'modelo']); // Para búsquedas combinadas
            $table->index('estado');             // Para filtros por estado
            $table->index('anio');               // Para filtros por año
        });
    }
    /**
     * Método down()
     * Se ejecuta al revertir la migración. 
     * Elimina la tabla "vehicles" si existe.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles'); // Borra la tabla
    }
};
