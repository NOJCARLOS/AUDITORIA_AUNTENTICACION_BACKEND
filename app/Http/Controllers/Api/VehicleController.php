<?php

namespace App\Http\Controllers\Api; // Se define el espacio de nombres de la API

use App\Http\Controllers\Controller;     // Se importa la clase base Controller de Laravel
use App\Http\Requests\VehicleStoreRequest; // Se importa la clase de validación para almacenar vehículos
use App\Models\Vehicle;                  // Se importa el modelo Vehicle que interactúa con la BD
use Illuminate\Http\Request;             // Se importa la clase Request para manejar solicitudes HTTP
use Illuminate\Support\Facades\DB;       // Se importa la fachada DB para operaciones de base de datos

/**
 * Controlador VehicleController
 * Gestiona operaciones relacionadas con los vehículos: listar, crear y mostrar detalle.
 */
class VehicleController extends Controller
{
    /**
     * Listar vehículos con posibilidad de filtros y paginación.
     *
     * @param Request $request Solicitud HTTP que puede incluir filtros (marca, estado, anio).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Lista paginada de vehículos.
     */
    public function index(Request $request) {
        $q = Vehicle::query(); // Se crea una consulta base sobre el modelo Vehicle
        // Si el parámetro 'marca' está presente, se filtra por la marca
        if ($request->filled('marca'))   
            $q->where('marca', $request->string('marca'));
        // Si el parámetro 'estado' está presente, se filtra por el estado
        if ($request->filled('estado'))  
            $q->where('estado', $request->string('estado'));
        // Si el parámetro 'anio' está presente, se filtra por el año
        if ($request->filled('anio'))    
            $q->where('anio', $request->integer('anio'));
        // Retorna los resultados más recientes con paginación (por defecto 15 registros por página)
        return $q->latest()->paginate($request->integer('per_page', 15));
    }

    /**
     * Crear un nuevo vehículo en la base de datos.
     * 
     * @param VehicleStoreRequest $request Solicitud validada con las reglas definidas en VehicleStoreRequest.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el vehículo creado.
     */
    public function store(VehicleStoreRequest $request) {
        // Se ejecuta la creación dentro de una transacción para asegurar integridad de datos
        $vehicle = DB::transaction(fn() => Vehicle::create($request->validated()));

        // Retorna un JSON con mensaje, datos y encabezado Location hacia el nuevo recurso
        return response()->json([
            'message' => 'Vehículo creado', // Mensaje de confirmación
            'data'    => $vehicle,          // Datos del vehículo creado
        ], 201, ['Location' => url("/api/vehicles/{$vehicle->id}")]); // HTTP 201 + URL del recurso
    }

    /**
     * Mostrar los detalles de un vehículo específico.
     *
     * @param Vehicle $vehicle Instancia de vehículo obtenida mediante inyección de dependencias.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos del vehículo.
     */
    public function show(Vehicle $vehicle) {
        // Retorna los datos del vehículo en formato JSON
        return response()->json($vehicle);
    }
}
