<?php

namespace App\Http\Controllers;

use App\Models\CriterioEvaluacionIndicador;
use App\Models\Curso;
use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\CursoMatricula;
use App\Services\Variable;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class EvaluacionController extends Controller
{
    public function indexNotasEstudiantes()
    {
        return Inertia::render('Cursos/Notas', [
            'periodo'=>DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get(),
            'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
            'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),

        ]);
    }

    public function index()
    {
        if(auth()->user()->id_perfil==Variable::DOCENTE){
            return Inertia::render('Cursos/CursoDocentes', [
                'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
                'periodo'=>DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get(),
                'secciones'=>DB::table('secciones')->where('estado','A')->get(),
                'turnos'=>DB::table('turnos')->where('estado','A')->get(),
                'ciclos'=>DB::table('ciclos')->where('estado','A')->get()
            ]);
        }
        //PARA EL ESTUDIANTE
        elseif(auth()->user()->id_perfil==Variable::ESTUDIANTE){
            return Inertia::render('Cursos/CursosEstudiantes', [
                'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
                'periodo'=>DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get(),
                'secciones'=>DB::table('secciones')->where('estado','A')->get(),
                'turnos'=>DB::table('turnos')->where('estado','A')->get(),
                'ciclos'=>DB::table('ciclos')->where('estado','A')->get()
            ]);
        }

    }
    public function getCursos(Request $request)
    {
        $seccion=$request->query('seccion');
        $turno=$request->query('turno');
        $ciclo=$request->query('ciclo');
          //Sacamos el id del user
        $user=auth()->user()->id;
        $perfil=auth()->user()->id_perfil;
        if(Variable::isTeacher($perfil)){
            //Filtramos los cursos para el Usuario Docente
            //case when condificion then 1 end
            $query=CursoDocente::selectRaw("COUNT(case when cd.estado='A' then 1 end) as total_clases,
            COUNT(case when cd.estado_clase='DESARROLLADA' and cd.estado='A' then 1 end ) as total_desarrolladas,
            t.descripcion as turno,s.descripcion as seccion,
            co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,
            cursos_docentes.uuid,cursos_docentes.id_docente as id_docente")
            ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
            ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
            ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
            ->join('ciclos as co','co.id','=','c.id_ciclo')
            ->leftjoin('clases_docentes as cd','cd.id_curso_docente','=','cursos_docentes.id')
            ->where('c.estado','A')
            ->where('cursos_docentes.id_docente',$user)
            ->where('cursos_docentes.id_periodo_clases',$request->input('id_periodo'))
            ->where('cursos_docentes.estado','A')
            ->groupBy('t.descripcion','s.descripcion','co.descripcion','c.descripcion','cursos_docentes.id','cursos_docentes.id_periodo_clases',
            'cursos_docentes.uuid','cursos_docentes.id_docente');
            /*->when(!$request->filled('turno'),function($q) use ($request){
                $q->where('cursos_docentes.id_turno',$request->input('turno'));
            });*/
            if($turno){
                $query->where('cursos_docentes.id_turno',$turno);
            }
            if($seccion){
                $query->where('cursos_docentes.id_seccion',$seccion);
            }
            if($ciclo){
                $query->where('c.id_ciclo',$ciclo);
            }

            //Metemos el ID del docente
            $cursos=$query->get();
            return response()->json($cursos);
        }
        //PARA ESTUDIANTES
        elseif(Variable::isStudent($perfil)){
            $cursos=CursoDocente::selectRaw("COUNT(case when cd.estado='A' then 1 end) as total_clases,COUNT(case when cd.estado_clase='DESARROLLADA' and cd.estado='A' then 1 end ) as total_desarrolladas,
            t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,
            c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,
            cursos_docentes.uuid,cursos_docentes.id_docente as id_docente,cm.id as id_matricula")
            ->join('cursos_matriculas as cm','cm.id_curso_docente','=','cursos_docentes.id')
            ->join('matriculas as m','m.id','=','cm.id_matricula')
            ->join('estudiantes as e','e.id','=','m.id_estudiante')
            ->join('usuarios as u','u.id','=','e.id_usuario')
            ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
            ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
            ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
            ->join('ciclos as co','co.id','=','c.id_ciclo')
            ->leftjoin('clases_docentes as cd','cd.id_curso_docente','=','cursos_docentes.id')
            ->where('c.estado','A')
            ->where('m.estado','A')
            ->where('cursos_docentes.estado','A')
            ->where('u.id',$user)
            ->where('m.id_periodo_clases',$request->input('id_periodo'))
            ->groupBy('cm.id','t.descripcion','s.descripcion','co.descripcion','c.descripcion','cursos_docentes.id','cursos_docentes.id_periodo_clases',
            'cursos_docentes.uuid','cursos_docentes.id_docente')
            ->get();

            return response()->json($cursos);
        }
        //Cómo ejemplo para los demás perfiles
        else
        {
        $cursos=CursoDocente::selectRaw('c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,c.uuid')
        ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
        ->where('c.estado','A')->where('cursos_docentes.id_periodo_clases',$request->input('id_periodo'))->get();
        return response()->json($cursos);
        }
        }


    public function store(Request $request)
    {
        set_time_limit(0);
        $datos=$request->all();
        foreach ($datos as $item) {
            $nota=$item['nota'];
            $id=$item['id'];
            CursoMatricula::where('id',$id)->update([
                'nota'=>$nota,
                'estado_nota'=>($nota>10)?'APROBADO':'DESAPROBADO'
            ]);

        }
    }
    public function getPromedios($uuid){
        $estudiantes=DB::select("select
        cd.id,
        p.apellido_paterno||' '||p.apellido_materno||' '||p.nombres as estudiante,
        cm.id as id_curso_matricula,
        cm.estado_nota,
        0 as porcentaje_faltas,
        coalesce(la.total_clases,0) as total_clases,
        coalesce(la.presente,0) as presente,
        coalesce(la.falta,0) as falta,
        coalesce(la.total_dictadas,0) as total_dictadas,
        la.*
        from
        usuarios u
        inner join personas p on p.id=u.id_persona
        inner join tipo_documentos_identidades tdi on tdi.id=p.id_tipo_documento_identidad
        inner join estudiantes as e on e.id_usuario=u.id
        inner join matriculas as m on (m.id_estudiante=e.id and m.estado='A')
        inner join cursos_matriculas as cm on (cm.id_matricula=m.id and cm.estado='A')
        inner join cursos_docentes as cd on cd.id=cm.id_curso_docente
        left join lateral(
          select
                cdd.id_curso_docente,
                count(cdd.id) as total_clases,
                count(ae.id) as total_dictadas,
                count( case when ae.tipo_asistencia='PRESENTE' then 1 end) as presente,
                count( case when ae.tipo_asistencia='FALTA' then 1 end) as falta
                from
                clases_docentes cdd
                left join asistencias_estudiantes ae on(ae.id_clase_docente=cdd.id
                and ae.id_curso_matricula=cm.id
                )
                where cdd.id_curso_docente=cd.id and cdd.estado='A'
                group by cdd.id_curso_docente
                order by cdd.id_curso_docente
            ) as  la on true
        where cd.uuid=?",[$uuid]);
        $estudiantesArray = collect($estudiantes)->toArray();
        $arrayNotasEstudiantes=[];
        $arrayNotas=[];
        $notaFinal=0;
        $indicadores=CursoDocenteIndicador::selectRaw('cd.uuid,cursos_docentes_indicadores.*')
        ->join('cursos_docentes as cd','cd.id','=','cursos_docentes_indicadores.id_curso_docente')
        ->where('cd.uuid',$uuid)
        ->where('cursos_docentes_indicadores.estado','A')
        ->orderBy('id','asc')->get();
        $indicadoresArray=$indicadores->toArray();
        $criterios=CriterioEvaluacionIndicador::selectRaw('nci.nota,criterios_evaluaciones_indicadores.porcentaje,criterios_evaluaciones_indicadores.nombre,
        criterios_evaluaciones_indicadores.id, criterios_evaluaciones_indicadores.id_curso_docente_indicador,nci.id_curso_matricula,
        nci.nota*criterios_evaluaciones_indicadores.porcentaje as nota_porcentaje')
        ->leftjoin('notas_criterios_indicadores as nci','nci.id_criterio_evaluacion_indicador','=','criterios_evaluaciones_indicadores.id')
        ->whereIn('criterios_evaluaciones_indicadores.id_curso_docente_indicador',array_column($indicadoresArray,'id'))
        ->whereIn('nci.id_curso_matricula',array_column($estudiantesArray,'id_curso_matricula'))
        ->where('nci.estado',"A")
        ->where('criterios_evaluaciones_indicadores.estado','A')->get();
        if($criterios->isEmpty()){
            $criterios=CriterioEvaluacionIndicador::selectRaw("coalesce(nci.nota, 0) as nota,criterios_evaluaciones_indicadores.porcentaje,criterios_evaluaciones_indicadores.nombre,
            criterios_evaluaciones_indicadores.id, criterios_evaluaciones_indicadores.id_curso_docente_indicador,
            nci.nota*criterios_evaluaciones_indicadores.porcentaje as nota_porcentaje")
            ->leftjoin('notas_criterios_indicadores as nci','nci.id_criterio_evaluacion_indicador','=','criterios_evaluaciones_indicadores.id')
            ->whereIn('criterios_evaluaciones_indicadores.id_curso_docente_indicador',array_column($indicadoresArray,'id'))
            ->where('criterios_evaluaciones_indicadores.estado','A')->get();
        }
     foreach($estudiantes as $estudiante){
        $arrayNotas=[];
        $notaFinal=0;
        foreach($indicadores as $indicador){
            $criterioIndicador=$criterios->where('id_curso_docente_indicador',$indicador->id)->where('id_curso_matricula',$estudiante->id_curso_matricula)->values()->all();
            $nota=collect($criterioIndicador)->sum('nota_porcentaje');
            $datos=[
                "descripcion"=>$indicador->descripcion,
                "porcentaje"=>$indicador->porcentaje,
                "nota"=>round($nota/100,2),
            ];
            $arrayNotas[]=$datos;
            if($datos['nota']>0){
                $notaFinal=$notaFinal+($datos['nota']*$datos['porcentaje']/100);
            }
        }
        //VALIDAMOS SI EL NUMERO DE FALTAS ES 0, PARA QUE NO DE ERROR EN DIVISIÓN
        $porcentaje=$estudiante->falta>0?round(($estudiante->falta/$estudiante->total_clases)*100,2):0;

        $arrayNotasEstudiantes[]=[
            'estudiante'=>$estudiante->estudiante,
            'id_curso_matricula'=>$estudiante->id_curso_matricula,
            'estado_nota'=>$estudiante->estado_nota,
            'indicadores'=>$arrayNotas,
            'promedio'=>round($notaFinal),
            'porcentaje'=>$porcentaje
        ];
     }
     return Response()->json($arrayNotasEstudiantes);
    }
    public function inhabilitarEstudiante($id_curso_matricula){
        $curso_matricula=CursoMatricula::find($id_curso_matricula);
        $curso_matricula->estado_nota='INHABILITADO';
        $curso_matricula->nota=05;
        $curso_matricula->update();
        return response()->json('ok');
    }
    public function getCalificar($uuid){
        $indicadores=CursoDocenteIndicador::with('criteriosEvaluacion')->selectRaw('cd.uuid,cursos_docentes_indicadores.*')
        ->join('cursos_docentes as cd','cd.id','=','cursos_docentes_indicadores.id_curso_docente')
        ->where('cd.uuid',$uuid)
        ->where('cursos_docentes_indicadores.estado','A')
        ->orderBy('id','asc')->get();
        return response()->json($indicadores);
    }
    public function getResumen($id_curso_docente){
        $resumen=CursoMatricula::selectRaw("COUNT( case when cursos_matriculas.estado_nota='APROBADO' then 1 end) as aprobados,
        COUNT(case when cursos_matriculas.estado_nota='DESAPROBADO' then 1 end) as desaprobados,
        COUNT(case when cursos_matriculas.estado_nota='INHABILITADO' then 1 end) as inhabilitado,
        COUNT(cursos_matriculas.id) as matriculados")
        ->join('matriculas as m','m.id','=','cursos_matriculas.id_matricula')
        ->join('estudiantes as e','e.id','=','m.id_estudiante')
        ->where('cursos_matriculas.id_curso_docente',$id_curso_docente)
        ->where('cursos_matriculas.estado','A')
        ->where('e.estado','A')
        ->where('m.estado','A')
        ->first();
       return response()->json($resumen);
    }
    public function subirNotas(Request $request){
       $estudiantes=$request->all();
        foreach($estudiantes as $estudiante){
                $id_curso_matricula=$estudiante['id_curso_matricula'];
                $obj=CursoMatricula::find($id_curso_matricula);
                    if($estudiante['estado_nota']!='INHABILITADO'){
                $obj->nota=$estudiante['promedio'];
                    if($estudiante['promedio']>=13){
                    $obj->estado_nota='APROBADO';
                }
                    elseif($estudiante['promedio']<13){
                $obj->estado_nota='DESAPROBADO';
                }
            }
            $obj->update();
        }
        return response()->json('ok');
    }
    public function edit($id)
    {
        $record=Curso::withCompetencia()->findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=Curso::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function cursoDocente($id){
        if($id){
            $response=CursoDocente::selecRaw('')
            ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
            ->where('c.estado','A');
        }
        else{
            $response=CursoDocente::selecRaw('')
            ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
            ->where('c.estado','A')->where('id_docente',$id);
        }
        return response()->json($response);
    }
    public function dataTable($idPlan)
    {
        set_time_limit(0);
        $records=Curso::selectRaw('
            cursos.id,cursos.descripcion,c.descripcion as ciclo,
            horas_teoria+horas_practica as horas,
            creditos_teoria+creditos_practica as creditos

        ')
            ->join('ciclos as c','c.id','=','cursos.id_ciclo')
            ->where('cursos.estado','A')->where('id_plan_estudio',$idPlan)->orderBy('cursos.id','desc');
        return DataTables::of($records)->toJson();
    }

    public function getCursoByPlanEstudioAndCiclo($id_plan_estudio, $id_ciclo)
    {
        $cursos = Curso::where('estado', 'A')
            ->where('id_plan_estudio', $id_plan_estudio)
            ->where('id_ciclo', $id_ciclo)
            ->get();
        return response()->json($cursos);
    }

}
