<?php

namespace App\Http\Controllers;

use App\Models\ClaseDocente;
use Illuminate\Http\Request;

class ClasesDocentesController extends Controller
{

    public function store(Request $request)
    { 
        set_time_limit(0);
        $request->validate([
            'id'=>'required',
            'descripcion'=>'required',
            'numero'=>'required',
            'fecha'=>'required',
            'hora_inicio'=>'required',
            'hora_fin'=>'required',
            'id_curso_docente'=>'required'
        ],[],[
           'descripcion'=>'Descripción',
            'numero'=>'Numero',
            'fecha'=>'Fecha',
            'hora_inicio'=>'Hora Inicio',
            'hora_fin'=>'Hora Fin',
            'id_curso_docente'=>'required' 
        ]);
       $obj=ClaseDocente::find($request->input('id'));
       if(is_null($obj)){
        $obj=new ClaseDocente();
        $obj->estado='A';
       }
       $obj->fill($request->all());
       $obj->save();
       return response()->json($obj);
    }
    public function getClases($id){
        $clases=ClaseDocente::where('estado','A')->where('id_curso_docente',$id)->orderBy('id','desc')->get();
        return response()->json($clases);
    }
    public function edit($id)
    {
        $record=ClaseDocente::findOrFail($id);
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=ClaseDocente::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
}
