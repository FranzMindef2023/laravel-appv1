<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRolesRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Roles; // <- Importación de User
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las organizaciones de la base de datos
            $roles = Roles::select([
                'idrol as id',
                'rol as name',
                'rol',
                DB::raw("CASE WHEN status = true THEN 'Activo' ELSE 'Inactivo' END as status"),
                DB::raw("TO_CHAR(created_at, 'DD/MM/YYYY HH24:MI:SS') as fcreate"), // Formato dd/MM/YYYY HH:MM:SS para created_at
                DB::raw("TO_CHAR(updated_at, 'DD/MM/YYYY HH24:MI:SS') as fupdate")  // Formato dd/MM/YYYY HH:MM:SS para updated_at
            ])->get();

            // Verificar si no se encontraron roles
            if ($roles->isEmpty()) {
                // Si no se encuentra ningún rol, retornar un error 404
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron roles.');
            }

            // Retornar una respuesta exitosa con los datos transformados
            return response()->json([
                'status' => true,
                'message' => 'Roles encontrados',
                'data' => $roles
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso cuando no se encuentran roles (404)
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales (500)
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los roles: ' . $e->getMessage()
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
    public function store(StoreRolesRequest $request)
    {
        try {
            // Crea el rol utilizando los datos validados del request
            $role = Roles::create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Rol de usuario registrado correctamente',
                'data' => $role,
            ], 200);

        } catch (ValidationException $e) {
            // Maneja errores de validación y devuelve un código 422
            return response()->json([
                'status' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors(), // Devuelve los errores de validación en detalle
            ], 422);

        } catch (\Throwable $th) {
            // Maneja otros errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error interno del servidor: ' . $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            // Buscar la organización por su ID
            $roles = Roles::where('idrol', $id)->firstOrFail();
    
            // Retornar una respuesta exitosa con los detalles de la organización
            return response()->json([
                'status' => true,
                'message' => 'Roles de usuarios encontrada',
                'data' => $roles
            ], 200); // Código de estado 200 para una solicitud exitosa
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra la organización, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Roles de usuarios no encontrada'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la roles: ' . $e->getMessage()
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
    public function update(StoreRolesRequest $request, int $id)
    {
        // return $request->all();
        try {
            // Buscar el usuario por iduser
            $response = Roles::where('idrol', $id)->firstOrFail();
            // Actualizar los datos de la organización con los datos validados
            $response->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Rol de Usuario actualizado correctamente',
                'data' => $response
            ], 200); // Código de estado 200 para éxito
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Rol de Usuario no encontrado'
            ], 404); // Código de estado 404 para no encontrado
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el rol de usuario: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
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
