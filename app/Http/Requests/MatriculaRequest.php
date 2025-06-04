<?php

namespace App\Http\Requests;

use App\Models\Matricula;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatriculaRequest extends FormRequest
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
            'id_tipo_proceso_matricula'=>'required',
            'id_tipo_matricula'=>'required',
            'id_estudiante' => 'required',
            'id_periodo_clases'=>'required',
            'id_turno'=>'required',
            'id_seccion'=>'required',
            'id_ciclo'=>'required',
            //'fecha_matricula'=>'required',
            //'id_usuario'=>'required',
            'id_cursos_docente'=>'required',

        ];
    }
    public function attributes()
    {
        return [
            'id_tipo_proceso_matricula'=>'Tipo Proceso Matricula',
            'id_tipo_matricula'=>'Tipo Matricula',
            'id_estudiante' => 'Estudiante',
            'id_periodo_clases'=>'Periodo de Clases',
            'id_turno'=>'Turno',
            'id_seccion'=>'Seccion',
            'id_ciclo'=>'Ciclo',
            'fecha_matricula'=>'Fecha Matricula',
            //'id_usuario'=>'Usuario',
            'id_matricula'=>'Matricula',
            'id_cursos_docente'=>'Curso Docente',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $found=Matricula::where('id_estudiante',$this->input('id_estudiante'))
                ->where('id_periodo_clases',$this->input('id_periodo_clases'))
                ->where('estado','A')
                ->where('id','<>',$this->input('id'))
                ->first();
            if($found){
                $validator->errors()->add('id_estudiante', 'El estudiante ya se encuentra matriculado en este periodo de clases');
            }
        });
    }
}
