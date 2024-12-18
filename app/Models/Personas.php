<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Personas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;

    // Establecer la clave primaria
    protected $primaryKey = 'idpersona';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'nombres',
        'appaterno',
        'apmaterno',
        'ci',
        'complemento',
        'codper',
        'email',
        'celular',
        'fechnacimeinto',
        'fechaegreso',
        'gsanguineo',
        'carnetmil',
        'carnetseg',
        'tipoper',
        'estserv',
        'idfuerza',
        'idespecialidad',
        'idgrado',
        'idsexo',
        'idarma',
        'idcv',
        'status',
        'idsituacion',
        'idexpedicion'
    ];
    
}
