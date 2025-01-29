<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements JWTSubject,Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasFactory, Notifiable;

    // Establecer la clave primaria
    protected $primaryKey = 'iduser';

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ci',         // Cédula de identidad
        'grado',
        'nombres',    // Nombres
        'appaterno',  // Apellido paterno
        'apmaterno',  // Apellido materno
        'email',      // Correo electrónico
        'celular',    // Número de celular
        'usuario',    // Nombre de usuario
        'password',   // Contraseña
        'status',     // Estado
        'idorg',
        'idpuesto'
    ];
    // App\Models\User.php
    public function roles()
    {
        return $this->belongsToMany(
            Roles::class,    // Modelo relacionado
            'user_roles',    // Tabla intermedia
            'iduser',        // Clave foránea en la tabla intermedia para el modelo actual
            'idrol'          // Clave foránea en la tabla intermedia para el modelo relacionado
        );
    }
    public function role()
    {
        return $this->hasOne(UserRole::class, 'iduser', 'iduser');
    }
    // App\Models\User.php
    public function puesto()
    {
        return $this->belongsTo(Puestos::class, 'idpuesto', 'idpuesto');
    }
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
   

}
