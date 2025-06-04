<?php

namespace App\Http\Controllers;

use App\Actions\GuardarModuloCompetencia;
use App\Models\IndicadorCompetencia;
use App\Models\ModuloCompetencia;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class IndicadorCompetenciaController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'id_modulo_competencia'=>'required|integer|required_zero',
            'numero'=>'required|integer',
            'descripcion'=>'required',
        ],[],[
            'id_modulo_competencia'=>'Unidad Competencia',
        ]);
        $obj=IndicadorCompetencia::find($request->input('id'));
        if(is_null($obj)){
            $obj=new IndicadorCompetencia();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);

    }
    public function edit($id)
    {
        $record=IndicadorCompetencia::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=IndicadorCompetencia::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }

    public function dataTable($idCompetencia)
    {
        set_time_limit(0);
        $records=IndicadorCompetencia::selectRaw('
            numero,descripcion
        ')
            ->where('estado','A')
            ->where('id_modulo_competencia',$idCompetencia)->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
