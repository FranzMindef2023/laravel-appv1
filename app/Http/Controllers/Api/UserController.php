<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User; // <- Importación de User  
use App\Models\UserRole;
use App\Models\UserAccesos;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Roles; 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Obtener todas las organizaciones de la base de datos
            $users = User::select([
                'users.iduser as id',
                'users.grado',
                'users.appaterno',
                'users.apmaterno',
                'users.nombres',
                'users.ci',
                DB::raw("CONCAT(users.grado, ' ', users.appaterno, ' ', users.apmaterno, ' ', users.nombres) as name"), // Concatenar las columnas
                'users.email',
                'users.celular',
                'users.usuario',
                'users.idorg',
                'users.idpuesto',
                'users.status',
                DB::raw("TO_CHAR(users.last_login, 'DD/MM/YYYY HH24:MI:SS') as lastlogin"), // Formatear fecha de último login
                DB::raw("CASE WHEN users.status = true THEN 'Activo' ELSE 'Inactivo' END as estado"), // Transformar estado
                DB::raw("TO_CHAR(users.created_at, 'DD/MM/YYYY HH24:MI:SS') as fcreate"), // Formatear created_at
                DB::raw("TO_CHAR(users.updated_at, 'DD/MM/YYYY HH24:MI:SS') as fupdate"), // Formatear updated_at
                'puestos.nompuesto as puesto', // Nombre del puesto
                'organizacion.nomorg as organizacion', // Nombre de la organización
                'organizacion.sigla as sigla' // Sigla de la organización
            ])
            ->join('puestos', 'users.idpuesto', '=', 'puestos.idpuesto') // Join con la tabla puestos
            ->join('organizacion', 'users.idorg', '=', 'organizacion.idorg') // Join con la tabla organizacion
            ->get();
        
            // Verificar si no se encontraron puesto
            if ($users->isEmpty()) {
                // Si no se encuentra ninguna organización, retornar un error 404
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron puestos.');
            }
        
            // Retornar una respuesta exitosa con los datos encontrados
            return response()->json([
                'status' => true,
                'message' => 'Lista de usuarios obtenida correctamente.',
                'data' => $users
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
                'message' => 'Error al obtener las usuarios: ' . $e->getMessage()
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
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create(array_merge(
                $request->validated()
            ));

            return response()->json([
                'status' => true,
                'message' => 'Usuario registrado correctamente',
                'data'=> $request->all()
            ], 200);
            
        } catch (\Throwable $th) {
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
        try {
            // Buscar el usuario por ID
            $user = User::findOrFail($id);
    
            // Retornar una respuesta exitosa con los detalles de la organización
            return response()->json([
                'status' => true,
                'message' => 'Usuario encontrado.',
                'data' => $user
            ], 200); // Código de estado 200 para una solicitud exitosa
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra la organización, retornar un error 404
            return response()->json([
                'status' => false,
                'message' => 'Usuario encontrado.'
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el usuario: ' . $e->getMessage()
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
    public function update(StoreUserRequest $request, int $id)
    {
        try {
            // Buscar el usuario por iduser
            $user = User::where('iduser', $id)->firstOrFail();

            // Obtener los datos validados del request
            $validatedData = $request->validated();

            // Verificar si se proporcionó un valor de contraseña no vacío
            if ($request->filled('password')) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            } else {
                // Si no se proporciona contraseña, eliminarla de los datos validados
                unset($validatedData['password']);
            }

            // Actualizar el usuario con los datos validados
            $user->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => $user,
            ], 200); // Código de estado 200 para éxito
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado',
            ], 404); // Código de estado 404 para no encontrado
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el usuario: ' . $th->getMessage(),
            ], 500); // Código de estado 500 para errores generales
        }
    }

    /**
     * Update estado de usuario
     */
    /**
     * Update estado de usuario
     */
    public function updateStatusUser(Request $request, int $id)
    {
        try {
            // Validar solo el campo status
            $validatedData = $request->validate([
                'status' => 'required|boolean' // Asegurar que solo se actualiza el estado (true o false)
            ]);

            // Buscar el usuario por iduser
            $user = User::where('iduser', $id)->firstOrFail();

            // Actualizar solo el estado
            $user->update(['status' => $validatedData['status']]);

            return response()->json([
                'status' => true,
                'message' => 'Estado del usuario actualizado correctamente',
                'data' => $user,
            ], 200); // Código de estado 200 para éxito
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado',
            ], 404); // Código de estado 404 para no encontrado
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors(),
            ], 422); // Código de estado 422 para validación fallida
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el estado del usuario: ' . $th->getMessage(),
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
    /**
     * Remove the specified resource from storage.
    */
    public function asignarRoles(Request $request){
        try {
            // Validar los datos del request
            $validatedData = $request->validate([
                'iduser' => 'required|numeric',
                'idrol' => 'required|numeric'
            ]);

            // Obtener el iduser del request
            $id = $validatedData['iduser'];

            // Buscar los roles por usuario y eliminarlos
            UserRole::where('iduser', $id)->delete();

            // Crear el nuevo rol para el usuario
            $userRole = UserRole::create([
                'iduser' => $id,
                'idrol' => $validatedData['idrol']
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Rol asignado correctamente',
                'data' => $userRole
            ], 200); // Código de estado 200 para éxito
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el rol de usuario: ' . $th->getMessage()
            ], 500); // Código de estado 500 para errores generales
        }
    }
    public function showroluser(int $id)
    {
        try {
            // Roles asignados al usuario con status en true
            $assignedRoles = \DB::table('roles')
                ->join('user_roles', 'roles.idrol', '=', 'user_roles.idrol')
                ->where('user_roles.iduser', $id)
                ->where('roles.status', true) // Filtrar por status true
                ->select('roles.idrol', 'roles.rol', \DB::raw('1 as assigned'))
                ->get();

            // Roles no asignados al usuario con status en true
            $unassignedRoles = \DB::table('roles')
                ->leftJoin('user_roles', function ($join) use ($id) {
                    $join->on('roles.idrol', '=', 'user_roles.idrol')
                        ->where('user_roles.iduser', '=', $id);
                })
                ->whereNull('user_roles.idrol')
                ->where('roles.status', true) // Filtrar por status true
                ->select('roles.idrol', 'roles.rol', \DB::raw('0 as assigned'))
                ->get();

            // Combinar los roles asignados y no asignados
            $roles = $assignedRoles->merge($unassignedRoles);

            // Retornar la respuesta exitosa
            return response()->json([
                'status' => true,
                'message' => 'Roles encontrados para el usuario',
                'data' => $roles
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los roles: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Register a new access for a user.
     */
    public function registrarAcceso(Request $request) {
        try {
            // Validar los datos del request
            $validatedData = $request->validate([
                'iduser' => 'required|numeric',
                'idorg' => 'required|numeric',
            ]);

            // Crear el nuevo acceso para el usuario
            $UserAccesos = UserAccesos::create([
                'iduser' => $validatedData['iduser'],
                'idorg' => $validatedData['idorg'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Acceso registrado correctamente',
                'data' => $UserAccesos,
            ], 200); // Código de estado 200 para creación exitosa
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el acceso: ' . $th->getMessage(),
            ], 500); // Código de estado 500 para errores generales
        }
    }
    /**
     * Remove a specified access from a user.
     */
    public function eliminarAcceso(int $iduser, int $idorg) {
        try {
            // Buscar y eliminar el acceso
            $deleted = UserAccesos::where('iduser', $iduser)
                ->where('idorg', $idorg)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'status' => true,
                    'message' => 'Acceso eliminado correctamente',
                ], 200); // Código de estado 200 para éxito
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'El acceso no existe o ya fue eliminado',
                ], 404); // Código de estado 404 si no se encuentra el recurso
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el acceso: ' . $th->getMessage(),
            ], 500); // Código de estado 500 para errores generales
        }
    }

    public function pruebasGas(){
        return 'conectado api';
    }
    /**
     * // Obtener todos los usuarios activos con sus roles y puesto
     */
    public function userIndex()
    {
        try {
            
            $users = User::where('status', true) // Filtrar usuarios activos
                ->with(['roles', 'puesto']) // Cargar roles y puesto relacionados
                ->get()
                ->map(function ($user) {
                    // Concatenar grado, nombres, appaterno y apmaterno como name
                    $user->name = trim("{$user->grado} {$user->nombres} {$user->appaterno} {$user->apmaterno}");

                    // Aplanar iduser como id
                    $user->id = $user->iduser;

                    // Aplanar los datos del rol
                    $user->idrol = $user->roles->isNotEmpty() ? $user->roles->first()->idrol : null;
                    $user->rol = $user->roles->isNotEmpty() ? $user->roles->first()->rol : null;

                    // Aplanar los datos del puesto
                    $user->nompuesto = $user->puesto ? $user->puesto->nompuesto : null;

                    // Eliminar datos innecesarios para aplanar la estructura
                    unset($user->roles, $user->puesto);

                    return $user;
                });

            // Verificar si no se encontraron usuarios
            if ($users->isEmpty()) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontraron usuarios activos.');
            }

            // Retornar una respuesta exitosa con los datos transformados
            return response()->json([
                'status' => true,
                'message' => 'Usuarios activos encontrados',
                'data' => $users
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar el caso cuando no se encuentran usuarios (404)
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Manejo de errores generales (500)
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage()
            ], 500);
        }
    }
    public function showUserAccesses(int $iduser, int $idpadre)
    {
        try {
            // Obtener hijos directos del nodo padre
            $childOrganizations = \DB::table('organizacion')
                ->where('idpadre', '=', $idpadre)
                ->where('status', true) // Solo organizaciones activas
                ->pluck('idorg')
                ->toArray();

            if (empty($childOrganizations)) {
                return response()->json([
                    'status' => true,
                    'message' => 'No hay organizaciones hijas para el ID proporcionado.',
                    'data' => []
                ], 200);
            }

            // Obtener accesos asignados
            $assignedAccesses = \DB::table('organizacion')
                ->join('user_accesos', 'organizacion.idorg', '=', 'user_accesos.idorg')
                ->where('user_accesos.iduser', $iduser)
                ->whereIn('organizacion.idorg', $childOrganizations)
                ->select(
                    'organizacion.idorg AS idorgani',
                    'organizacion.nomorg',
                    'organizacion.sigla',
                    'organizacion.idpadre',
                    \DB::raw('1 as assigned') // Marcado como asignado
                )
                ->get();

            // Obtener accesos no asignados
            $unassignedAccesses = \DB::table('organizacion')
                ->leftJoin('user_accesos', function ($join) use ($iduser) {
                    $join->on('organizacion.idorg', '=', 'user_accesos.idorg')
                        ->where('user_accesos.iduser', '=', $iduser);
                })
                ->whereNull('user_accesos.idorg') // Solo organizaciones no asignadas
                ->whereIn('organizacion.idorg', $childOrganizations)
                ->select(
                    'organizacion.idorg AS idorgani',
                    'organizacion.nomorg',
                    'organizacion.sigla',
                    'organizacion.idpadre',
                    \DB::raw('0 as assigned') // Marcado como no asignado
                )
                ->get();

            // Combinar accesos asignados y no asignados
            $accesses = $assignedAccesses->merge($unassignedAccesses);

            // Ordenar los resultados por idorg
            $sortedAccesses = $accesses->sortBy('idorgani')->values();

            return response()->json([
                'status' => true,
                'message' => 'Accesos encontrados.',
                'data' => $sortedAccesses
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los accesos: ' . $e->getMessage()
            ], 500);
        }
    }

}
