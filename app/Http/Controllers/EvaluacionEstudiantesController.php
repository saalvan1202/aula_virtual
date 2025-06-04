<?php

namespace App\Http\Controllers;

use App\Models\CriterioEvaluacionIndicador;
use App\Models\Curso;
use App\Models\CursoDocente;
use App\Models\CursoDocenteIndicador;
use App\Models\CursoMatricula;
use App\Models\Matricula;
use App\Services\Variable;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class EvaluacionEstudiantesController extends Controller
{
    public function indexNotasEstudiantes()
    {   
        $id_perfil=auth()->user()->id_perfil;
        if(Variable::isTeacher($id_perfil)){
            return Inertia::render('Cursos/NotasDocentes', [
                'periodo'=>DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get(),
                'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
                'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
                
            ]); 
        }
        elseif(Variable::isStudent($id_perfil)){
            $id_curo_matricula=0;
            $periodo=DB::table("periodo_clases")->where('estado','A')->orderBy('anio','desc')->get();
            $matricula=Matricula::selectRaw('cm.id')
            ->join('cursos_matriculas as cm','cm.id_matricula','=','matriculas.id')
            ->join('estudiantes as e','e.id','=','matriculas.id_estudiante')
            ->where('e.id_usuario',auth()->user()->id)
            ->where('matriculas.id_periodo_clases',$periodo[0]->id)
            ->first();
            if($matricula){
                $id_curo_matricula=$matricula->id;
            }
            return Inertia::render('Cursos/Notas', [
                'periodo'=>$periodo,
                'id_matricula'=>$id_curo_matricula,
                'tipo_documentos' => DB::table('tipo_documentos_identidades')->where('estado', 'A')->orderBy('id')->get(),
                'tipo_contratos' => DB::table('tipo_contratos')->where('estado', 'A')->orderBy('id')->get(),
                
            ]);
        }


    }
    public function indexCalificarCurso($uuid,$id){
        $curso=CursoDocente::selectRaw('c.descripcion as curso,t.descripcion as turno,ci.descripcion as ciclo')
    ->join('cursos as c','c.id','=','cursos_docentes.id_cursos')
    ->join('turnos as t','t.id','=','cursos_docentes.id_turno')
    ->join('ciclos as ci','ci.id','=','c.id_ciclo')
    ->where('cursos_docentes.uuid',$uuid)
    ->first();
        return Inertia::render('Cursos/CalificarDocente',['uuid'=>$uuid,'id_curso_docente'=>$id,'curso'=>$curso
    ]);
    }
    public function getNotasCursos($id,$id_matricula){ 
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
        };
        if(!$indicadores->isEmpty()){
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
            
        }
       
        $notaFinal=CursoMatricula::find($id_matricula);
       return response()->json([
        'data'=>$arrayNotas,
        'notaFinal'=>$notaFinal->nota
       ]);
    }

}
