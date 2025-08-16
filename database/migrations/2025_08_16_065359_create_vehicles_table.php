<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            //$table->id();
            //$table->timestamps();
            $table->id(); // ID del vehículo (PK autoincremental)
            $table->string('marca', 100);             // Toyota, Ford, Chevrolet...
            $table->string('modelo', 100);            // Camry, F-150, Silverado...
            $table->year('anio');                     // Año de fabricación
            $table->decimal('precio', 12, 2);         // Valor en moneda local
            $table->enum('estado', ['Disponible','Vendido','En mantenimiento'])->default('Disponible');
            $table->unsignedInteger('kilometraje');   // Kilómetros recorridos
            $table->string('color', 50);
            $table->timestamps();

            // Índices útiles para filtros frecuentes
            $table->index(['marca', 'modelo']);
            $table->index('estado');
            $table->index('anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
