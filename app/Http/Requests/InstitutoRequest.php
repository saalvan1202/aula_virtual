<?php

namespace App\Http\Requests;

use App\Services\ValidacionArchivo;
use Illuminate\Foundation\Http\FormRequest;

class InstitutoRequest extends FormRequest
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
            'denominacion'=>'required|max:200',
            'codigo'=>'nullable|max:15',
            'direccion'=>'nullable|max:200',
            'telefono'=>'nullable|max:50',
            'id_tipo_gestion_educativa'=>'required|integer|required_zero',
            'email'=>'nullable|email',
            'director_general'=>'nullable|max:150',
            'id_ubigeo'=>'required|integer|required_zero',
        ];
    }
    public function attributes()
    {
        return [
            'email'=>'Correo',
            'id_tipo_gestion_educativa'=>'Tipo Gestion',
            'director_general'=>'Director General',
            'id_ubigeo'=>'Ubigeo',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validacion=new ValidacionArchivo();
            $validacion->setExtension(['png']);
            if($this->hasFile('logo')){
                $error=$validacion->validate($this->file('logo'))->getError();
                if(!is_null($error)){
                    $validator->errors()->add('logo',$error);
                }
            }
            if($this->hasFile('banner')){
                $error=$validacion->validate($this->file('banner'))->getError();
                if(!is_null($error)){
                    $validator->errors()->add('logo',$error);
                }
            }
        });
    }
}
