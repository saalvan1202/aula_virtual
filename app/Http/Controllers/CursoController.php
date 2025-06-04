<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CursoController extends Controller
{

    public function store(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'id'=>'required',
            'id_plan_estudio'=>'required',
            'id_ciclo'=>'required|integer',
            'id_modulo_competencia'=>'required|integer|required_zero',
            'descripcion'=>'required|max:100',
            'horas_teoria'=>'required|integer|required_zero',
            'horas_practica'=>'required|integer',
            'creditos_teoria'=>'required|integer|required_zero',
            'creditos_practica'=>'required|integer',
        ],[],[
            'id_modulo_competencia'=>'Competencia',
            'id_ciclo'=>'Semestre',
        ]);
        $obj= Curso::find($request->input('id'));
        if(is_null($obj)){
            $obj= new Curso();
            $obj->uuid=(string)Str::uuid();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
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
