<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProcesoAdmision extends Model
{
    protected $table='procesos_admision';
    protected $fillable=['descripcion','tipo','fecha_inicio','fecha_fin','puntaje_minimo'];
}
