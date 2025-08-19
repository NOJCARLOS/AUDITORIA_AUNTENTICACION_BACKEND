<?php

namespace App\Http\Requests; // Se define el espacio de nombres para las clases de FormRequest

use Illuminate\Foundation\Http\FormRequest; // Se importa la clase base FormRequest de Laravel

/**
 * Clase VehicleStoreRequest
 * Se encarga de validar los datos de entrada para crear un vehículo.
 * Centraliza reglas de validación y autorización del request.
 */
class VehicleStoreRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     * Por el momento, se autoriza siempre (true). Si se requiere control de permisos,
     *
     * @return bool Valor booleano indicando autorización.
     */
    // Autorización del usuario: se permite la operación para cualquier solicitante.
    public function authorize(): bool
    {
        return true; // Se autoriza la petición; cambiar a lógica de permisos si aplica.
    }

    /**
     * Reglas de validación aplicables a la solicitud.
     * Define tipos, rangos y restricciones de cada campo del vehículo.
     *
     */
    public function rules(): array
    {
        return [
            // La marca es obligatoria, de tipo cadena y con longitud máxima de 100 caracteres.
            'marca'=> ['required', 'string', 'max:100'],

            // El modelo es obligatorio, de tipo cadena y con longitud máxima de 100 caracteres.
            'modelo'=> ['required', 'string', 'max:100'],

            // El año es obligatorio, entero y debe estar entre 1900 y 2100 (rango razonable).
            'anio'=> ['required', 'integer', 'between:1900,2100'],

            // El precio es obligatorio, numérico y no puede ser negativo.
            'precio'=> ['required', 'numeric', 'min:0'],

            // El estado es obligatorio y debe ser uno de los valores permitidos.
            'estado'=> ['required', 'in:Disponible,Vendido,En mantenimiento'],

            // El kilometraje es obligatorio, entero y no puede ser negativo.
            'kilometraje'=> ['required', 'integer', 'min:0'],

            // El color es obligatorio, de tipo cadena y con longitud máxima de 50 caracteres.
            'color'=> ['required', 'string', 'max:50'],
        ];
    }
}
