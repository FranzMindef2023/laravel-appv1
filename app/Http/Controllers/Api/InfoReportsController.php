<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InfoReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexReporPartes(int  $iduser)
    {
        try {
            // Consulta a la base de datos
            $results = DB::table('partesdiarias as p')
                ->select(
                    'p.gestion',
                    'p.gestion AS id',
                    'p.estado',
                    'p.estado AS name',
                    'p.fechaparte',
                    DB::raw('COUNT(p.idpersona) AS total'),
                    'p.iduser',
                    DB::raw("SUM(CASE WHEN p.forma_noforma = 'Forma' THEN 1 ELSE 0 END) AS total_forma"),
                    DB::raw("SUM(CASE WHEN p.forma_noforma = 'No Forma' THEN 1 ELSE 0 END) AS total_no_forma"),
                    'p.efectivo'
                )
                ->where('p.iduser', $iduser) // Filtrar por el iduser recibido desde el frontend
                ->groupBy('p.gestion', 'p.fechaparte', 'p.iduser', 'p.efectivo','p.estado')
                ->orderBy('p.fechaparte', 'desc')
                ->limit(5)
                ->get();

            // Retornar respuesta en formato JSON
            return response()->json([
                'status' => true,
                'message' => 'Datos obtenidos exitosamente.',
                'data' => $results
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retornar errores de validación
            return response()->json([
                'status' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function indexReporPartesRrHh()
    {
        try {
            // Consulta a la base de datos
            $results = DB::table('partesdiarias as p')
                ->select(
                    'p.gestion',
                    'p.gestion AS id',
                    'p.estado',
                    'p.estado AS name',
                    'p.fechaparte',
                    DB::raw('COUNT(p.idpersona) AS total'),
                    DB::raw("SUM(CASE WHEN p.forma_noforma = 'Forma' THEN 1 ELSE 0 END) AS total_forma"),
                    DB::raw("SUM(CASE WHEN p.forma_noforma = 'No Forma' THEN 1 ELSE 0 END) AS total_no_forma"),
                ) 
                ->groupBy('p.gestion', 'p.fechaparte','p.estado')
                ->orderBy('p.fechaparte', 'desc')
                ->limit(5)
                ->get();

            // Retornar respuesta en formato JSON
            return response()->json([
                'status' => true,
                'message' => 'Datos obtenidos exitosamente.',
                'data' => $results
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retornar errores de validación
            return response()->json([
                'status' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }
    public function userSeguimiento(){
        try {
            // Consulta a la base de datos
            $results = DB::table('users as u')
                        ->select([
                            'u.iduser',
                            'u.iduser AS id',
                            'u.iduser AS name',
                            'u.ci',
                            'u.celular',
                            'org.nomorg',
                            DB::raw("CONCAT(u.grado, ' ', u.nombres, ' ', u.appaterno, ' ', u.nombres) AS completo"),
                            DB::raw("STRING_AGG(DISTINCT CAST(ua.idorg AS TEXT), ', ') AS organizaciones_acceso"),
                            DB::raw("COUNT(DISTINCT a.idpersona) AS personas_control"),
                            DB::raw("COALESCE(MAX(sub.registros_hoy), 0) AS parte")
                        ])
                        ->join('user_accesos as ua', 'u.iduser', '=', 'ua.iduser')
                        ->join('organizacion as o', 'o.idorg', '=', 'ua.idorg')
                        ->join('organizacion as org', 'u.idorg', '=', 'org.idorg')
                        ->leftJoin('assignments as a', function ($join) {
                            $join->on('a.idorg', '=', 'ua.idorg')
                                ->whereNull('a.enddate');
                        })
                        ->leftJoin(DB::raw("
                            (SELECT pd.iduser, COUNT(*) AS registros_hoy
                            FROM partesdiarias pd
                            WHERE DATE(pd.fechahora) = CURRENT_DATE
                            GROUP BY pd.iduser) as sub
                        "), 'sub.iduser', '=', 'u.iduser')
                        ->where('u.status', true)
                        ->groupBy('u.iduser', 'u.ci', 'u.nombres', 'u.appaterno', 'u.usuario', 'org.nomorg')
                        ->orderBy('u.nombres')
                        ->orderBy('u.appaterno')
                        ->get();

            // Retornar respuesta en formato JSON
            return response()->json([
                'status' => true,
                'message' => 'Datos obtenidos exitosamente.',
                'data' => $results
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retornar errores de validación
            return response()->json([
                'status' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }
    /**REPORTES DE CUADROS DE INICIO DEL SISTEMA */
    public function userHomeCuadros(){
        try {
            // Consulta a la base de datos
            $results = DB::table('users')
                        ->where('status', true)
                        ->count();

            // Retornar respuesta en formato JSON
            return response()->json([
                'status' => true,
                'message' => 'Datos obtenidos exitosamente.',
                'data' => $results
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retornar errores de validación
            return response()->json([
                'status' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }
    public function countPersonal()
    {
        try {
            $count = DB::table('personas')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->whereNull('assignments.enddate')
                ->whereNull('assignments.motivofin')
                ->count();

            return response()->json([
                'status' => true,
                'message' => 'Cantidad de personas activas en la institución',
                'total' => $count
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al contar las personas activas: ' . $e->getMessage()
            ], 500);
        }
    }
    public function countNovedades()
    {
        try {
            $date = Carbon::today()->toDateString();
            $count = DB::table('personas')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->leftJoin('novedades', 'assignments.idassig', '=', 'novedades.idassig')
                ->where('novedades.activo', true)
                ->whereDate('novedades.startdate', '<=', $date)
                ->whereDate('novedades.enddate', '>=', $date)
                ->count();

            return response()->json([
                'status' => true,
                'message' => 'Cantidad de novedades encontradas para la fecha proporcionada',
                'total' => $count
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al contar las personas activas: ' . $e->getMessage()
            ], 500);
        }
    }
    public function countPartePersona()
    {
        try {
            $count = DB::table('partesdiarias')
                    ->whereDate('fechaparte', DB::raw('CURRENT_DATE'))
                    ->count();

            return response()->json([
                'status' => true,
                'message' => 'Cantidad de partes diarias para hoy',
                'total' => $count
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al contar las personas activas: ' . $e->getMessage()
            ], 500);
        }
    }
}