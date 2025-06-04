<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $table='matriculas';
    protected $fillable=[
        'id_tipo_proceso_matricula','id_tipo_matricula','id_estudiante','id_periodo_clases','id_turno',
        'id_seccion','id_ciclo','fecha_matricula','id_usuario','estado'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class,'id_estudiante');
    }
    public function cursosMatricula()
    {
        return $this->hasMany(CursoMatricula::class,'id_matricula');
    }
    public function scopeWithEstudiante($query)
    {
        $query->with(['estudiante'=>function($q){
            $q->autocomplete();
        }]);
    }
    public function scopeWithCursosMatricula($query)
    {
        $query->with(['cursosMatricula'=>function($q){
            $q->selectRaw("
            cursos_matriculas.id_matricula,
            cursos_matriculas.id as id_curso_matricula,
            cd.id,
            c.descripcion as curso,
            se.descripcion as seccion,
            c.creditos_teoria,
            c.creditos_practica,
            true as check
        ")->join('cursos_docentes as cd','cd.id','=','cursos_matriculas.id_curso_docente')
                ->join('cursos as c','c.id','=','cd.id_cursos')
                ->join('secciones as se','se.id','=','cd.id_seccion')
                ->where('cursos_matriculas.estado','A');
        }]);

    }
}
