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

class ProcesosAdmisionController extends Controller
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
            'fecha_inicio'=>'required|date',
            'fecha_fin'=>'required|date',
            'descripcion'=>'required|max:200',
            'puntaje_minimo'=>'required',
    
        ],[],[
            'fecha_inicio'=>'Fecha de Inicio',
            'fecha_fin'=>'Fecha de Fin',
            'descripcion'=>'Descripcion',
            'puntaje_minimo'=>'Puntaje Minimo'
        ]);
        $obj=ProcesoAdmision::find($request->input('id'));
        if(is_null($obj)){
            $obj=new ProcesoAdmision();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=ProcesoAdmision::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=ProcesoAdmision::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=ProcesoAdmision::selectRaw('id,descripcion,fecha_inicio,fecha_fin,puntaje_minimo')->where('estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }
    public function dataTableDetalle($id)
    {
        $records=ModalidadProcesoAdmision::selectRaw('modalidad_procesos_admision.id,modalidad_procesos_admision.fecha_examen
        ,modalidad_procesos_admision.hora_examen,modalidad_procesos_admision.vacantes,modalidades_admision.descripcion as modalidades')
        ->join('modalidades_admision','modalidades_admision.id','=','modalidad_procesos_admision.id_modalidad_admision')
        ->where('modalidad_procesos_admision.estado','A')
        ->where('modalidad_procesos_admision.id_proceso_admision',$id)->orderBy('id','desc');
        return DataTables::of($records)->toJson();  
    }

}
