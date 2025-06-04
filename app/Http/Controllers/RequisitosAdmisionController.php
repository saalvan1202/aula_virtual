<?php

namespace App\Http\Controllers;

use App\Models\Instituto;
use App\Models\ModalidadAdmision;
use App\Models\ModalidadRequisito;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class RequisitosAdmisionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admision/RequisitosAdmision',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'tipo_modalidades'=>ModalidadAdmision::where('estado','A')->get()
        ]);
    }
    public function filterModalidad($tipo){
        //Hacemos un where para obtener la modaldidad
        $modalidades=ModalidadAdmision::where('estado','A')->where('tipo',$tipo)->get();
 
        return response()->json($modalidades);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'id_modalidades_admision'=>'required|integer',
            'obligatorio'=>'required',
            'descripcion'=>'required|max:200',
    
        ],[],['descripcion'=>'Descripcion','obligatorio'=>'Obligatorio']);
        $obj=ModalidadRequisito::find($request->input('id'));
        if(is_null($obj)){
            $obj=new ModalidadRequisito();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=ModalidadRequisito::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=ModalidadRequisito::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=ModalidadRequisito::selectRaw('id,modalidades_admision.descripcion as modalidad,descripcion,obligatorio')->where('estado','A')->
        join('modalidades_admision','modalidades_admision.id','=','modalidades_requisitos.id_modalidades_admision')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
