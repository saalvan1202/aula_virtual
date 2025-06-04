<?php

namespace App\Http\Requests;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstudianteRequest extends FormRequest
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
    public function prepareForValidation()
    {
        $merge=[];
        if(!$this->filled('id_modalidad_educativa')){
            $merge['id_modalidad_educativa']=1;
        }
        if(!$this->filled('id_tipo_gestion_educativa')){
            $merge['id_tipo_gestion_educativa']=1;
        }
        if(!$this->filled('id_lengua_materna')){
            $merge['id_lengua_materna']=1;
        }
        if(!$this->filled('id_ubigeo')){
            $merge['id_ubigeo']=1;
        }
        if(!$this->filled('id_ubigeo_institucion')){
            $merge['id_ubigeo_institucion']=1;
        }
        if(!$this->filled('anio_egreso_institucion')){
            $merge['anio_egreso_institucion']='2025';
        }
        if(count($merge)>0){
            $this->merge($merge);
        }
    }
    public function rules()
    {
        return [
            'id'=>'required',
            'id_tipo_documento_identidad'=>'required',
            'numero_documento'=>'required|max:150',
            'nombres'=>'required',
            'apellido_paterno'=>'required',
            'apellido_materno'=>'required',
            'usuario'=>[
                'required',
                Rule::unique((new User())->getTable(), 'usuario')
                    ->ignore($this->get('id_usuario'), (new User())->getKeyName())
            ],
            'fecha_nacimiento'=>'required',
            'sexo'=>'required',
            'estado_civil'=>'',
            'telefono'=>'required',
            'id_sede' => 'required',
            'id_programa_estudio' => 'required',
            'id_plan_estudio' => 'required',
            'id_periodo_ingreso'=>'required',
            'id_tipo_estudiante' => 'required',
            'id_lengua_materna' => '',
            'id_pais' => '',
            'id_modalidad_educativa' => '',
            'id_nivel_educativa' => '',
            'id_tipo_gestion_educativa' => '',
            'id_ubigeo' => '',
            'direccion' => '',
            'celular' => '',
            'discapacidad' => '',
            'menor_edad' => 'required',
            'codigo_modular' => '',
            'institucion' => '',
            'id_ubigeo_institucion' => '',
            'direccion_institucion' => '',
            'anio_egreso_institucion' => '',
            'id_ciclo' => 'required',
            'estado_matricula' => 'required',
            'password'=>'required|min:8',
            'email'=>[
                'required',
                'email',
                Rule::unique((new User())->getTable(), 'email')
                    ->ignore($this->get('id_usuario'), (new User())->getKeyName())
            ],
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
            $found=Persona::where('id_tipo_documento_identidad',$this->input('id_tipo_documento_identidad'))
                ->where('numero_documento',$this->input('numero_documento'))
                ->where('id','<>',$this->input('id_personas'))
                ->where('estado','A')->first();
            if($found){
                $validator->errors()->add('numero_documento', 'ya existe estudiante con este numero documento');
            }
        });
    }
}
