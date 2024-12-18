<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use App\Http\Requests\StoreRolesRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // <- Importación de User
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class AuthController extends Controller
{
   /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['status' => false, 'message' => 'Credenciales incorrectas'], 401);
        }
    
        // Actualizar la fecha de último inicio de sesión
        $user = auth()->user();
        $user->update(['last_login' => now()]);
    
        // Responder con el token y los datos del usuario
        return $this->respondWithToken($token);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60, // Tiempo de expiración en segundos
            'post' => [
                'iduser' => $user->iduser,
                'ci' => $user->ci,
                'nombres' => $user->nombres,
                'appaterno' => $user->appaterno,
                'apmaterno' => $user->apmaterno,
                'email' => $user->email,
                'celular' => $user->celular,
                'usuario' => $user->usuario,
                'status' => $user->status,
                'last_login' => $user->last_login,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'ci' => 'required|string|unique:users,ci',
        'grado' => 'required|string|min:3|max:30',
        'nombres' => 'required|string|min:3|max:255',
        'appaterno' => 'nullable|string|min:3|max:255',
        'apmaterno' => 'nullable|string|min:3|max:255',
        'email' => 'nullable|email|unique:users,email',
        'celular' => 'required|digits_between:8,15',
        'usuario' => 'required|string|min:3|max:255|unique:users,usuario',
        'password' => 'required|min:8',
        'status' => 'required|boolean',
        'idorg' => 'required|integer|exists:organizacion,idorg',
        'idpuesto' => 'required|integer|exists:puestos,idpuesto',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }
    // return $request->all();
    $user = User::create(array_merge(
        $validator->validated(),
        ['password' => bcrypt($request->password)]
    ));

    return response()->json([
        'message' => 'Usuario registrado correctamente',
        'user' => $user
    ], 200);
}
}