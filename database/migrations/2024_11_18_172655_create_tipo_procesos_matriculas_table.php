<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoProcesosMatriculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_procesos_matriculas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipo_procesos_matriculas')->insert([
            [
                'descripcion' => 'NORMAL',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'REINGRESO',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'RESERVA',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'TRASLADO INGRESO',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'TRASLADO SALIDA',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ]
        ]);
        DB::table('periodo_clases')->insert([
            [
                'id'=>0,
                'anio'=>0,
                'descripcion'=>'NINGUNO',
                'estado'=>'I',
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
        Schema::dropIfExists('tipo_procesos_matriculas');
    }
}
