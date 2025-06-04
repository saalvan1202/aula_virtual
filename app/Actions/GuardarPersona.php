<?php

namespace App\Actions;

use App\Models\Persona;
use Illuminate\Http\Request;

class GuardarPersona
{
    public function handle(Request $request)
    {
        $obj=Persona::find($request->input('id_personas'));
        if(is_null($obj)){
            $obj=Persona::where('id_tipo_documento_identidad',$request->input('id_tipo_documento_identidad'))
                ->where('numero_documento',$request->input('numero_documento'))->first();
            if($obj && $obj->estado=='I'){
                $obj->estado='A';
            }
        }
        if(is_null($obj)){
            $obj=new Persona();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        return $obj;
    }
}
