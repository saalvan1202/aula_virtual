<?php

namespace App\Http\Requests;

use App\Models\Certificado;
use App\Models\Estudiante;
use Illuminate\Foundation\Http\FormRequest;

class CertificadoModularRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $student=Estudiante::find($this->input('id_estudiante'));
        $idPlan=0;
        if($student){
           $idPlan=$student->id_plan_estudio;
        }
        $this->merge([
            'id_plan_estudio'=>$idPlan,
        ]);
    }

    public function rules()
    {
        return [
            'id'=>'required',
            'id_estudiante'=>'required|integer|required_zero',
            'id_referencia'=>'required|integer|required_zero',
            'fecha_inicio'=>'required|date_format:Y-m-d',
            'fecha_fin'=>'required|date_format:Y-m-d',
            'numero_registro_institucional'=>'required|max:70',
            //'nombre_director'=>'required|max:150',
        ];
    }
    public function attributes()
    {
        return [
            'id_referencia'=>'Modulo',
            'id_estudiante'=>'Estudiante',
            'numero_registro_institucional'=>'Numero de Registro Institucional'
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $found=Certificado::where('id_estudiante',$this->input('id_estudiante'))
                ->where('id_plan_estudio',$this->input('id_plan_estudio'))
                ->where('id_referencia',$this->input('id_referencia'))
                ->where('tipo','MODULAR')
                ->where('estado','A')
                ->where('id','<>',$this->input('id'))->first();
            if($found){
                $validator->errors()->add('id_estudiante', 'ya existe certificado modular para este modulo');
            }
        });
    }
}
