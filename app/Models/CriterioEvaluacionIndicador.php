<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CriterioEvaluacionIndicador extends Model
{
    protected $table='criterios_evaluaciones_indicadores';
    protected $fillable=['nombre','descripcion','semana','tipo_evaluacion','porcentaje','id_curso_docente_indicador','estado_calificacion'];

    public static function getTipoEvaluaciones()
    {
        return [
            'CRITERIO'=>'CRITERIO',
            'SUSTITUTORIO'=>'EXÁMEN SUSTITUTORIO',
        ];
    }
    public static function validatePorcentaje($id,$porcentaje,$id_criterio){
        $sum=CriterioEvaluacionIndicador::where('estado','A')
        ->where('id_curso_docente_indicador',$id)
        ->sum('porcentaje');
        $obj=CriterioEvaluacionIndicador::find($id_criterio);
        if(!is_null($obj)){
            $sum=$sum-$obj->porcentaje;
        }
        if(($sum+$porcentaje)>100){
            return false;
        }
        return true;
    }
    //Llamamos a las actividades('tareas') seleccionadas en la configuración 
    public static function getActividadesTareasEstudiantes($actividades_configuracion){
        $actividades_estudiantes=DB::table('recursos_tareas_estudiantes')
        ->select('recursos_tareas_estudiantes.id','recursos_tareas_estudiantes.puntaje',
        'recursos_tareas_estudiantes.id_curso_matricula','recursos_tareas_estudiantes.id_recurso_tarea')
        ->where('recursos_tareas_estudiantes.estado', 'A')
        ->where('recursos_tareas_estudiantes.estado_calificacion', 'CALIFICADO')
        ->whereIn('recursos_tareas_estudiantes.id_recurso_tarea',array_column($actividades_configuracion,'id'))
        ->get();
        return $actividades_estudiantes;
    }
     //Llamamos a las actividades('foros') seleccionadas en la configuración 
     public static function getActividadesForosEstudiantes($actividades_configuracion){
        $actividades_estudiantes=DB::table('recursos_foros_respuestas')
        ->select('recursos_foros_respuestas.id','recursos_foros_respuestas.puntaje',
        'recursos_foros_respuestas.id_curso_matricula','recursos_foros_respuestas.id_recurso_foro')
        ->where('recursos_foros_respuestas.estado', 'A')
        ->where('recursos_foros_respuestas.estado_calificacion', 'CALIFICADO')
        ->whereIn('recursos_foros_respuestas.id_recurso_foro',array_column($actividades_configuracion,'id'))
        ->get();
        return $actividades_estudiantes;
    }
       //Llamamos a las actividades('examenes') seleccionadas en la configuración 
       public static function getActividadesExamenesEstudiantes($actividades_configuracion){
        $actividades_estudiantes=DB::table('recursos_tareas_estudiantes')
        ->select('recursos_tareas_estudiantes.id','recursos_tareas_estudiantes.puntaje',
        'recursos_tareas_estudiantes.id_curso_matricula','recursos_tareas_estudiantes.id_recurso_tarea')
        ->where('recursos_tareas_estudiantes.estado', 'A')
        ->whereIn('recursos_tareas_estudiantes.id_recurso_tarea',array_column($actividades_configuracion,'id'))
        ->get();
        return $actividades_estudiantes;
    }
    
    public static function getAlumnosCriteriosNotas($id,$uuid){
        $estudiantes=DB::select("select
        u.id as id_usuario,
        p.apellido_paterno||' '||p.apellido_materno||' '||p.nombres as estudiante,
        tdi.abreviatura as tipo_documento,
        p.numero_documento,
        coalesce(nci.id,'-1') as id_nota_criterio,
        nci.asistio as asistio ,
        nci.nota as nota_criterio,
        cm.id as id_curso_matricula
        from
        usuarios u
        inner join personas p on p.id=u.id_persona
        inner join tipo_documentos_identidades tdi on tdi.id=p.id_tipo_documento_identidad
        inner join estudiantes as e on e.id_usuario=u.id
        inner join matriculas as m on (m.id_estudiante=e.id and m.estado='A')
        inner join cursos_matriculas as cm on (cm.id_matricula=m.id and cm.estado='A')
        inner join cursos_docentes as cd on cd.id=cm.id_curso_docente
        left join notas_criterios_indicadores nci on (nci.id_curso_matricula=cm.id and nci.id_criterio_evaluacion_indicador=?)
        where cd.uuid=?",[$id,$uuid]);
        return $estudiantes;
    }
}
