<?php

namespace App\Http\Controllers;

use App\Models\Instituto;
use App\Models\ModalidadAdmision;
use App\Models\ModalidadRequisito;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class ModalidadesAdmisionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admision/ModalidadAdmision',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'descripcion'=>'required|max:200',
            'tipo'=>'required|max:15',
        ],[],['tipo'=>'Tipo Modalidad','descripcion'=>'Descripcion']);
        $obj=ModalidadAdmision::find($request->input('id'));
        if(is_null($obj)){
            $obj=new ModalidadAdmision();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=ModalidadAdmision::findOrFail($id);
        return response()->json($record);
    }
    public function dataTable()
    {
        $records=ModalidadAdmision::selectRaw('id,tipo,descripcion')->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }
    public function dataTableRequisitos($id)
    {
        $records=ModalidadRequisito::selectRaw('id,descripcion')->where('estado','A')->where('id_modalidades_admision',$id)->orderBy('id','desc');
        return DataTables::of($records)->toJson();  
    }

}
