<?php

namespace App\Models; // Se define el espacio de nombres para los modelos

use Illuminate\Database\Eloquent\Model; // Se importa la clase base Model de Eloquent

/**
 * Clase Vehicle
 * Representa el modelo Eloquent para la entidad "vehículos".
 * Se utiliza para interactuar con la tabla correspondiente en la base de datos,
 */
class Vehicle extends Model
{
    // Por defecto, Laravel asume que la tabla se llama "vehicles".
    /**
     * Campos que pueden asignarse de forma masiva.
     * Esto evita asignar accidentalmente columnas sensibles.
     */
    protected $fillable = [
        'marca',       // Marca del vehículo (ej. Toyota, Mazda)
        'modelo',      // Modelo del vehículo (ej. Corolla, CX-5)
        'anio',        // Año de fabricación del vehículo
        'precio',      // Precio del vehículo
        'estado',      // Estado actual (Disponible, Vendido, En mantenimiento)
        'kilometraje', // Kilometraje recorrido por el vehículo
        'color',       // Color del vehículo
    ];

    /**
     * Conversión automática de atributos al obtenerse desde la BD.
     * Define el tipo de dato esperado en PHP para cada campo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'anio'        => 'integer',     // Convierte el año en un entero
        'precio'      => 'decimal:2',  // Convierte el precio a decimal con 2 decimales
        'kilometraje' => 'integer',     // Convierte el kilometraje en un entero
    ];
}

