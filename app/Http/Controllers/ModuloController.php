<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class ModuloController extends Controller
{
    public function index()
    {
        return Inertia::render('Security/Module',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'titulo'=>'required|max:150',
            'ruta'=>'required|max:100',
            'orden'=>'required|integer'
        ]);
        $obj=Modulo::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Modulo();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        session()->forget('menus');
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=Modulo::findOrFail($id);
        return response()->json($record);
    }

    public function destroy($id)
    {
        $obj=Modulo::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function parent()
    {

        $records=Modulo::selectRaw('id,titulo')
            ->where('id_padre',0)->orderBy('orden')->get();
        return response()->json($records);
    }

    public function dataTable()
    {
        $records=Modulo::selectRaw('modulos.id,titulo,ruta')->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }
}
