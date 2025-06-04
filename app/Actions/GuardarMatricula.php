<?php

namespace App\Actions;

use App\Models\Matricula;
use App\Models\Persona;
use Illuminate\Http\Request;

class GuardarMatricula
{
    public function handle(Request $request)
    {
        $obj=Matricula::find($request->input('id'));
        if(is_null($obj)){
            $obj=new Matricula();
            $obj->estado='A';
            $obj->id_usuario=auth()->user()->id;
        }

        $obj->fill($request->all());
        $obj->fecha_matricula=date('Y-m-d');
        $obj->save();
        return $obj;
    }
}
