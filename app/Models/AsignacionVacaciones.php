<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Importa la clase Model de Eloquent
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
// use OwenIt\Auditing\Contracts\Auditable;

class AsignacionVacaciones extends Model  // Extiende de Model
{
    // use \OwenIt\Auditing\Auditable;
    use HasApiTokens, HasFactory, Notifiable;

    // Establecer la clave primaria
    // protected $primaryKey = 'idrol';
    public $incrementing = false; // Deshabilitar claves incrementales automáticas
    protected $primaryKey = null; // Indicar que no hay clave primaria
    // Deshabilitar la auditoría en este modelo
    protected $auditEnabled = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idpersona',         // Nombre del rol
        'gestion',
        'anios_servicio',
        'dias_asignados',
        'dias_utilizados'
    ];
    
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
