<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class ReglasVacaciones extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;

    // Establecer la clave primaria
    protected $primaryKey = 'id_regla';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'anios_servicio_min',
        'anios_servicio_max',
        'dias_vacaciones'
    ];
}
