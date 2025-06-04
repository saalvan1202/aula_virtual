<?php

namespace App\Http\Controllers;

use App\Models\Ambiente;
use App\Models\Instituto;
use App\Models\Sede;
use App\Models\TipoAmbiente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class AmbienteController extends Controller
{
    public function index()
    {
        return Inertia::render('Academy/Ambiente',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'sedes'=>Sede::where('estado','A')->get(),
            'tipo_ambiente'=>TipoAmbiente::where('estado','A')->get()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'id_sede'=>'required|integer',
            'descripcion'=>'required|max:100',
            'id_tipo_aula'=>'required|integer',
            'piso'=>'required|integer',
            'ubicacion'=>'required|max:100',
            'aforo'=>'required|integer',
        ],[],[
            'id_sede'=>'sede',
            'id_tipo_aula'=>'tipo Aula'
        ]);
        $obj=Ambiente::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Ambiente();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=Ambiente::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=Ambiente::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=Ambiente::selectRaw('aulas.id,aulas.descripcion,sedes.descripcion as sedes,tipo_aulas.descripcion as tipo_aulas,
        aulas.piso,aulas.ubicacion,aulas.aforo')
        ->join('sedes', 'sedes.id', '=', 'aulas.id_sede')
        ->join('tipo_aulas', 'tipo_aulas.id', '=', 'aulas.id_tipo_aula') 
            ->where('aulas.estado','A')->orderBy('id','desc');
        return DataTables::of($records)->toJson();
    }

    public function getAmbientesByTipo($id_tipo_aula)
    {
        $ambientes = Ambiente::where('estado', 'A')
            ->where('id_tipo_aula', $id_tipo_aula)
            ->get();
        return response()->json($ambientes);
    }

}
