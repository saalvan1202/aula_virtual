<?php

namespace App\Http\Controllers;

use App\Actions\GuardarModuloCompetencia;
use App\Models\ModuloCompetencia;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PlanEstudioModuloController extends Controller
{

    public function store(Request $request,GuardarModuloCompetencia $guardarModuloCompetencia)
    {
        $request->validate([
            'id'=>'required',
            'id_plan_estudio'=>'required',
            'codigo'=>'required|integer',
            'descripcion'=>'required|max:200',
        ],[],[
            'id_plan_estudio'=>'Plan Estudio',
        ]);
        $response=$guardarModuloCompetencia->isModulo()->handle($request->all());
        return response()->json($response);

    }
    public function edit($id)
    {
        $record=ModuloCompetencia::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=ModuloCompetencia::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function combo($idPlan)
    {
        $records=ModuloCompetencia::where('id_padre',0)
            ->where('id_plan_estudio',$idPlan)
            ->where('estado','A')
            ->orderBy('id')->get();
        return response()->json($records);
    }
    public function dataTable($idPlan)
    {
        set_time_limit(0);
        $records=ModuloCompetencia::selectRaw('id,codigo,descripcion')
            ->where('id_padre',0)->where('estado','A')->where('id_plan_estudio',$idPlan)->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
