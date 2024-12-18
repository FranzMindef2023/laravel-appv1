<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class TipoNovedad extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;
    // Define el nombre correcto de la tabla
    protected $table = 'tiponovedad';

    // Establecer la clave primaria
    protected $primaryKey = 'idnov';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'novedad','status'
    ];
    public function novedades()
    {
        return $this->hasMany(Novedades::class, 'idnov', 'idnov');
    }
}
