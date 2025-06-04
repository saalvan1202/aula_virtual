<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotaCriterioIndicador extends Model
{
    protected $table='notas_criterios_indicadores';
    protected $fillable=[
        'id_criterio_evaluacion_indicador','id_curso_matricula','asistio','nota',
    ];
}
