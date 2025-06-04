<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now=now();
        $per=DB::table('personas')->insertGetId([
            'nombres'=>'Admin',
            'apellido_paterno'=>'Administrador',
            'apellido_materno'=>'-',
            'id_tipo_documento_identidad'=>1,
            'numero_documento'=>'45825265',
            'estado'=>'A',
            'created_at'=>$now,
            'updated_at'=>$now,
        ]);
        DB::table('usuarios')->insertGetId([
            'id_perfil'=>1,
            'id_persona'=>$per,
            'usuario'=>'master',
            'password'=>bcrypt('Instituto$21'),
            'estado'=>'A',
            'created_at'=>$now,
            'updated_at'=>$now,
            'id_referencia'=>0,
            'referencia'=>'usuarios'
        ]);
    }
}
