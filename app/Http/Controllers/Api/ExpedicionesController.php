<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Expediciones; // <- Importación de User
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ExpedicionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todos los grados de la base de datos
            $grados = Expediciones::all();

            // Verificar si no se encontraron grados
            if ($grados->isEmpty()) {
                // Si no se encuentra ningún grado, retornar un error 404
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron grados.');
            }

            // Retornar una respuesta exitosa con los datos transformados
            return response()->json([
                'status' => true,
                'message' => 'Grados encontrados',
                'data' => $grados
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso cuando no se encuentran grados (404)
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales (500)
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los grados: ' . $e->getMessage()
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
