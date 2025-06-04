<?php

namespace App\Http\Requests;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'=>'required',
            'id_perfil'=>'required',
            'id_persona'=>'required',
            'id_tipo_documento_identidad'=>'required',
            'numero_documento'=>'required|max:150',
            'nombres'=>'required',
            'apellido_paterno'=>'required',
            'apellido_materno'=>'required',
            'usuario'=>[
                'required',
                Rule::unique((new User())->getTable(), 'usuario')
                    ->ignore($this->get('id'), (new User())->getKeyName())
            ],
            'password'=>'required|min:8',
            'email'=>[
                'required'
            ],
            'telefono'=>'required',
        ];
    }
    public function attributes()
    {
        return [
            'apellido_paterno'=>'Apellido Paterno',
            'apellido_materno'=>'Apellido Materno',
            'id_perfil'=>'Perfil',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $found=Persona::where('id_tipo_documento_identidad',$this->input('id_tipo_documento_identidad'))
                ->where('numero_documento',$this->input('numero_documento'))
                ->where('id','<>',$this->input('id_persona'))
                ->where('estado','A')->first();
            if($found){
                $validator->errors()->add('numero_documento', 'ya existe un usuario con este numero documento');
            }
        });
    }
}
