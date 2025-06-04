<?php

namespace App\Http\Controllers;

use App\Models\Ciclo;
use App\Models\Componente;
use App\Models\Curso;
use App\Models\PlanEstudio;
use App\Models\ProgramaEstudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class PlanEstudioController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/StudyPlan/StudyPlan',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'programa_estudios'=>ProgramaEstudio::select('id','descripcion')->where('estado','A')->orderBy('descripcion')->get()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'id_programa_estudio'=>'required|integer|required_zero',
            'descripcion'=>'required:max:100',
            'modalidad'=>'required:max:100',
        ],[],[
            'id_programa_estudio'=>'Programa Estudio',
        ]);
        return DB::transaction(function () use ($request){
            $obj=PlanEstudio::find($request->input('id'));
            if(is_null($obj)){
                $obj=new PlanEstudio();
                $obj->estado='A';
            }
            $obj->fill($request->all());
            $obj->save();
            return response()->json($obj);
        });

    }
    public function edit($id)
    {
        $record=PlanEstudio::findOrFail($id);
        return response()->json($record);
    }
    public function modules($id)
    {

        $plan=PlanEstudio::withProgramaEstudio()->findOrFail($id);
        return Inertia::render('Academy/StudyPlan/Detail',[
            'plan'=>$plan,
            'tabActive'=>'Module',
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'tipo_competencias'=>Componente::selectRaw('id,descripcion')->where('estado','A')->orderBy('id')->get(),
            'ciclos'=>Ciclo::selectRaw('id,descripcion,orden')->where('estado','A')->orderBy('orden')->get(),
            'menu_dependencia'=>[
                [
                    'menu'=>'planes_estudios',
                    'url'=>ltrim(request()->getPathInfo(),'/')
                ]
            ]
        ]);
    }
    public function destroy($id)
    {
        $obj=PlanEstudio::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        set_time_limit(0);
        $records=PlanEstudio::selectRaw('
            plan_estudios.id,plan_estudios.descripcion,
            plan_estudios.modalidad,
            programa_estudios.descripcion as programa_estudios
            ')
            ->join('programa_estudios','programa_estudios.id','=','plan_estudios.id_programa_estudio')
            ->where('plan_estudios.estado','A')->orderBy('id','desc');
        return DataTables::of($records)
            ->filterColumn('programa_estudios', function($query, $keyword) {
                $sql = "upper(programa_estudios.descripcion)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->toJson();
    }

    public function getPlanEstudioByPrograma($id_programa_estudio)
    {
        $planes_estudios = PlanEstudio::where('estado', 'A')
            ->where('id_programa_estudio', $id_programa_estudio)
            ->get();
        return response()->json($planes_estudios);
    }

    //VISTA DEL ESTUDIANTE
    public function getPlanEstudiante()
    {
        $ciclos=Ciclo::selectRaw('id,descripcion,orden')->where('estado','A')->orderBy('id','asc')->get();
        $cursos=Curso::selectRaw('cursos.descripcion,cursos.id_plan_estudio,cursos.id_ciclo,cursos.horas_teoria,
        cursos.horas_practica,cursos.creditos_teoria,cursos.creditos_practica')
        ->join('estudiantes as e','e.id_plan_estudio','=','cursos.id_plan_estudio')
        ->where('e.id_usuario',auth()->user()->id)->get();
        $resultado=[];
        foreach($ciclos as $ciclo){
            $cursosCiclo=$cursos->where('id_ciclo',$ciclo->id)->values();
            $resultado[]=[
                'id_ciclo'=>$ciclo->id,
                'ciclo'=>$ciclo->descripcion,
                'cursos'=>$cursosCiclo->toArray(),

            ];
        }
        return Inertia::render('Academy/PlanEstudioEstudiantes',[
            'cursos'=>$resultado,
        ]);
    }

}
