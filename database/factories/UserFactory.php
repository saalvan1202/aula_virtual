<?php

namespace Database\Factories;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'id_persona'=>Persona::factory(),
            'id_referencia'=>0,
            'es_usuario'=>'S',
            'id_perfil' => $this->faker->numberBetween(1,6),
            'email' => $this->faker->unique()->safeEmail,
            'id_tipo_contrato'=>$this->faker->numberBetween(0,2),
            'telefono'=>$this->faker->phoneNumber,
            'profesion'=>Str::upper($this->faker->jobTitle),
            'usuario' => $this->faker->userName,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'estado'=>'A'
        ];
    }
}
