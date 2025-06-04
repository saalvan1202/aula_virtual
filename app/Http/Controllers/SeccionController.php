<?php

namespace App\Http\Controllers;

use App\Models\PeriodoClase;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class SeccionController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/Section',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'descripcion'=>'required|max:100',
        ]);
        $obj=Seccion::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Seccion();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=Seccion::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=Seccion::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=Seccion::selectRaw('id,descripcion')
            ->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
