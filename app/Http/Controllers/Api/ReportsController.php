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

            return $this->generateDemostracion($novedades);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las novedades: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateDemostracion($novedades) {
        // Obtener la fecha actual en formato adecuado
        $currentDate = Carbon::now()->locale('es')->isoFormat('LL'); // Ejemplo: "20 de enero de 2025"

        // Datos que pasarás a la vista
        $data = [
            'title' => 'Demostración',
            'novedades' => $novedades,
            'date' => "La Paz, $currentDate", // Fecha dinámica
        ];

        // Generar el PDF utilizando DomPDF
        $pdf = app(PDF::class);
        $pdf->loadView('reports.demostracion', $data);

        // Retornar el PDF generado
        return $pdf->download('demostracion.pdf');
    }

    
    
}