<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleStoreRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
        // Listar (solo lectura para el front)
    public function index(Request $request) {
        $q = Vehicle::query();

        // Filtros opcionales
        if ($request->filled('marca'))   $q->where('marca', $request->string('marca'));
        if ($request->filled('estado'))  $q->where('estado', $request->string('estado'));
        if ($request->filled('anio'))    $q->where('anio',   $request->integer('anio'));

        return $q->latest()->paginate($request->integer('per_page', 15));
    }

    // Insertar uno desde JSON
    public function store(VehicleStoreRequest $request) {
        $vehicle = DB::transaction(fn() => Vehicle::create($request->validated()));

        return response()->json([
            'message' => 'Vehículo creado',
            'data'    => $vehicle,
        ], 201, ['Location' => url("/api/vehicles/{$vehicle->id}")]);
    }

    // Ver detalle
    public function show(Vehicle $vehicle) {
        return response()->json($vehicle);
    }
}

