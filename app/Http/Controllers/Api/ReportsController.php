<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon; // Importa Carbon para manejar fechas
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function generatePDF()
    {
        // Obtener la fecha actual en el formato deseado
        $currentDate = Carbon::now()->locale('es')->isoFormat('LL'); // Ejemplo: "20 de enero de 2025"

        // Datos que pasarás a la vista
        $data = [
            'title' => 'Reporte de Ejemplo',
            'content' => 'Este es el contenido dinámico que se incluirá en el PDF.',
            'date' => "La Paz, $currentDate", // Pasa la fecha dinámica al template
        ];

        // Cargar la vista y generar el PDF
        $pdf = app(PDF::class); // Instancia de DomPDF
        $pdf->loadView('reports.template', $data);

        // Descargar el archivo PDF
        return $pdf->download('reporte-ejemplo.pdf');
    }
    public function listNovedadesByDate($iduser, $date) {
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
            $users = DB::table('users as u')
            ->select(
                DB::raw("CONCAT(u.grado, ' ', u.nombres, ' ', u.appaterno, ' ', u.apmaterno) as nombre_completo"),
                'o.nomorg',
                'p.nompuesto'
            )
            ->join('organizacion as o', 'u.idorg', '=', 'o.idorg')
            ->join('puestos as p', 'u.idpuesto', '=', 'p.idpuesto')
            ->where('u.status', true)
            ->where('u.iduser', $iduser)
            ->first();
            // Obtener las personas que pertenecen a los idorg permitidos con novedades en la fecha proporcionada
            $novedades = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->leftJoin('novedades', 'assignments.idassig', '=', 'novedades.idassig')
                ->leftJoin('tiponovedad', 'novedades.idnov', '=', 'tiponovedad.idnov')
                ->select(
                    'organizacion.nomorg as organizacion',
                    'puestos.nompuesto as puesto',
                    'novedades.startdate',
                    'novedades.enddate',
                    'novedades.descripcion',
                    'tiponovedad.novedad as tipo_novedad',
                    DB::raw("TO_CHAR(novedades.startdate, 'DD/MM/YYYY') as inicio"),
                    DB::raw("TO_CHAR(novedades.enddate, 'DD/MM/YYYY') as fin"),
                    DB::raw("
                        CASE
                            WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                        END AS name
                    ")
                )
                ->whereIn('assignments.idorg', $accessibleOrgs)
                ->where('novedades.activo', true)
                ->whereDate('novedades.startdate', '<=', $date)
                ->whereDate('novedades.enddate', '>=', $date)
                ->orderBy('personas.idgrado', 'asc')
                ->get();

            if ($novedades->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron novedades para la fecha proporcionada.',
                    'data' => []
                ], 404);
            }

            return $this->generateDemostracion($novedades,$users);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las novedades: ' . $e->getMessage()
            ], 500);
        }
    }
    public function solPermisosRrhh($iduser, $date) {
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
            $users = DB::table('users as u')
            ->select(
                DB::raw("CONCAT(u.grado, ' ', u.nombres, ' ', u.appaterno, ' ', u.apmaterno) as nombre_completo"),
                'o.nomorg',
                'p.nompuesto'
            )
            ->join('organizacion as o', 'u.idorg', '=', 'o.idorg')
            ->join('puestos as p', 'u.idpuesto', '=', 'p.idpuesto')
            ->where('u.status', true)
            ->where('u.iduser', $iduser)
            ->first();
            // Obtener las personas que pertenecen a los idorg permitidos con novedades en la fecha proporcionada
            $novedades = DB::table('personas')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('sexos', 'personas.idsexo', '=', 'sexos.idsexo')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('statuscvs', 'personas.idcv', '=', 'statuscvs.idcv')
                ->leftJoin('assignments', 'personas.idpersona', '=', 'assignments.idpersona')
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->leftJoin('novedades', 'assignments.idassig', '=', 'novedades.idassig')
                ->leftJoin('tiponovedad', 'novedades.idnov', '=', 'tiponovedad.idnov')
                ->select(
                    'organizacion.nomorg as organizacion',
                    'puestos.nompuesto as puesto',
                    'novedades.startdate',
                    'novedades.enddate',
                    'novedades.descripcion',
                    'tiponovedad.novedad as tipo_novedad',
                    DB::raw("TO_CHAR(novedades.startdate, 'DD/MM/YYYY') as inicio"),
                    DB::raw("TO_CHAR(novedades.enddate, 'DD/MM/YYYY') as fin"),
                    DB::raw("
                        CASE
                            WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                        END AS name
                    ")
                )
                ->where('novedades.activo', true)
                ->whereDate('novedades.startdate', '<=', $date)
                ->whereDate('novedades.enddate', '>=', $date)
                ->orderBy('personas.idgrado', 'asc')
                ->get();

            if ($novedades->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron novedades para la fecha proporcionada.',
                    'data' => []
                ], 404);
            }

            return $this->generateDemostracion($novedades,$users);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las novedades: ' . $e->getMessage()
            ], 500);
        }
    }
    private function generateDemostracion($novedades,$users) {
        // Obtener la fecha actual en formato adecuado
        $currentDate = Carbon::now()->locale('es')->isoFormat('LL'); // Ejemplo: "20 de enero de 2025"

        // Datos que pasarás a la vista
        $data = [
            'title' => 'Demostración',
            'novedades' => $novedades,
            'date' => "La Paz, $currentDate", // Fecha dinámica
            'user'=>$users
        ];

        // Generar el PDF utilizando DomPDF
        $pdf = app(PDF::class);
        $pdf->loadView('reports.demostracion', $data);

        // Retornar el PDF generado
        return $pdf->download('demostracion.pdf');
    }

    public function parteReportsGeneral($iduser, $date)
    {
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
            $users = DB::table('users as u')
            ->select(
                DB::raw("CONCAT(u.grado, ' ', u.nombres, ' ', u.appaterno, ' ', u.apmaterno) as nombre_completo"),
                'o.nomorg',
                'p.nompuesto'
            )
            ->join('organizacion as o', 'u.idorg', '=', 'o.idorg')
            ->join('puestos as p', 'u.idpuesto', '=', 'p.idpuesto')
            ->where('u.status', true)
            ->where('u.iduser', $iduser)
            ->first();
            // Consulta general adaptada en Laravel
            $query = "
                SELECT
                    descripcion,
                    SUM(oficiales_generales) AS oficiales_generales,
                    SUM(oficiales_superiores) AS oficiales_superiores,
                    SUM(oficiales_subalternos) AS oficiales_subalternos,
                    SUM(suboficiales) AS suboficiales,
                    SUM(sargentos) AS sargentos,
                    SUM(civiles) AS civiles,
                    SUM(total_general) AS total_general
                FROM (
                    -- Primera consulta: Efectivos activos
                    SELECT
                        1 AS orden,
                        'EFECTIVO' AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(a.idassig), 0) AS total_general
                    FROM assignments a
                    LEFT JOIN personas p ON a.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    WHERE a.enddate IS NULL AND a.motivofin IS NULL
                    GROUP BY descripcion

                    UNION ALL

                    -- Segunda consulta: EFECTIVO ACTUAL
                    SELECT
                        2 AS orden,
                        'EFECTIVO ACTUAL' AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idpersona), 0) AS total_general
                    FROM partesdiarias pd
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    WHERE pd.fechaparte = :date

                    UNION ALL

                    -- Tercera consulta: Tipos de novedades
                    SELECT
                        3 AS orden,
                        tn.novedad AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idnov), 0) AS total_general
                    FROM tiponovedad tn
                    LEFT JOIN partesdiarias pd ON tn.idnov = pd.idnov AND pd.fechaparte = :date 
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    GROUP BY tn.idnov, tn.novedad

                    UNION ALL

                    -- Cuarta consulta: Formas (y total)
                    SELECT
                        4 AS orden,
                        base.descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idpersona), 0) AS total_general
                    FROM (
                        SELECT 'Forma' AS descripcion
                        UNION ALL
                        SELECT 'No Forma' AS descripcion
                    ) AS base
                    LEFT JOIN partesdiarias pd ON pd.forma_noforma = base.descripcion AND pd.fechaparte = :date
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    GROUP BY base.descripcion

                    UNION ALL

                    -- Quinta consulta: Total general
                    SELECT
                        5 AS orden,
                        'TOTAL' AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idpersona), 0) AS total_general
                    FROM partesdiarias pd
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    WHERE pd.fechaparte = :date
                ) subquery
                GROUP BY orden, descripcion
                ORDER BY orden, descripcion;
            ";

            // Ejecutar la consulta con parámetros
            $results = DB::select($query, [
                'date' => $date,
            ]);

            if (empty($results)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron registros para la fecha proporcionada.',
                    'data' => []
                ], 404);
            }
            // return $results;
            return $this->generateParteGeneral($results,$users);
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Reporte generado correctamente.',
            //     'data' => $results
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }
    public function parteReportsUsers($iduser, $date)
    {
        try {
            // Obtener todos los idorg permitidos para el usuario
            $accessibleOrgs = DB::table('user_accesos')
                ->where('iduser', $iduser)
                ->pluck('idorg')
                ->toArray();

            if (empty($accessibleOrgs)) {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no tiene accesos asignados.',
                    'data' => []
                ], 403);
            }
            $users = DB::table('users as u')
            ->select(
                DB::raw("CONCAT(u.grado, ' ', u.nombres, ' ', u.appaterno, ' ', u.apmaterno) as nombre_completo"),
                'o.nomorg',
                'p.nompuesto'
            )
            ->join('organizacion as o', 'u.idorg', '=', 'o.idorg')
            ->join('puestos as p', 'u.idpuesto', '=', 'p.idpuesto')
            ->where('u.status', true)
            ->where('u.iduser', $iduser)
            ->first();

            // Construir placeholders dinámicos
            $placeholders = implode(',', $accessibleOrgs);
            // Consulta general adaptada en Laravel
            $query = "
                SELECT
                    descripcion,
                    SUM(oficiales_generales) AS oficiales_generales,
                    SUM(oficiales_superiores) AS oficiales_superiores,
                    SUM(oficiales_subalternos) AS oficiales_subalternos,
                    SUM(suboficiales) AS suboficiales,
                    SUM(sargentos) AS sargentos,
                    SUM(civiles) AS civiles,
                    SUM(total_general) AS total_general
                FROM (
                    -- Primera consulta: Efectivos activos
                    SELECT
                        1 AS orden,
                        'EFECTIVO' AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(a.idassig), 0) AS total_general
                    FROM assignments a
                    LEFT JOIN personas p ON a.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    WHERE a.enddate IS NULL AND a.motivofin IS NULL AND idorg IN ($placeholders) 
                    GROUP BY descripcion

                    UNION ALL

                    -- Segunda consulta: EFECTIVO ACTUAL
                    SELECT
                        2 AS orden,
                        'EFECTIVO ACTUAL' AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idpersona), 0) AS total_general
                    FROM partesdiarias pd
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    WHERE pd.fechaparte = :date AND pd.iduser = :iduser

                    UNION ALL

                    -- Tercera consulta: Tipos de novedades
                    SELECT
                        3 AS orden,
                        tn.novedad AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idnov), 0) AS total_general
                    FROM tiponovedad tn
                    LEFT JOIN partesdiarias pd ON tn.idnov = pd.idnov AND pd.fechaparte = :date AND pd.iduser = :iduser
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    GROUP BY tn.idnov, tn.novedad

                    UNION ALL

                    -- Cuarta consulta: Formas (y total)
                    SELECT
                        4 AS orden,
                        base.descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idpersona), 0) AS total_general
                    FROM (
                        SELECT 'Forma' AS descripcion
                        UNION ALL
                        SELECT 'No Forma' AS descripcion
                    ) AS base
                    LEFT JOIN partesdiarias pd ON pd.forma_noforma = base.descripcion AND pd.fechaparte = :date AND pd.iduser = :iduser
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    GROUP BY base.descripcion

                    UNION ALL

                    -- Quinta consulta: Total general
                    SELECT
                        5 AS orden,
                        'TOTAL' AS descripcion,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
                        COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
                        COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
                        COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
                        COALESCE(COUNT(pd.idpersona), 0) AS total_general
                    FROM partesdiarias pd
                    LEFT JOIN personas p ON pd.idpersona = p.idpersona
                    LEFT JOIN grados g ON p.idgrado = g.idgrado
                    WHERE pd.fechaparte = :date AND pd.iduser = :iduser
                ) subquery
                GROUP BY orden, descripcion
                ORDER BY orden, descripcion;
            ";

            // Ejecutar la consulta con parámetros
            $results = DB::select($query, [
                'date' => $date,
                'iduser' => $iduser
            ]);

            if (empty($results)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron registros para la fecha proporcionada.',
                    'data' => []
                ], 404);
            }
            // return $results;
            return $this->generateParteGeneral($results,$users);
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Reporte generado correctamente.',
            //     'data' => $results
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateParteGeneral($results,$users){
        // Obtener la fecha actual en formato adecuado
        $currentDate = Carbon::now()->locale('es')->isoFormat('LL'); // Ejemplo: "20 de enero de 2025"

        // Datos que pasarás a la vista
        $data = [
            'title' => 'Demostración',
            'parte' => collect($results), // Convertir a colección
            'date' => "La Paz, $currentDate", // Fecha dinámica,
            'user'=>$users
        ];
        

        // Generar el PDF utilizando DomPDF
        $pdf = app(PDF::class);
        $pdf->loadView('reports.partegeneral', $data);

        // Retornar el PDF generado
        return $pdf->download('partegeneral.pdf');
    }
   //Imprimir la papeleta de permiso del personal
    public function PapeletaPermiso(int $idnovedad)
    {
        try {
            // Obtener la novedad, su tipo y la asignación relacionada
            $datos = DB::table('novedades')
                ->join('assignments', 'novedades.idassig', '=', 'assignments.idassig')
                ->join('tiponovedad', 'novedades.idnov', '=', 'tiponovedad.idnov')
                ->join('personas', 'assignments.idpersona', '=', 'personas.idpersona')
                ->leftJoin('fuerzas', 'personas.idfuerza', '=', 'fuerzas.idfuerza')
                ->leftJoin('especialidades', 'personas.idespecialidad', '=', 'especialidades.idespecialidad')
                ->leftJoin('grados', 'personas.idgrado', '=', 'grados.idgrado')
                ->leftJoin('armas', 'personas.idarma', '=', 'armas.idarma')
                ->leftJoin('organizacion', 'assignments.idorg', '=', 'organizacion.idorg')
                ->leftJoin('puestos', 'assignments.idpuesto', '=', 'puestos.idpuesto')
                ->select(
                    'novedades.*',
                    'tiponovedad.novedad as tipo_novedad',
                    'organizacion.nomorg as organizacion',
                    'puestos.nompuesto as puesto',
                    DB::raw("
                        CASE
                            WHEN grados.categoria = 'OG' THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad != 1 THEN CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma != 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', armas.abrearma, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            WHEN personas.idarma = 1 AND personas.idespecialidad = 1 THEN CONCAT(grados.abregrado, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                            ELSE CONCAT(grados.abregrado, ' ', especialidades.especialidad, ' ', personas.appaterno, ' ', personas.apmaterno, ' ', personas.nombres)
                        END AS nombre_completo
                    "),
                    DB::raw("CAST(personas.ci AS TEXT) AS ci"),
                    DB::raw("CAST(personas.celular AS TEXT) AS celular"),
                    DB::raw("TO_CHAR(novedades.startdate, 'DD/MM/YYYY') as desde"),
                    DB::raw("TO_CHAR(novedades.enddate, 'DD/MM/YYYY') as hasta"),
                )
                ->where('novedades.idnovedad', $idnovedad)
                ->first();

            // Verificar si no se encontró la información
            if (!$datos) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró la novedad o los datos relacionados.'
                ], 404);
            }
            // return $datos;
            return $this->generatePapeleta($datos);
            // Retornar una respuesta exitosa con los datos encontrados
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Datos encontrados para la papeleta de permiso.',
            //     'data' => $datos
            // ], 200);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }
    private function generatePapeleta($results){
        // Obtener la fecha actual en formato adecuado
        $currentDate = Carbon::now()->locale('es')->isoFormat('LL'); // Ejemplo: "20 de enero de 2025"

        // Datos que pasarás a la vista
        $data = [
            'title' => 'Demostración',
            'data' => $results, // Convertir a colección
            'date' => "La Paz, $currentDate", // Fecha dinámica,
        ];
        

        // Generar el PDF utilizando DomPDF
        $pdf = app(PDF::class);
        $pdf->loadView('reports.papeleta', $data);
        // Configurar el tamaño de la página a Letter y la orientación a Portrait
        $pdf->setPaper('letter', 'portrait');
        // Retornar el PDF generado
        return $pdf->download('papeleta.pdf');
    }

   
}