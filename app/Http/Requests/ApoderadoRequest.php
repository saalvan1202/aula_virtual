<?php

namespace App\Http\Requests;

use App\Models\Apoderado;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApoderadoRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'=>'required',
            'id_personas'=>'required',
            'id_tipo_documento_identidad'=>'required',
            'numero_documento'=>'required|max:150',
            'nombres'=>'required|max:150',
            'apellido_paterno'=>'required|max:150',
            'apellido_materno'=>'required|max:150',
            'email'=>'nullable|email|max:150',
            'direccion'=>'required',
            'telefono'=>'required',
        ];
    }
    public function attributes()
    {
        return [
            'apellido_paterno'=>'Apellido Paterno',
            'apellido_materno'=>'Apellido Materno',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $found=Apoderado::where('id_persona',$this->input('id_personas'))
                ->where('id','<>',$this->input('id_persona'))
                ->where('estado','A')->first();
            if($found){
                $validator->errors()->add('numero_documento', 'ya existe apoderado con este numero documento');
            }
        });
    }
}
