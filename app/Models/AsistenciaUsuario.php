<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AsistenciaUsuario extends Model
{
    protected $table='asistencias_usuarios';
    protected $fillable=[
        'tipo_asistencia','observaciones','id_periodo_clases','id_usuario','fecha'
    ];
}
