<?php

namespace App\Http\Controllers;

use App\Models\PeriodoClase;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class PeriodoClaseController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/ClassPeriod',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'anio'=>'required|integer',
            'descripcion'=>'required|max:100',
            'fecha_inicio'=>'required|date_format:Y-m-d',
            'fecha_fin'=>'required|date_format:Y-m-d',
        ]);
        $obj=PeriodoClase::find($request->input('id'));
        if(is_null($obj)){
            $obj=new PeriodoClase();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=PeriodoClase::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=PeriodoClase::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=PeriodoClase::selectRaw('id,anio,descripcion,fecha_inicio,fecha_fin')
            ->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
