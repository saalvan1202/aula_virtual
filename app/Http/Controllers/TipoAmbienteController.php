<?php

namespace App\Http\Controllers;

use App\Models\Instituto;
use App\Models\TipoAmbiente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class TipoAmbienteController extends Controller
{
    public function index()
    {
        return Inertia::render('Mantenimiento/TipoGeneral',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'tipo_ambientes'=>TipoAmbiente::where('estado','A')->get(),
            //Mándamos como props los valor que van a cambiar en la pagina de tipo
            'page'=>[
                'Titulo'=>"Administrar Tipo Ambientes",
                'TituloModal'=>"Tipo Ambiente"
            ]
            
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'descripcion'=>'required|max:100',
        ]);
        $obj=TipoAmbiente::find($request->input('id'));
        if(is_null($obj)){
            $obj=new TipoAmbiente();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=TipoAmbiente::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=TipoAmbiente::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=TipoAmbiente::selectRaw('tipo_aulas.id,tipo_aulas.descripcion')
        ->where('tipo_aulas.estado','A');
        return DataTables::of($records)->toJson();
    }

}
