<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoDocenteIndicador extends Model
{
    protected $table='cursos_docentes_indicadores';
    protected $fillable=['codigo','descripcion','semana_inicio','semana_fin','porcentaje','id_curso_docente'];

    public static function getSemanas(): array
    {
        $result = [];
        for ($i = 1; $i <= 20; $i++) {
            $result[] = [
                'id' => $i,
                'nombre' => 'SEMANA ' . $i
            ];
        }
        return $result;
    }
    public static function validatePorcentajes($uuid,$porcentaje,$id){
        $sum=CursoDocenteIndicador::join('cursos_docentes as cd','cd.id','=','cursos_docentes_indicadores.id_curso_docente')
        ->where('cursos_docentes_indicadores.estado','A')
        ->where('cd.uuid',$uuid)
        ->sum('cursos_docentes_indicadores.porcentaje');
        $obj=CursoDocenteIndicador::find($id);
        if(!is_null($obj)){
            $sum=$sum-$obj->porcentaje;
        }
        if(($sum+$porcentaje)>100){
            return false;
        }
        return true;
    }
    public function criteriosEvaluacion(){
        return $this->hasMany(CriterioEvaluacionIndicador::class,'id_curso_docente_indicador')->where('estado','A');
    }

}
