<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaAlumno;
use App\Models\ClaseDocente;
use App\Models\CriterioEvaluacionIndicador;
use App\Models\Curso;
use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\CursoMatricula;
use App\Services\Variable;
use Carbon\Carbon;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Ui\Presets\React;
use Yajra\DataTables\DataTables;

class AsistenciaEstudiantesController extends Controller
{
    public function indexAsistenciasEstudiantes()
    {
       $id_perfil=auth()->user()->id_perfil;
       if(Variable::isTeacher($id_perfil)){
            return Inertia::render('Cursos/Asistencias',[
                'periodo'=>DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get(),
            ]);
       }

       if(Variable::isStudent($id_perfil)){
            return Inertia::render('Cursos/AsistenciaEstudiantes', [
                'periodo'=>DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get(),
                'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
                'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
                'menu_dependencia'=>menu_dependencia('asistencia',Str::start(Str::after(request()->fullUrl(), request()->getBaseUrl()),''))
            ]);
       }
    }

    public function getAsistenciaAlumnos($id){
        //ID ESTUDAINTES ES LA DE PERIDO
        //ID DOCENTES ES LA DE CURSO_DOCENTE
        $user=auth()->user()->id;
        $id_perfil=auth()->user()->id_perfil;
        if(Variable::isTeacher($id_perfil)){
            $cursos=DB::select("select
            u.id as id_usuario,
            cd.id,
            p.apellido_paterno||' '||p.apellido_materno||' '||p.nombres as estudiante,
            tdi.abreviatura as tipo_documento,
            p.numero_documento,
            cm.id as id_matricula,
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
            where cd.id=?",[$id]);
        }
        elseif(Variable::isStudent($id_perfil))
        {
            // Lógica Hecha
            $cursos=DB::select("select
                c.descripcion,
                cursos_docentes.id,
                cursos_docentes.id_periodo_clases,
                cursos_docentes.uuid,
                cursos_docentes.id_docente as id_docente,
                cm.id as id_matricula,
                coalesce(la.total_clases,0) as total_clases,
                coalesce(la.presente,0) as presente,
                coalesce(la.falta,0) as falta,
                coalesce(la.total_dictadas,0) as total_dictadas,
                0 as porcentaje_faltas
                from cursos_docentes
                inner join cursos_matriculas as cm on cm.id_curso_docente = cursos_docentes.id
                inner join matriculas as m on m.id = cm.id_matricula
                inner join estudiantes as e on e.id = m.id_estudiante
                inner join usuarios as u on u.id = e.id_usuario
                inner join cursos as c on c.id = cursos_docentes.id_cursos
                left join lateral(
                select
                    cd.id_curso_docente,
                    count(cd.id) as total_clases,
                    count(ae.id) as total_dictadas,
                    count( case when ae.tipo_asistencia='PRESENTE' then 1 end) as presente,
                    count( case when ae.tipo_asistencia='FALTA' then 1 end) as falta
                    from
                    clases_docentes cd
                    left join asistencias_estudiantes ae on(ae.id_clase_docente=cd.id
                    and ae.id_curso_matricula=cm.id
                    )
                    where cd.id_curso_docente=cursos_docentes.id and cd.estado='A'
                    group by cd.id_curso_docente
                    order by cd.id_curso_docente
                ) as  la on true
                where c.estado = 'A'
                and m.estado = 'A'
                and u.id = ?
                and m.id_periodo_clases =?",[$user,$id]);
        }
        $cursos=collect($cursos)->map(function($item){
            $porcentaje=0;
            if($item->total_clases>0){
                $porcentaje=round(($item->falta/$item->total_clases)*100,2);
            }
            $item->porcentaje_faltas=$porcentaje;
            return $item;
        });
        return response()->json($cursos);
    }
    public function getDetalleAsistenciaAlumno($id_curso_docente,$id_curso_matricula,Request $request){
        $tipo_asistencia=$request->input('tipo_asistencia');
        $user=auth()->user()->id;
        $cursos=CursoDocente::selectRaw("cd.numero,cd.fecha,cd.hora_inicio,cd.hora_fin,
        cursos_docentes.uuid,ae.tipo_asistencia,ae.id_curso_matricula,'' as dia,cd.id_curso_docente")
        ->join('clases_docentes as cd','cd.id_curso_docente','=','cursos_docentes.id')
        ->join('asistencias_estudiantes as ae','ae.id_clase_docente','=','cd.id')
        ->where('cd.estado','A')
        ->where('ae.id_curso_matricula',$id_curso_matricula)
        ->where('cd.id_curso_docente',$id_curso_docente)
        ->get();
        $cursos=$cursos->map(function($item){
            $item->dia=Str::upper(Carbon::parse($item->fecha)->dayName);
            return $item;
        });
        return response()->json($cursos);
    }
    public function getNotasCursos($id,$id_matricula)
    {
        //$id_matricula hace referencia al id_curso_matricula
        $arrayNotas=[];
        $notaFinal=0;
        $indicadores=CursoDocenteIndicador::where('id_curso_docente',$id)->where('estado','A')->orderBy('id','asc')->get();
        $indicadoresArray=$indicadores->toArray();
        $criterios=CriterioEvaluacionIndicador::selectRaw('nci.nota,criterios_evaluaciones_indicadores.porcentaje,criterios_evaluaciones_indicadores.nombre,
        criterios_evaluaciones_indicadores.id, criterios_evaluaciones_indicadores.id_curso_docente_indicador,
        nci.nota*criterios_evaluaciones_indicadores.porcentaje as nota_porcentaje')
        ->leftjoin('notas_criterios_indicadores as nci','nci.id_criterio_evaluacion_indicador','=','criterios_evaluaciones_indicadores.id')
        ->whereIn('criterios_evaluaciones_indicadores.id_curso_docente_indicador',array_column($indicadoresArray,'id'))
        ->where('nci.id_curso_matricula',$id_matricula)
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
        foreach($indicadores as $indicador){
            $criterioIndicador=$criterios->where('id_curso_docente_indicador',$indicador->id)->values()->all();
            $nota=collect($criterioIndicador)->sum('nota_porcentaje');
            $datos=[
                "descripcion"=>$indicador->descripcion,
                "porcentaje"=>$indicador->porcentaje,
                "nota"=>round($nota/100,2),
                "criterios"=> $criterioIndicador,
            ];
            $arrayNotas[]=$datos;

        }
       return response()->json($arrayNotas);
    }
    public function index()
    {
        if(auth()->user()->id_perfil==Variable::DOCENTE){
            return Inertia::render('Cursos/CursoDocentes', [
                'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
                'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
                'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
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
                'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
                'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
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
            $query=CursoDocente::selectRaw('t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,
                cursos_docentes.uuid,cursos_docentes.id_docente as id_docente')
                ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
                ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
                ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
                ->join('ciclos as co','co.id','=','c.id_ciclo')
                ->where('c.estado','A')
                ->where('cursos_docentes.id_docente',$user)
                ->where('cursos_docentes.id_periodo_clases',$request->input('id_periodo'));
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
        elseif(Variable::isStudent(auth()->user()->id_perfil)){
            $cursos=CursoDocente::selectRaw('t.descripcion as turno,s.descripcion as seccion,co.descripcion as ciclo,c.descripcion,cursos_docentes.id,cursos_docentes.id_periodo_clases,
            cursos_docentes.uuid,cursos_docentes.id_docente as id_docente')
            ->join('cursos_matriculas as cm','cm.id_curso_docente','=','cursos_docentes.id')
            ->join('matriculas as m','m.id','=','cm.id_matricula')
            ->join('estudiantes as e','e.id','=','m.id_estudiante')
            ->join('usuarios as u','u.id','=','e.id_usuario')
            ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
            ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
            ->join('secciones as s','s.id','=','cursos_docentes.id_seccion')
            ->join('ciclos as co','co.id','=','c.id_ciclo')
            ->where('c.estado','A')
            ->where('u.id',$user)
            ->where('m.id_periodo_clases',$request->input('id_periodo'))
            ->get();

            return response()->json($cursos);
        }
        //Cómo ejemplo para los demás perfiles
        else {
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
