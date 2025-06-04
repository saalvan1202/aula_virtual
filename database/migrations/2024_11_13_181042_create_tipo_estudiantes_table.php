<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipo_estudiantes')->insert([
           [
               'descripcion' => 'HISTÓRICO',
               'estado' => 'A',
               'created_at'=>$now,
               'updated_at'=>$now
           ],
            [
                'descripcion' => 'BECADO',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'INGRESO DIRECTO',
                'estado' => 'A',
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
        Schema::dropIfExists('tipo_estudiantes');
    }
}
