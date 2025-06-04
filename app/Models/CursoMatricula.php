<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoMatricula extends Model
{
    protected $table='cursos_matriculas';
    protected $fillable=[
        'id_matricula','id_curso_docente','fecha_nota',
        'nota','estado_nota','estado'
    ];

    public function recursoTareaEstudiante()
    {
        return $this->HasMany(RecursoTareaEstudiante::class,'id_curso_matricula');
    }
    public function recursoForoRespuesta()
    {
        return $this->HasMany(RecursoForoRespuesta::class,'id_curso_matricula');
    }
    public function estudianteExamen()
    {
        return $this->HasMany(EstudianteExamen::class,'id_curso_matricula');
    }
    public function matricula()
    {
        return $this->belongsTo(CursoMatricula::class,'id_matricula');
    }

    public static function getInfoEstudiantesCurso($id_curso_docente,$id_curso){
        $info=CursoMatricula::selectRaw("u.id, c.id as id_curso, cursos_matriculas.id as id_curso_matricula, p.nombres, p.apellido_paterno, p.apellido_materno, p.numero_documento")
        ->join('matriculas as m','cursos_matriculas.id_matricula','=','m.id')
        ->join('cursos_docentes as cd','cursos_matriculas.id_curso_docente','=','cd.id')
        ->join('cursos as c','cd.id_cursos','=','c.id')
        ->join('estudiantes as e','m.id_estudiante','=','e.id')
        ->join('usuarios as u','e.id_usuario','=','u.id')
        ->join('personas as p','u.id_persona','=','p.id')
        ->where('cursos_matriculas.estado','A')
        ->where('u.estado','A')
        ->where('cd.id', $id_curso_docente)
        ->where('c.id',$id_curso)
        ->get();
        return $info;
    }

    public static function getInfoEstudianteMatricula($id_curso_matricula){
        $info=CursoMatricula::selectRaw("u.id, c.id as id_curso, cursos_matriculas.id as id_curso_matricula, p.nombres, p.apellido_paterno, p.apellido_materno, p.numero_documento")
        ->join('matriculas as m','cursos_matriculas.id_matricula','=','m.id')
        ->join('cursos_docentes as cd','cursos_matriculas.id_curso_docente','=','cd.id')
        ->join('cursos as c','cd.id_cursos','=','c.id')
        ->join('estudiantes as e','m.id_estudiante','=','e.id')
        ->join('usuarios as u','e.id_usuario','=','u.id')
        ->join('personas as p','u.id_persona','=','p.id')
        ->where('cursos_matriculas.estado','A')
        ->where('u.estado','A')
        ->where('cursos_matriculas.id', $id_curso_matricula)
        ->first();
        return $info;
    }
}
