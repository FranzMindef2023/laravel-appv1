<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssignmentsRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Assignments; 
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssignmentsController extends Controller
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
    public function store(StoreAssignmentsRequest $request)
    {
        try {
            $response = Assignments::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Persona asignada correctamente',
                'data'=> $request->all()
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }
    public function changeAssignment(StoreAssignmentsRequest $request){
        try {
            
            // Obtener los datos validados del request
            $data = $request->validated();

            // Verificar si se envió el idassig anterior para finalizar la asignación previa
            if ($request->has('idassig')) {
                $id = $request->input('idassig');

                    // Finalizar la asignación anterior usando el id proporcionado
                    $previousAssignment = Assignments::where('idassig', $id)->firstOrFail();
                
                if ($previousAssignment) {
                    $previousAssignment->update([
                        'enddate' => $data['startdate']
                    ]);
                }
            }
            
            $response = Assignments::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Cambio de asignación realizado correctamente',
                'data' => $response
            ], 200);
            
        } catch (\Throwable $th) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(int $id)
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
    public function update(StoreAssignmentsRequest $request, int $id)
    {
        try {
            // Buscar el puesto por su ID
            $puesto = Assignments::where('idassig', $id)->firstOrFail();
        
            // Actualizar los datos del puesto con los datos validados
            $puesto->update($request->validated());
        
            // Retornar una respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'La asignacion de la persona actualizado correctamente',
                'data' => $puesto
            ], 200); // Código de estado 200 para una actualización exitosa
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el ID, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'asignacion de la persona no encontrado'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la  asignacion de la persona: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Actualiza la fecha de finalización (enddate) para una asignación existente.
     */
    public function updateEndDate(Request $request, int $id){
        try {
            // Validar que se haya proporcionado la nueva fecha de finalización
            $request->validate([
                'enddate' => 'required|date'
            ]);

            // Buscar la asignación por su ID (idassig)
            $assignment = Assignments::where('idassig', $id)->firstOrFail();

            // Actualizar solo el campo enddate
            $assignment->update([
                'enddate' => $request->input('enddate')
            ]);

            // Retornar una respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Fecha de repliegue actualizada correctamente',
                'data' => $assignment
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el ID, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Asignación no encontrada'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la fecha de finalización: ' . $e->getMessage()
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
     * Display the specified resource.
     */
    public function showAssignments(int $id)
    {
        try {
            // Buscar el registro por idpersona donde enddate sea null
            $asignaciones = Assignments::where('idpersona', $id)
                ->whereNull('enddate')
                ->first();
            
            // Verificar si no se encontró un registro
            if (!$asignaciones) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron asignaciones para la persona con idpersona: ' . $id
                ], 404);
            }
            // Dar formato a startdate
            $asignaciones->startdate = date('d-m-Y', strtotime($asignaciones->startdate));
            // Retornar el registro encontrado
            return response()->json([
                'status' => true,
                'message' => 'Asignación encontrada',
                'data' => $asignaciones
            ], 200); // Código de estado 200 para éxito
            
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al mostrar la asignación: ' . $e->getMessage()
            ], 500);
        }
    }


}
