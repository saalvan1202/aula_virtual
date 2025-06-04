<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CicloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now=now();
        DB::table('ciclos')->insert([
            [
                'descripcion' => 'I',
                'orden'=>1,
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'II',
                'orden'=>2,
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'III',
                'orden'=>3,
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'IV',
                'orden'=>4,
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'V',
                'orden'=>5,
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'VI',
                'orden'=>6,
                'estado' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
