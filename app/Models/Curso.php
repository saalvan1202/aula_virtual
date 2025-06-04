<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table='cursos';
    protected $fillable=[
        'id_plan_estudio','id_ciclo','id_modulo_competencia', 'uuid',
        'descripcion','horas_teoria','horas_practica',
        'creditos_teoria','creditos_practica'
    ];
    public function competencia()
    {
        return $this->belongsTo(ModuloCompetencia::class,'id_modulo_competencia');
    }
    public function cursoDocente(){
        return $this->hasMany(CursoDocente::class,'id_cursos');
    }
    public function scopeWithCompetencia($query)
    {
        return $this->with(['competencia'=>function($q){
            return $q->selectAutocomplete();
        }]);
    }
}
