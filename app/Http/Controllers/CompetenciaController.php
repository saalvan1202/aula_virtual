<?php

namespace App\Http\Controllers;

use App\Actions\GuardarModuloCompetencia;
use App\Models\ModuloCompetencia;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CompetenciaController extends Controller
{

    public function store(Request $request,GuardarModuloCompetencia $guardarModuloCompetencia)
    {
        $request->validate([
            'id'=>'required',
            'id_padre'=>'required|integer|required_zero',
            'codigo'=>'required|max:10',
            'descripcion'=>'required|max:400',
            'id_componente'=>'required|integer|required_zero'
        ],[],[
            'id_padre'=>'Modulo',
            'id_componente'=>'Tipo Competencia',
        ]);
        $response=$guardarModuloCompetencia->isCompetencia($request->input('id_padre'))->handle($request->all());
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
    public function autocomplete($idPlan,Request $request)
    {
        $texto = "%" . preg_replace('/\s+/', '%', strtoupper($request->get('term'))) . "%";
        $records=(new ModuloCompetencia())->autocomplete($idPlan,$texto);
        return response()->json($records);
    }

    public function dataTable($idPlan)
    {
        set_time_limit(0);
        $records=ModuloCompetencia::selectRaw('
            modulos_competencias.id,modulos_competencias.codigo,modulos_competencias.descripcion,
            mc.codigo as modulo,
            mc.descripcion as modulo_descripcion
        ')
            ->join('modulos_competencias as  mc','mc.id','=','modulos_competencias.id_padre')
            ->where('modulos_competencias.id_padre','<>',0)
            ->where('modulos_competencias.estado','A')
            ->where('mc.id_plan_estudio',$idPlan)->orderBy('id','desc');
        return DataTables::of($records)
            ->filterColumn('modulo', function($query, $keyword) {
                $sql = "upper(mc.codigo)  ilike ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->toJson();
    }

}
