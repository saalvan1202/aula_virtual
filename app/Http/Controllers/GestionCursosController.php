<?php

namespace App\Http\Controllers;

use App\Actions\CrearArchivo;
use App\Models\Archivo;
use App\Models\ClaseDocente;
use App\Models\CriterioEvaluacionIndicador;
use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\Estudiante;
use App\Models\Horario;
use App\Models\PeriodoClase;
use App\Models\RecursoForo;
use App\Models\RecursoTarea;
use App\Services\Variable;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Ui\Presets\React;
use Ramsey\Uuid\Type\Integer;

class GestionCursosController extends Controller
{
    public function index($uuid)
    {
        $curso=CursoDocente::getInfoCurso($uuid);
        if(!$curso){
            abort('403','acceso no autorizado');
        }
        return Inertia::render('Cursos/GestionCursos', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'curso'=>$curso,
            'uuid'=>$uuid,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }
    //CULMINAR CURSO
    public function culminarCurso($uuid)
    {
        $curso=CursoDocente::where('uuid',$uuid)->first();
        if($curso->culminado=='S'){
            return response()->json([
                'state'=>true,
                'error'=>'La Únidad Didáctica ya se culminó'
            ]);
        }
        $curso->culminado='S';
        $curso->update();
        return response()->json($curso);
    }
    //INDICADORES CURSOS
    public function indexIndicadores($uuid)
    {
        $curso=CursoDocente::getInfoCurso($uuid);
        if(!Variable::isTeacher(auth()->user()->id_perfil)){
            abort('403','acceso no autorizado');
        }
        if(!$curso){
            abort('403','no eres docente de este curso');
        }
        return Inertia::render('Cursos/IndicadoresCursos',[
            'urlRoute'=>'indicador-docente/',
            'urlRouteCriterios'=>'criterios-evaluacion/',
            'uuid'=>$uuid,
            'curso'=>$curso,
            'estado'=>(object)['culminado'=>$curso->culminado],
            'semanas'=>CursoDocenteIndicador::getSemanas(),
            'tipo_evaluacion'=>CriterioEvaluacionIndicador::getTipoEvaluaciones(),
            'indicadores'=>CursoDocenteIndicador::selectRaw('cursos_docentes_indicadores.*,cd.culminado')
                ->join('cursos_docentes as cd','cd.id','=','cursos_docentes_indicadores.id_curso_docente')
                ->where('cursos_docentes_indicadores.estado','A')->where('cd.uuid',$uuid)->orderBy('id','asc')
                ->get(),
            'cursoDocente'=>(object)['id'=>$curso->id],
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]) ;


    }
    //ESTUDIANTES MATRICULADOS
    public function indexEstudiantes($uuid)
    {
        if(!Variable::isTeacher(auth()->user()->id_perfil)){
            abort('403','acceso no autorizado');
        }
        $curso=CursoDocente::getInfoCurso($uuid);
        if(!$curso){
           abort('403','no eres docente de este curso');
        }
        return Inertia::render('Cursos/EstudiantesCursos', [
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'curso'=>$curso,
            'uuid'=>$uuid,
            'estudiantes'=>CursoDocente::getAlumnosCurso($uuid),
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }
    //ESTUDIANTE
    public function getEstudiante($id)
    {
        $estudiante=Estudiante::selectRaw("estudiantes.id,p.nombres,p.apellido_paterno,p.apellido_materno,p.sexo,
            p.estado_civil,p.fecha_nacimiento,p.numero_documento,u.email,u.telefono")->with('foto')
            ->join('matriculas as m','m.id_estudiante','=','estudiantes.id')
            ->join('cursos_matriculas as cm','cm.id_matricula','=','m.id')
            ->join('cursos_docentes as cd','cd.id','=','cm.id_curso_docente')
            ->join('cursos as c','c.id','=','cd.id_cursos')
            ->join('usuarios as u','u.id','=','estudiantes.id_usuario')
            ->join('personas as p','p.id','=','u.id_persona')
            ->where('c.estado','A')
            ->where('estudiantes.id',$id)
            ->firstOrFail();
        if($estudiante->foto){
            $estudiante->foto->setAppends(['base64']);
        }

        $data=[
            'data'=>$estudiante
        ];
        return response()->json($data);

    }
    //HORARIO
    public function indexHorario()
    {
        $user=auth()->user()->id;
        $periodo=PeriodoClase::orderBy('id','desc')->limit(1)->get();
        if(Variable::isTeacher(auth()->user()->id_perfil)){
            $cursos=CursoDocente::selectRaw("t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,
            cursos_docentes.uuid,cursos_docentes.id_docente as id_docente")
            ->join('usuarios as u','u.id','=','cursos_docentes.id_docente')
            ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
            ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
            ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
            ->join('ciclos as co','co.id','=','c.id_ciclo')
            ->where('c.estado','A')
            ->where('u.id',$user)
            ->where('cursos_docentes.id_periodo_clases',$periodo[0]->id)
            ->get();
        }
        elseif(Variable::isStudent(auth()->user()->id_perfil)){
            $cursos=CursoDocente::selectRaw("t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,
                cursos_docentes.uuid,cursos_docentes.id_docente as id_docente,cm.id as id_matricula")
                ->join('cursos_matriculas as cm','cm.id_curso_docente','=','cursos_docentes.id')
                ->join('matriculas as m','m.id','=','cm.id_matricula')
                ->join('estudiantes as e','e.id','=','m.id_estudiante')
                ->join('usuarios as u','u.id','=','e.id_usuario')
                ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
                ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
                ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
                ->join('ciclos as co','co.id','=','c.id_ciclo')
                ->where('c.estado','A')
                ->where('m.estado','A')
                ->where('u.id',$user)
                ->where('m.id_periodo_clases',$periodo[0]->id)
                ->get();
        }
        $cursosArray=$cursos->toArray();
        return Inertia::render('Cursos/HorarioAlumno',[
            'urlRoute'=>'horario/',
            'hoy'=>Str::upper((Carbon::now()->dayName)),
            'hora'=>Carbon::now()->format('H:i'),
            'periodo'=>$periodo[0]->descripcion,
            'horarios'=>Horario::selectRaw('c.descripcion,horarios.*,a.descripcion as aula')
                ->join('cursos_docentes as cd','cd.id','=','horarios.id_curso_docente')
                ->join('cursos as c','c.id','=','cd.id_cursos')
                ->join('aulas as a','a.id','=','horarios.id_aula')
                ->where('horarios.estado','A')
                ->where('a.estado','A')
                ->whereIn('horarios.id_curso_docente',array_column($cursosArray,'id'))->get()
        ]);
    }
    //CLASES
    public function indexClases($uuid)
    {
        //$culminado=CursoDocente::where('uuid',$uuid)->first();
        $curso=CursoDocente::getInfoCurso($uuid);
        if(!$curso){
            abort('403','No eres docente de este curso');
        }
        $id_perfil=auth()->user()->id_perfil;
        if(!Variable::isTeacher($id_perfil)){
            abort('403','Acceso no autorizado');
        }
        return Inertia::render('Cursos/ClasesDocentes',[
            'urlRoute'=>'clases/',
            'curso'=>$curso,
            'culminado'=>$curso->culminado=='S'?true:false,
            'clases'=>ClaseDocente::selectRaw('clases_docentes.*,cd.culminado')
                ->join('cursos_docentes as cd','cd.id','=','clases_docentes.id_curso_docente')
                ->where('clases_docentes.estado','A')->where('cd.uuid',$uuid)
                ->orderByDesc('id')->get(),
            'uuid'=>$uuid,
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }
    public function getSilabo($id)
    {
        $record=CursoDocente::with('silabo')->where('id',$id)->firstOrFail();
        if($record->silabo){
            $pdfPath=storage_path('app/archivos/'.$record->silabo->referencia.'/'.$record->silabo->archivo);
            if (file_exists($pdfPath)) {
                $record->silabo=base64_encode(file_get_contents($pdfPath));
            }
        }
        else{
            $record->silabo=false;
        }
        return response()->json([
            'data'=>$record->culminado,
            'silabo'=>$record->silabo
    ]);
    }
    //AGREGAR SILABO DEL CURSO
    public function saveSilabo(Request $request,CrearArchivo $create)
    {
        $file=$request->file('silabo');
        $uuid=$request->input('uuid');
        $id=$request->input('id');
        $curso_docente=CursoDocente::where('uuid',$uuid)->where('estado','A')->first();
        if($request->hasFile('silabo')){
            $create->setSubject($curso_docente)->handle($file);
        }
        return response()->json("ok");
    }
    //ASISTENCIA
    public function indexAsistencia($uuid,Request $request)
    {
        if(!Variable::isTeacher(auth()->user()->id_perfil)){
            abort('403','acceso no autorizado');
        }
        $cursoDocente=CursoDocente::whereUuid($uuid)->first();
        if(is_null($cursoDocente)){
            abort('404','no existe el curso');
        }
        if($cursoDocente->id_docente!=auth()->user()->id){
            abort('403','no eres docente de este curso');
        }
        $id_clase_docente=$request->input('id_clase_docente');

        return Inertia::render('Cursos/Attendance',[
            'urlRoute'=>'asistencia/',
            'id_clase_docente'=>$request->input('id_clase_docente'),
            'id_curso_docente'=>$cursoDocente->id,
            'alumnos'=>DB::select("select
                u.id as id_usuario,
                p.apellido_paterno||' '||p.apellido_materno||' '||p.nombres as estudiante,
                tdi.abreviatura as tipo_documento,
                p.numero_documento,
                coalesce(ae.id,'-1') as id_asistencia,
                coalesce(ae.tipo_asistencia,'') as tipo_asistencia,
                ae.observaciones,
                --cd.culminado,
                cm.id as id_curso_matricula,
                la.*
                from
                usuarios u
                inner join personas p on p.id=u.id_persona
                inner join tipo_documentos_identidades tdi on tdi.id=p.id_tipo_documento_identidad
                inner join estudiantes as e on e.id_usuario=u.id
                inner join matriculas as m on (m.id_estudiante=e.id and m.estado='A')
                inner join cursos_matriculas as cm on (cm.id_matricula=m.id and cm.estado='A')
                inner join cursos_docentes as cd on cd.id=cm.id_curso_docente
                left join asistencias_estudiantes ae on (ae.id_curso_matricula=cm.id and ae.id_clase_docente=?)
                left join lateral(
                select
                    count(cd.id) as total_clases,
                    count(ae.id) as total_dictadas,
                    count( case when ae.tipo_asistencia='PRESENTE' then 1 end) as presente,
                    count( case when ae.tipo_asistencia='FALTA' then 1 end) as falta
                from
                    clases_docentes as cd left join
                    asistencias_estudiantes as ae on(ae.id_curso_matricula=cm.id)
                where cd.id_curso_docente=cd.id and cd.estado='A'
                group by cd.id_curso_docente)
                as la on true
                where cm.id_curso_docente=?",[$id_clase_docente,$cursoDocente->id]),
            'clase'=>ClaseDocente::find($id_clase_docente),
            'uuid'=>$uuid,
            'culminado'=>$cursoDocente->culminado,
            'menu_dependencia'=>menu_dependencia('aula-virtual',Str::start(Str::after(request()->fullUrl(), request()->getBaseUrl()),''))
            /*'menu_dependencia'=>[
                [
                    'menu'=>'aula-virtual',
                    'url'=>ltrim(Str::start(Str::after($request->fullUrl(), $request->getBaseUrl()),''),'/')
                ]
            ]*/
        ]);
    }
    //CALIFICACIONES
    public function indexCalificaciones($uuid,$id)
    {
        if(!Variable::isTeacher(auth()->user()->id_perfil)){
            abort('403','acceso no autorizado');
        }
        $cursoDocente=CursoDocente::whereUuid($uuid)->first();
        if(is_null($cursoDocente)){
            abort('404','no existe el curso');
        }
        if($cursoDocente->id_docente!=auth()->user()->id){
            abort('403','no eres docente de este curso');
        }
        return Inertia::render('Cursos/CalificacionCriterios',[
            'uuid'=>$uuid,
            'id_criterio_indicador'=>$id,
            'urlRoute'=>'calificar/',
            'criterio'=>CriterioEvaluacionIndicador::find($id),
            'alumnos'=>CriterioEvaluacionIndicador::getAlumnosCriteriosNotas($id,$uuid),
            'tareas'=>RecursoTarea::select('recursos_tareas.id','sr.nombre','cd.uuid','recursos_tareas.fecha_inicio')
                ->join('secciones_recursos as sr','sr.id','=','recursos_tareas.id_seccion_recurso')
                ->join('cursos_docentes_secciones as cds','cds.id','=','sr.id_curso_docente_seccion')
                ->join('cursos_docentes as cd','cd.id','=','cds.id_curso_docente')
                ->where('cd.uuid',$uuid)
                ->where('recursos_tareas.estado', 'A')->orderBy('recursos_tareas.id')->get(),
            'foros'=>RecursoForo::select('recursos_foros.id','sr.nombre','cd.uuid','recursos_foros.fecha_inicio')
                ->join('secciones_recursos as sr','sr.id','=','recursos_foros.id_seccion_recurso')
                ->join('cursos_docentes_secciones as cds','cds.id','=','sr.id_curso_docente_seccion')
                ->join('cursos_docentes as cd','cd.id','=','cds.id_curso_docente')
                ->where('cd.uuid',$uuid)
                ->where('recursos_foros.estado', 'A')->orderBy('recursos_foros.id')->get(),
            'menu_dependencia'=>menu_dependencia('aula-virtual')
        ]);
    }
    public function indexNotas($uuid){
        return Inertia::render('Cursos/NotasDocentes',[
            'uuid'=>$uuid,
        ]);
    }
}