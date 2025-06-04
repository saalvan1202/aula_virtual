<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\PeriodoClase;
use App\Services\Variable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class NotificacionesController extends Controller{

    public function getNotificaciones(){
        if(!Variable::isStudent(auth()->user()->id_perfil)){

            return response()->json([]);
        }
        $data=[];
        $periodo=PeriodoClase::where('estado','A')->orderBy('id','desc')->limit(1)->first();
        $alumno=Estudiante::selectRaw('cm.id')
        ->join('matriculas as m','m.id_estudiante','=','estudiantes.id')
        ->join('cursos_matriculas as cm','cm.id_matricula','=','m.id')
        ->where('estudiantes.id_usuario',auth()->user()->id)
        ->where('m.id_periodo_clases',$periodo->id)
        ->get();
        $alumnoArray=$alumno->toArray();
        if (empty($alumnoArray)) {
            $alumnoArray = []; // Para evitar errores si está vacío
            return response()->json($alumnoArray);
        }
        
        $placeholders = implode(', ',array_column($alumnoArray,'id'));
        $notificaciones=DB::select(" select * from 
        (SELECT sr.descripcion, sr.id,tr.icono,tr.nombre as tipo_actividad,cd.uuid,cds.id as seccion,rt.fecha_inicio,rt.fecha_fin,
            CASE 
                WHEN COUNT(rte.id) > 0 THEN 'COMPLETADO'
                ELSE 'PENDIENTE'
            END AS estado_notificacion,c.descripcion as curso
        FROM secciones_recursos sr 
        INNER JOIN cursos_docentes_secciones cds ON cds.id = sr.id_curso_docente_seccion
        INNER JOIN tipos_recursos tr on tr.id=sr.id_tipo_recurso
        INNER JOIN cursos_docentes cd on cd.id= cds.id_curso_docente
        inner join cursos as c on c.id=cd.id_cursos
        INNER JOIN recursos_tareas rt ON (sr.id = rt.id_seccion_recurso and rt.estado='A')
        LEFT JOIN recursos_tareas_estudiantes rte ON (rt.id = rte.id_recurso_tarea and rte.id_curso_matricula in ($placeholders)) 
        where cds.id_curso_docente in (select  cm.id_curso_docente 
        from estudiantes e 
        inner join matriculas m on (e.id=m.id_estudiante)
        inner join cursos_matriculas cm on (cm.id_matricula=m.id)
        where e.id_usuario = ? and e.estado ='A' and m.estado ='A'and cm.estado = 'A' and m.id_periodo_clases =?)
        GROUP BY sr.descripcion, sr.id,c.descripcion,tr.icono,tr.nombre,cd.uuid,cds.id,rt.fecha_inicio,rt.fecha_fin
            HAVING 
        CASE 
            WHEN COUNT(rte.id) > 0 THEN 'COMPLETADO'
            ELSE 'PENDIENTE'
        END = 'PENDIENTE'
        UNION
            SELECT sr.nombre, sr.id,tr.icono,tr.nombre,cd.uuid,cds.id as seccion,rf.fecha_inicio,rf.fecha_fin, 
            CASE 
                WHEN COUNT(rfr.id) > 0 THEN 'COMPLETADO'
                ELSE 'PENDIENTE'
            END AS estado_notificacion,c.descripcion as curso
        FROM secciones_recursos sr 
        INNER JOIN cursos_docentes_secciones cds ON cds.id = sr.id_curso_docente_seccion
        INNER JOIN tipos_recursos tr on tr.id=sr.id_tipo_recurso
        INNER JOIN cursos_docentes cd on cd.id= cds.id_curso_docente
        inner join cursos as c on c.id=cd.id_cursos
        INNER JOIN recursos_foros rf ON (sr.id = rf.id_seccion_recurso and rf.estado='A')
        LEFT JOIN recursos_foros_respuestas rfr ON (rf.id = rfr.id_recurso_foro and rfr.id_curso_matricula in ($placeholders))
        where cds.id_curso_docente in (select  cm.id_curso_docente 
        from estudiantes e 
        inner join matriculas m on (e.id=m.id_estudiante)
        inner join cursos_matriculas cm on (cm.id_matricula=m.id)
        where e.id_usuario = ? and e.estado ='A' and m.estado ='A'and cm.estado = 'A' and m.id_periodo_clases =?)
        GROUP BY sr.nombre, sr.id,c.descripcion,tr.icono,tr.nombre,cd.uuid,cds.id,rf.fecha_inicio,rf.fecha_fin
            HAVING 
        CASE 
            WHEN COUNT(rfr.id) > 0 THEN 'COMPLETADO'
            ELSE 'PENDIENTE'
        END = 'PENDIENTE'
        UNION
        SELECT sr.nombre, sr.id,tr.icono,tr.nombre,cd.uuid,cds.id as seccion,re.fecha_inicio,re.fecha_fin, 
        CASE 
         WHEN COUNT(ee.id) > 0 THEN 'COMPLETADO'
         ELSE 'PENDIENTE'
        END AS estado_notificacion,c.descripcion as curso
        FROM secciones_recursos sr 
        INNER JOIN cursos_docentes_secciones cds ON cds.id = sr.id_curso_docente_seccion
        INNER JOIN tipos_recursos tr on tr.id=sr.id_tipo_recurso
        INNER JOIN cursos_docentes cd on cd.id= cds.id_curso_docente
        inner join cursos as c on (c.id=cd.id_cursos)
        inner join recursos_examenes re on (re.id_seccion_recurso=sr.id)
        left join estudiantes_examenes as ee on (ee.id_recurso_examen=re.id  and ee.id_curso_matricula in ($placeholders))
        where cds.id_curso_docente in (select  cm.id_curso_docente 
        from estudiantes e 
        inner join matriculas m on (e.id=m.id_estudiante)
        inner join cursos_matriculas cm on (cm.id_matricula=m.id)
        where e.id_usuario = ? and e.estado ='A' and m.estado ='A'and cm.estado = 'A' and m.id_periodo_clases =?)
        GROUP BY sr.nombre, sr.id,c.descripcion,tr.icono,tr.nombre,cd.uuid,cds.id,re.fecha_inicio,re.fecha_fin
            HAVING 
        CASE 
            WHEN COUNT(ee.id) > 0 THEN 'COMPLETADO'
            ELSE 'PENDIENTE'
        END = 'PENDIENTE') as resultado
        ORDER BY tipo_actividad;",[auth()->user()->id,$periodo->id,auth()->user()->id,$periodo->id,auth()->user()->id,$periodo->id]);
        $fechaHoraActual=Carbon::now();
        if(!empty($notificaciones)){
            foreach($notificaciones  as $notificacion){
                $fi=Carbon::parse($notificacion->fecha_inicio);
                $ff=Carbon::parse($notificacion->fecha_fin);
                if($fechaHoraActual->between($fi,$ff)){
                        $notificacion->hora_inicio=$fi->format('H:i');
                        $notificacion->hora_fin=$ff->format('H:i');
                        $notificacion->fecha_inicio=$fi->format('Y-m-d');
                        $notificacion->fecha_fin=$ff->format('Y-m-d');
                        $data[]=$notificacion;
                }
            }
        }
        return response()->json($data);
    }
}