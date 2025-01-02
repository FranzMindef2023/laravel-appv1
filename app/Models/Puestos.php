<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Puestos extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, Notifiable;

    // Establecer la clave primaria
    protected $primaryKey = 'idpuesto';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'nompuesto',
        'status'
    ];
    // App\Models\Puestos.php
    public function users()
    {
        return $this->hasMany(User::class, 'idpuesto', 'idpuesto');
    }

}
