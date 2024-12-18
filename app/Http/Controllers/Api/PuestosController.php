<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePuestosRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Puestos; 
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PuestosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las organizaciones de la base de datos
            $puesto = Puestos::all();
        
            // Verificar si no se encontraron puesto
            if ($puesto->isEmpty()) {
                // Si no se encuentra ninguna organización, retornar un error 404
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron puestos.');
            }
        
            // Retornar una respuesta exitosa con los datos encontrados
            return response()->json([
                'status' => true,
                'message' => 'No se encontraron puestos',
                'data' => $puesto
            ], 200);
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso cuando no se encuentran organizaciones (404)
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales (500)
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las puestos: ' . $e->getMessage()
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
    public function store(StorePuestosRequest $request)
    {
        try {
            $puesto = Puestos::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Puesto registrado correctamente',
                'data'=> $request->all()
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            // Buscar el puesto por su ID
            $puesto = Puestos::where('idpuesto', $id)->firstOrFail();
        
            // Retornar una respuesta exitosa con los detalles del puesto
            return response()->json([
                'status' => true,
                'message' => 'Puesto encontrado',
                'data' => $puesto
            ], 200); // Código de estado 200 para una solicitud exitosa
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el puesto, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Puesto no encontrado'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el puesto: ' . $e->getMessage()
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
    public function update(StorePuestosRequest $request, int  $id)
    {
        try {
            // Buscar el puesto por su ID
            $puesto = Puestos::where('idpuesto', $id)->firstOrFail();
        
            // Actualizar los datos del puesto con los datos validados
            $puesto->update($request->validated());
        
            // Retornar una respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Puesto actualizado correctamente',
                'data' => $puesto
            ], 200); // Código de estado 200 para una actualización exitosa
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el ID, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Puesto no encontrado'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el puesto: ' . $e->getMessage()
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
