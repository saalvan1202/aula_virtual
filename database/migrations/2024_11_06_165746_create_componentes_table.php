<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateComponentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('componentes', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('componentes')->insert([
            [
                'id'=>0,
                'descripcion'=>'NINGUNA',
                'estado'=>'I',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
        ]);
        DB::table('componentes')->insert([
            [
                'descripcion'=>'COMPETENCIAS TÉCNICAS O ESPECIFICAS',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion'=>'COMPETENCIAS PARA LA EMPLEABILIDAD',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion'=>'COMPETENCIAS DE INVESTIGACION APLICADA E INNOVACIÓN',
                'estado'=>'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_competencias');
    }
}
