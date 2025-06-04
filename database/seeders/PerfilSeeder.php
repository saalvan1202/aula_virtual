<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now=now();
        DB::table('perfiles')->insert([
            [
                'descripcion' => 'SUPERADMINISTRADOR',
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'ADMINISTRADOR',
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'DIRECTOR',
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'DOCENTE',
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'ESTUDIANTE',
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'SECRETARIO ACADÉMICO',
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
