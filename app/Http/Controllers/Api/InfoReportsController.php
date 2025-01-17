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
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
// select *from ubigeo u 

// select *from organizacion o 
// select *from grados g 
// select *from statuscv s 
// select *from especialidades e 
// select *from armas a 
// select *from sexos s 
// select *from organizacion o 
// select *from puestos p 
// select *from users u 

// select *from statuscvs s 
// select *from expediciones e 
// select *from situaciones s 
// select *from personas p 
// select *from audits a 

// select *from organizacion o where o.idpadre =1300
// select *from organizacion o2 where o2.idorg between 1300 and 1399 order by o2.idorg asc 
// SELECT * 
// FROM organizacion o2 
// WHERE o2.idorg BETWEEN 1300 AND 1399 
// ORDER BY o2.idorg ASC;

// select *from organizacion o3 where o3.idpadre=1340

// select *from assignments a 

// SELECT o.idorg, o.nomorg, ua.iduser
// FROM organizacion o
// LEFT JOIN user_accesos ua
// ON o.idorg = ua.idorg AND ua.iduser = :iduser
// WHERE o.idpadre = :idpadre
//   AND o.status = true 
//   AND ua.idorg IS NULL;
//  select *from user_accesos ua 
//  update organizacion set status =true where idorg =idorg 
//  select *from user_accesos ua 

//  select *from personas p where P.
// select *from assignments a where a.idpersona=9
// select *from users u 
// select *from tiponovedad t 
// delete from tiponovedad t  

// select *from novedades n 

// select *from assignments a 

// select *from noved
// select *from horas h 
// select *from partesdiarias p 

// select *from grados g 
// select *from personas p where p.idpersona=6
// select *from assignments a where a.idpersona=6
// select *from novedades n where n.idassig=10

// SELECT 
//     tn.novedad AS descripcion,
//     COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
//     COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
//     COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
//     COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
//     COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
//     COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
//     COALESCE(COUNT(pd.idnov), 0) AS total_general
// FROM tiponovedad tn
// LEFT JOIN partesdiarias pd 
//     ON tn.idnov = pd.idnov
//     AND pd.fechaparte ='2025-01-17' and pd.iduser =4
// LEFT JOIN personas p 
//     ON pd.idpersona = p.idpersona
// LEFT JOIN grados g 
//     ON p.idgrado = g.idgrado
// GROUP BY tn.idnov, tn.novedad
// ORDER BY tn.novedad;

// SELECT 
//     'Efectivo' AS descripcion,
//     COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
//     COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
//     COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
//     COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
//     COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
//     COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
//     COALESCE(COUNT(a.idassig), 0) AS total_general
// FROM assignments a
// LEFT JOIN personas p 
//     ON a.idpersona = p.idpersona
// LEFT JOIN grados g 
//     ON p.idgrado = g.idgrado
// WHERE a.enddate IS NULL
//   AND a.motivofin IS NULL
// GROUP BY descripcion
// ORDER BY descripcion;








// select *from partesdiarias p 

// SELECT 
//     p.gestion, 
//     p.fechaparte, 
//     COUNT(p.idpersona) AS total,
//     p.iduser,
//     SUM(CASE WHEN p.forma_noforma = 'Forma' THEN 1 ELSE 0 END) AS total_forma,
//     SUM(CASE WHEN p.forma_noforma = 'No Forma' THEN 1 ELSE 0 END) AS total_no_forma,
//     p.efectivo 
// FROM 
//     partesdiarias p
// WHERE 
//     p.iduser = 4 
// GROUP BY 
//     p.gestion, p.fechaparte, p.iduser,p.efectivo 
// ORDER BY 
//     p.fechaparte DESC
// LIMIT 5;





// SELECT 
//     descripcion,
//     SUM(oficiales_generales) AS oficiales_generales,
//     SUM(oficiales_superiores) AS oficiales_superiores,
//     SUM(oficiales_subalternos) AS oficiales_subalternos,
//     SUM(suboficiales) AS suboficiales,
//     SUM(sargentos) AS sargentos,
//     SUM(civiles) AS civiles,
//     SUM(total_general) AS total_general
// FROM (
//     SELECT 
//         pd.forma_noforma AS descripcion,
//         COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
//         COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
//         COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
//         COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
//         COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
//         COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
//         COALESCE(COUNT(pd.idpersona), 0) AS total_general
//     FROM partesdiarias pd
//     LEFT JOIN personas p 
//         ON pd.idpersona = p.idpersona
//     LEFT JOIN grados g 
//         ON p.idgrado = g.idgrado
//     WHERE pd.fechaparte = '2025-01-17'
//     GROUP BY pd.forma_noforma

//     UNION ALL

//     SELECT 
//         'TOTAL' AS descripcion,
//         COALESCE(SUM(CASE WHEN g.categoria = 'OG' THEN 1 ELSE 0 END), 0) AS oficiales_generales,
//         COALESCE(SUM(CASE WHEN g.categoria = 'OSP' THEN 1 ELSE 0 END), 0) AS oficiales_superiores,
//         COALESCE(SUM(CASE WHEN g.categoria = 'OSB' THEN 1 ELSE 0 END), 0) AS oficiales_subalternos,
//         COALESCE(SUM(CASE WHEN g.categoria = 'SOF' THEN 1 ELSE 0 END), 0) AS suboficiales,
//         COALESCE(SUM(CASE WHEN g.categoria = 'SGT' THEN 1 ELSE 0 END), 0) AS sargentos,
//         COALESCE(SUM(CASE WHEN g.categoria = 'CIV' THEN 1 ELSE 0 END), 0) AS civiles,
//         COALESCE(COUNT(pd.idpersona), 0) AS total_general
//     FROM partesdiarias pd
//     LEFT JOIN personas p 
//         ON pd.idpersona = p.idpersona
//     LEFT JOIN grados g 
//         ON p.idgrado = g.idgrado
//     WHERE pd.fechaparte = '2025-01-17'
// ) subquery
// GROUP BY descripcion
// ORDER BY 
//     CASE 
//         WHEN descripcion = 'TOTAL' THEN 2 -- "TOTAL" siempre va al final
//         ELSE 1
//     END,
//     descripcion;
