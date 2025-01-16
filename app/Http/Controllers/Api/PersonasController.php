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
                    DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD-MM-YYYY') as fechnacimeinto"),
                    DB::raw("TO_CHAR(personas.fechaegreso, 'DD-MM-YYYY') as fechaegreso"),
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
    // Obtener una persona específica por su id
    public function showPersonal($idpersona) {
        try {
            $persona = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->leftJoin('situaciones', 'personas.idsituacion', '=', 'situaciones.idsituacion')
                ->leftJoin('gestiones', 'personas.idpersona', '=', 'gestiones.idpersona') // Unión con la tabla gestiones
                ->select(
                    'personas.*',
                    'assignments.idassig',
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
                    'situaciones.situacion',
                    'gestiones.gestion as gestion_ingreso', // Gestión de ingreso
                    DB::raw("
                        CASE
                            WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                        END AS namepersona
                    "),
                    DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD/MM/YYYY') as fechnacimeinto"),
                    DB::raw("TO_CHAR(gestiones.fechaingreso, 'DD/MM/YYYY') as fechaingreso"),
                    DB::raw("TO_CHAR(personas.fechaegreso, 'DD-MM-YYYY') as fechaegreso"),
                    DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                    DB::raw("CAST(personas.celular AS TEXT) AS celular"),
                    DB::raw("
                        CASE
                            WHEN gestiones.gestion IS NOT NULL THEN (EXTRACT(YEAR FROM CURRENT_DATE) - gestiones.gestion)
                            ELSE NULL
                        END AS gestiones_permanencia
                    ") // Cálculo de años de permanencia basado en la gestión de ingreso
                )
                ->where('personas.ci', '=', $idpersona) // Filtrar por el idpersona específico
                ->whereNull('assignments.enddate')
                ->whereNull('assignments.motivofin')
                ->first(); // Obtener solo una persona
    
            if (!$persona) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró la persona con el CI especificado.'
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Persona encontrada',
                'data' => $persona
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la persona: ' . $e->getMessage()
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
                'messageshowuseraccesses' => 'Error al actualizar la Persona: ' . $e->getMessage()
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
    /**
     * Desvinculados de la gestion actual.
     */
    public function getDesvinculadosGestionActual() {
        try {
            $desvinculados = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->leftJoin('assignments', function ($join) {
                    $join->on('personas.idpersona', '=', 'assignments.idpersona')
                         ->whereRaw('assignments.enddate = (SELECT MAX(a.enddate) FROM assignments a WHERE a.idpersona = personas.idpersona)');
                })
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->leftJoin('situaciones', 'personas.idsituacion', '=', 'situaciones.idsituacion')
                ->leftJoin('gestiones', function ($join) {
                    $join->on('personas.idpersona', '=', 'gestiones.idpersona')
                         ->whereRaw('gestiones.fechadesvin = assignments.enddate');
                })
                ->select(
                    'personas.*',
                    'personas.idpersona as id',
                    'assignments.idassig',
                    'assignments.startdate',
                    'assignments.enddate',
                    'gestiones.fechaingreso',
                    'gestiones.fechadesvin',
                    'gestiones.gestion as gestion_ingreso',
                    'fuerzas.fuerza',
                    'especialidades.especialidad',
                    'grados.grado',
                    'grados.abregrado',
                    'sexos.sexo',
                    'armas.arma',
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
                    DB::raw("TO_CHAR(gestiones.fechaingreso, 'DD/MM/YYYY') as fechaingreso"),
                    DB::raw("TO_CHAR(gestiones.fechadesvin, 'DD/MM/YYYY') as fechadesvin")
                )
                ->whereNotNull('gestiones.fechadesvin') // Solo registros con fecha de desvinculación
                ->whereYear('gestiones.fechadesvin', '=', DB::raw('EXTRACT(YEAR FROM CURRENT_DATE)')) // Filtrar por la gestión actual
                ->get();
    
            if ($desvinculados->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron personas desvinculadas durante la gestión actual.'
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Personas desvinculadas durante la gestión actual encontradas',
                'data' => $desvinculados
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las personas desvinculadas: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Listar personas que acceden a las organizaciones con permisos.
     */
    public function listPeopleByUserAccess($iduser) {
        try {
            // Obtener todos los idorg permitidos para el usuario
            $accessibleOrgs = DB::table('user_accesos')
                ->where('iduser', $iduser)
                ->pluck('idorg');

            if ($accessibleOrgs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no tiene accesos asignados.',
                    'data' => []
                ], 403);
            }

            // Obtener las personas que pertenecen a los idorg permitidos con datos adicionales
            $people = DB::table('personas')
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
                    'assignments.idassig',
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
                    DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD-MM-YYYY') as fechnacimeinto"),
                    DB::raw("TO_CHAR(personas.fechaegreso, 'DD-MM-YYYY') as fechaegreso"),
                    DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                    DB::raw("CAST(personas.celular AS TEXT) AS celular"),
                    DB::raw("DATE_PART('year', AGE(personas.fechnacimeinto)) AS edad")
                )
                ->whereIn('assignments.idorg', $accessibleOrgs)
                ->whereNull('assignments.enddate')
                ->whereNull('assignments.motivofin')
                ->orderBy('personas.idgrado', 'asc')
                ->get();

            if ($people->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron personas para los accesos asignados al usuario.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Personas encontradas',
                'data' => $people
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las personas: ' . $e->getMessage()
            ], 500);
        }
    } 
    
    /**
     * Listar personas que acceden a las organizaciones con permisos y que tienen novedades vigentes.
     */
    public function listPeopleByUserPermisos($iduser) {
        try {
            // Obtener todos los idorg permitidos para el usuario
            $accessibleOrgs = DB::table('user_accesos')
                ->where('iduser', $iduser)
                ->pluck('idorg');

            if ($accessibleOrgs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no tiene accesos asignados.',
                    'data' => []
                ], 403);
            }

            // Obtener las personas que pertenecen a los idorg permitidos con novedades vigentes
            $people = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->leftJoin('novedades', 'assignments.idassig', '=', 'novedades.idassig') // Relación con novedades
                ->leftJoin('tiponovedad', 'novedades.idnov', '=', 'tiponovedad.idnov') // Relación con tipo de novedad
                ->select(
                    'personas.*',
                    'assignments.idassig',
                    'novedades.idnovedad',
                    'tiponovedad.idnov',
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
                    'novedades.startdate',
                    'novedades.enddate',
                    'novedades.descripcion',
                    'tiponovedad.novedad as tipo_novedad', // Tipo de novedad
                    DB::raw("
                        CASE
                            WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                        END AS name
                    "),
                    DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD-MM-YYYY') as fechnacimeinto"),
                    DB::raw("TO_CHAR(personas.fechaegreso, 'DD-MM-YYYY') as fechaegreso"),
                    DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                    DB::raw("CAST(personas.celular AS TEXT) AS celular"),
                    DB::raw("DATE_PART('year', AGE(personas.fechnacimeinto)) AS edad"),
                    DB::raw("TO_CHAR(novedades.startdate, 'DD/MM/YYYY') as inicio"),
                    DB::raw("TO_CHAR(novedades.enddate, 'DD/MM/YYYY') as fin"),
                )
                ->whereIn('assignments.idorg', $accessibleOrgs)
                ->whereNull('assignments.enddate')
                ->whereNull('assignments.motivofin')
                ->where('novedades.activo', true) // Filtrar solo novedades activas
                ->where(function ($query) {
                    $query->whereNull('novedades.enddate')
                        ->orWhere('novedades.enddate', '>=', now()); // Vigencia de novedades
                })
                ->orderBy('personas.idgrado', 'asc')
                ->get();

            if ($people->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron personas con novedades vigentes para los accesos asignados al usuario.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Personas encontradas',
                'data' => $people
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las personas: ' . $e->getMessage()
            ], 500);
        }
    }
    public function listPeoplePartediaria($iduser) {
        try {
            // Obtener todos los idorg permitidos para el usuario
            $accessibleOrgs = DB::table('user_accesos')
                ->where('iduser', $iduser)
                ->pluck('idorg');
    
            if ($accessibleOrgs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no tiene accesos asignados.',
                    'data' => []
                ], 403);
            }
    
            // Obtener las personas que pertenecen a los idorg permitidos con datos adicionales
            $people = DB::table('personas')
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
                    'assignments.idassig',
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
                    DB::raw("TO_CHAR(personas.fechnacimeinto, 'DD-MM-YYYY') as fechnacimeinto"),
                    DB::raw("TO_CHAR(personas.fechaegreso, 'DD-MM-YYYY') as fechaegreso"),
                    DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                    DB::raw("CAST(personas.celular AS TEXT) AS celular"),
                    DB::raw("DATE_PART('year', AGE(personas.fechnacimeinto)) AS edad"),
                    DB::raw("
                        CASE
                            WHEN EXISTS (
                                SELECT 1 FROM novedades 
                                WHERE novedades.idassig = assignments.idassig
                                AND novedades.startdate <= now()::date
                                AND novedades.enddate >= now()::date
                            ) THEN 'No Forma'
                            ELSE 'Forma'
                        END AS estado_forma
                    "),
                    DB::raw("
                        COALESCE((
                            SELECT idnov 
                            FROM novedades 
                            WHERE novedades.idassig = assignments.idassig
                            AND novedades.startdate <= now()::date
                            AND novedades.enddate >= now()::date
                            LIMIT 1
                        ), null) AS idnov
                    ")
                )
                ->whereIn('assignments.idorg', $accessibleOrgs)
                ->whereNull('assignments.enddate')
                ->whereNull('assignments.motivofin')
                ->orderBy('personas.idgrado', 'asc')
                ->get();
    
            if ($people->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron personas para los accesos asignados al usuario.',
                    'data' => []
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Personas encontradas',
                'data' => $people
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las personas: ' . $e->getMessage()
            ], 500);
        }
    }
    


}
