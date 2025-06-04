<?php

namespace App\Http\Controllers;

use App\Models\Instituto;
use App\Models\ModalidadAdmision;
use App\Models\ModalidadProcesoAdmision;
use App\Models\ModalidadRequisito;
use App\Models\ProcesoAdmision;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class ModalidadesProcesoAdmisionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admision/ProcesoAdmision',[
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
            'id_modalidad_admision'=>'required',
            'id_proceso_admision'=>'required',
            'fecha_examen'=>'required|date',
            'hora_examen'=>'required|date_format:H:i:s',
            'vacantes'=>'required',
    
        ]);
        $obj=ModalidadProcesoAdmision::find($request->input('id'));
        if(is_null($obj)){
            $obj=new ModalidadProcesoAdmision();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function destroy($id)
    {
        $obj=ModalidadProcesoAdmision::findOrFail($id);
        $obj->delete();
        $obj->update();
        return response()->json('ok');
    }
    public function edit($id)
    {
        $record=ProcesoAdmision::findOrFail($id);
        return response()->json($record);
    }
    public function dataTable()
    {
        $records=ProcesoAdmision::selectRaw('id,descripcion,fecha_inicio,fecha_fin,puntaje_minimo')->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

}
