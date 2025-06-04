<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoDocente extends Model
{
    protected $table='cursos_docentes';
    protected $fillable=['uuid', 'id_periodo_clases','id_sede', 'id_turno', 'id_seccion', 'id_docente', 'id_cursos', 'estado'];

    protected $attributes=[
        'culminado'=>'N',
        'visible'=>'S'
    ];
    public function silabo()
    {
        return $this->belongsTo(Archivo::class,'id','id_referencia')->where('referencia',static::getTable());
    }
    public function periodoClase(){
        return $this->belongsTo(PeriodoClase::class, 'id_periodo_clases');
    }

    public function sede() {
        return $this->belongsTo(Sede::class, 'id_sede');
    }

    public function docente() {
        return $this->belongsTo(User::class, 'id_docente');
    }

    public function horario() {
        return $this->hasMany(Horario::class, 'id_curso_docente');
    }

    public function curso() {
        return $this->belongsTo(Curso::class, 'id_cursos');
    }
    public static  function getInfoCurso($uuid)
    {
        $info=CursoDocente::selectRaw("COUNT(case when cd.estado='A' then 1 end) as total_clases,COUNT(case when cd.estado_clase='DESARROLLADA' and cd.estado='A' then 1 end ) as total_desarrolladas,
        t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,
        cursos_docentes.id_periodo_clases,pc.descripcion as periodo,
        p.nombres||' '||p.apellido_paterno||' '||p.apellido_materno as docente,
        cursos_docentes.uuid,cursos_docentes.culminado,c.id as id_curso")
        ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
        ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
        ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
        ->join('ciclos as co','co.id','=','c.id_ciclo')
        ->join('periodo_clases as pc','pc.id','=','cursos_docentes.id_periodo_clases')
        ->join('usuarios as u','u.id','=','cursos_docentes.id_docente')
        ->join('personas as p','p.id','=','u.id_persona')
        ->leftjoin('clases_docentes as cd','cd.id_curso_docente','=','cursos_docentes.id')
        ->where('c.estado','A')
        ->where('cursos_docentes.estado','A')
        ->where('cursos_docentes.uuid',$uuid)
        ->where('cursos_docentes.id_docente',auth()->user()->id)
        ->groupBy('pc.descripcion','c.id','t.descripcion','s.descripcion','co.descripcion','c.descripcion','cursos_docentes.id','cursos_docentes.id_periodo_clases',
            'cursos_docentes.uuid','cursos_docentes.id_docente','p.nombres','p.apellido_paterno','p.apellido_materno')
        ->first();
        return $info;
    }
    public static function getInfoCursoEstudiante($uuid){
        $info=CursoDocente::selectRaw("t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases, cm.id as id_curso_matricula, m.id as id_matricula,
        cursos_docentes.uuid,cursos_docentes.id_docente as id_docente,pc.descripcion as periodo,p.nombres||' '||p.apellido_paterno||' '||p.apellido_materno as docente")
        ->join('cursos_matriculas as cm','cm.id_curso_docente','=','cursos_docentes.id')
        ->join('matriculas as m','m.id','=','cm.id_matricula')
        ->join('estudiantes as e','e.id','=','m.id_estudiante')
        ->join('usuarios as u','u.id','=','e.id_usuario')
        ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
        ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
        ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
        ->join('ciclos as co','co.id','=','c.id_ciclo')
        ->join('periodo_clases as pc','pc.id','=','cursos_docentes.id_periodo_clases')
        ->join('personas as p','p.id','=','u.id_persona')
        ->where('c.estado','A')
        ->where('u.id',auth()->user()->id)
        ->where('cursos_docentes.uuid',$uuid)
        ->first();
        return $info;
    }
    public static function getAlumnosCurso($uuid){
        return static::selectRaw("e.id as id_estudiante,c.uuid,cm.id,cm.nota,p.nombres||' '||p.apellido_paterno||' '||p.apellido_materno as estudiante,
        p.numero_documento,u.email,u.telefono")
        ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
        ->join('cursos_matriculas as cm','cm.id_curso_docente','=','cursos_docentes.id')
        ->join('matriculas as m','m.id','=','cm.id_matricula')
        ->join('estudiantes as e','e.id','=','m.id_estudiante')
        ->join('usuarios as u','u.id','=','e.id_usuario')
        ->join('personas as p','p.id','=','u.id_persona')
        ->where('c.estado','A')
        ->where('m.estado','A')
        ->where('cursos_docentes.uuid',$uuid)
        //->where('cursos_docentes.id',$id)
        ->get();
    }
}
