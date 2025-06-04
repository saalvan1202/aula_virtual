<?php

namespace App\Http\Controllers;

use App\Models\Instituto;
use App\Models\TipoAmbiente;
use App\Models\TipoMatricula;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Yajra\DataTables\DataTables;

class TipoMatriculaController extends Controller
{
    public function index()
    {
        return Inertia::render('Mantenimiento/TipoGeneral',[
            'urlRoute'=>ltrim(request()->route()->getPrefix(),'/'),
            'page'=>[
                'Titulo'=>"Administrar Tipo Matricula",
                'TituloModal'=>"Tipo Matricula"
            ]
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id'=>'required',
            'descripcion'=>'required|max:100',
        ]);
        $obj=TipoMatricula::find($request->input('id'));
        if(is_null($obj)){
            $obj=new TipoMatricula();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return response()->json($obj);
    }
    public function edit($id)
    {
        $record=TipoMatricula::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=TipoMatricula::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function dataTable()
    {
        $records=TipoMatricula::selectRaw('id,descripcion')
        ->where('tipo_matriculas.estado','A');
        return DataTables::of($records)->toJson();
    }

}
