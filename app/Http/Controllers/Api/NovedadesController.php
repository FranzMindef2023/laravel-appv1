<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNovedadesRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Novedades; 
use App\Models\Partesdiarias; 
use App\Models\TipoNovedad; // <- Importación de User
use App\Models\AsignacionVacaciones; 
use App\Models\Assignments; 
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NovedadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las novedades incluyendo la información del tipo de novedad
            $novedades = Novedades::where('activo', true)->with('tiponovedad')->get();

            if ($novedades->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron novedades registradas.',
                    'data' => []
                ], 404); // Código de estado 404 para recurso no encontrado
            }

            return response()->json([
                'status' => true,
                'message' => 'Novedades recuperadas correctamente.',
                'data' => $novedades
            ], 200); // Código de estado 200 para solicitud exitosa

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al recuperar las novedades: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
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
    public function store(StoreNovedadesRequest $request)
    {
        
        try {
            // Capturar la fecha y hora actual
            $fechaActual = Carbon::now();
            $gestion = $fechaActual->year;
            // Verificar si ya existe una novedad vigente para el mismo idasig (asignación), basada en la fecha de finalización.
            $novedadesVigentes = Novedades::where('idassig', $request->input('idassig'))
                ->where('activo', true)
                ->where('enddate', '>', now())
                ->with('tiponovedad') // Traer la relación con el tipo de novedad
                ->get();

            if ($novedadesVigentes->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ya existe un permiso vigente para este usuario.',
                    'data' => $novedadesVigentes // Incluye el tipo de novedad relacionado
                ], 404); // Código de estado 400 para solicitud inválida
            }
            //descontar la vacaciones 
            if($request->input('idnov')==2){
                //seleccionamos la persona 
                $persona=Assignments::where('idassig', $request->input('idassig'))
                ->where('estado', 'A')
                ->first();
                if (!$persona) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No se encontró la persona asignada.',
                        'data' => null
                    ], 404);
                }
                // Obtener la primera vacación vigente para la persona en la gestión actual
                $vacacionVigente = AsignacionVacaciones::where('idpersona', $persona->idpersona)
                    ->where('gestion', $gestion)
                    ->first(); // En lugar de get(), obtenemos solo el primer registro
                if (!$vacacionVigente) {
                    return response()->json([
                        'status' => false,
                        'message' => "No existe vacación programada para la gestión $gestion.",
                        'data' => null
                    ], 404);
                }
                $diasDisponibles=$vacacionVigente->dias_asignados;
                $diasUtilizadas=$vacacionVigente->dias_utilizados;
                $diasProgramados = $this->contarDiasHabiles($request->input('startdate'), $request->input('enddate'));
                if($diasDisponibles<$diasProgramados){
                    return response()->json([
                        'status' => true,
                        'message' => 'No se puede registrar la vacación porque excede los días disponibles.',
                        'data' => $diasDisponibles
                    ], 404);
                }
                $diasStockDisponibles=$diasDisponibles-$diasProgramados;
                $diasStockUtilizados=$diasUtilizadas+$diasProgramados;
                // Solo actualizar los campos específicos
                $vacacionVigente->update([
                    'dias_asignados' => $diasStockDisponibles,
                    'dias_utilizados' => $diasStockUtilizados,
                    'updated_at' => now() // Opcional, para registrar cuándo se modificó
                ]);

            }
            // Crear la nueva novedad si no existe una vigente.
            $response = Novedades::create($request->validated());

            // Recargar la novedad con la relación para incluir el tipo de novedad
            $response->load('tipoNovedad');

            return response()->json([
                'status' => true,
                'message' => 'Permiso registrada correctamente.',
                'data' => $response
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el Permiso: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }


    

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            // Buscar la novedad por ID e incluir la información del tipo de novedad
            $novedad = Novedades::with('tiponovedad')->where('idnovedad', $id)
            ->first();

            if (!$novedad) {
                return response()->json([
                    'status' => false,
                    'message' => 'Novedad no encontrada.'
                ], 404); // Código de estado 404 para recurso no encontrado
            }

            return response()->json([
                'status' => true,
                'message' => 'Novedad encontrada.',
                'data' => $novedad
            ], 200); // Código de estado 200 para una solicitud exitosa

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al mostrar la novedad: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }
    /**
     * Display a listing of the active novelties sorted by expiration date.
     */
    public function indexVigentes()
    {
        try {
            // Obtener todas las novedades vigentes, ordenadas por la fecha de vencimiento (enddate)
            $novedadesVigentes = Novedades::with('tipoNovedad')
                ->where('activo', true)
                ->where('enddate', '>', now())
                ->orderBy('enddate', 'asc')
                ->get();

            if ($novedadesVigentes->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron novedades vigentes.',
                    'data' => []
                ], 404); // Código de estado 404 para recurso no encontrado
            }

            return response()->json([
                'status' => true,
                'message' => 'Novedades vigentes recuperadas correctamente.',
                'data' => $novedadesVigentes
            ], 200); // Código de estado 200 para solicitud exitosa

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al recuperar las novedades vigentes: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
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
    public function update(StoreNovedadesRequest $request, int $id)
    {
        try {
            // Buscar la novedad por su ID
            // Buscar el puesto por su ID
            // $novedad = Novedades::where('idpersona', $id)->firstOrFail();
            $novedad = Novedades::findOrFail($id);

            // Actualizar los campos con los datos validados
            $novedad->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Novedad actualizada correctamente.',
                'data' => $novedad
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si la novedad no se encuentra, devolver un error 404
            return response()->json([
                'status' => false,
                'message' => 'Novedad no encontrada.'
            ], 404);
        } catch (\Throwable $th) {
            // Manejo de otros errores
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la novedad: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar la novedad por su ID
            $novedad = Novedades::findOrFail($id);
    
            // Marcar la novedad como inactiva en lugar de eliminarla
            $novedad->activo = false;
            $novedad->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Novedad desactivada correctamente.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si la novedad no se encuentra, devolver un error 404
            return response()->json([
                'status' => false,
                'message' => 'Novedad no encontrada.',
            ], 404);
        } catch (\Throwable $th) {
            // Manejo de otros errores
            return response()->json([
                'status' => false,
                'message' => 'Error al desactivar la novedad: ' . $th->getMessage()
            ], 500);
        }
    }
    /**
     * Store multiple resources in storage.
     */
    public function storeMassive(Request $request)
    {
        try {
            $iduser = $request->input('iduser'); // El usuario viene aparte en el request
            // Obtener todos los idorg permitidos para el usuario
            $accessibleOrgs = DB::table('user_accesos')
            ->where('iduser', $iduser)
            ->pluck('idorg');

            if ($accessibleOrgs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no tiene accesos asignados.',
                    'total' => 0
                ], 403);
            }

            // Contar las personas vigentes asignadas al usuario
            $count = DB::table('assignments')
                ->whereIn('idorg', $accessibleOrgs)
                ->whereNull('enddate') // Vigentes: enddate debe ser NULL
                ->whereNull('motivofin') // Vigentes: motivofin debe ser NULL
                ->count();
            // Validar los datos masivos
            $validated = $request->validate([
                'partes' => 'required|array',
                'partes.*.idpersona' => 'required|exists:personas,idpersona',
                'partes.*.estado_forma' => 'nullable|string|max:50',
                'partes.*.idnov' => 'nullable|integer|exists:tiponovedad,idnov',
                'partes.*.estado' => 'nullable|string|max:255',
            ]);
            // return $validated['partes'];
            
            if (!$iduser || !DB::table('users')->where('iduser', $iduser)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no válido o no encontrado.',
                ], 400);
            }

            // Obtener las horas generales del sistema
            $horas = DB::table('horas')->first();

            if (!$horas) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se han definido horarios generales en el sistema.',
                ], 404);
            }

            // Convertir las horas a objetos Carbon
            $horainicial = $horas->horainicial;
            $horafinal = $horas->horafinal;

            // Capturar la fecha y hora actual
            $fechaActual = Carbon::now();
            $gestion = $fechaActual->year;
            $mes = str_pad($fechaActual->month, 2, '0', STR_PAD_LEFT);
            $dia = str_pad($fechaActual->day, 2, '0', STR_PAD_LEFT);
            $horaActual = $fechaActual->format('H:i:s');
            $fechaparte = $fechaActual->format('Y-m-d');

            // Verificar si la hora actual está dentro del rango permitido
            // return $horaActual.$horainicial.$horafinal;
            if ($horaActual < $horainicial || $horaActual > $horafinal) {
                return response()->json([
                    'status' => false,
                    'message' => 'La hora actual está fuera del rango permitido.',
                ], 400);
            }
             // Generar un código único
             $codigo = $gestion.$mes.$dia.$iduser;
            // Validar y registrar cada parte
            $resultados = [];
            foreach ($validated['partes'] as $parte) {
                // Validar si ya existe un registro para este idassig en la fecha actual
                if (PartesDiarias::where('idpersona', $parte['idpersona'])
                    ->where('fechaparte', $fechaparte)
                    ->exists()) {
                    $resultados[] = [
                        'parte' => $parte,
                        'status' => false,
                        'message' => "Ya existe un registro para idpersona {$parte['idpersona']} en la fecha {$fechaparte}.",
                    ];
                    continue;
                }

                // Completar los datos
                $parte['fechahora'] = $fechaActual->format('Y-m-d H:i:s');
                $parte['fechaparte'] = $fechaparte;
                $parte['gestion'] = $gestion;
                $parte['mes'] = $mes;
                $parte['codigo'] = $codigo;
                $parte['iduser'] = $iduser;
                $parte['estado'] = 'pendiente';
                $parte['forma_noforma'] = $parte['estado_forma'];
                $parte['efectivo'] = $count;

                // Registrar el parte
                $registro = Partesdiarias::create($parte);

                $resultados[] = [
                    'parte' => $parte,
                    'status' => true,
                    'message' => 'Parte registrado correctamente.',
                    'data' => $registro,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Todos los partes se registraron correctamente.',
                'results' => $resultados,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar los partes diarios: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Verificador de Vacaciones.
     */
    public function verificadorVacaciones(int $idper, int $idVacacion)
    {
        try {
            // Capturar la fecha y hora actual
            $fechaActual = Carbon::now();
            $gestion = $fechaActual->year;

            if ($idVacacion == 2) {
                // Obtener la primera vacación vigente para la persona en la gestión actual
                $vacacionVigente = AsignacionVacaciones::where('idpersona', $idper)
                    ->where('gestion', $gestion)
                    ->first(); // En lugar de get(), obtenemos solo el primer registro

                // Si no hay vacaciones programadas
                if (!$vacacionVigente) {
                    return response()->json([
                        'status' => false,
                        'message' => "No existe vacación programada para la gestión $gestion.",
                        'data' => null
                    ], 200);
                }

                // Verificar si tiene días disponibles
                if ($vacacionVigente->dias_asignados <= 0) {
                    return response()->json([
                        'status' => false,
                        'message' => "Vacación agotada para la gestión $gestion.",
                        'data' => $vacacionVigente
                    ], 200);
                }

                return response()->json([
                    'status' => true,
                    'message' => "El personal seleccionado cuenta con {$vacacionVigente->dias_asignados} días de vacaciones vigentes.",
                    'data' => $vacacionVigente
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'ok',
                'data' => []
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al verificar vacaciones: ' . $th->getMessage()
            ], 500);
        }
    }
    /**
     * Verificador de horas antisipads de la vacacion
     */
    public function verificadorVacacionesHoras(int $idper, int $idVacacion, $fecha)
    {
        try {
            // Capturar la fecha y hora actual
            $fechaActual = Carbon::now()->startOfDay();;
            $fechaparte = $fechaActual->format('Y-m-d');
           // Fecha proporcionada (hasta el final del día)
            $fechaProporcionada = Carbon::parse($fecha)->endOfDay();

            // Calcular diferencia en horas
            $horasDiferencia = $fechaActual->diffInHours($fechaProporcionada)+1;

            if ($idVacacion == 2) {
                // Obtener la primera vacación vigente para la persona en la gestión actual
                $vacacionprioridad = TipoNovedad::where('idnov', $idVacacion)
                    ->first(); // En lugar de get(), obtenemos solo el primer registro

                // Si no hay vacaciones programadas
                if (!$vacacionprioridad) {
                    return response()->json([
                        'status' => false,
                        'message' => "No existe vacación programada para la gestión $gestion.",
                        'data' => null
                    ], 200);
                }

                // Verificar si tiene días disponibles
                if ($horasDiferencia<$vacacionprioridad->prioridad ) {
                    return response()->json([
                        'status' => false,
                        'message' => "La Vacación debe ser programada con  $vacacionprioridad->prioridad. Horas Antes",
                        'data' => $horasDiferencia
                    ], 200);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'ok',
                'data' => []
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al verificar vacaciones: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Verificador cantidad de dias programadas en la gestion que no sobrepase los dias que esta sacando
     */
    public function verificadorVacacionesDiasVigentes(int $idper, int $idVacacion, $fechInicio, $fechFin)
    {
        try {
            // Capturar la fecha y hora actual
            $fechaActual = Carbon::now();
            $gestion = $fechaActual->year;
            // Convertir fechas a objetos Carbon
            // Definir las fechas
            $fechaInicio = Carbon::parse($fechInicio);
            $fechaFin = Carbon::parse($fechFin);

            // Calcular la diferencia en días e incluir el día de inicio
            $diasDiferencia = $this->contarDiasHabiles($fechInicio, $fechFin);

            if ($idVacacion == 2) {
                // Obtener la primera vacación vigente para la persona en la gestión actual
                $vacacionVigente = AsignacionVacaciones::where('idpersona', $idper)
                    ->where('gestion', $gestion)
                    ->first(); // En lugar de get(), obtenemos solo el primer registro

                // Si no hay vacaciones programadas
                if (!$vacacionVigente) {
                    return response()->json([
                        'status' => false,
                        'message' => "No existe vacación programada para la gestión $gestion.",
                        'data' => null
                    ], 200);
                }

                // Verificar si tiene días disponibles
                if ($diasDiferencia>$vacacionVigente->dias_asignados ) {
                    return response()->json([
                        'status' => false,
                        'message' => "La cantidad dias de Vacación exede lo programado para gestion $gestion",
                        'data' => $vacacionVigente
                    ], 200);
                }
                return response()->json([
                    'status' => true,
                    'message' => "El personal esta seguro de programar $diasDiferencia dias de vacación de la fecha: $fechInicio hasta la fecha $fechFin ??",
                    'data' => $diasDiferencia
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'ok',
                'data' => $diasDiferencia
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al verificar vacaciones: ' . $th->getMessage()
            ], 500);
        }
    }
    private function contarDiasHabiles($fechInicio, $fechFin) {
        $fechaInicio = Carbon::parse($fechInicio);
        $fechaFin = Carbon::parse($fechFin);
        $diasHabiles = 0;
    
        // Recorrer cada día en el rango
        while ($fechaInicio->lte($fechaFin)) {
            // Contar solo si el día no es sábado (6) ni domingo (7)
            if ($fechaInicio->dayOfWeek !== Carbon::SATURDAY && $fechaInicio->dayOfWeek !== Carbon::SUNDAY) {
                $diasHabiles++;
            }
            // Avanzar al siguiente día
            $fechaInicio->addDay();
        }
    
        return $diasHabiles;
    }

}
