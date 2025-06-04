<?php

namespace App\Http\Controllers;

use App\Models\CriterioEvaluacionIndicador;
use App\Models\Curso;
use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\NotaCriterioIndicador;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\Input;
use Yajra\DataTables\DataTables;


class CriteriosEvaluacieonesIndicadoresController extends Controller
{

  public function store(Request $request)
  {
    $id_curso_docente_indicador=$request->input('id_curso_docente_indicador');
    $porcentaje=$request->input('porcentaje');
    set_time_limit(0);
    $request->validate([
      'id'=>'required',
      'nombre'=>'required',
      'descripcion'=>'required',
      'semana'=>'required',
      'tipo_evaluacion'=>'required',
      'porcentaje'=>'required',
      'id_curso_docente_indicador'=>'required'
    ],[],[
    'nombre'=>'Nombre',
      'descripcion'=>'Descripción',
      'semana'=>'Semana',
      'tipo_evaluacion'=>'Tipo de Evaluación',
      'porcentaje'=>'Porcentaje',
    ]);
    if(CriterioEvaluacionIndicador::validatePorcentaje($id_curso_docente_indicador,$porcentaje,$request->input('id'))){
      $obj=CriterioEvaluacionIndicador::find($request->input('id'));
      if(is_null($obj)){
        $obj=new CriterioEvaluacionIndicador();
        $obj->estado='A';
      }
      $obj->fill($request->all());
      $obj->save();
      return response()->json($obj);
    }
    return Response()->json([
      'error'=>'La suma de los porcentajes excedió el 100%',
      'data'=>false,
      'status'=>203
      ]) ;
  }
  public function edit($id)
  {
    $obj=CriterioEvaluacionIndicador::findOrFail($id);
    return Response()->json($obj);
  }
  public function destroy($id){
    $obj=CriterioEvaluacionIndicador::findOrFail($id);
    $obj->estado='I';
    $obj->update();
    return Response()->json('ok');
  }
  public function getCriteriosEvaluaciones($id)
  {
    $evaluaciones=CriterioEvaluacionIndicador::selectRaw('criterios_evaluaciones_indicadores.*')
    ->where('criterios_evaluaciones_indicadores.estado','A')
    ->where('criterios_evaluaciones_indicadores.id_curso_docente_indicador',$id)->orderBy('id','asc')->get();
    return Response()->json($evaluaciones);
  }
  public function calificarCriteriosEvaluacion(Request $request)
  {
    $configuracion=$request->input('configuracion');
    $tipoActividad=$request->input('tipoActividad');
    $id_recurso="";
    $estudiantes=CursoDocente::selectRaw('cm.id as id_curso_matricula,cm.nota,p.nombres||\' \'||p.apellido_paterno||\' \'||p.apellido_materno as estudiante')
    ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
    ->join('cursos_matriculas as cm','cm.id_curso_docente','=','cursos_docentes.id')
    ->join('matriculas as m','m.id','=','cm.id_matricula')
    ->join('estudiantes as e','e.id','=','m.id_estudiante')
    ->join('usuarios as u','u.id','=','e.id_usuario')
    ->join('personas as p','p.id','=','u.id_persona')
    ->where('c.estado','A')
    ->where('m.estado','A')
    ->where('cursos_docentes.uuid',$request->input('uuid'))
    ->get();
   if($tipoActividad=='tareas'){
    $actividades_tareas_alumnos=CriterioEvaluacionIndicador::getActividadesTareasEstudiantes($configuracion);
    $id_recurso='id_recurso_tarea';

   }
   elseif($tipoActividad=='foros'){
    $actividades_tareas_alumnos=CriterioEvaluacionIndicador::getActividadesForosEstudiantes($configuracion);
    $id_recurso='id_recurso_foro';
   }
    //ACTIVIDADES SELECCIONADAS EN LA CONFIGURACIÓN
   foreach($estudiantes as $estudiante){
    $notaFinal=array_reduce($configuracion,function($total,$config)use($actividades_tareas_alumnos,$estudiante,$id_recurso){
      $actividad=$actividades_tareas_alumnos->where('id_curso_matricula',$estudiante['id_curso_matricula'])
      ->where($id_recurso,$config['id'])->first();
      if($actividad){
        return $total+($actividad->puntaje*($config['peso']/100));
      }
      return 0;
      
    },0);
    $result[] = [
      'id_matricula' => $estudiante['id_curso_matricula'],
      'nota_final' => round($notaFinal,2)
    ];
   }
   return Response()->json($result);
  }
  public function getEstudiantes(Request $request){
    $id=$request->input('id');
    $uuid=$request->input('uuid');
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
        return response()->json($estudiantes);
  }
  public function subirNotas(Request $request)
  {
    //CALIFICADO
    set_time_limit(0);
    return DB::transaction(function () use ($request) {
        foreach ($request->input('alumnos') as $value) {
            if($value['id_nota_criterio']!='-1'){
                NotaCriterioIndicador::where('id',$value['id_nota_criterio'])
                    ->update([
                        'asistio'=>trim($value['asistio']),
                        'nota'=>$value['nota_criterio'],
                    ]);
                continue;
            }
            $obj=new NotaCriterioIndicador();
            $obj->id_criterio_evaluacion_indicador=$request->input('id_criterio_evaluacion_indicador');
            $obj->id_curso_matricula=$value['id_curso_matricula'];
            $obj->asistio=$value['asistio'];
            $obj->nota=$value['nota_criterio'];
            $obj->estado='A';
            $obj->save();
        }
        //ACTUALIZAMOS EL ESTADO DEL CRITERIO DE EVALUACIÓN A CALIFICADO
        $criterio=CriterioEvaluacionIndicador::find($request->input('id_criterio_evaluacion_indicador'));
        $criterio->estado_calificacion='CALIFICADO';
        $criterio->update();
        return response()->json('ok');
    });
  }
  public function detalleActiviadadesAlumnos(Request $request)
  {
    //FALTA AGREGAR FOROS Y EXÁMENES
    $id_curso_matricula=$request->input('id_curso_matricula');
    $activiades=$request->input('actividades');
    $detalle=DB::table('recursos_tareas')->select('recursos_tareas.id','sr.nombre','recursos_tareas.fecha_inicio'
    ,'rte.puntaje','rte.estado_calificacion')
    ->join('secciones_recursos as sr','sr.id','=','recursos_tareas.id_seccion_recurso')
    ->join('recursos_tareas_estudiantes as rte','rte.id_recurso_tarea','=','recursos_tareas.id')
    ->whereIn('rte.id_recurso_tarea',array_column($activiades,'id'))
    ->where('rte.id_curso_matricula',$id_curso_matricula)
    ->get();
   return response()->json($detalle);
  }
}
