<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoIdentidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now=now();
        DB::table('tipo_documentos_identidades')->insert([
            [
                'descripcion' => 'DNI - DOC. NACIONAL DE IDENTIDAD',
                'abreviatura' => 'D.N.I',
                'estado' => 'A',
                'codigo'=>'1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'RUC - REGISTRO ÚNICO DE CONTRIBUYENTE',
                'abreviatura' => 'R.U.C',
                'estado' => 'I',
                'codigo'=>'6',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'CARNET DE EXTRANJERÍA',
                'abreviatura' => 'CARNET EXT.',
                'estado' => 'A',
                'codigo'=>'4',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'descripcion' => 'PASAPORTE',
                'abreviatura' => 'PASAPORTE',
                'estado' => 'A',
                'codigo'=>'7',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
