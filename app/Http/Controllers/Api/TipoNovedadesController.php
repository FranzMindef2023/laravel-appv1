<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTipoNovedadesRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\TipoNovedad; // <- Importación de User
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TipoNovedadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las organizaciones de la base de datos
            $response = TipoNovedad::all();
        
            // Verificar si no se encontraron organizaciones
            if ($response->isEmpty()) {
                // Si no se encuentra ninguna organización, retornar un error 404
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron tipo de novedades.');
            }
        
            // Retornar una respuesta exitosa con los datos encontrados
            return response()->json([
                'status' => true,
                'message' => 'Tipo de novedades encontradas',
                'data' => $response
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
                'message' => 'Error al obtener los tipo de novedades: ' . $e->getMessage()
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
    public function store(StoreTipoNovedadesRequest $request)
    {
        try {
            $response = TipoNovedad::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Tipo de novedad registrado correctamente',
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
    public function show(int  $id)
    {
        try {
            // Buscar la organización por su ID
            $response = TipoNovedad::where('idnov', $id)->firstOrFail();
    
            // Retornar una respuesta exitosa con los detalles de la organización
            return response()->json([
                'status' => true,
                'message' => 'Tipo de novedad encontrada',
                'data' => $response
            ], 200); // Código de estado 200 para una solicitud exitosa
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra la organización, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Tipo de novedad no encontrada'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el Tipo de novedad: ' . $e->getMessage()
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
    public function update(StoreTipoNovedadesRequest $request, int  $id)
    {
        // return $request->all();
        try {
            // Buscar la organización por su ID
            $organizacion = TipoNovedad::where('idnov', $id)->firstOrFail();
    
            // Actualizar los datos de la organización con los datos validados
            $organizacion->update($request->validated());
    
            // Retornar una respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Tipo de novedad actualizada correctamente',
                'data' => $organizacion
            ], 200); // Código de estado 200 para una actualización exitosa
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el ID, retornamos un error 404
            return response()->json([
                'status' => false,
                'message' => 'Tipo de novedad no encontrada'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el Tipo de novedad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar la organización por su ID
            $response = TipoNovedad::where('idnov', $id)->firstOrFail();
    
            // Eliminar la organización encontrada
            $response->delete();
    
            // Retornar una respuesta exitosa
            return response()->json([
                'status' => true,
                'message' => 'Organización eliminada correctamente'
            ], 200); // Código de estado 200 para una eliminación exitosa
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra la organización, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Organización no encontrada'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar la organización: ' . $e->getMessage()
            ], 500);
        }
    }
}
