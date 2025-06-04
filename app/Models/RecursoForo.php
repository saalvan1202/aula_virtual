<?php

namespace App\Models;

use App\Traits\ScopeCaducadoNoDisponible;
use Illuminate\Database\Eloquent\Model;
use App\Models\RecursoForoRespuesta;

class RecursoForo extends Model
{
    use ScopeCaducadoNoDisponible;
    protected $table = 'recursos_foros';
    protected $fillable = [
        'fecha_inicio', 'fecha_fin', 'semana',
        'modo_calificacion','puntaje','maximo_comentarios'
    ];

    public function seccionRecurso()
    {
        return $this->belongsTo(SeccionRecurso::class,'id_seccion_recurso');
    }
    public function recursoForoRespuesta()
    {
        return $this->hasMany(RecursoForoRespuesta::class, 'id_recurso_foro');
    }

    public static function getModoCalificaciones()
    {
        return array_combo([
            'MAXIMA'=>'MAXIMA',
            'PROMEDIO'=>'PROMEDIO'
        ]);
    }

    public static  function getInfoEstudiantes($id)
    {
        $info=RecursoForoRespuesta::selectRaw("p.nombres,p.apellido_paterno,cm.id as id_curso_matricula,u.id as id_usuario_estudiante")
        ->join('cursos_matriculas as cm','cm.id','=','recursos_foros_respuestas.id_curso_matricula')
        ->join('matriculas as m','m.id','=','cm.id_matricula')
        ->join('estudiantes as e','e.id','=','m.id_estudiante')
        ->join('usuarios as u','u.id','=','e.id_usuario')
        ->join('personas as p','p.id','=','u.id_persona')
        ->where('recursos_foros_respuestas.id',$id)
        ->first();
        return $info;
    }

}
