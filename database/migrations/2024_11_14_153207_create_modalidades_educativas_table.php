<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateModalidadesEducativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modalidades_educativas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',100);
            $table->string('abreviatura',50);
            $table->string('estado',1);
            $table->timestamps();
        });
        $now=now();
        DB::table('modalidades_educativas')->insert([

            [
                'descripcion' => Str::upper('Educación Básica Regular'),
                'abreviatura' => 'EBR',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => Str::upper('Educación Básica Alternativa'),
                'abreviatura' => 'EBA',
                'estado' => 'A',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'descripcion' => Str::upper('Educación Básica Especial'),
                'abreviatura' => 'EBE',
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
        Schema::dropIfExists('modalidades_educativas');
    }
}
