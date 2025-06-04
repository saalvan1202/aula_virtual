<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstudianteFactory extends Factory
{

    protected $model = Estudiante::class;

    public function definition(): array
    {
        $id_tipo=$this->faker->numberBetween(1,3);
        $estado_matricula='REGULAR';
        if($id_tipo==3){
            $estado_matricula='INGRESANTE';
        }
        $plan=DB::selectOne("select pe.id,pe.id_programa_estudio,ps.id_sede 
            from plan_estudios as pe
            inner join programas_sedes ps on(ps.id_programa_estudio=pe.id_programa_estudio and ps.estado='A')
            inner join programa_estudios pee on(pee.id=ps.id_programa_estudio and pee.estado='A')
            where pe.estado ='A'
            order by 
            random() limit  1");
        $id_plan=$plan->id;
        $programa=$plan->id_programa_estudio;
        /*$programa=$this->faker->numberBetween(2,3);
        $id_plan=3;
        if($programa==3){
            $id_plan=4;
        }*/
        return [
            'id_usuario' => User::factory(),
            'id_sede'=>$plan->id_sede,
            'id_programa_estudio'=>$programa,
            'id_plan_estudio'=>$id_plan,
            'id_tipo_estudiante'=>$id_tipo,
            'id_lengua_materna'=>1,
            'id_pais'=>174,
            'id_modalidad_educativa'=>1,
            'id_nivel_educativa'=>3,
            'id_tipo_gestion_educativa'=>$this->faker->numberBetween(1,2),
            'estado'=>'A',
            'id_ubigeo'=>$this->faker->randomElement(['18293','18358','18345','18335']),
            'direccion'=>Str::upper($this->faker->address),
            'celular'=>$this->faker->phoneNumber,
            'discapacidad'=>'N',
            'menor_edad'=>'N',
            'codigo_modular'=>$this->faker->numerify('#######'),
            'institucion'=>Str::upper($this->faker->text(100)),
            'id_ubigeo_institucion'=>$this->faker->randomElement(['18293','18358','18345','18335']),
            'direccion_institucion'=>Str::upper($this->faker->address),
            'anio_egreso_institucion'=>$this->faker->randomElement(['2021','2022','2023','2024']),
            'id_ciclo'=>$this->faker->numberBetween(1,4),
            'estado_matricula'=>$estado_matricula,
            'id_periodo_ingreso'=>0
        ];
    }
}
