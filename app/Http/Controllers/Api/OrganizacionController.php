<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrganizacionRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Organizacion; 
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrganizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las organizaciones de la base de datos
            $organizaciones = Organizacion::all();
        
            // Verificar si no se encontraron organizaciones
            if ($organizaciones->isEmpty()) {
                // Si no se encuentra ninguna organización, retornar un error 404
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron organizaciones.');
            }
        
            // Retornar una respuesta exitosa con los datos encontrados
            return response()->json([
                'status' => true,
                'message' => 'Organizaciones encontradas',
                'data' => $organizaciones
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
                'message' => 'Error al obtener las organizaciones: ' . $e->getMessage()
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
    public function store(StoreOrganizacionRequest $request)
    {
        try {
            $organizacion = Organizacion::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Organizacion registrado correctamente',
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
    public function show(int $id)
    {
        try {
            // Buscar la organización por su ID
            $organizacion = Organizacion::where('idorg', $id)->firstOrFail();
    
            // Retornar una respuesta exitosa con los detalles de la organización
            return response()->json([
                'status' => true,
                'message' => 'Organización encontrada',
                'data' => $organizacion
            ], 200); // Código de estado 200 para una solicitud exitosa
    
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
                'message' => 'Error al obtener la organización: ' . $e->getMessage()
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
    public function update(StoreOrganizacionRequest $request, int $id)
    {
        try {
            // Buscar la organización por su ID
            $organizacion = Organizacion::where('idorg', $id)->firstOrFail();
    
            // Actualizar los datos de la organización con los datos validados
            $organizacion->update($request->validated());
    
            // Retornar una respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Organización actualizada correctamente',
                'data' => $organizacion
            ], 200); // Código de estado 200 para una actualización exitosa
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el ID, retornamos un error 404
            return response()->json([
                'status' => false,
                'message' => 'Organización no encontrada'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la organización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            // Buscar la organización por su ID
            $organizacion = Organizacion::where('idorg', $id)->firstOrFail();
    
            // Eliminar la organización encontrada
            $organizacion->delete();
    
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
    public function obtenerHijos(int $id){
        try {
            // Buscar la organización padre por su ID
            $organizacionPadre = Organizacion::findOrFail($id);

            // Obtener las organizaciones hijas
            $hijos = $organizacionPadre->hijos;

            // Verificar si tiene hijos
            if ($hijos->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron organizaciones hijas para el ID '.$id.' proporcionado.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Organizaciones hijas encontradas.',
                'data' => $hijos
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Organización no encontrada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener las organizaciones hijas: ' . $e->getMessage()
            ], 500);
        }
    }

}
