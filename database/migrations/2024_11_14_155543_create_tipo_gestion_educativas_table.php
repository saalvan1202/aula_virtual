<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoGestionEducativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_gestion_educativas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('tipo_gestion_educativas')->insert([

            [
                'descripcion' => 'PUBLICO',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => 'PRIVADO',
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
        Schema::dropIfExists('tipo_gestion_educativas');
    }
}
