<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNovedadesRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Novedades; 
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NovedadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las novedades incluyendo la información del tipo de novedad
            $novedades = Novedades::where('activo', true)->with('tiponovedad')->get();

            if ($novedades->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron novedades registradas.',
                    'data' => []
                ], 404); // Código de estado 404 para recurso no encontrado
            }

            return response()->json([
                'status' => true,
                'message' => 'Novedades recuperadas correctamente.',
                'data' => $novedades
            ], 200); // Código de estado 200 para solicitud exitosa

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al recuperar las novedades: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
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
    public function store(StoreNovedadesRequest $request)
    {
        try {
            // Verificar si ya existe una novedad vigente para el mismo idasig (asignación), basada en la fecha de finalización.
            $novedadesVigentes = Novedades::where('idassig', $request->input('idassig'))
                ->where('activo', true)
                ->where('enddate', '>', now())
                ->with('tiponovedad') // Traer la relación con el tipo de novedad
                ->get();

            if ($novedadesVigentes->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ya existe una novedad vigente para este usuario.',
                    'data' => $novedadesVigentes // Incluye el tipo de novedad relacionado
                ], 400); // Código de estado 400 para solicitud inválida
            }

            // Crear la nueva novedad si no existe una vigente.
            $response = Novedades::create($request->validated());

            // Recargar la novedad con la relación para incluir el tipo de novedad
            $response->load('tipoNovedad');

            return response()->json([
                'status' => true,
                'message' => 'Novedad registrada correctamente.',
                'data' => $response
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar la novedad: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }


    

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            // Buscar la novedad por ID e incluir la información del tipo de novedad
            $novedad = Novedades::with('tiponovedad')->where('idnovedad', $id)
            ->first();

            if (!$novedad) {
                return response()->json([
                    'status' => false,
                    'message' => 'Novedad no encontrada.'
                ], 404); // Código de estado 404 para recurso no encontrado
            }

            return response()->json([
                'status' => true,
                'message' => 'Novedad encontrada.',
                'data' => $novedad
            ], 200); // Código de estado 200 para una solicitud exitosa

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al mostrar la novedad: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }
    /**
     * Display a listing of the active novelties sorted by expiration date.
     */
    public function indexVigentes()
    {
        try {
            // Obtener todas las novedades vigentes, ordenadas por la fecha de vencimiento (enddate)
            $novedadesVigentes = Novedades::with('tipoNovedad')
                ->where('activo', true)
                ->where('enddate', '>', now())
                ->orderBy('enddate', 'asc')
                ->get();

            if ($novedadesVigentes->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron novedades vigentes.',
                    'data' => []
                ], 404); // Código de estado 404 para recurso no encontrado
            }

            return response()->json([
                'status' => true,
                'message' => 'Novedades vigentes recuperadas correctamente.',
                'data' => $novedadesVigentes
            ], 200); // Código de estado 200 para solicitud exitosa

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al recuperar las novedades vigentes: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
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
    public function update(StoreNovedadesRequest $request, string $id)
    {
        try {
            // Buscar la novedad por su ID
            $novedad = Novedades::findOrFail($id);

            // Actualizar los campos con los datos validados
            $novedad->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Novedad actualizada correctamente.',
                'data' => $novedad
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si la novedad no se encuentra, devolver un error 404
            return response()->json([
                'status' => false,
                'message' => 'Novedad no encontrada.'
            ], 404);
        } catch (\Throwable $th) {
            // Manejo de otros errores
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la novedad: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar la novedad por su ID
            $novedad = Novedades::findOrFail($id);
    
            // Marcar la novedad como inactiva en lugar de eliminarla
            $novedad->activo = false;
            $novedad->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Novedad desactivada correctamente.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si la novedad no se encuentra, devolver un error 404
            return response()->json([
                'status' => false,
                'message' => 'Novedad no encontrada.',
            ], 404);
        } catch (\Throwable $th) {
            // Manejo de otros errores
            return response()->json([
                'status' => false,
                'message' => 'Error al desactivar la novedad: ' . $th->getMessage()
            ], 500);
        }
    }
}
