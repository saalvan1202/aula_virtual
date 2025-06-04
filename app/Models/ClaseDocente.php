<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClaseDocente extends Model
{
    protected $table='clases_docentes';
    protected $fillable=['descripcion','numero','fecha','hora_inicio','hora_fin','id_curso_docente'];
}
