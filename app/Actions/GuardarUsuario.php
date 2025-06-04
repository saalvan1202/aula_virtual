<?php

namespace App\Actions;

use App\Models\User;
use App\Services\Variable;
use Illuminate\Http\Request;

class GuardarUsuario
{
    protected $attributes=[];
    protected $keyId='id';
    public function setKeyId($key)
    {
        $this->keyId=$key;

        return $this;
    }
    public function handle($person,Request $request)
    {
        $obj=User::find($request->input($this->keyId));
        if(is_null($obj)){
            $obj=new User();
            $obj->estado='A';
            $obj->password=bcrypt($request->input('password'));
        }else{
            if($request->input('password')!=Variable::PASSWORD){
                $obj->password=bcrypt($request->input('password'));
            }
        }
        $obj->id_persona=$person->id;
        $obj->fill($request->all());
        foreach($this->attributes as $key=>$attribute){
            $obj->{$key}=$attribute;
        }
        $obj->save();
        return $obj;
    }
    public function isTeacher(): GuardarUsuario
    {
        $this->attributes=[
            'id_perfil'=>Variable::DOCENTE,
        ];
        return $this;
    }
    public function isStudent(): GuardarUsuario
    {
        $this->attributes=[
            'id_perfil'=>Variable::ESTUDIANTE,
        ];
        return $this;
    }
}
