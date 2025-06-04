<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EstudianteApoderado extends Model
{
    protected $table='estudiantes_apoderados';
    protected $fillable=[
        'parentesco','representante','id_apoderado','estado'
    ];
}
