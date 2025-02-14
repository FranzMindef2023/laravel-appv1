<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\AsignacionVacaciones; 
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class AsignacionVacacionesController extends Controller
{
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
        try {
            // return $request->all();
            // $iduser = $request->input('iduser'); // El usuario viene aparte en el request
            // Validar los datos masivos
            $validated = $request->validate([
                'vacacion' => 'required|array',
                'vacacion.*.id' => 'required|exists:personas,idpersona',
                'vacacion.*.gestion_actual' => 'required|integer',
                'vacacion.*.anios' => 'required|integer',
                'vacacion.*.dias_vacaciones' => 'required|integer',
                'vacacion.*.dias' => 'required|integer',
            ]);
            // Validar y registrar cada parte
            $resultados = [];
            foreach ($validated['vacacion'] as $vacacion) {
                // Validar si ya existe un registro para este idassig en la fecha actual
                if (AsignacionVacaciones::where('idpersona', $vacacion['id'])
                    ->where('gestion', $vacacion['gestion_actual'])
                    ->exists()) {
                    $resultados[] = [
                        'vacaciones' => $vacacion,
                        'status' => false,
                        'message' => "Ya existe un registro para idpersona {$vacacion['id']} en la gestion {$vacacion['gestion_actual']}.",
                    ];
                    continue;
                }
                $vacacion['idpersona'] = $vacacion['id'];
                $vacacion['gestion'] = $vacacion['gestion_actual'];
                $vacacion['anios_servicio'] = $vacacion['anios'];
                $vacacion['dias_asignados'] = $vacacion['dias_vacaciones'];
                $vacacion['dias_servicio'] = $vacacion['dias'];
                // Registrar el vacacion
                $registro = AsignacionVacaciones::create($vacacion);

                $resultados[] = [
                    'vacacion' => $vacacion,
                    'status' => true,
                    'message' => 'Vacacion registrado correctamente.',
                    'data' => $registro,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Todos las vacaciones se registraron correctamente.',
                'results' => $resultados,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar los vacaciones: ' . $th->getMessage()
            ], 500);
        }
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
