<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PersonaFactory extends Factory
{

    protected $model = Persona::class;

    public function definition(): array
    {
        return [
            'nombres' => Str::upper($this->faker->firstName),
            'apellido_paterno'=>Str::upper($this->faker->lastName),
            'apellido_materno'=>Str::upper($this->faker->lastName),
            'id_tipo_documento_identidad'=>1,
            'numero_documento'=>$this->faker->unique()->dni(),
            'fecha_nacimiento'=>$this->faker->date(),
            'sexo'=>$this->faker->randomElement(['M','F']),
            'estado_civil'=>$this->faker->randomElement(['SOLTERO','CASADO','CONVIVIENTE']),
            'estado'=>'A'
        ];
    }
}
