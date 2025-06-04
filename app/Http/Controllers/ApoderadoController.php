<?php

namespace App\Http\Controllers;


use App\Actions\GuardarPersona;
use App\Http\Requests\ApoderadoRequest;
use App\Models\Apoderado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApoderadoController
{
    public function index()
    {

    }
    public function store(ApoderadoRequest $request,GuardarPersona $guardarPersona)
    {
        set_time_limit(0);
        return DB::transaction(function () use ($request,$guardarPersona) {
            $person=$guardarPersona->handle($request);
            $obj=Apoderado::find($request->input('id'));
            if(is_null($obj)){
                $obj=new Apoderado();
                $obj->estado='A';
            }
            $obj->id_persona=$person->id;
            $obj->fill($request->all());
            $obj->save();
            $obj->numero_documento=$person->numero_documento;
            $obj->apellidos_completos=$person->apellidos_completos;
            $obj->tipo_documento=$person->tipoDocumentoIdentidad->abreviatura;
            return response()->json($obj);
        });

    }
    public function edit($id)
    {
        $record=Apoderado::joinPersona()->where('usuarios.id',$id)->firstOrFail();
        return response()->json($record);
    }
    public function destroy($id)
    {
        $obj=Apoderado::findOrFail($id);
        $obj->estado='I';
        $obj->update();
        return response()->json('ok');
    }
    public function autocomplete(Request $request)
    {
        $texto = "%" . preg_replace('/\s+/', '%', strtoupper($request->get('term'))) . "%";
        $records=(new Apoderado())->autocomplete($texto);
        return response()->json($records);
    }
    public function dataTable()
    {

    }
}
