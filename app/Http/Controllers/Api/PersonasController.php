<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePersonasRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Personas; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PersonasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            // Obtener todas las personas junto con sus relaciones utilizando join
            $personas = DB::table('personas')
                    ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                    ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                    ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                    ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                    ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                    ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                    ->select(
                        'personas.*',
                        'personas.idpersona as id',
                        'fuerzas.fuerza as fuerza',
                        'especialidades.especialidad as especialidad',
                        'grados.grado as grado',
                        'grados.abregrado',
                        'sexos.sexo as sexo',
                        'armas.arma as arma',
                        'statuscvs.name as status_civil',
                        DB::raw("
                            CASE
                                WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.appaterno, ' ', personas.nombres)
                                WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                                WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                                WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                                ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            END AS name
                        "),
                        DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD-MM-YYYY') as fechnacimeinto"),
                        DB::raw("TO_CHAR(personas.fechaegreso, 'DD-MM-YYYY') as fechaegreso"),
                        DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                        DB::raw("CAST(personas.celular AS TEXT) AS celular")
                    )
                    ->orderBy('personas.idgrado', 'asc')
                    ->get();

            // Verificar si no se encontraron personas
            if ($personas->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron personas.'
                ], 404);
            }

            // Retornar una respuesta exitosa con los datos encontrados
            return response()->json([
                'status' => true,
                'message' => 'Personas encontradas',
                'data' => $personas
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las personas: ' . $e->getMessage()
            ], 500);
        }
    }
    // Obtener todas las personas activas junto con sus relaciones
    public function indexPersonal(){
        try {
            
            $personas = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->select(
                    'personas.*',
                    'personas.idpersona as id',
                    'fuerzas.fuerza as fuerza',
                    'especialidades.especialidad as especialidad',
                    'grados.grado as grado',
                    'grados.abregrado',
                    'sexos.sexo as sexo',
                    'armas.arma as arma',
                    'statuscvs.name as status_civil',
                    'organizacion.nomorg as organizacion',
                    'puestos.nompuesto as puesto',
                    DB::raw("
                        CASE
                            WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                        END AS name
                    "),
                    DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD/MM/YYYY') as fechnacimeinto"),
                    DB::raw("TO_CHAR(personas.fechaegreso, 'DD/MM/YYYY') as fechaegreso"),
                    DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                    DB::raw("CAST(personas.celular AS TEXT) AS celular"),
                    DB::raw("DATE_PART('year', AGE(personas.fechnacimeinto)) AS edad")
                )
                ->whereNull('assignments.enddate')
                ->whereNull('assignments.motivofin')
                ->orderBy('personas.idgrado', 'asc')
                ->get();
    
            // Verificar si no se encontraron personas
            if ($personas->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron personas activas.'
                ], 404);
            }
    
            // Retornar una respuesta exitosa con los datos encontrados
            return response()->json([
                'status' => true,
                'message' => 'Personas activas encontradas',
                'data' => $personas
            ], 200);
    
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las personas: ' . $e->getMessage()
            ], 500);
        }
    }
    


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonasRequest $request)
    {
        try {
            $response = Personas::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Persona registrado correctamente',
                'data'=> $request->all()
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            // Buscar la persona por su ID junto con sus relaciones usando join
            $persona = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->select(
                    'personas.*',
                    'fuerzas.fuerza',
                    'especialidades.especialidad',
                    'grados.grado',
                    'grados.abregrado',
                    'sexos.sexo',
                    'armas.arma',
                    'armas.abrearma',
                    'statuscvs.name as status_civil'
                )
                ->where('personas.idpersona', $id)
                ->first();
    
            // Verificar si no se encontró la persona
            if (!$persona) {
                return response()->json([
                    'status' => false,
                    'message' => 'Persona no encontrada'
                ], 404);
            }
    
            // Retornar una respuesta exitosa con los detalles de la persona
            return response()->json([
                'status' => true,
                'message' => 'Persona encontrada',
                'data' => $persona
            ], 200);
    
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la persona: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePersonasRequest $request, int $id)
    {
        try {
            // Buscar el puesto por su ID
            $puesto = Personas::where('idpersona', $id)->firstOrFail();
        
            // Actualizar los datos del puesto con los datos validados
            $puesto->update($request->validated());
        
            // Retornar una respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Persona actualizado correctamente',
                'data' => $puesto
            ], 200); // Código de estado 200 para una actualización exitosa
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el ID, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Persona no encontrado'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la Persona: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
