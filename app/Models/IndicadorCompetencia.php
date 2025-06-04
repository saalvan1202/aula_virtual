<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IndicadorCompetencia extends Model
{
    protected $table='indicadores_competencias';
    protected $fillable=[
        'id_modulo_competencia','numero','descripcion'
    ];
}
