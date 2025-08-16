<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    //protected $table = 'vehiculos'; // ← importante, tu tabla se llama vehicles
    protected $fillable = [
        'marca','modelo','anio','precio','estado','kilometraje','color',
    ];

    protected $casts = [
        'anio'        => 'integer',
        'precio'      => 'decimal:2',
        'kilometraje' => 'integer',
    ];//
}
