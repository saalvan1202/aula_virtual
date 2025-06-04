<?php

namespace App\Http\Controllers;

use App\Models\CursoDocente;
use App\Models\CursoMatricula;
use App\Models\User;
use App\Services\Variable;
use Carbon\Carbon;
use Inertia\Inertia;

class GestionCursosEstudiantesController extends Controller
{
    public function index($uuid)
    {

        $curso=CursoDocente::getInfoCursoEstudiante($uuid);
        $docente=User::selectRaw("p.nombres||' '||p.apellido_paterno||' '||p.apellido_materno as docente")
            ->join('personas as p','p.id','=','usuarios.id_persona')
            ->where('usuarios.id',$curso->id_docente)
            ->first();
        if(!$curso){
            return Inertia::render('Home', []);
        }
        $curso->docente=$docente->docente;
        return Inertia::render('Cursos/GestionCursosEstudiantes', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'curso'=>$curso,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);

    }
    public function listaActividadesTipo($tipo_actividad,$uuid)
    {
        if(!Variable::isStudent(auth()->user()->id_perfil)){
            abort('403','No eres estudiante');
        }
        $id_perfil=auth()->user()->id;
        $validate=CursoMatricula::selectRaw('')
            ->join('cursos_docentes as cd','cd.id','=','cursos_matriculas.id_curso_docente')
            ->join('matriculas as m','m.id','=','cursos_matriculas.id_matricula')
            ->join('estudiantes as e','e.id','=','m.id_estudiante')
            ->where('cd.uuid',$uuid)
            ->where('e.id_usuario',$id_perfil)
            ->where('m.estado','A')
            ->first();
        if(!$validate){
            abort('403','No eres alumno de este curso');
        }
        $actividades=[];
        switch($tipo_actividad){
            case 'tareas':
                $actividades=CursoDocente::selectRaw('sr.nombre,c.descripcion as nombre_curso,sr.id,rt.fecha_inicio,rt.fecha_fin,sr.descripcion,
                    tp.icono,cds.id as seccion,cursos_docentes.uuid')
                    ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
                    ->join('cursos_docentes_secciones as cds','cds.id_curso_docente','=','cursos_docentes.id')
                    ->join('secciones_recursos as sr','sr.id_curso_docente_seccion','=','cds.id')
                    ->join('tipos_recursos as tp','tp.id','=','sr.id_tipo_recurso')
                    ->join('recursos_tareas as rt','rt.id_seccion_recurso','=','sr.id')
                    ->where('cursos_docentes.uuid',$uuid)
                    ->where('rt.estado','A')
                    ->get();
                break;
            case 'foros':
                $actividades=CursoDocente::selectRaw('sr.nombre,c.descripcion as nombre_curso,sr.id,rf.fecha_fin,rf.fecha_inicio,sr.descripcion,tp.icono,cds.id as seccion,
                    cursos_docentes.uuid')
                    ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
                    ->join('cursos_docentes_secciones as cds','cds.id_curso_docente','=','cursos_docentes.id')
                    ->join('secciones_recursos as sr','sr.id_curso_docente_seccion','=','cds.id')
                    ->join('tipos_recursos as tp','tp.id','=','sr.id_tipo_recurso')
                    ->join('recursos_foros as rf','rf.id_seccion_recurso','=','sr.id')
                    ->where('cursos_docentes.uuid',$uuid)
                    ->where('rf.estado','A')
                    ->get();
                    break;
            case 'examenes':
                $actividades=CursoDocente::selectRaw('sr.nombre,c.descripcion as nombre_curso,sr.id,re.fecha_fin,re.fecha_inicio,sr.descripcion,tp.icono,cds.id as seccion,
                    cursos_docentes.uuid')
                    ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
                    ->join('cursos_docentes_secciones as cds','cds.id_curso_docente','=','cursos_docentes.id')
                    ->join('secciones_recursos as sr','sr.id_curso_docente_seccion','=','cds.id')
                    ->join('tipos_recursos as tp','tp.id','=','sr.id_tipo_recurso')
                    ->join('recursos_examenes as re','re.id_seccion_recurso','=','sr.id')
                    ->where('cursos_docentes.uuid',$uuid)
                    ->where('re.estado','A')
                    ->get();
                    break;
        }
        $fechaHoraActual=Carbon::now();
        if(empty(!$actividades)){
            foreach($actividades as $actividad){
                $fi=Carbon::parse($actividad->fecha_inicio);
                $ff=Carbon::parse($actividad->fecha_fin);
                $actividad->hora_inicio=$fi->format('H:i');
                $actividad->hora_fin=$ff->format('H:i');
                $actividad->fecha_inicio=$fi->format('Y-m-d');
                $actividad->fecha_fin=$ff->format('Y-m-d');
                $actividad->estado_actividad=false;
                if($fechaHoraActual->between($fi,$ff)){
                        $actividad->estado_actividad=true;
                }
            }
        }
        return Inertia::render('Cursos/ListaActividadesTipo',[
            'tipo_actividad'=>$tipo_actividad,
            'actividades'=>$actividades,
            'uuid'=>$uuid,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }
}
