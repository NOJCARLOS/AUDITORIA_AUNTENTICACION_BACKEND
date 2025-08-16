<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    //autorizacion del usaurio, por el momento se queda en false, es decir sin autenticacion
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marca'        => ['required','string','max:100'],
            'modelo'       => ['required','string','max:100'],
            'anio'         => ['required','integer','between:1900,2100'],
            'precio'       => ['required','numeric','min:0'],
            'estado'       => ['required','in:Disponible,Vendido,En mantenimiento'],
            'kilometraje'  => ['required','integer','min:0'],
            'color'        => ['required','string','max:50'],
        ];
    }
}



