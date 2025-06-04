<?php

namespace App\Models;

use App\Traits\ScopeCaducadoNoDisponible;
use Illuminate\Database\Eloquent\Model;

class RecursoTarea extends Model
{
    use ScopeCaducadoNoDisponible;
    protected $table = 'recursos_tareas';
    protected $fillable = [
        'id_seccion_recurso', 'nombre','descripcion','fecha_inicio','fecha_fin','calificacion','tipo_calificacion',
        'tipo','url','tipo_archivos','numero_intento','grupal'
    ];
    public function seccionRecurso()
    {
        return $this->belongsTo(SeccionRecurso::class,'id_seccion_recurso');
    }
    public function archivos()
    {
        return $this->hasMany(Archivo::class,'id_referencia')
            ->where('referencia',static::getTable());
    }
    public static function getTipoArchivos()
    {
        return array_combo([
            ''=>'TODOS LOS ARCHIVOS',
            'pdf'=>'ARCHIVOS PDF',
            'doc,pdf,docx,txt'=>'ARCHIVOS DE DOCUMENTO(.pdf,.doc,.docx,.txt)',
            'xls,xlsx'=>'ARCHIVOS DE EXCEL(.xls,.xlsx)',
        ]);
    }
    public function recursoTareaEstudiante()
    {
        return $this->hasMany(RecursoTareaEstudiante::class, 'id_recurso_tarea');
    }
    public function scopeWithTareaEstudiante($query,$idCursoMatricula)
    {
        return $query->with(['recursoTareaEstudiante' => function ($query) use($idCursoMatricula) {
            $query->where('estado', 'A')
                ->select(
                    'id', // Clave primaria
                    'id_recurso_tarea', // Relación con RecursoTarea
                    'id_curso_matricula', // Relación con CursoMatricula
                    'puntaje', // Puntaje obtenido
                    'estado_calificacion', // Estado de la calificación
                    'estado_envio', // Estado del envío
                    'estado', // Estado general
                    'observaciones',
                    'fecha_calificacion',
                )->when($idCursoMatricula!=0, function ($query) use ($idCursoMatricula) {
                    $query->where('id_curso_matricula', $idCursoMatricula)
                        ->addSelect('created_at')
                        ->with(['archivos' => function ($query) {
                            $query->select(
                                'id',
                                'id_referencia',
                                'nombre',
                                'archivo'
                            );
                        }])
                        ->orderBy('created_at', 'asc');
                });
        }]);
    }

    // Accessor para calcular intentos_disponibles
    public function getIntentosDisponiblesAttribute()
    {
        $intentosUsados = $this->recursoTareaEstudiante()->count();
        return max(0, $this->numero_intento - $intentosUsados);
    }
    public static function getInfoCursoMatriculaEstudiante($id){
        $info=CursoDocente::selectRaw("t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases, cm.id as id_curso_matricula,
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
        ->where('cm.id',$id)
        ->first();
        return $info;
    }
}
