<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModalidadProcesoAdmision extends Model
{
    protected $table='modalidad_procesos_admision';
    protected $fillable=['id_modalidad_admision','id_proceso_admision','fecha_examen','hora_examen','vacantes','tipo'];
}
