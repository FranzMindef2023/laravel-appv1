<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}