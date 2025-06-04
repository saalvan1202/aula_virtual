<?php

namespace App\Actions;

use App\Models\ModuloCompetencia;

class GuardarModuloCompetencia
{
    protected $attributes;
    public function isModulo()
    {
        $this->attributes=[
          'id_padre'=>0,
          'id_componente'=>0,
        ];
        return $this;
    }
    public function isCompetencia($idModulo)
    {
        $modulo=ModuloCompetencia::find($idModulo);
        $this->attributes=[
            'id_plan_estudio'=>$modulo->id_plan_estudio,
        ];
        return $this;
    }
    public function handle($attributes=[])
    {
        $obj=ModuloCompetencia::find($attributes['id']);
        if(is_null($obj)){
            $obj=new ModuloCompetencia();
            $obj->estado='A';
        }
        $obj->fill($attributes);
        foreach($this->attributes as $key=>$attribute){
            $obj->{$key}=$attribute;
        }
        $obj->save();
        return $obj;
    }

}
