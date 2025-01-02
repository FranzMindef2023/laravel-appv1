<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Organizacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;
    // Define el nombre correcto de la tabla
    protected $table = 'organizacion';
    // Establecer la clave primaria
    protected $primaryKey = 'idorg';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'nomorg',
        'sigla',
        'idpadre',
        'status'
    ];
    // Relación para obtener las organizaciones hijas
    public function hijos()
    {
        return $this->hasMany(Organizacion::class, 'idpadre', 'idorg');
    }
    // Relación para obtener el padre de una organización
    public function parent()
    {
        return $this->belongsTo(Organizacion::class, 'idpadre', 'idorg');
    }
    // Relación para obtener los hijos de una organización
    public function children()
    {
        return $this->hasMany(Organizacion::class, 'idpadre', 'idorg');
    }
}
