<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Novedades extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;

    // Establecer la clave primaria
    protected $primaryKey = 'idnov';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'idassig',
        'idnov',
        'descripcion',
        'startdate',
        'enddate',
        'activo'
    ];
    // Relación con el modelo TipoNovedad
    public function tipoNovedad()
    {
        return $this->belongsTo(TipoNovedad::class, 'idnov', 'idnov');
    }
}
