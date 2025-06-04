<?php

namespace App\Actions;

use App\Models\ProgramaEstudio;
use Illuminate\Http\Request;

class GuardarProgramaEstudio
{
    public function handle(Request $request)
    {
        $obj=ProgramaEstudio::find($request->input('id'));
        if(is_null($obj)){
            $obj=new ProgramaEstudio();
            $obj->estado='A';
        }
        $obj->fill($request->all());
        $obj->save();
        $this->sedes($obj,$request->input('sedes'));
        return $obj;
    }
    public function sedes(ProgramaEstudio $obj,$sedes)
    {
        $ids=[];
        foreach($sedes as $sede){
            $found=$obj->sedes()->where('id_sede',$sede['id_sede'])->first();
            if($found){
                $found->estado='A';
                $found->update();
                $ids[] = $sede['id_sede'];
                continue;
            }
            $obj->sedes()->create([
                'id_sede'=>$sede['id_sede'],
                'estado'=>'A'
            ]);
            $ids[]=$sede['id_sede'];
        }
        $obj->sedes()->whereNotIn('id_sede',$ids)->update([
            'estado'=>'I'
        ]);
    }
}
