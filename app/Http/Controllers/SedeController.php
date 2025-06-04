<?php

namespace App\Http\Controllers;

use App\Models\Instituto;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class SedeController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/Sede',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'institutos'=>Instituto::where('estado','A')->get()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'id_instituto'=>'required|integer',
            'descripcion'=>'required|max:100',
        ]);
        $obj=Sede::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Sede();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=Sede::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=Sede::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=Sede::selectRaw('sedes.id,sedes.descripcion,institutos.denominacion as instituto')
            ->join('institutos','institutos.id','=','sedes.id_instituto')
            ->where('sedes.estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
