<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table='permisos';
    protected $fillable=[
        'id_perfil','id_modulo'
    ];
}
