<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AsistenciaAlumno extends Model
{
    protected $table='asistencias_estudiantes';
    protected $fillable=[
        'tipo_asistencia','observaciones','id_clase_docente','id_curso_matricula',
    ];
}
